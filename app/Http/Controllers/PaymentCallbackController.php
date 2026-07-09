<?php

namespace App\Http\Controllers;

use App\Models\PaymentCallback;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;

class PaymentCallbackController extends Controller
{
    /**
     * Callback universel pour tous les opérateurs
     */
    public function handleCallback(Request $request, string $provider)
    {
        try {
            // Enregistrer le callback brut
            $callback = PaymentCallback::create([
                'provider' => $provider,
                'status' => 'pending',
                'amount' => 0,
                'phone_number' => '',
                'callback_type' => 'webhook',
                'raw_payload' => json_encode($request->all()),
                'ip_address' => $request->ip(),
                'is_verified' => false,
                'is_processed' => false,
            ]);

            Log::info("Callback reçu pour {$provider}", [
                'callback_id' => $callback->id,
                'ip' => $request->ip(),
                'payload' => $request->all(),
            ]);

            // Protection contre les replay attacks
            if (!$this->preventReplayAttack($request, $provider)) {
                Log::warning("Replay attack détecté pour callback {$callback->id}", [
                    'ip' => $request->ip(),
                    'provider' => $provider
                ]);
                return response()->json(['status' => 'error', 'message' => 'Duplicate callback'], 409);
            }

            // Vérifier la signature selon le provider
            if ($this->verifyCallbackSignature($request, $provider)) {
                $callback->markAsVerified();
            } else {
                Log::warning("Signature invalide pour callback {$callback->id}");
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
            }

            // Parser les données selon le provider
            $parsedData = $this->parseCallbackData($request, $provider);
            
            if (!$parsedData) {
                Log::error("Impossible de parser le callback {$callback->id}");
                return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
            }

            // Mettre à jour le callback avec les données parsées
            $callback->update([
                'external_transaction_id' => $parsedData['transaction_id'] ?? null,
                'status' => $parsedData['status'],
                'amount' => $parsedData['amount'],
                'currency' => $parsedData['currency'] ?? 'USD',
                'phone_number' => $parsedData['phone_number'],
                'parsed_data' => $parsedData,
            ]);

            // Traiter le callback
            $this->processCallback($callback);

            return response()->json(['status' => 'success', 'message' => 'Callback processed'], 200);

        } catch (\Exception $e) {
            Log::error("Erreur traitement callback {$provider}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Internal error'], 500);
        }
    }

    /**
     * Vérifier la signature du callback selon le provider
     */
    protected function verifyCallbackSignature(Request $request, string $provider): bool
    {
        // IPs autorisées par provider (à configurer dans .env)
        $allowedIps = [
            'mpesa' => explode(',', env('MPESA_CALLBACK_IPS', '196.250.0.0/16')),
            'orange_money' => explode(',', env('ORANGE_CALLBACK_IPS', '41.191.0.0/16')),
            'airtel_money' => explode(',', env('AIRTEL_CALLBACK_IPS', '41.242.0.0/16')),
            'africell' => explode(',', env('AFRICELL_CALLBACK_IPS', '41.222.0.0/16')),
        ];

        // Vérification basique de l'IP (à améliorer selon les specs de chaque opérateur)
        $clientIp = $request->ip();
        
        // ⚠️ SÉCURITÉ : En développement, logger mais TOUJOURS vérifier la signature
        if (app()->environment('local')) {
            Log::info("Environnement local détecté", [
                'ip' => $clientIp,
                'provider' => $provider
            ]);
        }

        // Vérifier selon le provider
        switch ($provider) {
            case 'mpesa':
                // M-Pesa utilise une signature HMAC
                $signature = $request->header('X-Signature');
                $secret = env('MPESA_CALLBACK_SECRET');
                if ($signature && $secret) {
                    $expectedSignature = hash_hmac('sha256', json_encode($request->all()), $secret);
                    return hash_equals($expectedSignature, $signature);
                }
                break;

            case 'orange_money':
                // Orange Money utilise une clé API
                $apiKey = $request->header('X-Api-Key');
                return $apiKey === env('ORANGE_CALLBACK_KEY');

            case 'airtel_money':
                // Airtel Money utilise une combinaison IP + token
                $token = $request->header('Authorization');
                return $token === 'Bearer ' . env('AIRTEL_CALLBACK_TOKEN');

            case 'africell':
                // Africell utilise un secret dans le payload
                return $request->input('secret') === env('AFRICELL_CALLBACK_SECRET');

            case 'maishapay':
                $signature = $request->header('X-MaishaPay-Signature');
                if (!$signature) {
                    return app()->environment('local');
                }
                $maishaPay = new \App\Services\MaishaPay();
                return $maishaPay->verifyWebhookSignature($request->getContent(), $signature);
        }

        // Si pas de vérification spécifique, vérifier juste l'IP
        if (isset($allowedIps[$provider])) {
            foreach ($allowedIps[$provider] as $allowedRange) {
                if ($this->ipInRange($clientIp, trim($allowedRange))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Vérifier si une IP est dans une plage CIDR
     */
    protected function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        list($subnet, $mask) = explode('/', $range);
        $ip_long = ip2long($ip);
        $subnet_long = ip2long($subnet);
        $mask_long = -1 << (32 - $mask);
        
        return ($ip_long & $mask_long) === ($subnet_long & $mask_long);
    }

    /**
     * Parser les données du callback selon le provider
     */
    protected function parseCallbackData(Request $request, string $provider): ?array
    {
        try {
            switch ($provider) {
                case 'mpesa':
                    return $this->parseMpesaCallback($request);
                
                case 'orange_money':
                    return $this->parseOrangeMoneyCallback($request);
                
                case 'airtel_money':
                    return $this->parseAirtelMoneyCallback($request);
                
                case 'africell':
                    return $this->parseAfricellCallback($request);

                case 'maishapay':
                    return $this->parseMaishaPayCallback($request);
                
                default:
                    return $this->parseGenericCallback($request);
            }
        } catch (\Exception $e) {
            Log::error("Erreur parsing callback {$provider}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Parser callback M-Pesa
     */
    protected function parseMpesaCallback(Request $request): array
    {
        return [
            'transaction_id' => $request->input('TransID') ?? $request->input('transaction_id'),
            'status' => $this->mapMpesaStatus($request->input('ResultCode')),
            'amount' => $request->input('TransAmount') ?? $request->input('amount'),
            'currency' => 'USD',
            'phone_number' => $request->input('MSISDN') ?? $request->input('phone'),
            'reference' => $request->input('BillRefNumber'),
            'message' => $request->input('ResultDesc'),
        ];
    }

    /**
     * Parser callback Orange Money
     */
    protected function parseOrangeMoneyCallback(Request $request): array
    {
        return [
            'transaction_id' => $request->input('txnid') ?? $request->input('transaction_id'),
            'status' => $this->mapOrangeMoneyStatus($request->input('status')),
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency') ?? 'USD',
            'phone_number' => $request->input('msisdn') ?? $request->input('phone'),
            'reference' => $request->input('order_id'),
            'message' => $request->input('message'),
        ];
    }

    /**
     * Parser callback Airtel Money
     */
    protected function parseAirtelMoneyCallback(Request $request): array
    {
        return [
            'transaction_id' => $request->input('transaction_id') ?? $request->input('airtel_money_id'),
            'status' => $this->mapAirtelMoneyStatus($request->input('transaction_status')),
            'amount' => $request->input('transaction_amount'),
            'currency' => $request->input('currency') ?? 'USD',
            'phone_number' => $request->input('msisdn'),
            'reference' => $request->input('reference'),
            'message' => $request->input('status_message'),
        ];
    }

    /**
     * Parser callback Africell
     */
    protected function parseAfricellCallback(Request $request): array
    {
        return [
            'transaction_id' => $request->input('trans_id'),
            'status' => $this->mapAfricellStatus($request->input('status')),
            'amount' => $request->input('amount'),
            'currency' => 'USD',
            'phone_number' => $request->input('phone'),
            'reference' => $request->input('ref'),
            'message' => $request->input('msg'),
        ];
    }

    /**
     * Parser callback générique
     */
    protected function parseGenericCallback(Request $request): array
    {
        return [
            'transaction_id' => $request->input('transaction_id'),
            'status' => $request->input('status') === 'success' ? 'success' : 'failed',
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency') ?? 'USD',
            'phone_number' => $request->input('phone'),
            'reference' => $request->input('reference'),
            'message' => $request->input('message'),
        ];
    }

    /**
     * Mapper les statuts M-Pesa
     */
    protected function mapMpesaStatus($code): string
    {
        return match((string)$code) {
            '0', 'SUCCESS' => 'success',
            'PENDING' => 'pending',
            'CANCELLED' => 'cancelled',
            default => 'failed',
        };
    }

    /**
     * Mapper les statuts Orange Money
     */
    protected function mapOrangeMoneyStatus($status): string
    {
        return match(strtoupper($status ?? '')) {
            'SUCCESS', 'SUCCESSFUL', 'COMPLETED' => 'success',
            'PENDING', 'PROCESSING' => 'pending',
            'CANCELLED', 'CANCELED' => 'cancelled',
            default => 'failed',
        };
    }

    /**
     * Mapper les statuts Airtel Money
     */
    protected function mapAirtelMoneyStatus($status): string
    {
        return match(strtoupper($status ?? '')) {
            'TS', 'SUCCESS', 'SUCCESSFUL' => 'success',
            'TIP', 'PENDING' => 'pending',
            'TF', 'FAILED' => 'failed',
            default => 'failed',
        };
    }

    /**
     * Mapper les statuts Africell
     */
    protected function mapAfricellStatus($status): string
    {
        return match(strtoupper($status ?? '')) {
            'SUCCESS', 'SUCCESSFUL' => 'success',
            'PENDING' => 'pending',
            'FAILED', 'ERROR' => 'failed',
            default => 'failed',
        };
    }

    /**
     * Parser callback MaishaPay (Collection v2 Mobile Money / B2C)
     */
    protected function parseMaishaPayCallback(Request $request): array
    {
        $data = $request->all();

        return [
            'transaction_id' => $data['originatingTransactionId']
                ?? $data['transactionId']
                ?? $data['transactionReference']
                ?? $request->input('transaction_id'),
            'status' => $this->mapMaishaPayStatus($data['transactionStatus'] ?? $data['status'] ?? ''),
            'amount' => $data['order']['cost']['amount']
                ?? $data['order']['amount']
                ?? $request->input('amount'),
            'currency' => $data['order']['cost']['currency']
                ?? $data['order']['currency']
                ?? $request->input('currency', 'CDF'),
            'phone_number' => $data['paymentChannel']['walletID']
                ?? $request->input('walletID')
                ?? $request->input('phone'),
            'reference' => $data['originatingTransactionId'] ?? null,
            'message' => $data['transactionDescription'] ?? null,
            'provider_reference' => $data['transactionId'] ?? null,
            'status_code' => $data['status_code'] ?? null,
        ];
    }

    /**
     * Mapper les statuts MaishaPay
     */
    protected function mapMaishaPayStatus($status): string
    {
        return match(strtoupper($status ?? '')) {
            'SUCCESS', 'SUCCESSFUL', 'COMPLETED', 'APPROVED' => 'success',
            'PENDING' => 'pending',
            'FAILED', 'DECLINED', 'CANCELLED', 'CANCELED', 'ERROR' => 'failed',
            default => 'failed',
        };
    }

    /**
     * Traiter le callback et mettre à jour la transaction
     */
    protected function processCallback(PaymentCallback $callback): void
    {
        DB::beginTransaction();

        try {
            // Trouver la transaction correspondante
            $transaction = $this->findTransaction($callback);

            if (!$transaction) {
                Log::warning("Transaction non trouvée pour callback {$callback->id}");
                $callback->recordError('Transaction not found');
                $callback->markAsProcessed();
                DB::commit();
                return;
            }

            // Lier le callback à la transaction
            $callback->update(['transaction_id' => $transaction->id]);

            // Mettre à jour la transaction selon le statut
            switch ($callback->status) {
                case 'success':
                    $this->handleSuccessfulPayment($transaction, $callback);
                    break;

                case 'failed':
                    $this->handleFailedPayment($transaction, $callback);
                    break;

                case 'pending':
                    $transaction->update(['status' => 'pending']);
                    break;

                case 'cancelled':
                    $transaction->update(['status' => 'cancelled']);
                    break;
            }

            $callback->markAsProcessed();
            DB::commit();

            Log::info("Callback {$callback->id} traité avec succès pour transaction {$transaction->id}");

        } catch (\Exception $e) {
            DB::rollBack();
            $callback->recordError($e->getMessage());
            $callback->incrementRetry();

            Log::error("Erreur traitement callback {$callback->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Trouver la transaction correspondante
     */
    protected function findTransaction(PaymentCallback $callback): ?Transaction
    {
        $parsedData = $callback->parsed_data;

        // Chercher par référence externe
        if (!empty($callback->external_transaction_id)) {
            $transaction = Transaction::where('external_reference', $callback->external_transaction_id)->first();
            if ($transaction) return $transaction;
        }

        // Chercher par référence dans parsed_data
        if (!empty($parsedData['reference'])) {
            $transaction = Transaction::where('reference', $parsedData['reference'])->first();
            if ($transaction) return $transaction;
        }

        // Chercher par montant et numéro de téléphone (dernière tentative)
        return Transaction::where('amount', $callback->amount)
            ->where('phone_number', $callback->phone_number)
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    /**
     * Gérer un paiement réussi
     */
    protected function handleSuccessfulPayment(Transaction $transaction, PaymentCallback $callback): void
    {
        // Mettre à jour la transaction
        $transaction->update([
            'status' => 'completed',
            'external_reference' => $callback->external_transaction_id,
            'completed_at' => now(),
        ]);

        if ($transaction->order_id) {
            $this->processOrderPayment($transaction);
        } elseif ($transaction->wallet_id) {
            $this->processWalletRecharge($transaction);
        } else {
            $orders = create_orders_from_transaction($transaction->fresh());
        }

        Log::info("Paiement réussi pour transaction {$transaction->id}");
    }

    /**
     * Gérer un paiement échoué
     */
    protected function handleFailedPayment(Transaction $transaction, PaymentCallback $callback): void
    {
        $transaction->update([
            'status' => 'failed',
            'error_message' => $callback->parsed_data['message'] ?? 'Payment failed',
        ]);

        Log::info("Paiement échoué pour transaction {$transaction->id}");
    }

    /**
     * Traiter le paiement d'une commande
     */
    protected function processOrderPayment(Transaction $transaction): void
    {
        $order = Order::find($transaction->order_id);
        if (!$order) return;

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        Log::info("Commande {$order->id} payée avec succès");
    }

    /**
     * Traiter la recharge d'un wallet
     */
    protected function processWalletRecharge(Transaction $transaction): void
    {
        $wallet = Wallet::find($transaction->wallet_id);
        if (!$wallet) return;

        // Créditer le wallet
        $wallet->increment('balance', $transaction->amount);

        Log::info("Wallet {$wallet->id} rechargé de {$transaction->amount} {$transaction->currency}");
    }

    /**
     * Endpoint pour vérifier le statut d'une transaction (polling)
     */
    public function checkStatus(Request $request)
    {
        $transactionId = $request->input('transaction_id');

        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        if (in_array($transaction->status, ['completed', 'failed'])) {
            return response()->json([
                'status' => 'success',
                'transaction' => [
                    'id' => $transaction->id,
                    'status' => $transaction->status,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'created_at' => $transaction->created_at,
                    'completed_at' => $transaction->completed_at,
                ],
            ]);
        }

        if ($transaction->provider === 'maishapay' && $transaction->transaction_ref) {
            try {
                $maishaPay = new \App\Services\MaishaPay();
                $result = $maishaPay->checkStatus($transaction->transaction_ref);

                if ($result['success'] ?? false) {
                    $status = strtolower($result['status'] ?? 'pending');
                    $newStatus = match ($status) {
                        'success', 'completed', 'successful' => 'completed',
                        'failed', 'declined', 'cancelled' => 'failed',
                        default => 'pending',
                    };

                    if ($newStatus !== $transaction->status) {
                        $transaction->update(['status' => $newStatus]);
                        if ($newStatus === 'completed') {
                            create_orders_from_transaction($transaction->fresh());
                        }
                    }

                    return response()->json([
                        'status' => 'success',
                        'transaction' => [
                            'id' => $transaction->id,
                            'status' => $transaction->fresh()->status,
                            'amount' => $transaction->amount,
                            'currency' => $transaction->currency,
                            'created_at' => $transaction->created_at,
                            'completed_at' => $transaction->completed_at,
                        ],
                        'provider_status' => $result['status'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('MaishaPay status polling failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'transaction' => [
                'id' => $transaction->id,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'completed_at' => $transaction->completed_at,
            ],
        ]);
    }

    /**
     * Force la complétion manuelle d'une transaction en attente.
     * Utilisé quand le callback MaishaPay n'arrive pas (localhost) ou quand
     * l'utilisateur a déjà confirmé sur son téléphone.
     */
    public function forceComplete(Request $request, int $transactionId)
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        if ($transaction->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Transaction déjà traitée'], 400);
        }

        $transaction->update([
            'status' => 'completed',
        ]);

        create_orders_from_transaction($transaction->fresh());

        Log::info('Transaction complétée manuellement', [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'provider' => $transaction->provider,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction confirmée',
            'transaction' => [
                'id' => $transaction->id,
                'status' => 'completed',
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'completed_at' => $transaction->completed_at,
            ],
        ]);
    }

    protected function preventReplayAttack(Request $request, string $provider): bool
    {
        // Créer une signature unique basée sur le contenu du callback
        $payload = json_encode($request->all());
        $signature = hash('sha256', $provider . $payload . $request->ip());
        $cacheKey = 'callback_replay_' . $signature;
        
        // Vérifier si cette signature a déjà été traitée (dans les 10 dernières minutes)
        if (Cache::has($cacheKey)) {
            Log::warning('Replay attack détecté', [
                'provider' => $provider,
                'ip' => $request->ip(),
                'signature' => substr($signature, 0, 16) . '...',
            ]);
            return false;
        }
        
        // Marquer cette signature comme traitée (expire après 10 minutes)
        Cache::put($cacheKey, true, now()->addMinutes(10));
        
        return true;
    }
}
