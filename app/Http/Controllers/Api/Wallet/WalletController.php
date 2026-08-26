<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Api\ApiController;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Services\MobileMoneyService;
use App\Services\PaymentService;
use App\Services\WalletService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletController extends ApiController
{
    private $paymentService;
    private $mobileMoneyService;
    private $walletService;

    public function __construct(PaymentService $paymentService, MobileMoneyService $mobileMoneyService, WalletService $walletService)
    {
        $this->paymentService = $paymentService;
        $this->mobileMoneyService = $mobileMoneyService;
        $this->walletService = $walletService;
    }

    /**
     * API: Wallets de l'utilisateur (USD + CDF)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $usdWallet = $this->walletService->getOrCreateUserWallet($user, 'USD');
            $cdfWallet = $this->walletService->getOrCreateUserWallet($user, 'CDF');

            return $this->successResponse([
                'wallet' => [
                    'USD' => $usdWallet,
                    'CDF' => $cdfWallet,
                ],
                'total_usd_equivalent' => $usdWallet->balance + ($cdfWallet->balance / 2500)
            ], 'Wallets récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des wallets', 500);
        }
    }

    /**
     * API: Transactions de l'utilisateur
     */
    public function transactions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $walletIds = $user->wallets()->pluck('id');

            $transactions = WalletTransaction::whereIn('wallet_id', $walletIds)
                ->with('wallet')
                ->orderBy('created_at', 'desc')
                ->paginate($request->per_page ?? 15);

            return $this->paginatedResponse($transactions, 'Transactions récupérées avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des transactions', 500);
        }
    }

    /**
     * API: Recharger le wallet
     */
    public function addFunds(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'wallet_id' => 'required|exists:wallets,id',
                'amount' => 'required|numeric|min:1',
                'payment_method' => 'required|string|in:illicocash,orange_money,airtel_money,mpesa,africell',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $wallet = Wallet::findOrFail($request->wallet_id);

            if ($wallet->user_id !== $request->user()->id) {
                return $this->errorResponse('Accès non autorisé', 403);
            }

            $paymentData = [
                'buyer_id' => $request->user()->id,
                'amount' => $request->amount,
                'purpose' => 'Recharge de wallet ' . $wallet->currency,
            ];

            switch ($request->payment_method) {
                case 'illicocash':
                    $response = $this->paymentService->payWithIllicocash($paymentData);
                    break;
                case 'orange_money':
                    $response = $this->paymentService->payWithOrangeMoney($paymentData);
                    break;
                case 'airtel_money':
                    $response = $this->paymentService->payWithAirtelMoney($paymentData);
                    break;
                case 'mpesa':
                    $response = $this->paymentService->payWithMpesa($paymentData);
                    break;
                case 'africell':
                    $response = $this->paymentService->payWithAfricell($paymentData);
                    break;
                default:
                    return $this->errorResponse('Méthode de paiement non supportée', 400);
            }

            if ($response['status'] === 'pending') {
                $wallet->transactions()->create([
                    'type' => 'credit',
                    'amount' => $request->amount,
                    'balance_after' => $wallet->balance,
                    'description' => 'Recharge via ' . ucfirst($request->payment_method),
                    'reference' => $response['provider'] . '-' . time() . '-' . rand(1000, 9999),
                    'status' => 'pending',
                    'provider' => $request->payment_method
                ]);
            }

            return $this->successResponse($response, 'Paiement initié avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de l\'ajout de fonds', 500);
        }
    }

    /**
     * API: Retirer des fonds
     */
    public function withdraw(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'wallet_id' => 'required|exists:wallets,id',
                'amount' => 'required|numeric|min:0.01',
                'phone_number' => ['required', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'min:9', 'max:15'],
                'payment_method' => 'required|string|in:orange_money,airtel_money,mpesa,africell,illicocash,agent',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $wallet = Wallet::findOrFail($request->wallet_id);

            if ($wallet->user_id !== $request->user()->id) {
                return $this->errorResponse('Accès non autorisé', 403);
            }

            [$transaction, $withdrawalRequest] = $this->walletService->createWithdrawal($wallet, $request->all());

            try {
                $cashOutResponse = $this->mobileMoneyService->cashOut(
                    $request->payment_method,
                    $request->phone_number,
                    $request->amount,
                    $wallet->currency,
                    $transaction
                );

                $withdrawalRequest->update([
                    'provider_reference' => $cashOutResponse['provider_reference'] ?? null,
                    'provider_response' => json_encode($cashOutResponse),
                    'status' => $cashOutResponse['status'] ?? 'processing',
                ]);

                $transaction->update(['status' => $cashOutResponse['status'] ?? 'processing']);

                return $this->successResponse([
                    'withdrawal' => $withdrawalRequest,
                    'transaction' => $transaction,
                ], 'Demande de retrait en cours de traitement');
            } catch (\Exception $apiError) {
                Log::error('Cash-out API error', ['error' => $apiError->getMessage()]);

                $withdrawalRequest->update(['status' => 'failed']);
                $transaction->update(['status' => 'failed']);

                return $this->successResponse([
                    'withdrawal' => $withdrawalRequest,
                    'message' => 'Demande enregistrée mais envoi échoué. Réessai manuel prévu.'
                ], 'Retrait en attente', 202);
            }
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors du retrait', 500);
        }
    }

    /**
     * API: Convertir entre wallets
     */
    public function convert(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'from_wallet_id' => 'required|exists:wallets,id',
                'to_wallet_id' => 'required|exists:wallets,id',
                'amount' => 'required|numeric|min:0.01',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $fromWallet = Wallet::findOrFail($request->from_wallet_id);
            $toWallet = Wallet::findOrFail($request->to_wallet_id);

            $result = $this->walletService->convertCurrency($fromWallet, $toWallet, (float) $request->amount, $request->user()->id);

            return $this->successResponse($result, 'Conversion effectuée avec succès');
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la conversion', 500);
        }
    }

    /**
     * API: Retrait via MaishaPay (payout unifié)
     */
    public function withdrawMaishaPay(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'wallet_id' => 'required|exists:wallets,id',
                'amount' => 'required|numeric|min:100',
                'phone_number' => ['required', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/'],
                'operator' => 'nullable|string|in:VODACOM,ORANGE,AIRTEL,AFRICELL',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $wallet = Wallet::findOrFail($request->wallet_id);

            if ($wallet->user_id !== $request->user()->id) {
                return $this->errorResponse('Accès non autorisé', 403);
            }

            if ($request->amount > $wallet->balance) {
                return $this->errorResponse('Solde insuffisant', 400);
            }

            // Vérifier que MaishaPay est activé
            if (!config('services.maishapay.enabled')) {
                return $this->errorResponse('Service MaishaPay non disponible', 503);
            }

            $maishaPay = new \App\Services\MaishaPay();

            if (!$maishaPay->isConfigured()) {
                return $this->errorResponse('MaishaPay non configuré', 503);
            }

            // Détecter l'opérateur si non fourni
            $operator = $request->operator ?? $maishaPay->detectOperator($request->phone_number);

            if (!$operator) {
                return $this->errorResponse('Impossible de détecter l\'opérateur pour ce numéro', 400);
            }

            DB::beginTransaction();

            $reference = 'WTH-MP-' . time() . '-' . rand(1000, 9999);

            $metadata = [
                'phone_number' => $request->phone_number,
                'payment_method' => 'maishapay',
                'operator' => $operator,
                'withdrawal_date' => now()->toDateTimeString(),
            ];

            $transaction = $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $wallet->balance,
                'description' => "Retrait MaishaPay vers {$request->phone_number} ({$operator})",
                'reference' => $reference,
                'status' => 'processing',
                'provider' => 'maishapay',
                'metadata' => json_encode($metadata)
            ]);

            $withdrawalRequest = WithdrawalRequest::create([
                'wallet_transaction_id' => $transaction->id,
                'phone_number' => $request->phone_number,
                'payment_method' => 'maishapay',
                'amount' => $request->amount,
                'currency' => $wallet->currency,
                'status' => 'processing',
            ]);

            $wallet->decrement('balance', $request->amount);
            $transaction->update(['balance_after' => $wallet->fresh()->balance]);

            DB::commit();

            // Initier le payout via MaishaPay
            try {
                $payoutResult = $maishaPay->initiatePayout([
                    'phone' => $request->phone_number,
                    'amount' => $request->amount,
                    'currency' => $wallet->currency,
                    'operator' => $operator,
                    'reference' => $reference,
                    'description' => "Retrait VintApp - {$reference}",
                    'user_id' => $request->user()->id,
                    'transaction_id' => $transaction->id,
                    'purpose' => 'withdrawal',
                ]);

                $withdrawalRequest->update([
                    'provider_reference' => $payoutResult['provider_reference'] ?? $payoutResult['transaction_id'] ?? null,
                    'provider_response' => json_encode($payoutResult),
                    'status' => $payoutResult['success'] ? 'processing' : 'failed',
                ]);

                $transaction->update([
                    'status' => $payoutResult['success'] ? 'processing' : 'failed'
                ]);

                if ($payoutResult['success']) {
                    return $this->successResponse([
                        'withdrawal' => $withdrawalRequest->fresh(),
                        'transaction' => $transaction->fresh(),
                        'maishapay_reference' => $payoutResult['transaction_id'] ?? null,
                        'operator' => $operator,
                    ], $payoutResult['message'] ?? 'Retrait initié avec succès');
                } else {
                    // Rembourser en cas d'échec immédiat
                    DB::transaction(function () use ($wallet, $transaction, $request) {
                        $wallet->increment('balance', $request->amount);
                        $wallet->transactions()->create([
                            'type' => 'credit',
                            'amount' => $request->amount,
                            'balance_after' => $wallet->fresh()->balance,
                            'description' => 'Remboursement - Échec MaishaPay payout',
                            'reference' => 'REFUND-' . $transaction->reference,
                            'status' => 'completed',
                        ]);
                    });

                    return $this->errorResponse($payoutResult['message'] ?? 'Échec du payout MaishaPay', 400);
                }
            } catch (\Exception $apiError) {
                Log::error('MaishaPay payout API error', [
                    'error' => $apiError->getMessage(),
                    'reference' => $reference,
                ]);

                $withdrawalRequest->update(['status' => 'pending']);
                $transaction->update(['status' => 'pending']);

                return $this->successResponse([
                    'withdrawal' => $withdrawalRequest->fresh(),
                    'message' => 'Demande enregistrée. Traitement en cours.'
                ], 'Retrait en attente de traitement', 202);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MaishaPay withdrawal error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erreur lors du retrait', 500);
        }
    }

    /**
     * API: Statut d'un retrait MaishaPay
     */
    public function withdrawMaishaPayStatus(Request $request, string $transactionId): JsonResponse
    {
        try {
            $transaction = WalletTransaction::where('reference', $transactionId)
                ->orWhere('id', $transactionId)
                ->first();

            if (!$transaction) {
                return $this->errorResponse('Transaction non trouvée', 404);
            }

            // Vérifier que l'utilisateur est propriétaire
            if ($transaction->wallet->user_id !== $request->user()->id) {
                return $this->errorResponse('Accès non autorisé', 403);
            }

            $withdrawalRequest = WithdrawalRequest::where('wallet_transaction_id', $transaction->id)->first();

            // Si le statut est toujours en processing, vérifier via MaishaPay
            if ($transaction->status === 'processing' && config('services.maishapay.enabled')) {
                try {
                    $maishaPay = new \App\Services\MaishaPay();
                    $providerRef = $withdrawalRequest->provider_reference ?? $transaction->reference;
                    $statusResult = $maishaPay->checkPayoutStatus($providerRef);

                    if ($statusResult['success'] && $statusResult['status'] !== 'processing') {
                        $transaction->update(['status' => $statusResult['status']]);
                        $withdrawalRequest?->update(['status' => $statusResult['status']]);
                    }
                } catch (\Exception $e) {
                    Log::warning('MaishaPay status check failed', ['error' => $e->getMessage()]);
                }
            }

            return $this->successResponse([
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'provider_reference' => $withdrawalRequest->provider_reference ?? null,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
            ], 'Statut récupéré');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la vérification', 500);
        }
    }

    /**
     * API: Opérateurs de paiement disponibles
     */
    public function getPayoutOperators(Request $request): JsonResponse
    {
        $operators = [
            [
                'code' => 'VODACOM',
                'name' => 'M-Pesa (Vodacom)',
                'prefixes' => ['81', '82', '83'],
                'supported_via_maishapay' => true,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
            [
                'code' => 'ORANGE',
                'name' => 'Orange Money',
                'prefixes' => ['84', '85', '89'],
                'supported_via_maishapay' => true,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
            [
                'code' => 'AIRTEL',
                'name' => 'Airtel Money',
                'prefixes' => ['97', '98', '99'],
                'supported_via_maishapay' => true,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
            [
                'code' => 'AFRICELL',
                'name' => 'Africell Money',
                'prefixes' => ['90', '91'],
                'supported_via_maishapay' => true,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
        ];

        $maishaPayEnabled = config('services.maishapay.enabled', false);

        return $this->successResponse([
            'operators' => $operators,
            'maishapay_enabled' => $maishaPayEnabled,
            'country_code' => '+243',
            'country' => 'RDC',
        ], 'Opérateurs disponibles');
    }
}
