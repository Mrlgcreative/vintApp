<?php

namespace Tests\Feature;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * K-PAY est un agrégateur : un seul endpoint reçoit tous les évènements, le
 * type étant porté par `event`. Le rattachement se fait par `paymentId`
 * (notre external_reference) ou, à défaut, par `externalId` (notre reference).
 */
class KPayWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function configureKPay(string $secret = 'kpay-webhook-secret'): void
    {
        config()->set('payments.providers.kpay.credential', 'KPAY_TEST_WEBHOOK_SECRET');
        putenv("KPAY_TEST_WEBHOOK_SECRET={$secret}");
        $_ENV['KPAY_TEST_WEBHOOK_SECRET'] = $secret;
    }

    private function kpayPayment(array $overrides = []): Payment
    {
        return $this->pendingPayment(array_merge([
            'method' => 'kpay',
            'provider_key' => 'kpay',
            'currency' => 'CDF',
            'amount' => 5000,
            'reference' => 'REF-KPAY-1',
            'external_reference' => 'KPAY-PAY-1',
        ], $overrides));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'event' => 'payment.completed',
            'paymentId' => 'KPAY-PAY-1',
            'externalId' => 'REF-KPAY-1',
            'status' => 'COMPLETED',
            'amount' => 5000,
            'currency' => 'CDF',
            'phoneNumber' => '243810000000',
        ], $overrides);
    }

    public function test_valid_webhook_completes_payment_and_records_event(): void
    {
        $this->configureKPay();
        $payment = $this->kpayPayment();

        $payload = $this->payload();

        $this->postJson('/v1/webhooks/kpay', $payload, [
            'X-KPAY-Signature' => $this->hmac($payload, 'kpay-webhook-secret'),
        ])->assertOk()->assertJsonPath('data.outcome', 'processed');

        $payment->refresh();
        $this->assertSame('completed', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertDatabaseHas('outbox_messages', ['type' => 'payment.completed']);
    }

    public function test_webhook_matches_by_external_id_when_payment_id_is_absent(): void
    {
        $this->configureKPay();
        $payment = $this->kpayPayment();

        $payload = $this->payload(['paymentId' => null]);

        $this->postJson('/v1/webhooks/kpay', $payload, [
            'X-KPAY-Signature' => $this->hmac($payload, 'kpay-webhook-secret'),
        ])->assertOk()->assertJsonPath('data.outcome', 'processed');

        $this->assertSame('completed', $payment->fresh()->status);
    }

    public function test_webhook_refused_with_invalid_signature(): void
    {
        $this->configureKPay();

        $this->postJson('/v1/webhooks/kpay', $this->payload(), [
            'X-KPAY-Signature' => 'signature-bidon',
        ])->assertStatus(403);

        $this->assertDatabaseCount('payment_callbacks', 0);
    }

    public function test_failed_status_marks_payment_failed(): void
    {
        $this->configureKPay();
        $payment = $this->kpayPayment();

        $payload = $this->payload([
            'event' => 'payment.failed',
            'status' => 'FAILED',
            'failureReason' => 'Solde insuffisant',
        ]);

        $this->postJson('/v1/webhooks/kpay', $payload, [
            'X-KPAY-Signature' => $this->hmac($payload, 'kpay-webhook-secret'),
        ])->assertOk();

        $payment->refresh();
        $this->assertSame('failed', $payment->status);
        $this->assertSame('Solde insuffisant', $payment->error_message);
        $this->assertDatabaseHas('outbox_messages', ['type' => 'payment.failed']);
    }

    public function test_cancelled_status_marks_payment_cancelled(): void
    {
        $this->configureKPay();
        $payment = $this->kpayPayment();

        $payload = $this->payload([
            'event' => 'payment.cancelled',
            'status' => 'CANCELLED',
        ]);

        $this->postJson('/v1/webhooks/kpay', $payload, [
            'X-KPAY-Signature' => $this->hmac($payload, 'kpay-webhook-secret'),
        ])->assertOk();

        $this->assertSame('cancelled', $payment->fresh()->status);
    }

    public function test_payout_event_is_acknowledged_without_effect(): void
    {
        $this->configureKPay();
        $payment = $this->kpayPayment();

        // Un retrait peut frapper le même endpoint : il ne concerne pas ce
        // service et ne doit surtout pas solder le paiement.
        $payload = $this->payload([
            'event' => 'payout.completed',
            'status' => 'COMPLETED',
        ]);

        $this->postJson('/v1/webhooks/kpay', $payload, [
            'X-KPAY-Signature' => $this->hmac($payload, 'kpay-webhook-secret'),
        ])->assertOk();

        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertDatabaseCount('payment_callbacks', 0);
        $this->assertDatabaseCount('outbox_messages', 0);
    }
}
