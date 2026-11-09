<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCalculationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function buyer_price_adds_the_4_5_percent_surcharge(): void
    {
        $this->assertEquals(104.5, buyer_price(100.0));
        $this->assertEquals(0.0, buyer_price(0.0));
        $this->assertEqualsWithDelta(10972.5, buyer_price(10500.0), 0.001);
    }

    /** @test */
    public function cart_totals_include_the_buyer_surcharge_in_prices(): void
    {
        $totals = cart_totals([
            ['price' => 100.0, 'quantity' => 2, 'currency' => 'USD'],
        ]);

        // 2 × 104.50 + 5 % livraison
        $this->assertEquals(209.0, $totals['subtotal']);
        $this->assertEquals(10.45, $totals['transport_fee']);
        $this->assertEquals(219.45, $totals['total']);
    }

    /** @test */
    public function withdrawal_breakdown_deducts_only_the_aggregator_fee(): void
    {
        Setting::set('withdrawal_fee_percentage', '2.5', ['label' => 'Test']);

        $breakdown = withdrawal_fee_breakdown(1000.0);

        $this->assertSame(2.5, $breakdown['percentage']);
        $this->assertEquals(25.0, $breakdown['fee']);
        $this->assertEquals(975.0, $breakdown['net']);
        // Le supplément d'encaissement n'est jamais déduit au retrait
        $this->assertNotEquals(1000.0 * 0.045, $breakdown['fee']);
    }
}
