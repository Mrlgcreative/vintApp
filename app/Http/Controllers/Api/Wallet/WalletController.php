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
     * API: Opérateurs de paiement disponibles
     */
    public function getPayoutOperators(Request $request): JsonResponse
    {
        $operators = [
            [
                'code' => 'VODACOM',
                'name' => 'M-Pesa (Vodacom)',
                'prefixes' => ['81', '82', '83'],
                'supported_via_maishapay' => false,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
            [
                'code' => 'ORANGE',
                'name' => 'Orange Money',
                'prefixes' => ['84', '85', '89'],
                'supported_via_maishapay' => false,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
            [
                'code' => 'AIRTEL',
                'name' => 'Airtel Money',
                'prefixes' => ['97', '98', '99'],
                'supported_via_maishapay' => false,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
            [
                'code' => 'AFRICELL',
                'name' => 'Africell Money',
                'prefixes' => ['90', '91'],
                'supported_via_maishapay' => false,
                'min_amount' => 100,
                'max_amount' => 5000000,
            ],
        ];

        $maishaPayEnabled = false;

        return $this->successResponse([
            'operators' => $operators,
            'maishapay_enabled' => $maishaPayEnabled,
            'country_code' => '+243',
            'country' => 'RDC',
        ], 'Opérateurs disponibles');
    }
}
