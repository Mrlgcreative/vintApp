<?php

namespace Tests\Feature;

use App\Models\DeliveryAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KPayPredictProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_predict_provider_returns_operator_for_rdc_number(): void
    {
        \App\Models\Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
        $user = User::factory()->create();
        $this->withSession(['gps_location_validated' => true]);

        $response = $this->actingAs($user)->getJson(route('payments.kpay.predict-provider', ['phone' => '0813456789']));

        $response->assertOk()
            ->assertJson(['success' => true, 'provider' => 'VODACOM']);
    }

    public function test_predict_provider_returns_null_for_short_number(): void
    {
        \App\Models\Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
        $user = User::factory()->create();
        $this->withSession(['gps_location_validated' => true]);

        $response = $this->actingAs($user)->getJson(route('payments.kpay.predict-provider', ['phone' => '081']));

        $response->assertOk()
            ->assertJson(['success' => false, 'provider' => null]);
    }

    public function test_kpay_page_includes_predict_script(): void
    {
        \App\Models\Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
        $user = User::factory()->create();
        $address = DeliveryAddress::create([
            'user_id' => $user->id,
            'full_name' => 'Jean Test',
            'phone' => '0813456789',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'is_default' => true,
        ]);

        $cart = [
            1 => ['id' => 1, 'name' => 'Article', 'price' => 10500.0, 'currency' => 'CDF', 'quantity' => 1],
        ];

        $response = $this->actingAs($user)->withSession([
            'gps_location_validated' => true,
        ])->post(route('payments.kpay.checkout'), [
            'delivery_address_id' => $address->id,
            'cart_items' => json_encode($cart),
            'total_amount' => 10500.0,
            'currency' => 'CDF',
        ]);

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('payments/kpay/predict-provider', $content, 'Le script predict doit être présent');
        $this->assertStringContainsString('fetch(predictUrl', $content, 'Le JS de prédiction doit être rendu');
    }
}
