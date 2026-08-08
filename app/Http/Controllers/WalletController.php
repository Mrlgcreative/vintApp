<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;
use App\Services\MobileMoneyService;
use App\Services\WalletService;
use App\Traits\ApiResponses;

class WalletController extends Controller
{
    use ApiResponses;
    
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
     * Affiche les wallets de l'utilisateur.
     */
    
    public function index()
    {
        $user = Auth::user();
        
        // Créer les wallets s'ils n'existent pas
        $usdWallet = $this->walletService->getOrCreateUserWallet($user, 'USD');
        $cdfWallet = $this->walletService->getOrCreateUserWallet($user, 'CDF');

        // Récupérer les transactions récentes avec pagination
        $recentTransactions = WalletTransaction::whereIn('wallet_id', [$usdWallet->id, $cdfWallet->id])
            ->with('wallet')
            ->orderBy('created_at', 'desc')
            ->paginate(15); // Pagination de 15 par page

        return view('wallet.index', compact('usdWallet', 'cdfWallet', 'recentTransactions'));
    }

    /**
     * Affiche l'historique des transactions d'un wallet.
     */
    public function transactions(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.transactions', compact('wallet', 'transactions'));
    }

    /**
     * Affiche le formulaire d'ajout de fonds.
     */
    public function addFunds(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('wallet.add-funds', compact('wallet'));
    }

    /**
     * Traite l'ajout de fonds.
     */
    public function storeAddFunds(Request $request, Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($wallet, $validated) {
                $wallet->increment('balance', $validated['amount']);
                
                $wallet->transactions()->create([
                    'type' => 'credit',
                    'amount' => $validated['amount'],
                    'balance_after' => $wallet->fresh()->balance,
                    'description' => $validated['description'] ?? 'Ajout de fonds',
                    'reference' => 'ADD-' . time() . '-' . rand(1000, 9999),
                ]);
            });

            return redirect()->route('wallet.index')
                ->with('success', 'Fonds ajoutés avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout des fonds : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de retrait de fonds.
     */
    public function withdrawFunds(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('wallet.withdraw-funds', compact('wallet'));
    }

    /**
     * Traite le retrait de fonds avec API de décaissement automatique.
     */
    public function storeWithdrawFunds(Request $request, Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $wallet->balance,
            'phone_number' => ['required', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'min:9', 'max:15'],
            // Ajout de maishapay et cinetpay comme méthodes de décaissement
            'payment_method' => 'required|string|in:maishapay,cinetpay,orange_money,airtel_money,mpesa,africell,illicocash,agent',
            // Si payment_method == agent, on attend l'id de l'agent ou son numéro
            'agent_id' => 'nullable|integer',
            'agent_phone' => ['nullable', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'min:9', 'max:15', 'required_if:payment_method,agent'],
            'description' => 'nullable|string|max:255',
        ]);

        try {
            [$transaction, $withdrawalRequest] = $this->walletService->createWithdrawal($wallet, $validated);

            // 4. Appeler l'API de décaissement (asynchrone)
            try {
                // Si le paiement se fait via un agent, appeler la méthode dédiée
                if ($validated['payment_method'] === 'agent') {
                    $agentId = $validated['agent_id'] ?? null;
                    $agentPhone = $validated['agent_phone'] ?? $validated['phone_number'];

                    $cashOutResponse = $this->mobileMoneyService->cashOutAgent(
                        $agentId,
                        $agentPhone,
                        $validated['amount'],
                        $wallet->currency,
                        $transaction
                    );
                } elseif ($validated['payment_method'] === 'maishapay') {
                    // Décaissement via MaishaPay B2C (détection automatique de l'opérateur)
                    $cashOutResponse = $this->mobileMoneyService->cashOut(
                        'maishapay',
                        $validated['phone_number'],
                        $validated['amount'],
                        $wallet->currency,
                        $transaction
                    );
                } elseif ($validated['payment_method'] === 'cinetpay') {
                    // Décaissement via l'API de transfert CinetPay
                    $cashOutResponse = $this->mobileMoneyService->cashOut(
                        'cinetpay',
                        $validated['phone_number'],
                        $validated['amount'],
                        $wallet->currency,
                        $transaction
                    );
                } else {
                    $cashOutResponse = $this->mobileMoneyService->cashOut(
                        $validated['payment_method'],
                        $validated['phone_number'],
                        $validated['amount'],
                        $wallet->currency,
                        $transaction
                    );
                }

                // Mettre à jour avec la réponse du provider
                $withdrawalRequest->update([
                    'provider_reference' => $cashOutResponse['provider_reference'] ?? null,
                    'provider_response' => json_encode($cashOutResponse),
                    'status' => $cashOutResponse['status'] ?? 'processing',
                ]);

                // Mettre à jour le statut de la transaction
                if (isset($cashOutResponse['status'])) {
                    $transaction->update(['status' => $cashOutResponse['status']]);
                }

                // Si échec immédiat, rembourser
                if (isset($cashOutResponse['status']) && $cashOutResponse['status'] === 'failed') {
                    DB::transaction(function () use ($wallet, $validated, $transaction) {
                        $wallet->increment('balance', $validated['amount']);
                        $transaction->update([
                            'status' => 'failed',
                            'balance_after' => $wallet->fresh()->balance
                        ]);
                    });

                    return redirect()->route('wallet.index')
                        ->with('error', 'Le décaissement a échoué : ' . ($cashOutResponse['message'] ?? 'Erreur inconnue'));
                }

                Log::info('Cash-out initiated', [
                    'transaction_id' => $transaction->id,
                    'withdrawal_request_id' => $withdrawalRequest->id,
                    'response' => $cashOutResponse
                ]);

                return redirect()->route('wallet.index')
                    ->with('success', 'Demande de retrait en cours de traitement ! Les fonds seront envoyés vers ' . $validated['phone_number'] . ' sous quelques minutes.');

            } catch (\Exception $apiError) {
                Log::error('Cash-out API error', [
                    'error' => $apiError->getMessage(),
                    'transaction_id' => $transaction->id
                ]);

                // Marquer comme échoué mais ne pas rembourser immédiatement
                // (permettre une réessai manuel par l'admin)
                $withdrawalRequest->update([
                    'status' => 'failed',
                    'provider_response' => json_encode(['error' => $apiError->getMessage()])
                ]);
                
                $transaction->update(['status' => 'failed']);

                return redirect()->route('wallet.index')
                    ->with('warning', 'La demande de retrait a été enregistrée mais l\'envoi a échoué. Notre équipe va réessayer manuellement.');
            }

        } catch (\DomainException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Withdrawal creation error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors du retrait : ' . $e->getMessage());
        }
    }

    /**
     * API pour obtenir le solde d'un wallet.
     */
    public function getBalance(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return response()->json([
            'balance' => $wallet->balance,
            'formatted_balance' => $wallet->formatted_balance,
            'currency' => $wallet->currency,
        ]);
    }

    /**
     * Initie un paiement mobile pour recharger le wallet
     */
    public function rechargeWithMobilePayment(Request $request, Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:illicocash,orange_money,airtel_money,mpesa,africell',
        ]);

        try {
            $paymentData = [
                'buyer_id' => Auth::id(),
                'amount' => $validated['amount'],
                'purpose' => 'Recharge de wallet ' . $wallet->currency,
            ];

            // Rediriger vers la méthode de paiement appropriée dans PaymentController
            switch ($validated['payment_method']) {
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
                    throw new \Exception('Méthode de paiement non supportée');
            }

            // Si le paiement est initié avec succès, créer une transaction en attente
            if ($response['status'] === 'pending') {
                DB::transaction(function () use ($wallet, $validated, $response) {
                    $wallet->transactions()->create([
                        'type' => 'credit_pending',
                        'amount' => $validated['amount'],
                        'balance_after' => $wallet->balance,
                        'description' => 'Recharge via ' . ucfirst($validated['payment_method']),
                        'reference' => $response['provider'] . '-' . time() . '-' . rand(1000, 9999),
                        'status' => 'pending',
                        'provider' => $validated['payment_method']
                    ]);
                });
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'initiation du paiement : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convertit un montant d'un wallet à un autre (USD <-> CDF)
     */
    public function convertCurrency(Request $request)
    {
        $validated = $request->validate([
            'from_wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $fromWallet = Wallet::findOrFail($validated['from_wallet_id']);
        $toWallet = Wallet::findOrFail($validated['to_wallet_id']);

        try {
            $result = $this->walletService->convertCurrency($fromWallet, $toWallet, (float) $validated['amount'], Auth::id());

            return response()->json(array_merge(['status' => 'success', 'message' => 'Conversion effectuée avec succès'], $result));

        } catch (\DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la conversion : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook pour recevoir les callbacks des opérateurs mobile money
     * Route: POST /wallet/withdrawals/webhook/{provider}
     */
    public function handleWithdrawalWebhook(Request $request, $provider)
    {
        Log::info('Withdrawal webhook received', [
            'provider' => $provider,
            'payload' => $request->all()
        ]);

        try {
            // Valider le provider (inclut maishapay et cinetpay comme agrégateurs)
            $validProviders = ['orange_money', 'airtel_money', 'mpesa', 'africell', 'illicocash', 'maishapay', 'cinetpay'];
            if (!in_array($provider, $validProviders)) {
                Log::warning('Invalid provider in webhook', ['provider' => $provider]);
                return response()->json(['status' => 'error', 'message' => 'Provider invalide'], 400);
            }

            // Vérifier la signature/authentification du webhook
            if (!$this->mobileMoneyService->verifyWebhookSignature($provider, $request)) {
                Log::warning('Invalid webhook signature', ['provider' => $provider]);
                return response()->json(['status' => 'error', 'message' => 'Signature invalide'], 401);
            }

            // Extraire la référence de transaction
            $reference = $this->mobileMoneyService->extractReferenceFromWebhook($provider, $request);
            
            if (!$reference) {
                Log::error('No reference found in webhook', ['provider' => $provider, 'payload' => $request->all()]);
                return response()->json(['status' => 'error', 'message' => 'Référence introuvable'], 400);
            }

            // Trouver la transaction (pour maishapay, chercher aussi dans les transactions avec aggregator)
            $transaction = WalletTransaction::where('reference', $reference)
                ->where('type', 'debit')
                ->when($provider === 'maishapay', function ($query) {
                    // Pour MaishaPay, chercher les transactions avec n'importe quel provider mobile money
                    return $query->whereIn('provider', ['orange_money', 'airtel_money', 'mpesa', 'africell', 'maishapay']);
                }, function ($query) use ($provider) {
                    return $query->where('provider', $provider);
                })
                ->first();

            if (!$transaction) {
                Log::error('Transaction not found for webhook', [
                    'reference' => $reference,
                    'provider' => $provider
                ]);
                return response()->json(['status' => 'error', 'message' => 'Transaction introuvable'], 404);
            }

            // Extraire le statut du webhook
            $webhookStatus = $this->mobileMoneyService->extractStatusFromWebhook($provider, $request);
            $providerReference = $this->mobileMoneyService->extractProviderReferenceFromWebhook($provider, $request);

            Log::info('Webhook status extracted', [
                'reference' => $reference,
                'status' => $webhookStatus,
                'provider_reference' => $providerReference
            ]);

            // Mettre à jour la demande de retrait
            $withdrawalRequest = WithdrawalRequest::where('wallet_transaction_id', $transaction->id)->first();

            if ($withdrawalRequest) {
                $withdrawalRequest->update([
                    'status' => $webhookStatus,
                    'provider_reference' => $providerReference ?? $withdrawalRequest->provider_reference,
                    'provider_response' => json_encode(array_merge(
                        json_decode($withdrawalRequest->provider_response ?? '{}', true),
                        ['webhook' => $request->all(), 'webhook_received_at' => now()->toDateTimeString()]
                    ))
                ]);
            }

            // Mettre à jour la transaction
            $transaction->update(['status' => $webhookStatus]);

            // Si le retrait a échoué, rembourser le wallet
            if ($webhookStatus === 'failed') {
                DB::transaction(function () use ($transaction) {
                    $wallet = $transaction->wallet;
                    $wallet->increment('balance', $transaction->amount);
                    
                    $wallet->transactions()->create([
                        'type' => 'credit',
                        'amount' => $transaction->amount,
                        'balance_after' => $wallet->fresh()->balance,
                        'description' => 'Remboursement suite à échec de retrait - Ref: ' . $transaction->reference,
                        'reference' => 'REFUND-' . $transaction->reference,
                        'status' => 'completed',
                    ]);
                });

                Log::info('Wallet refunded due to failed withdrawal', [
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount
                ]);
            }

            // Si complété avec succès
            if ($webhookStatus === 'completed') {
                Log::info('Withdrawal completed successfully', [
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'phone' => json_decode($transaction->metadata, true)['phone_number'] ?? 'N/A'
                ]);

                // Optionnel: Envoyer notification à l'utilisateur
                // event(new WithdrawalCompleted($transaction));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook traité avec succès'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du traitement du webhook'
            ], 500);
        }
    }

    /**
     * Réessayer un retrait échoué (Admin uniquement)
     * Route: POST /wallet/withdrawals/{withdrawalRequest}/retry
     */
    public function retryFailedWithdrawal(WithdrawalRequest $withdrawalRequest)
    {
        // Vérifier les permissions admin (à adapter selon votre système)
        if (!Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        if ($withdrawalRequest->status !== 'failed') {
            return redirect()->back()
                ->with('error', 'Seuls les retraits échoués peuvent être réessayés.');
        }

        try {
            $transaction = $withdrawalRequest->transaction;
            
            // Mettre à jour le statut en "processing"
            $withdrawalRequest->update(['status' => 'processing']);
            $transaction->update(['status' => 'processing']);

            // Réessayer l'API de décaissement
            $cashOutResponse = $this->mobileMoneyService->cashOut(
                $withdrawalRequest->payment_method,
                $withdrawalRequest->phone_number,
                $withdrawalRequest->amount,
                $withdrawalRequest->currency,
                $transaction
            );

            // Mettre à jour avec la nouvelle réponse
            $withdrawalRequest->update([
                'provider_reference' => $cashOutResponse['provider_reference'] ?? $withdrawalRequest->provider_reference,
                'provider_response' => json_encode(array_merge(
                    json_decode($withdrawalRequest->provider_response ?? '{}', true),
                    ['retry' => $cashOutResponse, 'retry_at' => now()->toDateTimeString()]
                )),
                'status' => $cashOutResponse['status'] ?? 'processing',
            ]);

            $transaction->update(['status' => $cashOutResponse['status'] ?? 'processing']);

            Log::info('Withdrawal retry initiated', [
                'withdrawal_request_id' => $withdrawalRequest->id,
                'response' => $cashOutResponse
            ]);

            return redirect()->back()
                ->with('success', 'Le retrait a été réessayé avec succès.');

        } catch (\Exception $e) {
            Log::error('Withdrawal retry error', [
                'withdrawal_request_id' => $withdrawalRequest->id,
                'error' => $e->getMessage()
            ]);

            $withdrawalRequest->update(['status' => 'failed']);
            $transaction->update(['status' => 'failed']);

            return redirect()->back()
                ->with('error', 'Erreur lors de la réessai : ' . $e->getMessage());
        }
    }

    /**
     * Transfère la commission de la plateforme depuis le wallet pending vers le wallet entreprise
     * et crédite le montant net au wallet principal du vendeur.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferCommission(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'seller_id' => 'required|exists:users,id',
            'currency' => 'required|in:USD,CDF',
        ]);

        try {
            $result = $this->walletService->transferCommission(
                $request->order_id,
                (float) $request->amount,
                $request->seller_id,
                $request->currency
            );

            return response()->json(array_merge(['success' => true, 'message' => 'Commission transférée avec succès'], ['data' => $result]), 200);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            Log::error('Erreur lors du transfert de commission', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du transfert de commission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ==================== API Methods ====================

    /**
     * Get user wallets via API
     */
    public function apiShow(Request $request)
    {
        try {
            $user = $request->user();
            
            $usdWallet = $this->walletService->getOrCreateUserWallet($user, 'USD');
            $cdfWallet = $this->walletService->getOrCreateUserWallet($user, 'CDF');

            return $this->successResponse([
                'usd_wallet' => $usdWallet,
                'cdf_wallet' => $cdfWallet,
                'total_usd_equivalent' => $usdWallet->balance + ($cdfWallet->balance / 2500)
            ], 'Wallets récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des wallets', 500);
        }
    }

    /**
     * Get wallet transactions via API
     */
    public function apiTransactions(Request $request)
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
     * Add funds via API
     */
    public function apiAddFunds(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
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
                    'type' => 'credit_pending',
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
     * Withdraw funds via API
     */
    public function apiWithdraw(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
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
        } catch (\DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors du retrait', 500);
        }
    }

    /**
     * Convert currency between wallets via API
     */
    public function apiConvert(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
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
        } catch (\DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la conversion', 500);
        }
    }

    /**
     * Withdraw funds via MaishaPay API (unified payout)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiWithdrawMaishaPay(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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
     * Check MaishaPay payout status
     * 
     * @param string $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiWithdrawMaishaPayStatus(Request $request, string $transactionId)
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
     * Get available payout operators
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGetPayoutOperators(Request $request)
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
