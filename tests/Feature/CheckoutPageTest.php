<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Models\Setting;
use App\Models\User;
use App\Services\KPay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_shows_payment_method_selector(): void
    {
        $user = User::factory()->create();
        Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);

        $response = $this->actingAs($user)->withSession([
            'gps_location_validated' => true,
            'cart' => [
                1 => [
                    'id' => 1,
                    'name' => 'iPhone 14',
                    'price' => 100.0,
                    'currency' => 'USD',
                    'quantity' => 1,
                    'image' => null,
                ],
            ],
        ])->get(route('cart.checkout'));

        $response->assertOk();
        $response->assertSee('data-method="kpay"', false);
        $response->assertDontSee("Frais d'encaissement", false); // masqué au client, inclus dans le prix affiché
        $response->assertSee('109.73 USD', false); // 100 + 4,5 % supplément acheteur + 5 % livraison
        $response->assertDontSee('data-method="maishapay"', false);
        $response->assertSee('id="kpayForm"', false);
        $response->assertDontSee('id="maishapayForm"', false);
        $response->assertSee('/payments/kpay/checkout', false);
        $response->assertDontSee('/payments/maishapay/checkout', false);
    }

    public function test_kpay_checkout_renders_payment_page(): void
    {
        Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
        $user = User::factory()->create();
        $address = DeliveryAddress::create([
            'user_id' => $user->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'is_default' => true,
        ]);

        $cart = [
            1 => ['id' => 1, 'name' => 'Article', 'price' => 5000.0, 'currency' => 'CDF', 'quantity' => 2],
        ];

        // Le checkout lit le panier réel en base (créé par syncSessionToDb côté web).
        Cart::create([
            'session_id' => 'test-session',
            'user_id' => $user->id,
            'item_id' => 1,
            'item_name' => 'Article',
            'price' => 5000.0,
            'currency' => 'CDF',
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->withSession([
            'gps_location_validated' => true,
        ])->post(route('payments.kpay.checkout'), [
            'delivery_address_id' => $address->id,
            'cart_items' => json_encode($cart),
            'total_amount' => 10500.0,
            'currency' => 'CDF',
        ]);

        $response->assertOk();
        $response->assertViewIs('payments.kpay');
        $response->assertSee('K-PAY', false);
        $response->assertSee('VODACOM', false);
        $response->assertSee('10,972.50 CDF', false); // 2 × 5 000 + 4,5 % supplément + 5 % livraison (recalculé serveur)
        $response->assertDontSee('Page sécurisée', false);
        $response->assertDontSee('Choisissez l\'opérateur sur la page K-PAY', false);
        $response->assertSessionHas('kpay_checkout');
    }

    public function test_kpay_checkout_converts_usd_cart_to_cdf(): void
    {
        Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
        $user = User::factory()->create();
        $address = DeliveryAddress::create([
            'user_id' => $user->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'is_default' => true,
        ]);

        $cart = [
            1 => ['id' => 1, 'name' => 'iPhone', 'price' => 5.0, 'currency' => 'USD', 'quantity' => 2],
        ];

        Cart::create([
            'session_id' => 'test-session',
            'user_id' => $user->id,
            'item_id' => 1,
            'item_name' => 'iPhone',
            'price' => 5.0,
            'currency' => 'USD',
            'quantity' => 2,
        ]);

        // Total serveur : 2 × 5,23 (supplément 4,5 % inclus) + 5 % livraison = 10,97 USD
        $buyerUnit = buyer_price(5.0);
        $expectedUsd = round($buyerUnit * 2 + round(round($buyerUnit * 2, 2) * 0.05, 2), 2);

        $response = $this->actingAs($user)->withSession([
            'gps_location_validated' => true,
        ])->post(route('payments.kpay.checkout'), [
            'delivery_address_id' => $address->id,
            'cart_items' => json_encode($cart),
            'total_amount' => 10.0,
            'currency' => 'USD',
        ]);

        $response->assertOk();
        $response->assertViewIs('payments.kpay');

        $rate = Cache::get('usd_cdf_rate');
        $this->assertNotNull($rate, 'Le taux usd_cdf_rate doit être en cache');

        $expectedCdf = round($expectedUsd * (float) $rate, 2);
        $response->assertSee(number_format($expectedCdf, 2).' CDF', false);
        $response->assertSee(number_format($expectedUsd, 2).' USD', false);

        $session = session('kpay_checkout');
        $this->assertEquals('USD', $session['currency']);
        $this->assertEquals($expectedCdf, (float) $session['total_cdf'], 'Le montant CDF doit correspondre au taux');
    }

    public function test_kpay_checkout_blocks_amount_over_max_limit(): void
    {
        Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
        $user = User::factory()->create();
        $address = DeliveryAddress::create([
            'user_id' => $user->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'is_default' => true,
        ]);

        $limit = KPay::MAX_AMOUNT_CDF;
        $cart = [
            1 => ['id' => 1, 'name' => 'Article', 'price' => $limit + 100.0, 'currency' => 'CDF', 'quantity' => 1],
        ];

        Cart::create([
            'session_id' => 'test-session',
            'user_id' => $user->id,
            'item_id' => 1,
            'item_name' => 'Article',
            'price' => $limit + 100.0,
            'currency' => 'CDF',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->withSession([
            'gps_location_validated' => true,
        ])->from(route('cart.checkout'))->post(route('payments.kpay.checkout'), [
            'delivery_address_id' => $address->id,
            'cart_items' => json_encode($cart),
            'total_amount' => $limit + 100.0,
            'currency' => 'CDF',
        ]);

        $response->assertRedirect(route('cart.checkout'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('limite maximale', session('error'));
        $this->assertNull(session('kpay_checkout'), 'Aucune session de checkout ne doit être créée');
    }
}
