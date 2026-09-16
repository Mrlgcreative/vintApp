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

    public function test_check_kpay_status_completes_and_creates_orders(): void
    {
        Http::fake([
            '*/api/v1/payments/*' => Http::response([
                'status' => 'COMPLETED',
                'id' => 'kpay_payment_1',
            ], 200),
        ]);

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

        $this->actingAsBuyer($buyer);

        $this->getJson(route('api.v1.payments.kpay.status', $transaction->id))
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.is_final', true);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'confirmed',
        ]);
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
