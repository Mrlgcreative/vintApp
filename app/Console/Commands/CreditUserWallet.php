<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class CreditUserWallet extends Command
{
    protected $signature = 'wallet:credit {user_id} {amount} {currency=USD}';
    protected $description = 'Créditer le wallet principal USD d\'un utilisateur (test)';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = (float)$this->argument('amount');
        $currency = $this->argument('currency');

        $user = User::find($userId);
        if (!$user) {
            $this->error("Utilisateur {$userId} introuvable");
            return 1;
        }

        $wallet = $user->wallets()->where('currency', $currency)->where('type', Wallet::TYPE_MAIN)->first();
        if (!$wallet) {
            $wallet = $user->wallets()->create([
                'currency' => $currency,
                'balance' => 0.00,
                'is_active' => true,
                'type' => Wallet::TYPE_MAIN,
            ]);
        }

        $wallet->credit($amount);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => WalletTransaction::TYPE_CREDIT,
            'amount' => $amount,
            'balance_after' => $wallet->fresh()->balance,
            'description' => "Crédit test via commande artisan",
            'reference' => 'TEST_CREDIT_' . time(),
            'status' => 'completed',
        ]);

        $this->info("Crédit de {$amount} {$currency} appliqué au wallet #{$wallet->id}. Nouveau solde: {$wallet->fresh()->balance}");
        return 0;
    }
}
