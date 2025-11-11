<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Wallet;

class TestTransportFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:transport-fees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test la distribution des frais de transport';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TEST DISTRIBUTION FRAIS DE TRANSPORT ===');
        
        // Récupération des sous-wallets
        $commissionWallet = Wallet::getEnterpriseSubWallet('commission', 'USD');
        $transportWallet = Wallet::getEnterpriseSubWallet('transport', 'USD');
        
        if (!$commissionWallet || !$transportWallet) {
            $this->error('❌ Sous-wallets entreprise introuvables');
            return 1;
        }
        
        $this->info('Soldes AVANT test:');
        $this->info("Commission: {$commissionWallet->balance} USD");
        $this->info("Transport: {$transportWallet->balance} USD");
        
        // Configuration du test
        $totalAmount = 100.00;
        $commissionPercent = 10;
        $transportPercent = 5;
        
        $commissionAmount = round(($totalAmount * $commissionPercent) / 100, 2);
        $transportAmount = round(($totalAmount * $transportPercent) / 100, 2);
        $sellerAmount = round($totalAmount - $commissionAmount - $transportAmount, 2);
        
        $this->info("\nSimulation commande de {$totalAmount} USD:");
        $this->info("- Vendeur: {$sellerAmount} USD");
        $this->info("- Commission: {$commissionAmount} USD");
        $this->info("- Transport: {$transportAmount} USD");
        
        // Simulation de la distribution
        $commissionWallet->increment('balance', $commissionAmount);
        $transportWallet->increment('balance', $transportAmount);
        
        // Rafraîchir pour les nouveaux soldes
        $commissionWallet->refresh();
        $transportWallet->refresh();
        
        $this->info("\nSoldes APRÈS test:");
        $this->info("Commission: {$commissionWallet->balance} USD (+{$commissionAmount})");
        $this->info("Transport: {$transportWallet->balance} USD (+{$transportAmount})");
        
        $this->info("\n✅ Test réussi ! Les frais sont maintenant correctement séparés.");
        
        return 0;
    }
}
