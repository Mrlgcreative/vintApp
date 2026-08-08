<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use DomainException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Obtient ou crée un wallet pour un utilisateur et une devise donnée.
     */
    public function getOrCreateUserWallet(User $user, string $currency): Wallet
    {
        $wallet = $user->wallets()->where('currency', $currency)->first();

        if (!$wallet) {
            $wallet = $user->wallets()->create([
                'currency' => $currency,
                'balance' => 0.00,
                'is_active' => true,
                'status' => 'active',
            ]);
        }

        return $wallet;
    }

    /**
     * Approuve un wallet (statut unifié 'active').
     */
    public function approveWallet(Wallet $wallet, int $adminId): void
    {
        DB::transaction(function () use ($wallet, $adminId) {
            $wallet->update([
                'status' => 'active',
                'is_active' => true,
                'verified_at' => now(),
                'verified_by' => $adminId,
            ]);

            Transaction::create([
                'transaction_id' => 'WALLET-APPROVAL-' . strtoupper(\Illuminate\Support\Str::random(12)),
                'user_id' => $wallet->user_id,
                'buyer_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'transfer',
                'amount' => 0,
                'currency' => $wallet->currency,
                'status' => 'completed',
                'description' => "Wallet approuvé par l'administrateur",
                'purpose' => "Wallet approuvé par l'administrateur",
                'provider' => 'Admin Approval',
                'payment_method' => 'wallet',
                'processed_by' => $adminId,
            ]);
        });

        Log::info('Wallet approuvé', [
            'admin_id' => $adminId,
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,
        ]);
    }

    /**
     * Rejette un wallet avec une raison.
     */
    public function rejectWallet(Wallet $wallet, int $adminId, string $reason): void
    {
        DB::transaction(function () use ($wallet, $adminId, $reason) {
            $wallet->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_by' => $adminId,
            ]);

            Transaction::create([
                'transaction_id' => 'WALLET-REJECT-' . strtoupper(\Illuminate\Support\Str::random(12)),
                'user_id' => $wallet->user_id,
                'buyer_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'transfer',
                'amount' => 0,
                'currency' => $wallet->currency,
                'status' => 'failed',
                'description' => 'Wallet rejeté par l\'administrateur',
                'purpose' => 'Wallet rejeté par l\'administrateur',
                'provider' => 'Admin Approval',
                'payment_method' => 'wallet',
                'failure_reason' => $reason,
                'processed_by' => $adminId,
            ]);
        });
    }

    /**
     * Transfère le montant d'une vente : debite le wallet pending du vendeur,
     * crédite la commission dans le wallet entreprise et le net dans le wallet main.
     *
     * @return array Détail de la distribution
     */
    public function transferCommission(int $orderId, float $amount, int $sellerId, string $currency): array
    {
        return DB::transaction(function () use ($orderId, $amount, $sellerId, $currency) {
            $enterpriseWallet = Wallet::where('type', 'enterprise')
                ->where('currency', $currency)
                ->whereNull('user_id')
                ->lockForUpdate()
                ->first();

            if (!$enterpriseWallet) {
                throw new DomainException("Wallet entreprise {$currency} introuvable");
            }

            $pendingWallet = Wallet::where('user_id', $sellerId)
                ->where('type', 'pending')
                ->where('currency', $currency)
                ->lockForUpdate()
                ->first();

            if (!$pendingWallet) {
                throw new DomainException('Wallet pending du vendeur introuvable');
            }

            $sellerWallet = Wallet::where('user_id', $sellerId)
                ->where('type', 'main')
                ->where('currency', $currency)
                ->lockForUpdate()
                ->first();

            if (!$sellerWallet) {
                throw new DomainException('Wallet principal du vendeur introuvable');
            }

            if ($pendingWallet->balance < $amount) {
                throw new DomainException('Solde insuffisant dans le wallet pending');
            }

            $commissionRate = $enterpriseWallet->commission_rate;
            $commissionAmount = round(($amount * $commissionRate) / 100, 2);
            $sellerAmount = round($amount - $commissionAmount, 2);

            $pendingWallet->decrement('balance', $amount);
            $enterpriseWallet->increment('balance', $commissionAmount);
            $sellerWallet->increment('balance', $sellerAmount);

            WalletTransaction::create([
                'wallet_id' => $pendingWallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $pendingWallet->fresh()->balance,
                'status' => 'completed',
                'description' => "Transfert commission vente #{$orderId}",
                'reference' => "SALE_CONFIRMED_{$orderId}",
            ]);

            WalletTransaction::create([
                'wallet_id' => $enterpriseWallet->id,
                'type' => 'credit',
                'amount' => $commissionAmount,
                'balance_after' => $enterpriseWallet->fresh()->balance,
                'status' => 'completed',
                'description' => "Commission {$commissionRate}% sur vente #{$orderId}",
                'reference' => "COMMISSION_{$orderId}",
            ]);

            WalletTransaction::create([
                'wallet_id' => $sellerWallet->id,
                'type' => 'credit',
                'amount' => $sellerAmount,
                'balance_after' => $sellerWallet->fresh()->balance,
                'status' => 'completed',
                'description' => "Paiement vente #{$orderId} (net après commission)",
                'reference' => "PAYMENT_{$orderId}",
            ]);

            Log::info('Commission transférée avec succès', [
                'order_id' => $orderId,
                'amount_total' => $amount,
                'commission' => $commissionAmount,
                'seller_net' => $sellerAmount,
                'currency' => $currency,
                'commission_rate' => $commissionRate,
            ]);

            return [
                'order_id' => $orderId,
                'montant_total' => $amount,
                'montant_commission' => $commissionAmount,
                'taux_commission' => $commissionRate,
                'montant_vendeur_net' => $sellerAmount,
                'currency' => $currency,
                'wallets' => [
                    'entreprise_balance' => $enterpriseWallet->fresh()->balance,
                    'vendeur_balance' => $sellerWallet->fresh()->balance,
                    'pending_balance' => $pendingWallet->fresh()->balance,
                ],
            ];
        });
    }

    /**
     * Crée une demande de retrait (transaction debit + WithdrawalRequest + blocage des fonds).
     *
     * @return array [WalletTransaction, WithdrawalRequest]
     */
    public function createWithdrawal(Wallet $wallet, array $data): array
    {
        return DB::transaction(function () use ($wallet, $data) {
            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            if ($data['amount'] > $wallet->balance) {
                throw new DomainException('Solde insuffisant pour effectuer ce retrait.');
            }

            $metadata = [
                'phone_number' => $data['phone_number'],
                'payment_method' => $data['payment_method'],
                'withdrawal_date' => now()->toDateTimeString(),
            ];
            if (!empty($data['agent_id'])) {
                $metadata['agent_id'] = $data['agent_id'];
            }
            if (!empty($data['agent_phone'])) {
                $metadata['agent_phone'] = $data['agent_phone'];
            }

            $transaction = $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $data['amount'],
                'balance_after' => $wallet->balance,
                'description' => $data['description'] ?? 'Retrait de fonds vers ' . $data['phone_number'],
                'reference' => 'WTH-' . time() . '-' . rand(1000, 9999),
                'status' => 'processing',
                'provider' => $data['payment_method'],
                'metadata' => json_encode($metadata),
            ]);

            $withdrawalRequest = WithdrawalRequest::create([
                'wallet_transaction_id' => $transaction->id,
                'phone_number' => $data['phone_number'],
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'currency' => $wallet->currency,
                'status' => 'processing',
            ]);

            $wallet->decrement('balance', $data['amount']);
            $transaction->update(['balance_after' => $wallet->fresh()->balance]);

            return [$transaction, $withdrawalRequest];
        });
    }

    /**
     * Convertit un montant entre deux wallets de devises différentes.
     *
     * @return array Détail de la conversion
     */
    public function convertCurrency(Wallet $fromWallet, Wallet $toWallet, float $amount, int $userId): array
    {
        if ($fromWallet->user_id !== $userId || $toWallet->user_id !== $userId) {
            throw new DomainException('Accès non autorisé');
        }

        if ($fromWallet->currency === $toWallet->currency) {
            throw new DomainException('Les deux wallets ont la même devise');
        }

        if ($fromWallet->balance < $amount) {
            throw new DomainException('Solde insuffisant dans le wallet source');
        }

        $rate = Cache::remember('usd_cdf_rate', 3600, function () {
            return 2500.00;
        });

        $convertedAmount = $fromWallet->currency === 'USD'
            ? $amount * $rate
            : $amount / $rate;

        DB::transaction(function () use ($fromWallet, $toWallet, $amount, $convertedAmount) {
            $fromWallet->decrement('balance', $amount);
            $fromWallet->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $fromWallet->fresh()->balance,
                'description' => 'Conversion de ' . $fromWallet->currency . ' vers ' . $toWallet->currency,
                'reference' => 'CONV-' . time() . '-' . rand(1000, 9999),
                'status' => 'completed',
            ]);

            $toWallet->increment('balance', $convertedAmount);
            $toWallet->transactions()->create([
                'type' => 'credit',
                'amount' => $convertedAmount,
                'balance_after' => $toWallet->fresh()->balance,
                'description' => 'Conversion de ' . $fromWallet->currency . ' vers ' . $toWallet->currency,
                'reference' => 'CONV-' . time() . '-' . rand(1000, 9999),
                'status' => 'completed',
            ]);
        });

        return [
            'from_currency' => $fromWallet->currency,
            'to_currency' => $toWallet->currency,
            'amount' => $amount,
            'converted_amount' => round($convertedAmount, 2),
            'rate' => $rate,
            'from_balance' => $fromWallet->fresh()->balance,
            'to_balance' => $toWallet->fresh()->balance,
        ];
    }
}
