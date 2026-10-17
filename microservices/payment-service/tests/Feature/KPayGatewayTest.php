<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Branchement de l'agrégateur K-PAY : deux clés suffisent. Sans credentials,
 * l'API reste utilisable et se contente d'enregistrer l'intention.
 */
class KPayGatewayTest extends TestCase
{
    use RefreshDatabase;

    private function configureKPay(array $overrides = []): void
    {
        config()->set('kpay', array_merge([
            'enabled' => true,
            'environment' => 'sandbox',
            'api_key' => 'kpay_test_abc',
            'secret_key' => 'secret-key',
            'base_url' => 'https://admin.kpay.site',
            'default_provider' => 'VODACOM_MPESA_COD',
            'return_url' => 'https://shop.test/return',
            'cancel_url' => 'https://shop.test/cancel',
            'timeout' => 30,
        ], $overrides));
    }

    private function identityResponse()
    {
        config()->set('payments.auth_service.url', 'http://auth.test');
        config()->set('payments.auth_service.shared_secret', 'service-secret');

        return Http::response([
            'success' => true,
            'data' => ['active' => true, 'identity' => ['user_id' => 42, 'roles' => ['user']]],
        ]);
    }

    public function test_gateway_initiation_stores_reference_and_returns_url(): void
    {
        $this->configureKPay();

        Http::fake([
            '*/v1/token/introspect' => $this->identityResponse(),
            'admin.kpay.site/api/v1/payments/init' => Http::response([
                'id' => 'KPAY-ID-1',
                'reference' => 'KPAY-REF-1',
                'status' => 'PENDING',
                'mode' => 'GATEWAY',
                'gatewayUrl' => 'https://pay.kpay.site/checkout/abc',
                'message' => 'Paiement initié.',
            ]),
        ]);

        $this->withToken('bon-token')->postJson('/v1/payments', [
            'amount' => 5000,
            'currency' => 'CDF',
            'method' => 'kpay',
            'mode' => 'GATEWAY',
            'reference' => 'REF-CLIENT-1',
            'designation' => 'Commande 1',
        ])->assertCreated()
            ->assertJsonPath('data.checkout.mode', 'GATEWAY')
            ->assertJsonPath('data.checkout.gateway_url', 'https://pay.kpay.site/checkout/abc');

        $this->assertDatabaseHas('payments', [
            'reference' => 'REF-CLIENT-1',
            'external_reference' => 'KPAY-ID-1',
            'status' => 'pending',
            'provider_key' => 'kpay',
        ]);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/api/v1/payments/init')
            && $request['externalId'] === 'REF-CLIENT-1'
            && $request['currency'] === 'CDF'
            && $request->hasHeader('X-API-Key', 'kpay_test_abc'));
    }

    public function test_ussd_initiation_normalizes_phone_and_maps_operator(): void
    {
        $this->configureKPay();

        Http::fake([
            '*/v1/token/introspect' => $this->identityResponse(),
            'admin.kpay.site/api/v1/payments/init' => Http::response([
                'id' => 'KPAY-ID-2',
                'status' => 'PENDING',
                'mode' => 'USSD',
                'message' => 'ok',
            ]),
        ]);

        $this->withToken('bon-token')->postJson('/v1/payments', [
            'amount' => 1000,
            'method' => 'kpay',
            'mode' => 'USSD',
            'operator' => 'AIRTEL',
            'phone_number' => '0812345678',
        ])->assertCreated();

        Http::assertSent(fn ($request) => str_contains($request->url(), '/api/v1/payments/init')
            && $request['provider'] === 'AIRTEL_COD'
            && $request['phoneNumber'] === '243812345678');
    }

    public function test_failed_initiation_marks_payment_failed(): void
    {
        $this->configureKPay();

        Http::fake([
            '*/v1/token/introspect' => $this->identityResponse(),
            'admin.kpay.site/api/v1/payments/init' => Http::response([
                'message' => 'Solde marchand insuffisant',
            ], 422),
        ]);

        $this->withToken('bon-token')->postJson('/v1/payments', [
            'amount' => 1000,
            'method' => 'kpay',
            'mode' => 'GATEWAY',
        ])->assertStatus(502);

        $this->assertDatabaseHas('payments', [
            'status' => 'failed',
            'error_message' => 'Solde marchand insuffisant',
            'provider_key' => 'kpay',
        ]);
    }

    public function test_intention_is_recorded_without_http_call_when_disabled(): void
    {
        $this->configureKPay(['enabled' => false]);

        Http::fake(['*/v1/token/introspect' => $this->identityResponse()]);

        $this->withToken('bon-token')->postJson('/v1/payments', [
            'amount' => 1000,
            'method' => 'kpay',
            'currency' => 'CDF',
        ])->assertCreated();

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'kpay.site'));
    }

    public function test_kpay_rejects_non_cdf_currency(): void
    {
        $this->configureKPay();

        Http::fake(['*/v1/token/introspect' => $this->identityResponse()]);

        $this->withToken('bon-token')->postJson('/v1/payments', [
            'amount' => 1000,
            'method' => 'kpay',
            'currency' => 'USD',
        ])->assertStatus(422)->assertJsonValidationErrors('currency');
    }
}
