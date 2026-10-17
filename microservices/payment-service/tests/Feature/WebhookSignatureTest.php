<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La signature d'un webhook est la seule barrière : un opérateur non
 * configuré ou mal configuré doit TOUT refuser.
 */
class WebhookSignatureTest extends TestCase
{
    use RefreshDatabase;

    private function mpesaPayload(array $overrides = []): array
    {
        return array_merge([
            'TransID' => 'MPESA-XYZ-001',
            'BillRefNumber' => 'REF-123',
            'ResultCode' => '0',
            'TransAmount' => '5000',
            'MSISDN' => '243000000',
            'ResultDesc' => 'Accepted',
        ], $overrides);
    }

    public function test_webhook_refused_when_secret_not_configured(): void
    {
        config()->set('payments.providers.mpesa.credential', 'MPESA_TEST_SECRET');
        // Secret volontairement absent de l'env.

        $response = $this->postJson('/v1/webhooks/mpesa', $this->mpesaPayload(), [
            'X-Signature' => $this->hmac($this->mpesaPayload(), 'peu-importe'),
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('payment_callbacks', 0);
    }

    public function test_webhook_refused_when_secret_is_placeholder(): void
    {
        config()->set('payments.providers.mpesa.credential', 'MPESA_TEST_SECRET');
        // Un placeholder en production ferait accepter n'importe quelle signature.
        putenv('MPESA_TEST_SECRET=DEMO_SECRET');
        $_ENV['MPESA_TEST_SECRET'] = 'DEMO_SECRET';

        $response = $this->postJson('/v1/webhooks/mpesa', $this->mpesaPayload(), [
            'X-Signature' => 'DEMO_SECRET',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('payment_callbacks', 0);

        putenv('MPESA_TEST_SECRET');
        unset($_ENV['MPESA_TEST_SECRET']);
    }

    public function test_webhook_refused_with_invalid_signature(): void
    {
        $this->configureMpesa('vrai-secret');

        $this->postJson('/v1/webhooks/mpesa', $this->mpesaPayload(), [
            'X-Signature' => 'signature-bidon',
        ])->assertStatus(403);

        $this->assertDatabaseCount('payment_callbacks', 0);
    }

    public function test_webhook_refused_without_signature_header(): void
    {
        $this->configureMpesa('vrai-secret');

        $this->postJson('/v1/webhooks/mpesa', $this->mpesaPayload())->assertStatus(403);

        $this->assertDatabaseCount('payment_callbacks', 0);
    }

    public function test_webhook_accepted_with_valid_signature(): void
    {
        $this->configureMpesa('vrai-secret');

        $payment = $this->pendingPayment(['reference' => 'REF-123']);

        $payload = $this->mpesaPayload();

        $response = $this->postJson('/v1/webhooks/mpesa', $payload, [
            'X-Signature' => $this->hmac($payload, 'vrai-secret'),
        ]);

        $response->assertOk()->assertJsonPath('data.outcome', 'processed');

        $this->assertDatabaseHas('payment_callbacks', ['provider' => 'mpesa', 'is_verified' => true]);
        $this->assertSame('completed', $payment->fresh()->status);
    }

    public function test_unknown_provider_is_rejected(): void
    {
        $this->postJson('/v1/webhooks/inconnu', [])->assertStatus(404);
    }

    public function test_orangemoney_uses_api_key_header(): void
    {
        config()->set('payments.providers.orange_money.credential', 'ORANGE_TEST');
        putenv('ORANGE_TEST=cle-orange-valide');
        $_ENV['ORANGE_TEST'] = 'cle-orange-valide';

        // Mauvaise clé : refus avant tout traitement.
        $this->postJson('/v1/webhooks/orange_money', [
            'txnid' => 'OM-1', 'order_id' => 'REF-OM', 'status' => 'SUCCESS', 'amount' => 1000,
        ], ['X-Api-Key' => 'mauvaise'])->assertStatus(403);

        // Bonne clé : la signature passe, seul le rattachement échoue (404).
        $this->postJson('/v1/webhooks/orange_money', [
            'txnid' => 'OM-1', 'order_id' => 'REF-OM', 'status' => 'SUCCESS', 'amount' => 1000,
        ], ['X-Api-Key' => 'cle-orange-valide'])->assertStatus(404);

        // Le callback est bien authentifié et persisté.
        $this->assertDatabaseHas('payment_callbacks', [
            'provider' => 'orange_money',
            'is_verified' => true,
        ]);

        putenv('ORANGE_TEST');
        unset($_ENV['ORANGE_TEST']);
    }

    private function configureMpesa(string $secret): void
    {
        config()->set('payments.providers.mpesa.credential', 'MPESA_TEST_SECRET');
        putenv("MPESA_TEST_SECRET={$secret}");
        $_ENV['MPESA_TEST_SECRET'] = $secret;
    }
}
