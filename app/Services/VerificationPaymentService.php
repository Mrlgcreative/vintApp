<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\ProductAuthenticityCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificationPaymentService
{
    /**
     * Traiter le paiement des frais de vérification d'authenticité
     */
    public function processVerificationPayment(ProductAuthenticityCheck $check): array
    {
        DB::beginTransaction();
        
        try {
            // Récupérer le wallet utilisateur (source) - le propriétaire est accessible via la relation vendor
            $user = $check->vendor;
            if (!$user) {
                throw new \Exception('Utilisateur propriétaire introuvable pour cette vérification');
            }

            $userWallet = $user->wallets()
                ->where('currency', 'USD') // Les frais sont en USD
                ->where('type', Wallet::TYPE_MAIN)
                ->first();

            if (!$userWallet) {
                throw new \Exception('Wallet utilisateur USD introuvable');
            }

            // Vérifier si l'utilisateur a suffisamment de fonds
            if ($userWallet->balance < $check->verification_fee) {
                throw new \Exception('Solde insuffisant pour payer les frais de vérification');
            }

            // Récupérer le sous-wallet entreprise pour les vérifications
            $enterpriseWallet = Wallet::getEnterpriseSubWallet(
                Wallet::SUBTYPE_VERIFICATION,
                'USD'
            );

            if (!$enterpriseWallet) {
                throw new \Exception('Wallet entreprise pour vérifications introuvable');
            }

            // Débiter l'utilisateur
            $userWallet->debit($check->verification_fee);

            // Créer la transaction de débit
            WalletTransaction::create([
                'wallet_id' => $userWallet->id,
                'type' => WalletTransaction::TYPE_DEBIT,
                'amount' => $check->verification_fee,
                'balance_after' => $userWallet->fresh()->balance,
                'description' => "Frais de vérification d'authenticité - Produit #{$check->item_id}",
                'reference' => "VERIF_PAYMENT_{$check->id}",
                'status' => 'completed',
            ]);

            // Distribuer les frais vers les sous-wallets entreprise (commission / transport / boost)
            $splits = config('enterprise_wallet.verification_fee_split', [
                'commission' => 0.60,
                'transport' => 0.25,
                'boost' => 0.15,
            ]);

            foreach ($splits as $subtype => $ratio) {
                $amount = round($check->verification_fee * $ratio, 2);
                if ($amount <= 0) {
                    continue;
                }

                $subWallet = Wallet::getEnterpriseSubWallet($subtype, 'USD');
                if (!$subWallet) {
                    // Si un sous-wallet manque, créer/créer en mémoire -> fallback au wallet enterprise principal
                    $subWallet = $enterpriseWallet;
                }

                $subWallet->credit($amount);

                WalletTransaction::create([
                    'wallet_id' => $subWallet->id,
                    'type' => WalletTransaction::TYPE_CREDIT,
                    'amount' => $amount,
                    'balance_after' => $subWallet->fresh()->balance,
                    'description' => "Part ({$subtype}) des frais de vérification - Vérification #{$check->id}",
                    'reference' => "VERIF_RECEIVED_{$check->id}_{$subtype}",
                    'status' => 'completed',
                ]);
            }

            // Marquer le paiement comme effectué
            $check->update([
                'payment_completed' => true,
                'payment_completed_at' => now(),
                'payment_method' => 'wallet_usd'
            ]);

            DB::commit();

            Log::info('Verification payment processed successfully', [
                'check_id' => $check->id,
                'user_id' => $user->id,
                'amount' => $check->verification_fee,
                'user_wallet_balance_after' => $userWallet->fresh()->balance,
                'enterprise_wallet_balance_after' => $enterpriseWallet->fresh()->balance
            ]);

            return [
                'success' => true,
                'message' => 'Paiement effectué avec succès',
                'data' => [
                    'amount_paid' => $check->verification_fee,
                    'remaining_balance' => $userWallet->fresh()->balance,
                    'transaction_reference' => "VERIF_PAYMENT_{$check->id}"
                ]
            ];

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Verification payment failed', [
                'check_id' => $check->id,
                'user_id' => $check->user_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Vérifier si l'utilisateur peut payer les frais de vérification
     */
    public function canUserPayVerificationFee(ProductAuthenticityCheck $check): array
    {
        $userWallet = $check->user->wallets()
            ->where('currency', 'USD')
            ->where('type', Wallet::TYPE_MAIN)
            ->first();

        if (!$userWallet) {
            return [
                'can_pay' => false,
                'reason' => 'Wallet USD introuvable'
            ];
        }

        if ($userWallet->balance < $check->verification_fee) {
            return [
                'can_pay' => false,
                'reason' => 'Solde insuffisant',
                'current_balance' => $userWallet->balance,
                'required_amount' => $check->verification_fee,
                'shortage' => $check->verification_fee - $userWallet->balance
            ];
        }

        return [
            'can_pay' => true,
            'current_balance' => $userWallet->balance,
            'amount_to_pay' => $check->verification_fee,
            'balance_after' => $userWallet->balance - $check->verification_fee
        ];
    }

    /**
     * Obtenir les statistiques des paiements de vérification
     */
    public function getVerificationPaymentStats(): array
    {
        $verificationWalletUSD = Wallet::getEnterpriseSubWallet(Wallet::SUBTYPE_VERIFICATION, 'USD');
        $verificationWalletCDF = Wallet::getEnterpriseSubWallet(Wallet::SUBTYPE_VERIFICATION, 'CDF');

        // Compter les vérifications payées
        $totalVerificationsPaid = ProductAuthenticityCheck::where('payment_completed', true)->count();
        
        // Revenus totaux en USD et CDF
        $totalRevenueUSD = $verificationWalletUSD ? $verificationWalletUSD->balance : 0;
        $totalRevenueCDF = $verificationWalletCDF ? $verificationWalletCDF->balance : 0;

        // Moyennes
        $avgFeeUSD = $totalVerificationsPaid > 0 ? $totalRevenueUSD / $totalVerificationsPaid : 0;

        return [
            'total_verifications_paid' => $totalVerificationsPaid,
            'total_revenue' => [
                'USD' => $totalRevenueUSD,
                'CDF' => $totalRevenueCDF
            ],
            'wallet_balances' => [
                'USD' => $totalRevenueUSD,
                'CDF' => $totalRevenueCDF
            ],
            'average_fee_usd' => round($avgFeeUSD, 2),
            'enterprise_wallets' => [
                'verification_usd' => $verificationWalletUSD ? $verificationWalletUSD->id : null,
                'verification_cdf' => $verificationWalletCDF ? $verificationWalletCDF->id : null
            ]
        ];
    }
}