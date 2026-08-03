<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_shows_payment_method_selector(): void
    {
        $user = User::factory()->create();
        \App\Models\Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);

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
        $response->assertSee('method-maishapay', false);
        $response->assertSee('method-cinetpay', false);
        $response->assertSee('method-pawapay', false);
        $response->assertSee('id="maishapayForm"', false);
        $response->assertSee('id="cinetpayForm"', false);
        $response->assertSee('id="pawapayForm"', false);
        $response->assertSee('/payments/maishapay/checkout', false);
        $response->assertSee('/payments/checkout/initiate', false);
        $response->assertSee('/payments/pawapay/checkout', false);
    }
}
