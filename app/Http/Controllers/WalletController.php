<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;
use App\Services\MobileMoneyService;

class WalletController extends Controller
{
    private $paymentService;
    private $mobileMoneyService;

    public function __construct(PaymentService $paymentService, MobileMoneyService $mobileMoneyService)
    {
        $this->paymentService = $paymentService;
        $this->mobileMoneyService = $mobileMoneyService;
    }
    
    /**
     * Affiche les wallets de l'utilisateur.
     */
    
    public function index()
    {
        $user = Auth::user();
        
        // Créer les wallets s'ils n'existent pas
        $usdWallet = $this->getOrCreateUserWallet($user, 'USD');
        $cdfWallet = $this->getOrCreateUserWallet($user, 'CDF');

        // Récupérer les transactions récentes
        $recentTransactions = WalletTransaction::whereIn('wallet_id', [$usdWallet->id, $cdfWallet->id])
            ->with('wallet')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('wallet.index', compact('usdWallet', 'cdfWallet', 'recentTransactions'));
    }

    /**
     * Obtient ou crée un wallet pour un utilisateur et une devise donnée.
     */
    private function getOrCreateUserWallet($user, $currency)
    {
        $wallet = $user->wallets()->where('currency', $currency)->first();
        
        if (!$wallet) {
            $wallet = $user->wallets()->create([
                'currency' => $currency,
                'balance' => 0.00,
                'is_active' => true,
            ]);
        }
        
        return $wallet;
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
            'payment_method' => 'required|string|in:orange_money,airtel_money,mpesa,africell,illicocash',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validated['amount'] > $wallet->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Solde insuffisant pour effectuer ce retrait.');
        }

        try {
            DB::beginTransaction();

            // 1. Créer la transaction de retrait
            $transaction = $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $validated['amount'],
                'balance_after' => $wallet->balance, // Balance avant débit
                'description' => $validated['description'] ?? 'Retrait de fonds vers ' . $validated['phone_number'],
                'reference' => 'WTH-' . time() . '-' . rand(1000, 9999),
                'status' => 'processing',
                'provider' => $validated['payment_method'],
                'metadata' => json_encode([
                    'phone_number' => $validated['phone_number'],
                    'payment_method' => $validated['payment_method'],
                    'withdrawal_date' => now()->toDateTimeString(),
                ])
            ]);

            // 2. Créer la demande de retrait
            $withdrawalRequest = WithdrawalRequest::create([
                'wallet_transaction_id' => $transaction->id,
                'phone_number' => $validated['phone_number'],
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'currency' => $wallet->currency,
                'status' => 'processing',
            ]);

            // 3. Débiter immédiatement le wallet (fonds bloqués)
            $wallet->decrement('balance', $validated['amount']);
            $transaction->update(['balance_after' => $wallet->fresh()->balance]);

            DB::commit();

            // 4. Appeler l'API de décaissement (asynchrone)
            try {
                $cashOutResponse = $this->mobileMoneyService->cashOut(
                    $validated['payment_method'],
                    $validated['phone_number'],
                    $validated['amount'],
                    $wallet->currency,
                    $transaction
                );

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

        } catch (\Exception $e) {
            DB::rollBack();
            
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

        // Vérifier que les deux wallets appartiennent à l'utilisateur connecté
        if ($fromWallet->user_id !== Auth::id() || $toWallet->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès non autorisé'
            ], 403);
        }

        // Vérifier que les devises sont différentes
        if ($fromWallet->currency === $toWallet->currency) {
            return response()->json([
                'status' => 'error',
                'message' => 'Les deux wallets ont la même devise'
            ], 400);
        }

        // Vérifier que le solde est suffisant
        if ($fromWallet->balance < $validated['amount']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Solde insuffisant dans le wallet source'
            ], 400);
        }

        try {
            // Récupérer le taux de change
            $rate = Cache::remember('usd_cdf_rate', 3600, function () {
                return 2500.00; // Taux fixe, vous pouvez le rendre dynamique
            });

            // Calculer le montant converti
            $convertedAmount = $fromWallet->currency === 'USD'
                ? $validated['amount'] * $rate    // USD vers CDF
                : $validated['amount'] / $rate;   // CDF vers USD

            DB::transaction(function () use ($fromWallet, $toWallet, $validated, $convertedAmount, $rate) {
                // Débiter le wallet source
                $fromWallet->decrement('balance', $validated['amount']);
                
                $fromWallet->transactions()->create([
                    'type' => 'debit',
                    'amount' => $validated['amount'],
                    'balance_after' => $fromWallet->fresh()->balance,
                    'description' => 'Conversion de ' . $fromWallet->currency . ' vers ' . $toWallet->currency,
                    'reference' => 'CONV-' . time() . '-' . rand(1000, 9999),
                    'status' => 'completed',
                ]);

                // Créditer le wallet destination
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

            return response()->json([
                'status' => 'success',
                'message' => 'Conversion effectuée avec succès',
                'from_currency' => $fromWallet->currency,
                'to_currency' => $toWallet->currency,
                'amount' => $validated['amount'],
                'converted_amount' => round($convertedAmount, 2),
                'rate' => $rate,
                'from_balance' => $fromWallet->fresh()->balance,
                'to_balance' => $toWallet->fresh()->balance,
            ]);

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
            // Valider le provider
            $validProviders = ['orange_money', 'airtel_money', 'mpesa', 'africell', 'illicocash'];
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

            // Trouver la transaction
            $transaction = WalletTransaction::where('reference', $reference)
                ->where('type', 'debit')
                ->where('provider', $provider)
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
            DB::beginTransaction();

            $orderId = $request->order_id;
            $amount = $request->amount;
            $sellerId = $request->seller_id;
            $currency = $request->currency;

            // Récupérer le wallet entreprise pour cette devise
            $enterpriseWallet = Wallet::where('type', 'enterprise')
                ->where('currency', $currency)
                ->whereNull('user_id')
                ->first();

            if (!$enterpriseWallet) {
                return response()->json([
                    'success' => false,
                    'message' => "Wallet entreprise {$currency} introuvable",
                ], 404);
            }

            // Récupérer le wallet pending du vendeur
            $pendingWallet = Wallet::where('user_id', $sellerId)
                ->where('type', 'pending')
                ->where('currency', $currency)
                ->first();

            if (!$pendingWallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet pending du vendeur introuvable',
                ], 404);
            }

            // Récupérer le wallet principal du vendeur
            $sellerWallet = Wallet::where('user_id', $sellerId)
                ->where('type', 'main')
                ->where('currency', $currency)
                ->first();

            if (!$sellerWallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet principal du vendeur introuvable',
                ], 404);
            }

            // Vérifier que le pending wallet a suffisamment de fonds
            if ($pendingWallet->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant dans le wallet pending',
                    'available_balance' => $pendingWallet->balance,
                    'required_amount' => $amount,
                ], 400);
            }

            // Calculer la commission (récupérer le taux du wallet entreprise)
            $commissionRate = $enterpriseWallet->commission_rate;
            $commissionAmount = round(($amount * $commissionRate) / 100, 2);
            $sellerAmount = round($amount - $commissionAmount, 2);

            // 1. Débiter le pending wallet
            DB::statement(
                'UPDATE wallets SET balance = balance - ? WHERE id = ?',
                [$amount, $pendingWallet->id]
            );

            // 2. Créditer le wallet entreprise (commission)
            DB::statement(
                'UPDATE wallets SET balance = balance + ? WHERE id = ?',
                [$commissionAmount, $enterpriseWallet->id]
            );

            // 3. Créditer le wallet du vendeur (montant net)
            DB::statement(
                'UPDATE wallets SET balance = balance + ? WHERE id = ?',
                [$sellerAmount, $sellerWallet->id]
            );

            // Logger les transactions
            // Transaction 1: Débit du pending
            WalletTransaction::create([
                'wallet_id' => $pendingWallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "Transfert commission vente #{$orderId}",
                'reference' => "SALE_CONFIRMED_{$orderId}",
            ]);

            // Transaction 2: Crédit entreprise (commission)
            WalletTransaction::create([
                'wallet_id' => $enterpriseWallet->id,
                'type' => 'credit',
                'amount' => $commissionAmount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "Commission {$commissionRate}% sur vente #{$orderId}",
                'reference' => "COMMISSION_{$orderId}",
            ]);

            // Transaction 3: Crédit vendeur (net)
            WalletTransaction::create([
                'wallet_id' => $sellerWallet->id,
                'type' => 'credit',
                'amount' => $sellerAmount,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "Paiement vente #{$orderId} (net après commission)",
                'reference' => "PAYMENT_{$orderId}",
            ]);

            DB::commit();

            // Rafraîchir les wallets pour obtenir les nouveaux soldes
            $enterpriseWallet->refresh();
            $sellerWallet->refresh();
            $pendingWallet->refresh();

            Log::info('Commission transférée avec succès', [
                'order_id' => $orderId,
                'amount_total' => $amount,
                'commission' => $commissionAmount,
                'seller_net' => $sellerAmount,
                'currency' => $currency,
                'commission_rate' => $commissionRate,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commission transférée avec succès',
                'data' => [
                    'order_id' => $orderId,
                    'montant_total' => $amount,
                    'montant_commission' => $commissionAmount,
                    'taux_commission' => $commissionRate,
                    'montant_vendeur_net' => $sellerAmount,
                    'currency' => $currency,
                    'wallets' => [
                        'entreprise_balance' => $enterpriseWallet->balance,
                        'vendeur_balance' => $sellerWallet->balance,
                        'pending_balance' => $pendingWallet->balance,
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

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
}
