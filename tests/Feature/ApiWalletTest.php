<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiWalletTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_get_his_wallets()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/wallet')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['usd_wallet', 'cdf_wallet', 'total_usd_equivalent'],
            ]);
    }

    /** @test */
    public function user_can_list_his_transactions()
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 100,
        ]);
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => 50,
            'balance_after' => 150,
            'description' => 'Test',
            'reference' => 'REF-'.uniqid(),
            'status' => 'completed',
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/wallet/transactions')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function user_cannot_add_funds_to_someone_elses_wallet()
    {
        $user = User::factory()->create();
        $otherWallet = Wallet::create([
            'user_id' => User::factory()->create()->id,
            'currency' => 'USD',
            'balance' => 100,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/wallet/add-funds', [
            'wallet_id' => $otherWallet->id,
            'amount' => 50,
            'payment_method' => 'orange_money',
        ])->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function user_can_preview_the_net_amount_of_a_withdrawal()
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'currency' => 'CDF',
            'balance' => 5000,
        ]);
        Sanctum::actingAs($user);
        Setting::set('withdrawal_fee_percentage', '2', ['label' => 'Test']);

        $this->postJson('/api/v1/wallet/withdraw/preview', [
            'wallet_id' => $wallet->id,
            'amount' => 1000,
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.withdrawal_fee_percentage', 2)
            ->assertJsonPath('data.withdrawal_fee', 20)
            ->assertJsonPath('data.net_amount', 980)
            ->assertJsonPath('data.currency', 'CDF');
    }

    /** @test */
    public function withdrawal_preview_rejects_someone_elses_wallet()
    {
        $user = User::factory()->create();
        $otherWallet = Wallet::create([
            'user_id' => User::factory()->create()->id,
            'currency' => 'USD',
            'balance' => 100,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/wallet/withdraw/preview', [
            'wallet_id' => $otherWallet->id,
            'amount' => 50,
        ])->assertStatus(403);
    }

    /** @test */
    public function user_can_get_payout_operators()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/wallet/withdraw/operators')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(4, 'data.operators')
            ->assertJsonPath('data.maishapay_enabled', config('services.maishapay.enabled', false));
    }

    /** @test */
    public function unauthenticated_user_is_rejected()
    {
        $this->getJson('/api/v1/wallet')->assertUnauthorized();
    }
}
