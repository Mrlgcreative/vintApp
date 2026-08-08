<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WalletService::class);
    }

    /** @test */
    public function it_creates_and_reuses_a_user_wallet()
    {
        $user = User::factory()->create();

        $wallet = $this->service->getOrCreateUserWallet($user, 'USD');

        $this->assertSame('USD', $wallet->currency);
        $this->assertSame(0.0, (float) $wallet->balance);
        $this->assertTrue((bool) $wallet->is_active);
        $this->assertSame($user->id, $wallet->user_id);

        $same = $this->service->getOrCreateUserWallet($user, 'USD');
        $this->assertSame($wallet->id, $same->id);
        $this->assertSame(1, Wallet::where('user_id', $user->id)->count());
    }

    /** @test */
    public function it_approves_a_wallet_and_records_a_transaction()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'currency' => 'USD', 'type' => 'pending', 'balance' => 0, 'status' => 'pending']);

        $this->service->approveWallet($wallet, $admin->id);

        $wallet->refresh();
        $this->assertSame('active', $wallet->status);
        $this->assertTrue((bool) $wallet->is_active);
        $this->assertSame($admin->id, $wallet->verified_by);
        $this->assertNotNull($wallet->verified_at);

        $tx = Transaction::where('wallet_id', $wallet->id)->first();
        $this->assertNotNull($tx);
        $this->assertStringStartsWith('WALLET-APPROVAL-', $tx->transaction_id);
        $this->assertSame('completed', $tx->status);
        $this->assertSame($user->id, $tx->buyer_id);
    }

    /** @test */
    public function it_rejects_a_wallet_with_a_reason()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'currency' => 'USD', 'type' => 'pending', 'balance' => 0, 'status' => 'pending']);

        $this->service->rejectWallet($wallet, $admin->id, 'Documents invalides');

        $wallet->refresh();
        $this->assertSame('rejected', $wallet->status);
        $this->assertSame('Documents invalides', $wallet->rejection_reason);
        $this->assertSame($admin->id, $wallet->verified_by);

        $tx = Transaction::where('wallet_id', $wallet->id)->first();
        $this->assertNotNull($tx);
        $this->assertStringStartsWith('WALLET-REJECT-', $tx->transaction_id);
        $this->assertSame('failed', $tx->status);
    }

    /** @test */
    public function it_converts_currency_between_two_wallets()
    {
        $user = User::factory()->create();
        $usd = $this->service->getOrCreateUserWallet($user, 'USD');
        $cdf = $this->service->getOrCreateUserWallet($user, 'CDF');
        $usd->increment('balance', 1000);

        $result = $this->service->convertCurrency($usd, $cdf, 100, $user->id);

        $this->assertEquals(250000.0, $result['converted_amount']);
        $this->assertEquals(2500.0, $result['rate']);
        $this->assertSame(900.0, (float) $usd->fresh()->balance);
        $this->assertSame(250000.0, (float) $cdf->fresh()->balance);
    }

    /** @test */
    public function it_rejects_conversion_when_balance_is_insufficient()
    {
        $user = User::factory()->create();
        $usd = $this->service->getOrCreateUserWallet($user, 'USD');
        $cdf = $this->service->getOrCreateUserWallet($user, 'CDF');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Solde insuffisant dans le wallet source');

        $this->service->convertCurrency($usd, $cdf, 100, $user->id);
    }

    /** @test */
    public function it_rejects_conversion_of_another_users_wallet()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $usd = $this->service->getOrCreateUserWallet($owner, 'USD');
        $cdf = $this->service->getOrCreateUserWallet($owner, 'CDF');

        $this->expectException(DomainException::class);

        $this->service->convertCurrency($usd, $cdf, 10, $other->id);
    }
}
