<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Distribution;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Répartit le montant d'une transaction entre vendeur, transporteur et service
     * et enregistre chaque part dans la table distributions.
     *
     * @param Transaction $transaction
     * @param array $beneficiaries [
     *   'seller' => user_id,
     *   'carrier' => user_id,
     *   'service' => user_id
     * ]
     * @return array Détail de la distribution
     */
    public function distributeFunds(Transaction $transaction, array $beneficiaries)
    {
        $amount = $transaction->amount;
        $distribution = [
            [
                'beneficiary_type' => 'seller',
                'beneficiary_id' => $beneficiaries['seller'],
                'percentage' => 70,
                'amount' => round($amount * 0.7, 2),
            ],
            [
                'beneficiary_type' => 'carrier',
                'beneficiary_id' => $beneficiaries['carrier'],
                'percentage' => 20,
                'amount' => round($amount * 0.2, 2),
            ],
            [
                'beneficiary_type' => 'service',
                'beneficiary_id' => $beneficiaries['service'],
                'percentage' => 10,
                'amount' => round($amount * 0.1, 2),
            ],
        ];

        DB::transaction(function () use ($transaction, $distribution) {
            foreach ($distribution as $part) {
                Distribution::create([
                    'transaction_id' => $transaction->id,
                    'beneficiary_id' => $part['beneficiary_id'],
                    'beneficiary_type' => $part['beneficiary_type'],
                    'amount' => $part['amount'],
                    'percentage' => $part['percentage'],
                ]);
            }
        });

        return $distribution;
    }
} 