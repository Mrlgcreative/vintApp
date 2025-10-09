<?php

namespace App\Listeners;

use App\Events\SaleConfirmed;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransferCommissionToEnterprise implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event - Transfer commission from pending wallet to enterprise and seller.
     *
     * @param SaleConfirmed $event
     * @return void
     */
    public function handle(SaleConfirmed $event): void
    {
        try {
            DB::beginTransaction();

            $order = $event->order;
            $amount = $event->amount;
            $sellerId = $event->sellerId;
            $currency = $event->currency;

            // Récupérer le wallet entreprise pour cette devise
            $enterpriseWallet = Wallet::where('type', 'enterprise')
                ->where('currency', $currency)
                ->whereNull('user_id')
                ->first();

            if (!$enterpriseWallet) {
                throw new \Exception("Wallet entreprise {$currency} introuvable");
            }

            // Récupérer le wallet pending du vendeur
            $pendingWallet = Wallet::where('user_id', $sellerId)
                ->where('type', 'pending')
                ->where('currency', $currency)
                ->first();

            if (!$pendingWallet) {
                throw new \Exception("Wallet pending du vendeur introuvable");
            }

            // Récupérer le wallet principal du vendeur
            $sellerWallet = Wallet::where('user_id', $sellerId)
                ->where('type', 'main')
                ->where('currency', $currency)
                ->first();

            if (!$sellerWallet) {
                throw new \Exception("Wallet principal du vendeur introuvable");
            }

            // Vérifier que le pending wallet a suffisamment de fonds
            if ($pendingWallet->balance < $amount) {
                throw new \Exception("Solde insuffisant dans le wallet pending");
            }

            // Calculer la commission (ex: 5%)
            $commissionRate = $enterpriseWallet->commission_rate;
            $commissionAmount = round(($amount * $commissionRate) / 100, 2);
            $sellerAmount = round($amount - $commissionAmount, 2);

            // 1. Débiter le pending wallet
            $pendingWallet->balance -= $amount;
            $pendingWallet->save();

            // 2. Créditer le wallet entreprise (commission)
            $enterpriseWallet->balance += $commissionAmount;
            $enterpriseWallet->save();

            // 3. Créditer le wallet du vendeur (montant net)
            $sellerWallet->balance += $sellerAmount;
            $sellerWallet->save();

            // Logger les transactions
            // Transaction 1: Débit du pending
            WalletTransaction::create([
                'wallet_id' => $pendingWallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "Transfert commission vente #{$order->id}",
                'reference' => "SALE_CONFIRMED_{$order->id}",
            ]);

            // Transaction 2: Crédit entreprise (commission)
            WalletTransaction::create([
                'wallet_id' => $enterpriseWallet->id,
                'type' => 'credit',
                'amount' => $commissionAmount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "Commission {$commissionRate}% sur vente #{$order->id}",
                'reference' => "COMMISSION_{$order->id}",
            ]);

            // Transaction 3: Crédit vendeur (net)
            WalletTransaction::create([
                'wallet_id' => $sellerWallet->id,
                'type' => 'credit',
                'amount' => $sellerAmount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "Paiement vente #{$order->id} (net après commission)",
                'reference' => "PAYMENT_{$order->id}",
            ]);

            DB::commit();

            Log::info("Commission transférée avec succès", [
                'order_id' => $order->id,
                'amount_total' => $amount,
                'commission' => $commissionAmount,
                'seller_net' => $sellerAmount,
                'currency' => $currency,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Erreur lors du transfert de commission", [
                'order_id' => $event->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}

