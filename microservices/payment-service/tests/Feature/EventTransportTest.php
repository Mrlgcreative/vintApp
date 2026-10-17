<?php

namespace Tests\Feature;

use App\Contracts\EventPublisher;
use App\Models\OutboxMessage;
use App\Models\Payment;
use App\Models\ProcessedWebhookEvent;
use App\Services\NullEventPublisher;
use App\Services\OutboxRelay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class EventTransportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // L'outbox est la source de vérité : les tests portent sur son contenu,
        // pas sur la présence d'un Redis.
        $this->app->instance(EventPublisher::class, $this->app->make(NullEventPublisher::class));
    }

    /**
     * Payload M-Pesa : les noms de champs sont ceux du parser
     * (ProviderPayloadParser::mpesa), pas ceux du monolithe.
     */
    private function mpesaPayload(string $reference, string $resultCode = '0', string $desc = 'Accepted'): array
    {
        return [
            'TransID' => 'TRX-'.$reference,
            'BillRefNumber' => $reference,
            'ResultCode' => $resultCode,
            'TransAmount' => '5000',
            'Currency' => 'USD',
            'MSISDN' => '243000000',
            'ResultDesc' => $desc,
        ];
    }

    private function postMpesa(array $payload, string $secret = 'secret-mpesa-dev')
    {
        return $this->postJson('/v1/webhooks/mpesa', $payload, [
            'X-Signature' => hash_hmac('sha256', json_encode($payload), $secret),
        ]);
    }

    private function makePendingPayment(string $reference, int $amount = 5000): Payment
    {
        return Payment::create([
            'user_id' => 1,
            'amount' => $amount,
            'currency' => 'USD',
            'method' => 'mobile_money',
            'provider_key' => 'mpesa',
            'status' => 'pending',
            'reference' => $reference,
        ]);
    }

    public function test_completed_webhook_writes_outbox_message_in_same_transaction(): void
    {
        $this->makePendingPayment('REF-OUTBOX-1');

        $this->postMpesa($this->mpesaPayload('REF-OUTBOX-1'))->assertOk();

        $message = OutboxMessage::query()->sole();

        $this->assertSame('payment.completed', $message->type);
        $this->assertNull($message->published_at);
        $this->assertSame(0, $message->attempts);
        $this->assertSame('mpesa', $message->payload['provider']);
        $this->assertSame(5000, $message->payload['amount']);
        // transaction_ref = référence renvoyée par l'opérateur, pas la nôtre.
        $this->assertSame('TRX-REF-OUTBOX-1', $message->payload['transaction_ref']);
        $this->assertSame('vintapp.payment', $message->stream);
        $this->assertTrue(Str::isUuid($message->event_id));
    }

    public function test_failed_webhook_records_reason(): void
    {
        $this->makePendingPayment('REF-OUTBOX-2', 2500);

        $this->postMpesa($this->mpesaPayload('REF-OUTBOX-2', '9999', 'Solde insuffisant'))->assertOk();

        $message = OutboxMessage::query()->sole();

        $this->assertSame('payment.failed', $message->type);
        $this->assertSame('Solde insuffisant', $message->payload['reason']);
        $this->assertSame(2500, $message->payload['amount']);
    }

    public function test_no_outbox_message_when_payment_already_completed(): void
    {
        $payment = $this->makePendingPayment('REF-OUTBOX-3');
        $payment->forceFill(['status' => 'completed', 'paid_at' => now()])->save();

        $this->postMpesa($this->mpesaPayload('REF-OUTBOX-3'))->assertOk();

        // Un opérateur notifie deux fois le succès : pas de second événement.
        $this->assertSame(0, OutboxMessage::query()->count());
    }

    public function test_rolled_back_payment_does_not_leave_outbox_message(): void
    {
        $this->makePendingPayment('REF-OUTBOX-4');

        /*
         * Échec métier survenant après le traitement du webhook : le
         * rollback doit emporter l'événement avec le statut du paiement.
         */
        try {
            DB::transaction(function () {
                $this->postMpesa($this->mpesaPayload('REF-OUTBOX-4'))->assertOk();

                throw new RuntimeException('échec métier après traitement');
            });

            $this->fail('La transaction aurait dû être annulée.');
        } catch (RuntimeException $e) {
            $this->assertSame('échec métier après traitement', $e->getMessage());
        }

        // La transaction ayant été annulée, ni le statut completed ni
        // l'événement ne subsistent : pas d'événement orphelin pour un
        // paiement qui n'a jamais abouti.
        $this->assertSame(0, OutboxMessage::query()->count());
        $this->assertSame('pending', Payment::query()->where('reference', 'REF-OUTBOX-4')->sole()->status);
    }

    public function test_relay_publishes_pending_messages_and_marks_them(): void
    {
        $message = OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => 'payment.completed',
            'stream' => 'vintapp.payment',
            'payload' => ['payment_id' => 1, 'amount' => 5000],
            'available_at' => now(),
        ]);

        $published = [];

        $this->app->instance(EventPublisher::class, new class($published) implements EventPublisher
        {
            public function __construct(private array &$published) {}

            public function publish(OutboxMessage $message): void
            {
                $this->published[] = $message->event_id;
            }

            public function name(): string
            {
                return 'spy';
            }
        });

        $result = $this->app->make(OutboxRelay::class)->flush();

        $this->assertSame(1, $result['published']);
        $this->assertSame(0, $result['failed']);
        $this->assertSame([$message->event_id], $published);
        $this->assertNotNull($message->fresh()->published_at);
    }

    public function test_relay_keeps_message_when_publisher_throws(): void
    {
        $message = OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => 'payment.completed',
            'stream' => 'vintapp.payment',
            'payload' => ['payment_id' => 1],
            'available_at' => now(),
        ]);

        $this->app->instance(EventPublisher::class, new class implements EventPublisher
        {
            public function publish(OutboxMessage $message): void
            {
                throw new RuntimeException('redis indisponible');
            }

            public function name(): string
            {
                return 'broken';
            }
        });

        config()->set('events.outbox.backoff_seconds', 60);

        $result = $this->app->make(OutboxRelay::class)->flush();

        $this->assertSame(0, $result['published']);
        $this->assertSame(1, $result['failed']);

        $fresh = $message->fresh();

        // Non publié, compté, reprogrammé : la prochaine passe réessaiera.
        $this->assertNull($fresh->published_at);
        $this->assertSame(1, $fresh->attempts);
        $this->assertStringContainsString('redis indisponible', $fresh->last_error);
        $this->assertTrue($fresh->available_at->isFuture());
    }

    public function test_relay_skips_backed_off_message(): void
    {
        $message = OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => 'payment.completed',
            'stream' => 'vintapp.payment',
            'payload' => ['payment_id' => 1],
            'available_at' => now()->addMinutes(10),
        ]);

        $this->app->instance(EventPublisher::class, new class implements EventPublisher
        {
            public function publish(OutboxMessage $message): void
            {
                throw new RuntimeException('ne devrait pas être appelé');
            }

            public function name(): string
            {
                return 'never';
            }
        });

        $result = $this->app->make(OutboxRelay::class)->flush();

        $this->assertSame(0, $result['published']);
        $this->assertSame(0, $result['failed']);
        $this->assertSame(0, $message->fresh()->attempts);
    }

    public function test_unknown_publisher_driver_fails_loudly(): void
    {
        config()->set('events.publisher', 'kafka-typo');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('EVENT_PUBLISHER inconnu');

        $this->app->forgetInstance(EventPublisher::class);
        $this->app->make(EventPublisher::class);
    }

    public function test_deduplication_row_still_prevents_second_effect(): void
    {
        $this->makePendingPayment('REF-OUTBOX-5');

        $payload = $this->mpesaPayload('REF-OUTBOX-5');

        $this->postMpesa($payload)->assertOk();
        $this->postMpesa($payload)->assertOk();

        // Le rejeu est acquitté sans second effet : un seul événement.
        $this->assertSame(1, OutboxMessage::query()->count());
        $this->assertSame(1, ProcessedWebhookEvent::query()->count());
    }
}
