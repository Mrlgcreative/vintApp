<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawFundsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
    }

    /** @test */
    public function withdraw_page_shows_the_net_amount_section_and_preview_endpoint(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'currency' => 'CDF',
            'balance' => 5000,
        ]);
        \App\Models\Setting::set('withdrawal_fee_percentage', '2', ['label' => 'Test']);

        $response = $this->actingAs($user)->withSession(['gps_location_validated' => true])->get(route('wallet.withdraw-funds', $wallet));

        $response->assertOk();
        $response->assertSee('Vous recevrez (net)', false);
        $response->assertSee('withdraw-preview', false);
        // Les frais d'encaissement ne sont jamais déduits au retrait.
        $response->assertSee("jamais les frais d'encaissement", false);
    }

    /** @test */
    public function withdraw_preview_endpoint_returns_fee_and_net_amount(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'currency' => 'CDF',
            'balance' => 5000,
        ]);
        \App\Models\Setting::set('withdrawal_fee_percentage', '2', ['label' => 'Test']);

        $response = $this->actingAs($user)->withSession(['gps_location_validated' => true])->getJson(route('wallet.withdraw-preview', $wallet) . '?amount=1000');

        $response->assertOk();
        $response->assertJsonPath('withdrawal_fee_percentage', 2);
        $response->assertJsonPath('withdrawal_fee', 20);
        $response->assertJsonPath('net_amount', 980);
    }
}
