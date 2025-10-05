<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Distribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    /**
     * Traite un paiement via Illicocash
     */
    public function payWithIllicocash(array $paymentData)
    {
        if (!config('payments.providers.illicocash.enabled')) {
            return ['status' => 'error', 'message' => 'Illicocash désactivé.'];
        }

        // Configuration Illicocash
        $apiKey = config('payments.providers.illicocash.api_key');
        $apiSecret = config('payments.providers.illicocash.api_secret');

        try {
            // TODO: Implémenter l'appel API réel à Illicocash
            return [
                'status' => 'pending',
                'message' => 'Paiement Illicocash en cours...',
                'provider' => 'illicocash'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur Illicocash: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Traite un paiement via Orange Money
     */
    public function payWithOrangeMoney(array $paymentData)
    {
        if (!config('payments.providers.orange_money.enabled')) {
            return ['status' => 'error', 'message' => 'Orange Money désactivé.'];
        }

        $apiKey = config('payments.providers.orange_money.api_key');
        $apiSecret = config('payments.providers.orange_money.api_secret');

        try {
            // TODO: Implémenter l'appel API réel à Orange Money
            return [
                'status' => 'pending',
                'message' => 'Paiement Orange Money en cours...',
                'provider' => 'orange_money'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur Orange Money: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Traite un paiement via Airtel Money
     */
    public function payWithAirtelMoney(array $paymentData)
    {
        if (!config('payments.providers.airtel_money.enabled')) {
            return ['status' => 'error', 'message' => 'Airtel Money désactivé.'];
        }

        $apiKey = config('payments.providers.airtel_money.api_key');
        $apiSecret = config('payments.providers.airtel_money.api_secret');

        try {
            // TODO: Implémenter l'appel API réel à Airtel Money
            return [
                'status' => 'pending',
                'message' => 'Paiement Airtel Money en cours...',
                'provider' => 'airtel_money'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur Airtel Money: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Traite un paiement via Mpesa
     */
    public function payWithMpesa(array $paymentData)
    {
        if (!config('payments.providers.mpesa.enabled')) {
            return ['status' => 'error', 'message' => 'Mpesa désactivé.'];
        }

        $apiKey = config('payments.providers.mpesa.api_key');
        $apiSecret = config('payments.providers.mpesa.api_secret');

        try {
            // TODO: Implémenter l'appel API réel à Mpesa
            return [
                'status' => 'pending',
                'message' => 'Paiement Mpesa en cours...',
                'provider' => 'mpesa'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur Mpesa: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Traite un paiement via Africell
     */
    public function payWithAfricell(array $paymentData)
    {
        if (!config('payments.providers.africell.enabled')) {
            return ['status' => 'error', 'message' => 'Africell Money désactivé.'];
        }

        $apiKey = config('payments.providers.africell.api_key');
        $apiSecret = config('payments.providers.africell.api_secret');

        try {
            // TODO: Implémenter l'appel API réel à Africell Money
            return [
                'status' => 'pending',
                'message' => 'Paiement Africell Money en cours...',
                'provider' => 'africell'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur Africell: ' . $e->getMessage()
            ];
        }
    }
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