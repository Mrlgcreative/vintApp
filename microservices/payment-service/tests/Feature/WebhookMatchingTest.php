<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Un webhook authentifié ne doit pouvoir solder QUE le paiement qui porte sa
 * propre référence. C'est la garantie centrale du service.
 */
class WebhookMatchingTest extends TestCase
{
    use RefreshDatabase;

    private function configureMpesa(string $secret): void
    {
        config()->set('payments.providers.mpesa.credential', 'MPESA_TEST_SECRET');
        putenv("MPESA_TEST_SECRET={$secret}");
        $_ENV['MPESA_TEST_SECRET'] = $secret;
    }

    public function test_webhook_without_known_reference_does_not_touch_other_payments(): void
    {
        $this->configureMpesa('secret');

        // Un autre client a un paiement en attente avec le même montant.
        $victime = $this->pendingPayment([
            'amount' => 5000,
            'reference' => 'REF-AUTRE',
            'phone_number' => '243000000',
        ]);

        $payload = [
            'TransID' => 'MPESA-INCONNU',
            'BillRefNumber' => 'REF-QUELQUE-CHOSE',
            'ResultCode' => '0',
            'TransAmount' => '5000',
            'MSISDN' => '243000000',
        ];

        $this->postJson('/v1/webhooks/mpesa', $payload, [
            'X-Signature' => $this->hmac($payload, 'secret'),
        ])->assertStatus(404);

        // Le fallback « montant + téléphone » du monolithe aurait soldé ce
        // paiement à tort. Ici il reste en attente.
        $this->assertSame('pending', $victime->fresh()->status);
    }

    public function test_webhook_matches_on_reference(): void
    {
        $this->configureMpesa('secret');

        $payment = $this->pendingPayment(['reference' => 'REF-BON']);

        $payload = [
            'TransID' => 'MPESA-1',
            'BillRefNumber' => 'REF-BON',
            'ResultCode' => '0',
            'TransAmount' => '5000',
        ];

        $this->postJson('/v1/webhooks/mpesa', $payload, [
            'X-Signature' => $this->hmac($payload, 'secret'),
        ])->assertOk();

        $this->assertSame('completed', $payment->fresh()->status);
    }

    public function test_webhook_does_not_match_across_providers(): void
    {
        $this->configureMpesa('secret');
        config()->set('payments.providers.maishapay.credential', 'MAISHA_TEST');
        putenv('MAISHA_TEST=maisha-secret');
        $_ENV['MAISHA_TEST'] = 'maisha-secret';

        $mpesa = $this->pendingPayment([
            'reference' => 'REF-PARTAGE',
            'provider_key' => 'mpesa',
            'method' => 'mpesa',
        ]);

        $payload = [
            'transactionId' => 'MAISHA-1',
            'order' => ['reference' => 'REF-PARTAGE', 'amount' => 5000, 'currency' => 'CDF'],
            'transactionStatus' => 'SUCCESS',
        ];

        $this->postJson('/v1/webhooks/maishapay', $payload, [
            'Authorization' => 'Bearer maisha-secret',
        ])->assertStatus(404);

        // Même référence, mais émise par un autre opérateur : aucun effet.
        $this->assertSame('pending', $mpesa->fresh()->status);

        putenv('MAISHA_TEST');
        unset($_ENV['MAISHA_TEST']);
    }

    public function test_replayed_webhook_is_acknowledged_without_second_effect(): void
    {
        $this->configureMpesa('secret');

        $payment = $this->pendingPayment(['reference' => 'REF-REPLAY']);

        $payload = [
            'TransID' => 'MPESA-REPLAY',
            'BillRefNumber' => 'REF-REPLAY',
            'ResultCode' => '0',
            'TransAmount' => '5000',
        ];
        $signature = ['X-Signature' => $this->hmac($payload, 'secret')];

        $this->postJson('/v1/webhooks/mpesa', $payload, $signature)
            ->assertOk()
            ->assertJsonPath('data.outcome', 'processed');

        $firstPaidAt = $payment->fresh()->paid_at;

        $this->postJson('/v1/webhooks/mpesa', $payload, $signature)
            ->assertOk()
            ->assertJsonPath('data.outcome', 'already_processed');

        $this->assertTrue($firstPaidAt->equalTo($payment->fresh()->paid_at));
    }

    public function test_failed_webhook_marks_payment_failed(): void
    {
        $this->configureMpesa('secret');

        $payment = $this->pendingPayment(['reference' => 'REF-ECHEC']);

        // Un ResultCode inconnu retombe sur 'failed' (fail-safe : on ne crédite
        // jamais un paiement sur un statut qu'on ne sait pas lire).
        $payload = [
            'TransID' => 'MPESA-FAIL',
            'BillRefNumber' => 'REF-ECHEC',
            'ResultCode' => '9999',
            'TransAmount' => '5000',
            'ResultDesc' => 'Statut inconnu',
        ];

        $this->postJson('/v1/webhooks/mpesa', $payload, [
            'X-Signature' => $this->hmac($payload, 'secret'),
        ])->assertOk();

        $payment->refresh();
        $this->assertSame('failed', $payment->status);
    }

    public function test_cancelled_webhook_marks_payment_cancelled(): void
    {
        $this->configureMpesa('secret');

        $payment = $this->pendingPayment(['reference' => 'REF-ANNULE']);

        $payload = [
            'TransID' => 'MPESA-CANCEL',
            'BillRefNumber' => 'REF-ANNULE',
            'ResultCode' => 'CANCELLED',
            'TransAmount' => '5000',
        ];

        $this->postJson('/v1/webhooks/mpesa', $payload, [
            'X-Signature' => $this->hmac($payload, 'secret'),
        ])->assertOk();

        $this->assertSame('cancelled', $payment->fresh()->status);
    }
}
