<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use App\Services\KPay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiKPayPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function item(User $seller, int $quantity = 5): Item
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-'.uniqid()]);

        return Item::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Description',
            'price' => 100,
            'currency' => 'USD',
            'quantity' => $quantity,
            'status' => 'active',
        ]);
    }

    /**
     * Authentifie comme l'utilisateur avec un vrai token Sanctum
     * (comme le mobile) pour éviter la session web.
     */
    private function actingAsBuyer(User $buyer): void
    {
        $this->withHeader('Authorization', 'Bearer '.$buyer->createToken('test')->plainTextToken);
    }

    public function test_initiate_kpay_requires_phone_for_ussd(): void
    {
        $buyer = User::factory()->create();
        $this->actingAsBuyer($buyer);

        $this->postJson(route('api.v1.payments.kpay.initiate'), [
            'amount' => 15000,
            'currency' => 'CDF',
        ])->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_initiate_kpay_rejects_amount_above_max(): void
    {
        $buyer = User::factory()->create();
        $this->actingAsBuyer($buyer);

        $this->postJson(route('api.v1.payments.kpay.initiate'), [
            'amount' => KPay::MAX_AMOUNT_CDF + 1,
            'currency' => 'CDF',
            'phone' => '0812345678',
            'operator' => 'VODACOM',
        ])->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_initiate_kpay_ussd_creates_pending_transaction(): void
    {
        Http::fake([
            '*/api/v1/payments/init' => Http::response([
                'status' => 'PENDING',
                'id' => 'kpay_payment_1',
                'reference' => 'REF_1',
                'mode' => 'USSD',
                'message' => 'Paiement initié.',
            ], 201),
        ]);

        $buyer = User::factory()->create();
        $this->actingAsBuyer($buyer);

        $response = $this->postJson(route('api.v1.payments.kpay.initiate'), [
            'amount' => 15000,
            'currency' => 'CDF',
            'phone' => '0812345678',
            'operator' => 'VODACOM',
            'purpose' => 'Commande test',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.reference', 'kpay_payment_1');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $buyer->id,
            'provider' => 'kpay',
            'status' => 'pending',
            'transaction_ref' => 'kpay_payment_1',
            'amount' => 15000,
            'currency' => 'CDF',
        ]);
    }

    public function test_initiate_kpay_gateway_returns_gateway_url(): void
    {
        Http::fake([
            '*/api/v1/payments/init' => Http::response([
                'status' => 'PENDING',
                'id' => 'kpay_payment_2',
                'reference' => 'REF_2',
                'mode' => 'GATEWAY',
                'gatewayUrl' => 'https://admin.kpay.site/gateway/abc',
                'message' => 'Paiement initié.',
            ], 201),
        ]);

        $buyer = User::factory()->create();
        $this->actingAsBuyer($buyer);

        $response = $this->postJson(route('api.v1.payments.kpay.initiate'), [
            'amount' => 15000,
            'currency' => 'CDF',
            'mode' => 'GATEWAY',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.mode', 'GATEWAY')
            ->assertJsonPath('data.gateway_url', 'https://admin.kpay.site/gateway/abc');
    }

    public function test_initiate_kpay_marks_failed_when_provider_errors(): void
    {
        Http::fake([
            '*/api/v1/payments/init' => Http::response([
                'message' => 'Opérateur invalide',
            ], 400),
        ]);

        $buyer = User::factory()->create();
        $this->actingAsBuyer($buyer);

        $this->postJson(route('api.v1.payments.kpay.initiate'), [
            'amount' => 15000,
            'currency' => 'CDF',
            'phone' => '0812345678',
            'operator' => 'VODACOM',
        ])->assertStatus(400)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $buyer->id,
            'provider' => 'kpay',
            'status' => 'failed',
        ]);
    }

    public function test_kpay_webhook_completed_creates_orders(): void
    {
        config()->set('services.kpay.webhook_secret', 'whsec_test_123');

        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create());

        $transaction = Transaction::create([
            'user_id' => $buyer->id,
            'buyer_id' => $buyer->id,
            'transaction_id' => 'KPAY-TEST-1',
            'transaction_ref' => 'kpay_payment_1',
            'amount' => 100,
            'currency' => 'USD',
            'provider' => 'kpay',
            'status' => 'pending',
            'type' => Transaction::TYPE_PURCHASE,
            'payment_method' => 'kpay',
            'purpose' => 'Commande test',
            'metadata' => json_encode([
                'cart' => [
                    $item->id => ['id' => $item->id, 'name' => 'Article', 'price' => 100.0, 'currency' => 'USD', 'quantity' => 1],
                ],
            ]),
        ]);

        $payload = json_encode([
            'event' => 'payment.completed',
            'paymentId' => 'kpay_payment_1',
            'status' => 'COMPLETED',
            'amount' => 100,
            'currency' => 'USD',
        ]);

        // Le webhook est la SEULE source du statut final : signature HMAC-SHA256
        // du body brut avec le webhook secret (pas le gateway secret).
        $signature = hash_hmac('sha256', $payload, 'whsec_test_123');

        $this->call('POST', '/api/v1/kpay/callback/deposit', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_KPAY_SIGNATURE' => $signature,
            'HTTP_X_KPAY_EVENT' => 'payment.completed',
        ], $payload)->assertOk();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'confirmed',
        ]);

        // Le polling API ne joint plus K-PAY : il relit l'état local.
        $this->actingAsBuyer($buyer);

        $this->getJson(route('api.v1.payments.kpay.status', $transaction))
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.is_final', true);
    }

    public function test_kpay_webhook_rejects_bad_signature(): void
    {
        config()->set('services.kpay.webhook_secret', 'whsec_test_123');

        $buyer = User::factory()->create();

        $transaction = Transaction::create([
            'user_id' => $buyer->id,
            'buyer_id' => $buyer->id,
            'transaction_id' => 'KPAY-TEST-2',
            'transaction_ref' => 'kpay_payment_2',
            'amount' => 100,
            'currency' => 'USD',
            'provider' => 'kpay',
            'status' => 'pending',
            'type' => Transaction::TYPE_PURCHASE,
            'payment_method' => 'kpay',
            'purpose' => 'Commande test',
            'metadata' => json_encode(['cart' => []]),
        ]);

        $payload = json_encode([
            'event' => 'payment.completed',
            'paymentId' => 'kpay_payment_2',
            'status' => 'COMPLETED',
        ]);

        $this->call('POST', '/api/v1/kpay/callback/deposit', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_KPAY_SIGNATURE' => str_repeat('f', 64),
            'HTTP_X_KPAY_EVENT' => 'payment.completed',
        ], $payload)->assertStatus(403);

        $this->assertSame('pending', $transaction->fresh()->status);
    }

    public function test_check_payment_status_makes_no_http_call(): void
    {
        // K-PAY n'a pas d'endpoint de statut : aucun appel réseau ne doit
        // partir (sinon 404 en boucle depuis la page de statut qui polle).
        Http::fake();

        $service = new \App\Services\KPay([
            'api_key' => 'kpay_live_x',
            'secret_key' => str_repeat('a', 64),
            'environment' => 'production',
            'base_url' => 'https://admin.kpay.site',
        ]);

        $result = $service->checkPaymentStatus('54227f2d-ea45-4db2-9f8f-ebfa65002ccf');

        $this->assertFalse($result['success']);
        $this->assertSame('NOT_FOUND', $result['status']);
        Http::assertNothingSent();
    }

    public function test_check_kpay_status_reflects_webhook_updated_state(): void
    {
        // Le webhook met la transaction à jour ; le polling API ne fait que
        // relire l'état local, sans joindre K-PAY.
        Http::fake();

        $buyer = User::factory()->create();

        $transaction = Transaction::create([
            'user_id' => $buyer->id,
            'buyer_id' => $buyer->id,
            'transaction_id' => 'KPAY-MVH80ZV9-DE1620BE1CD5',
            'transaction_ref' => '54227f2d-ea45-4db2-9f8f-ebfa65002ccf',
            'amount' => 100,
            'currency' => 'USD',
            'provider' => 'kpay',
            'status' => 'completed',
            'type' => Transaction::TYPE_PURCHASE,
            'payment_method' => 'kpay',
            'purpose' => 'Commande test',
            'metadata' => json_encode(['cart' => []]),
        ]);

        $this->actingAsBuyer($buyer);

        $this->getJson(route('api.v1.payments.kpay.status', $transaction))
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.is_final', true);

        Http::assertNothingSent();
    }

    public function test_predict_provider_requires_phone(): void
    {
        $buyer = User::factory()->create();
        $this->actingAsBuyer($buyer);

        $this->postJson(route('api.v1.payments.kpay.predict-provider'), [])
            ->assertStatus(422);
    }

    public function test_predict_provider_returns_mapped_operator(): void
    {
        Http::fake([
            '*/api/v1/payments/predict-provider' => Http::response([
                'country' => 'COD',
                'provider' => 'AIRTEL_COD',
                'phoneNumber' => '243812345678',
            ], 200),
        ]);

        $buyer = User::factory()->create();
        $this->actingAsBuyer($buyer);

        $this->postJson(route('api.v1.payments.kpay.predict-provider'), ['phone' => '0812345678'])
            ->assertOk()
            ->assertJsonPath('data.provider', 'AIRTEL')
            ->assertJsonPath('data.kpay_provider', 'AIRTEL_COD');
    }
}
