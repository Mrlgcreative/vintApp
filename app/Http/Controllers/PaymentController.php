<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Models\Order;
use App\Models\Refund;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\StorageSyncService;
use App\Traits\ApiResponses;

class PaymentController extends Controller
{
    use ApiResponses;

    protected $paymentService;
    protected $notificationService;

    public function __construct(PaymentService $paymentService, NotificationService $notificationService)
    {
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }
    

    /**
     * Process payment request
     */

    public function processPayment(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:orange_money,mpesa,airtel_money,africell,illicocash',
            'amount' => 'required|numeric|min:1',
            'phone' => 'required|string|min:9|max:9',
            'purpose' => 'required|string',
            'buyer_id' => 'required|exists:users,id'
        ]);

        try {
            $paymentData = $request->only(['amount', 'phone', 'purpose', 'buyer_id']);
            
            // Sélectionner la méthode de paiement appropriée
            $methodName = 'payWith' . str_replace('_', '', ucfirst($request->provider));
            if (!method_exists($this->paymentService, $methodName)) {
                throw new \Exception('Méthode de paiement non supportée');
            }

            // Créer la transaction initiale
            $transaction = Transaction::create([
                'user_id' => $request->buyer_id,
                'amount' => $request->amount,
                'provider' => $request->provider,
                'status' => 'pending',
                'purpose' => $request->purpose,
                'phone_number' => $request->phone
            ]);

            // Appeler le service de paiement
            $result = $this->paymentService->{$methodName}($paymentData);

            if ($result['status'] === 'pending') {
                return response()->json([
                    'status' => 'success',
                    'message' => $result['message'],
                    'transaction_id' => $transaction->id
                ]);
            }

            // En cas d'erreur
            $transaction->update(['status' => 'failed']);
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Une erreur est survenue lors du traitement du paiement'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement du paiement'
            ], 500);
        }
    }
    // Paiement Illicocash
    public function payWithIllicocash(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string',
        ]);
        if (!config('payments.providers.illicocash.enabled')) {
            return response()->json(['status' => 'error', 'message' => 'Illicocash désactivé.'], 403);
        }
        // Préparer les données pour l'API Illicocash
        $apiKey = config('payments.providers.illicocash.api_key');
        $apiSecret = config('payments.providers.illicocash.api_secret');
        // TODO: Appel API Illicocash ici (Http::withToken($apiKey)->post(...))
        // TODO: Créer la transaction avec status 'pending'
        // TODO: Retourner la réponse de l'API ou un message d'attente
        return response()->json(['status' => 'pending', 'message' => 'Paiement Illicocash en cours...', 'provider' => 'illicocash']);
    }

    // Paiement Orange Money
    public function payWithOrangeMoney(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string',
        ]);
        if (!config('payments.providers.orange_money.enabled')) {
            return response()->json(['status' => 'error', 'message' => 'Orange Money désactivé.'], 403);
        }
        $apiKey = config('payments.providers.orange_money.api_key');
        $apiSecret = config('payments.providers.orange_money.api_secret');
        // TODO: Appel API Orange Money ici
        // TODO: Créer la transaction avec status 'pending'
        return response()->json(['status' => 'pending', 'message' => 'Paiement Orange Money en cours...', 'provider' => 'orange_money']);
    }

    // Paiement Airtel Money
    public function payWithAirtelMoney(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string',
        ]);
        if (!config('payments.providers.airtel_money.enabled')) {
            return response()->json(['status' => 'error', 'message' => 'Airtel Money désactivé.'], 403);
        }
        $apiKey = config('payments.providers.airtel_money.api_key');
        $apiSecret = config('payments.providers.airtel_money.api_secret');
        // TODO: Appel API Airtel Money ici
        // TODO: Créer la transaction avec status 'pending'
        return response()->json(['status' => 'pending', 'message' => 'Paiement Airtel Money en cours...', 'provider' => 'airtel_money']);
    }

    // Paiement Vodacom Mpesa
    public function payWithMpesa(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:USD,CDF',
            'phone' => 'required|string|regex:/^[0-9]{9}$/',
            'purpose' => 'required|string',
        ]);

        if (!config('payments.providers.mpesa.enabled')) {
            return response()->json(['status' => 'error', 'message' => 'M-Pesa désactivé.'], 403);
        }

        $apiKey = config('payments.providers.mpesa.api_key');
        $apiSecret = config('payments.providers.mpesa.api_secret');

        if (!$apiKey || !$apiSecret) {
            return response()->json(['status' => 'error', 'message' => 'Configuration M-Pesa manquante.'], 500);
        }

        // Récupérer le panier pour traitement des commandes
        $cart = session('cart', []);
        
        // Déterminer la devise prioritaire et calculer le montant total
        $totalAmount = 0;
        $priorityCurrency = $request->currency ?? 'USD';
        
        if (!empty($cart)) {
            // Compter les devises dans le panier
            $currencyCounts = [];
            foreach ($cart as $itemId => $cartItem) {
                $item = \App\Models\Item::find($itemId);
                if ($item) {
                    $currency = $item->currency ?? 'USD';
                    $currencyCounts[$currency] = ($currencyCounts[$currency] ?? 0) + 1;
                }
            }
            
            // Déterminer la devise prioritaire (la plus fréquente)
            if (!empty($currencyCounts)) {
                arsort($currencyCounts);
                $priorityCurrency = array_key_first($currencyCounts);
            }
            
            // Récupérer le taux de change
            $exchangeRate = \Illuminate\Support\Facades\Cache::remember('usd_cdf_rate', 3600, function () {
                try {
                    $controller = new ExchangeRateController();
                    $response = $controller->getRate();
                    $data = $response->getData(true);
                    return $data['rate'] ?? 2650.00;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur récupération taux: ' . $e->getMessage());
                    return 2650.00;
                }
            });
            
            // Calculer le montant total dans la devise prioritaire
            foreach ($cart as $itemId => $cartItem) {
                $item = \App\Models\Item::find($itemId);
                if ($item) {
                    $itemTotal = $item->price * $cartItem['quantity'];
                    $itemCurrency = $item->currency ?? 'USD';
                    
                    // Convertir si nécessaire
                    if ($itemCurrency !== $priorityCurrency) {
                        if ($priorityCurrency === 'USD' && $itemCurrency === 'CDF') {
                            $itemTotal = $itemTotal / $exchangeRate;
                        } elseif ($priorityCurrency === 'CDF' && $itemCurrency === 'USD') {
                            $itemTotal = $itemTotal * $exchangeRate;
                        }
                    }
                    
                    $totalAmount += $itemTotal;
                }
            }
            
            $totalAmount = round($totalAmount, 2);
        } else {
            $totalAmount = $request->amount;
        }

        // Générer un ID de transaction unique
        $transaction_id = 'MPESA-' . strtoupper(\Illuminate\Support\Str::random(12));
        
        try {
            // Préparer la requête M-Pesa
            $mpesaData = [
                'BusinessShortCode' => config('payments.providers.mpesa.shortcode', '174379'),
                'Password' => base64_encode(config('payments.providers.mpesa.shortcode', '174379') . config('payments.providers.mpesa.passkey', '') . now()->format('YmdHis')),
                'Timestamp' => now()->format('YmdHis'),
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int)$totalAmount,
                'PartyA' => '243' . $request->phone, // Numéro client avec code pays
                'PartyB' => config('payments.providers.mpesa.shortcode', '174379'),
                'PhoneNumber' => '243' . $request->phone,
                'CallBackURL' => route('payment.callback', ['provider' => 'mpesa']),
                'AccountReference' => $transaction_id,
                'TransactionDesc' => $request->purpose
            ];

            // Obtenir le token d'authentification M-Pesa
            $authResponse = \Illuminate\Support\Facades\Http::withBasicAuth($apiKey, $apiSecret)
                ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

            if (!$authResponse->successful()) {
                throw new \Exception('Erreur d\'authentification M-Pesa');
            }

            $accessToken = $authResponse->json('access_token');

            // Initier la transaction M-Pesa
            $paymentResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', $mpesaData);

            if (!$paymentResponse->successful()) {
                throw new \Exception('Erreur lors de l\'initiation du paiement M-Pesa');
            }

            $responseData = $paymentResponse->json();

            // Créer la transaction en base avec statut pending
            $user = \App\Models\User::find($request->buyer_id);
            $wallet = $user->wallet ?? \App\Models\Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'currency' => $priorityCurrency,
                    'status' => 'active'
                ]
            );

            $transaction = \App\Models\Transaction::create([
                'transaction_id' => $transaction_id,
                'external_id' => $responseData['CheckoutRequestID'] ?? null,
                'user_id' => $request->buyer_id,
                'buyer_id' => $request->buyer_id,
                'wallet_id' => $wallet->id,
                'amount' => $totalAmount,
                'currency' => $priorityCurrency,
                'provider' => 'Vodacom M-Pesa',
                'phone' => $request->phone,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'type' => 'deposit',
                'payment_method' => 'mpesa',
                'metadata' => json_encode([
                    'mpesa_checkout_request_id' => $responseData['CheckoutRequestID'] ?? null,
                    'mpesa_merchant_request_id' => $responseData['MerchantRequestID'] ?? null,
                ])
            ]);

            return response()->json([
                'status' => 'pending',
                'message' => 'Paiement M-Pesa initié. Veuillez confirmer sur votre téléphone.',
                'transaction_id' => $transaction_id,
                'provider' => 'mpesa',
                'checkout_request_id' => $responseData['CheckoutRequestID'] ?? null
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur M-Pesa: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du traitement du paiement M-Pesa: ' . $e->getMessage()
            ], 500);
        }
    }

    // Paiement Africell Money
    public function payWithAfricell(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string',
        ]);
        if (!config('payments.providers.africell.enabled')) {
            return response()->json(['status' => 'error', 'message' => 'Africell Money désactivé.'], 403);
        }
        $apiKey = config('payments.providers.africell.api_key');
        $apiSecret = config('payments.providers.africell.api_secret');
        // TODO: Appel API Africell Money ici
        // TODO: Créer la transaction avec status 'pending'
        return response()->json(['status' => 'pending', 'message' => 'Paiement Africell Money en cours...', 'provider' => 'africell']);
    }

    // Callback générique pour tous les opérateurs
    public function handleCallback(Request $request)
    {
        // TODO: Identifier l'opérateur, vérifier la transaction, mettre à jour le statut
        // TODO: Si succès, appeler PaymentService::distributeFunds et enregistrer la distribution
        // TODO: Retourner un message JSON clair avec le détail de la distribution
        return response()->json(['status' => 'success', 'message' => 'Callback reçu', 'distribution' => null]);
    }

    // Simulation de paiement mobile (pour tests)
    public function simulatePayment(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:USD,CDF',
            'purpose' => 'required|string',
            'provider' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        
        // Récupérer le panier
        $cart = session('cart', []);
        
        // Déterminer la devise prioritaire et calculer le montant total
        $totalAmount = 0;
        $priorityCurrency = 'USD'; // Devise par défaut
        
        if (!empty($cart)) {
            // Compter les devises dans le panier
            $currencyCounts = [];
            foreach ($cart as $itemId => $cartItem) {
                $item = \App\Models\Item::find($itemId);
                if ($item) {
                    $currency = $item->currency ?? 'USD';
                    $currencyCounts[$currency] = ($currencyCounts[$currency] ?? 0) + 1;
                }
            }
            
            // Déterminer la devise prioritaire (la plus fréquente)
            if (!empty($currencyCounts)) {
                arsort($currencyCounts);
                $priorityCurrency = array_key_first($currencyCounts);
            }
            
            // Récupérer le taux de change depuis l'API
            $exchangeRate = \Illuminate\Support\Facades\Cache::remember('usd_cdf_rate', 3600, function () {
                try {
                    $controller = new ExchangeRateController();
                    $response = $controller->getRate();
                    $data = $response->getData(true);
                    return $data['rate'] ?? 2650.00; // Taux de secours
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur récupération taux: ' . $e->getMessage());
                    return 2650.00; // Taux de secours
                }
            });
            
            // Calculer le montant total dans la devise prioritaire
            foreach ($cart as $itemId => $cartItem) {
                $item = \App\Models\Item::find($itemId);
                if ($item) {
                    $itemTotal = $item->price * $cartItem['quantity'];
                    $itemCurrency = $item->currency ?? 'USD';
                    
                    // Convertir si nécessaire
                    if ($itemCurrency !== $priorityCurrency) {
                        if ($priorityCurrency === 'USD' && $itemCurrency === 'CDF') {
                            // CDF vers USD
                            $itemTotal = $itemTotal / $exchangeRate;
                        } elseif ($priorityCurrency === 'CDF' && $itemCurrency === 'USD') {
                            // USD vers CDF
                            $itemTotal = $itemTotal * $exchangeRate;
                        }
                    }
                    
                    $totalAmount += $itemTotal;
                }
            }
            
            $totalAmount = round($totalAmount, 2);
        } else {
            // Si pas de panier, utiliser les données de la requête
            $totalAmount = $request->amount;
            $priorityCurrency = $request->currency;
        }
        
        // Simuler un délai de traitement (3-5 secondes)
        sleep(rand(3, 5));
        
        // 80% de chance de succès
        $success = rand(1, 100) <= 80;
        
        // Générer un ID de transaction unique
        $transaction_id = 'TXN-' . strtoupper(\Illuminate\Support\Str::random(12));
        
        if ($success) {
            // Récupérer ou créer le wallet de l'utilisateur
            $user = \App\Models\User::find($request->buyer_id);
            $wallet = $user->wallet ?? \App\Models\Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'currency' => $priorityCurrency, // Devise prioritaire
                    'status' => 'active'
                ]
            );
            
            // Créer une transaction simulée
            $transaction = \App\Models\Transaction::create([
                'transaction_id' => $transaction_id,
                'user_id' => $request->buyer_id,
                'buyer_id' => $request->buyer_id, // Pour compatibilité avec l'ancienne structure
                'wallet_id' => $wallet->id,
                'amount' => $totalAmount, // Montant total dans la devise prioritaire
                'currency' => $priorityCurrency, // Devise prioritaire du panier
                'provider' => $request->provider ?? 'Mobile Money',
                'phone' => $request->phone ?? 'N/A',
                'purpose' => $request->purpose,
                'status' => 'completed',
                'type' => 'deposit',
                'payment_method' => 'orange_money', // Par défaut
            ]);
            
            // Créer automatiquement les commandes depuis le panier
            if (!empty($cart)) {
                // Récupérer l'adresse de livraison par défaut du client
                $defaultDeliveryAddress = \App\Models\DeliveryAddress::where('user_id', $request->buyer_id)
                    ->where('is_default', true)
                    ->first();
                
                foreach ($cart as $itemId => $cartItem) {
                    $item = \App\Models\Item::find($itemId);
                    if ($item) {
                        $orderAmount = $item->price * $cartItem['quantity'];
                        
                        // Créer ou récupérer le wallet "pending" du vendeur
                        $seller = \App\Models\User::find($item->user_id);
                        $sellerPendingWallet = \App\Models\Wallet::firstOrCreate(
                            [
                                'user_id' => $seller->id,
                                'type' => 'pending',
                                'currency' => $item->currency
                            ],
                            [
                                'balance' => 0,
                                'status' => 'active',
                                'is_active' => true
                            ]
                        );
                        
                        // Ajouter le montant au wallet pending du vendeur
                        $sellerPendingWallet->increment('balance', $orderAmount);
                        
                        // Préparer les données de commande avec l'adresse de livraison si disponible
                        $orderData = [
                            'buyer_id' => $request->buyer_id,
                            'seller_id' => $item->user_id,
                            'item_id' => $item->id,
                            'quantity' => $cartItem['quantity'],
                            'unit_price' => $item->price,
                            'total_amount' => $orderAmount,
                            'currency' => $item->currency, // Devise originale de l'article
                            'status' => 'confirmed', // Paiement effectué, prêt pour expédition
                            'paid_at' => now(),
                            'notes' => 'Paiement effectué via ' . ($request->provider ?? 'Mobile Money') . ' - Transaction: ' . $transaction_id . ' - Montant en attente dans le wallet pending du vendeur',
                        ];
                        
                        // Ajouter l'adresse de livraison si disponible
                        if ($defaultDeliveryAddress) {
                            $orderData['delivery_address_id'] = $defaultDeliveryAddress->id;
                            $orderData['shipping_address'] = $defaultDeliveryAddress->address;
                            $orderData['shipping_city'] = $defaultDeliveryAddress->city;
                            $orderData['shipping_phone'] = $defaultDeliveryAddress->phone;
                        } else {
                            // Valeurs par défaut si pas d'adresse de livraison
                            $orderData['shipping_address'] = 'À définir';
                            $orderData['shipping_city'] = 'À définir';
                            $orderData['shipping_phone'] = $request->phone ?? 'N/A';
                        }
                        
                        // Créer la commande
                        $order = \App\Models\Order::create($orderData);
                        
                        // Mettre à jour le stock
                        $item->quantity -= $cartItem['quantity'];
                        if ($item->quantity <= 0) {
                            $item->status = 'sold';
                        }
                        $item->save();
                        
                        // Log pour traçabilité
                        \Illuminate\Support\Facades\Log::info("Paiement ajouté au wallet pending", [
                            'seller_id' => $seller->id,
                            'order_id' => $order->id,
                            'amount' => $orderAmount,
                            'currency' => $item->currency,
                            'pending_wallet_balance' => $sellerPendingWallet->balance
                        ]);
                    }
                }
                
                // Vider le panier après la création des commandes
                session()->forget('cart');
            }
            
            return response()->json([
                'status' => 'success',
                'transaction_id' => $transaction_id,
                'message' => 'Paiement réussi',
                'amount' => $totalAmount,
                'currency' => $priorityCurrency,
                'distribution' => [
                    ['beneficiary_type' => 'seller', 'amount' => round($totalAmount * 0.7, 2)],
                    ['beneficiary_type' => 'carrier', 'amount' => round($totalAmount * 0.2, 2)],
                    ['beneficiary_type' => 'service', 'amount' => round($totalAmount * 0.1, 2)],
                ]
            ]);
        } else {
            $errors = [
                'Solde insuffisant',
                'Numéro de téléphone invalide',
                'Délai d\'attente dépassé',
                'Transaction refusée par l\'opérateur',
                'Erreur de réseau'
            ];
            
            return response()->json([
                'status' => 'error',
                'message' => $errors[array_rand($errors)]
            ], 400);
        }
    }
    
    public function paymentSuccess($transaction_id)
    {
        // Chercher par ID numérique ou par transaction_id
        $transaction = \App\Models\Transaction::where('id', $transaction_id)
            ->orWhere('transaction_id', $transaction_id)
            ->first();
        
        if (!$transaction) {
            return redirect()->route('payments.error')->with('error', 'Transaction introuvable');
        }

        // Récupérer les commandes liées à cette transaction
        $orders = \App\Models\Order::where('buyer_id', $transaction->user_id)
            ->where('status', 'confirmed')
            ->whereDate('paid_at', now()->toDateString())
            ->with(['seller', 'item'])
            ->get();

        // Vérifier si l'utilisateur a déjà noté ces vendeurs
        $unratedOrders = $orders->filter(function ($order) {
            return !\App\Models\Review::where('order_id', $order->id)
                ->where('reviewer_id', $order->buyer_id)
                ->exists();
        });
        
        return view('payments.success', compact('transaction', 'orders', 'unratedOrders'));
    }
    
    public function paymentError(Request $request)
    {
        $error = $request->query('error', 'Une erreur est survenue lors du paiement');
        $amount = $request->query('amount', 0);
        $provider = $request->query('provider', 'Mobile Money');
        $currency = $request->query('currency', 'USD');
        
        return view('payments.error', compact('error', 'amount', 'provider', 'currency'));
    }

    /**
     * Demande de remboursement pour marchandise non conforme
     */
    public function requestRefund(Request $request, Order $order)
    {
        try {
            Log::info('Refund request started', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'buyer_id' => $order->buyer_id,
                'auth_user_id' => Auth::id(),
                'confirmed_by_buyer_at' => $order->confirmed_by_buyer_at
            ]);
            
            $request->validate([
                'reason' => 'required|string|min:10|max:1000',
                'refund_type' => 'required|in:partial,full',
                'refund_amount' => 'nullable|numeric|min:0',
                'evidence_photos' => 'nullable|array|max:5',
                'evidence_photos.*' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'errors' => $e->validator->errors()
            ], 422);
        }

        // Charger les relations nécessaires
        $order->load(['buyer', 'seller', 'item']);
        
        // Vérifier que l'utilisateur est bien l'acheteur
        if ($order->buyer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error' => 'Vous n\'êtes pas autorisé à demander un remboursement pour cette commande'
            ], 403);
        }

        // Vérifier que la commande est éligible au remboursement
        if (!$this->isRefundEligible($order)) {
            return response()->json([
                'success' => false,
                'error' => 'Cette commande n\'est plus éligible au remboursement'
            ], 400);
        }

        // Traitement des photos de preuve
        $evidencePhotos = [];
        if ($request->hasFile('evidence_photos')) {
            foreach ($request->file('evidence_photos') as $photo) {
                $path = $photo->store('refund_evidence', 'public');
                StorageSyncService::syncFile($path);
                $evidencePhotos[] = $path;
            }
        }

        // Calculer le montant du remboursement
        $refundAmount = $request->refund_type === 'full' 
            ? $order->total_amount 
            : min($request->refund_amount ?? $order->total_amount, $order->total_amount);

        // Créer la demande de remboursement
        $refund = \App\Models\Refund::create([
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'transaction_id' => $this->getTransactionIdForOrder($order),
            'refund_amount' => $refundAmount,
            'original_amount' => $order->total_amount,
            'currency' => $order->currency,
            'reason' => $request->reason,
            'refund_type' => $request->refund_type,
            'status' => 'pending',
            'evidence_photos' => json_encode($evidencePhotos),
            'requested_at' => now()
        ]);

        // Notifier le vendeur de la demande de remboursement
        $this->notifySellerOfRefundRequest($order, $refund);

        // Log de la demande
        \Illuminate\Support\Facades\Log::info('Demande de remboursement créée', [
            'refund_id' => $refund->id,
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'amount' => $refundAmount,
            'reason' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Votre demande de remboursement a été soumise avec succès',
            'refund_id' => $refund->id,
            'refund' => $refund
        ]);
    }

    /**
     * Traiter une demande de remboursement (vendeur ou admin)
     */
    public function processRefund(Request $request, Refund $refund)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,negotiate',
            'admin_notes' => 'nullable|string|max:1000',
            'counter_offer_amount' => 'nullable|numeric|min:0'
        ]);

        // Charger les relations nécessaires
        $refund->load(['order.buyer', 'order.seller']);
        
        // Vérifier les autorisations
        if (!$this->canProcessRefund($refund, Auth::user())) {
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'êtes pas autorisé à traiter cette demande'
                ], 403);
            } else {
                return redirect()->route('admin.refunds.index')
                    ->with('error', 'Vous n\'êtes pas autorisé à traiter cette demande');
            }
        }

        switch ($request->action) {
            case 'approve':
                return $this->approveRefund($refund, $request->admin_notes);
                
            case 'reject':
                return $this->rejectRefund($refund, $request->admin_notes);
                
            case 'negotiate':
                return $this->negotiateRefund($refund, $request->counter_offer_amount, $request->admin_notes);
        }
    }

    /**
     * Approuver et exécuter le remboursement
     */
    private function approveRefund($refund, $adminNotes = null)
    {
        try {
            // Récupérer la transaction originale
            $originalTransaction = \App\Models\Transaction::where('transaction_id', $refund->transaction_id)->first();
            
            if (!$originalTransaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction originale introuvable'
                ], 404);
            }

            // Créer la transaction de remboursement
            $refundTransactionId = 'REFUND-' . strtoupper(\Illuminate\Support\Str::random(12));
            
            $refundTransaction = \App\Models\Transaction::create([
                'transaction_id' => $refundTransactionId,
                'user_id' => $refund->buyer_id,
                'buyer_id' => $refund->buyer_id,
                'wallet_id' => $originalTransaction->wallet_id,
                'amount' => $refund->refund_amount,
                'currency' => $refund->currency,
                'provider' => 'Remboursement automatique',
                'phone' => $originalTransaction->phone ?? 'N/A',
                'purpose' => 'Remboursement commande #' . $refund->order->order_number,
                'status' => 'completed',
                'type' => 'refund',
                'payment_method' => 'wallet',
                'description' => 'Remboursement approuvé pour la commande #' . $refund->order->order_number,
                'metadata' => json_encode([
                    'original_transaction_id' => $originalTransaction->transaction_id,
                    'refund_id' => $refund->id,
                    'refund_reason' => $refund->reason
                ])
            ]);

            // Créditer le wallet de l'acheteur
            $buyerWallet = \App\Models\Wallet::where('user_id', $refund->buyer_id)->first();
            if ($buyerWallet) {
                $buyerWallet->increment('balance', $refund->refund_amount);
            }

            // Débiter le wallet pending du vendeur
            $sellerPendingWallet = \App\Models\Wallet::where([
                'user_id' => $refund->seller_id,
                'type' => 'pending',
                'currency' => $refund->currency
            ])->first();
            
            if ($sellerPendingWallet && $sellerPendingWallet->balance >= $refund->refund_amount) {
                $sellerPendingWallet->decrement('balance', $refund->refund_amount);
            }

            // Mettre à jour le statut de la demande de remboursement
            $refund->update([
                'status' => 'approved',
                'approved_at' => now(),
                'refund_transaction_id' => $refundTransactionId,
                'admin_notes' => $adminNotes,
                'processed_by' => Auth::id()
            ]);

            // Mettre à jour le statut de la commande
            $refund->order->update([
                'status' => 'refunded',
                'notes' => ($refund->order->notes ?? '') . ' | Remboursement approuvé: ' . $refund->refund_amount . ' ' . $refund->currency
            ]);

            // Notifier l'acheteur et le vendeur
            $this->notifyRefundApproval($refund, $refundTransaction);

            // Log du remboursement
            \Illuminate\Support\Facades\Log::info('Remboursement approuvé et exécuté', [
                'refund_id' => $refund->id,
                'transaction_id' => $refundTransactionId,
                'amount' => $refund->refund_amount,
                'currency' => $refund->currency
            ]);

            // Déterminer le type de réponse selon la provenance de la requête
            if (request()->expectsJson()) {
                // Requête AJAX - retourner JSON
                return response()->json([
                    'status' => 'success',
                    'message' => 'Remboursement approuvé et exécuté avec succès',
                    'refund_transaction_id' => $refundTransactionId,
                    'refund' => $refund->fresh()
                ]);
            } else {
                // Requête normale depuis l'interface admin - rediriger avec message
                return redirect()->route('admin.refunds.index')
                    ->with('success', 'Remboursement approuvé et exécuté avec succès ! Transaction ID: ' . $refundTransactionId);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors du remboursement: ' . $e->getMessage());
            
            // Déterminer le type de réponse selon la provenance de la requête
            if (request()->expectsJson()) {
                // Requête AJAX - retourner JSON
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erreur lors du traitement du remboursement: ' . $e->getMessage()
                ], 500);
            } else {
                // Requête normale depuis l'interface admin - rediriger avec erreur
                return redirect()->route('admin.refunds.index')
                    ->with('error', 'Erreur lors du traitement du remboursement: ' . $e->getMessage());
            }
        }
    }

    /**
     * Rejeter une demande de remboursement
     */
    private function rejectRefund($refund, $adminNotes = null)
    {
        $refund->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => $adminNotes,
            'processed_by' => Auth::id()
        ]);

        // Notifier l'acheteur du rejet
        $this->notifyRefundRejection($refund);

        // Déterminer le type de réponse selon la provenance de la requête
        if (request()->expectsJson()) {
            // Requête AJAX - retourner JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Demande de remboursement rejetée',
                'refund' => $refund->fresh()
            ]);
        } else {
            // Requête normale depuis l'interface admin - rediriger avec message
            return redirect()->route('admin.refunds.index')
                ->with('success', 'Demande de remboursement rejetée avec succès');
        }
    }

    /**
     * Négocier le montant du remboursement
     */
    private function negotiateRefund($refund, $counterOfferAmount, $adminNotes = null)
    {
        $refund->update([
            'status' => 'negotiation',
            'counter_offer_amount' => $counterOfferAmount,
            'admin_notes' => $adminNotes,
            'processed_by' => Auth::id()
        ]);

        // Notifier l'acheteur de la contre-proposition
        $this->notifyRefundNegotiation($refund);

        // Déterminer le type de réponse selon la provenance de la requête
        if (request()->expectsJson()) {
            // Requête AJAX - retourner JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Contre-proposition envoyée à l\'acheteur',
                'refund' => $refund->fresh()
            ]);
        } else {
            // Requête normale depuis l'interface admin - rediriger avec message
            return redirect()->route('admin.refunds.index')
                ->with('success', 'Contre-proposition envoyée à l\'acheteur avec succès');
        }
    }

    /**
     * Vérifier si une commande est éligible au remboursement
     */
    private function isRefundEligible($order)
    {
        // La commande doit être confirmée par l'acheteur (réception confirmée)
        if (!$order->confirmed_by_buyer_at) {
            Log::info('Refund not eligible: no buyer confirmation', ['order' => $order->order_number]);
            return false;
        }

        // Vérifier qu'il n'y a pas déjà une demande de remboursement
        if ($order->refunds()->exists()) {
            Log::info('Refund not eligible: refund already exists', ['order' => $order->order_number]);
            return false;
        }

        // Délai de 30 jours après confirmation de réception (étendu pour test)
        $daysSinceConfirmation = $order->confirmed_by_buyer_at->diffInDays(now());
        if ($daysSinceConfirmation > 30) {
            Log::info('Refund not eligible: too old', [
                'order' => $order->order_number,
                'days_since_confirmation' => $daysSinceConfirmation
            ]);
            return false;
        }

        Log::info('Refund eligible', [
            'order' => $order->order_number,
            'confirmed_at' => $order->confirmed_by_buyer_at,
            'days_since' => $daysSinceConfirmation
        ]);

        return true;
    }

    /**
     * Vérifier si un utilisateur peut traiter une demande de remboursement
     */
    private function canProcessRefund($refund, $user)
    {
        // Admin peut traiter toutes les demandes
        if ($this->isAdmin($user)) {
            return true;
        }

        // Vendeur peut traiter ses propres demandes
        return $refund->seller_id === $user->id;
    }

    /**
     * Vérifier si un utilisateur est admin
     */
    private function isAdmin($user)
    {
        return \Illuminate\Support\Facades\DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
    }

    /**
     * Récupérer l'ID de transaction pour une commande
     */
    private function getTransactionIdForOrder($order)
    {
        $transaction = \App\Models\Transaction::where('user_id', $order->buyer_id)
            ->whereDate('created_at', $order->paid_at->toDateString())
            ->where('status', 'completed')
            ->first();
            
        return $transaction ? $transaction->transaction_id : null;
    }

    /**
     * Notifications (à implémenter selon vos besoins)
     */
    private function notifySellerOfRefundRequest($order, $refund)
    {
        // TODO: Envoyer notification au vendeur
    }

    private function notifyRefundApproval($refund, $transaction)
    {
        try {
            $this->notificationService->createRefundApprovedNotification($refund);
            Log::info('Notifications de remboursement approuvé envoyées', [
                'refund_id' => $refund->id,
                'buyer_id' => $refund->buyer_id,
                'seller_id' => $refund->seller_id
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications de remboursement approuvé: ' . $e->getMessage());
        }
    }

    private function notifyRefundRejection($refund)
    {
        try {
            $this->notificationService->createRefundRejectedNotification($refund);
            Log::info('Notifications de remboursement rejeté envoyées', [
                'refund_id' => $refund->id,
                'buyer_id' => $refund->buyer_id,
                'seller_id' => $refund->seller_id
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications de remboursement rejeté: ' . $e->getMessage());
        }
    }

    private function notifyRefundNegotiation($refund)
    {
        try {
            $this->notificationService->createRefundNegotiationNotification($refund);
            Log::info('Notifications de négociation de remboursement envoyées', [
                'refund_id' => $refund->id,
                'buyer_id' => $refund->buyer_id,
                'seller_id' => $refund->seller_id,
                'counter_offer' => $refund->counter_offer_amount
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications de négociation: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // CINETPAY INTEGRATION
    // ========================================================================

    /**
     * Initier un paiement depuis le checkout (panier multi-articles)
     */
    public function initiateCheckoutPayment(Request $request)
    {
        $request->validate([
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'cart_items' => 'required|json',
            'total_amount' => 'required|numeric|min:5',
            'currency' => 'nullable|string|max:3',
        ]);

        $cartItems = json_decode($request->cart_items, true);
        $totalAmount = $request->total_amount;
        $currency = $request->currency ?? 'XOF';
        
        // Générer l'ID de transaction unique
        $transactionId = 'CHECKOUT-' . date('YmdHis') . '-' . auth()->id();

        // Créer l'enregistrement de paiement
        $payment = \App\Models\Payment::create([
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'buyer_id' => auth()->id(),
            'amount' => $totalAmount,
            'currency' => $currency,
            'designation' => "Paiement panier - " . count($cartItems) . " article(s)",
            'status' => 'pending',
            'method' => 'cinetpay',
            'ip_address' => $request->ip(),
            'metadata' => [
                'cart_items' => $cartItems,
                'delivery_address_id' => $request->delivery_address_id,
            ],
        ]);

        // Initialiser CinetPay
        $cinetPay = new \App\Services\CinetPay(
            config('services.cinetpay.site_id'),
            config('services.cinetpay.api_key'),
            config('services.cinetpay.platform'),
            config('services.cinetpay.version')
        );

        // Configurer le paiement
        $cinetPay->setTransId($transactionId)
            ->setAmount($totalAmount)
            ->setDesignation($payment->designation)
            ->setCurrency($currency)
            ->setCustom(json_encode([
                'user_id' => auth()->id(),
                'payment_id' => $payment->id,
                'type' => 'checkout',
                'delivery_address_id' => $request->delivery_address_id,
            ]))
            ->setNotifyUrl(route('payments.cinetpay.notify'))
            ->setReturnUrl(route('payments.cinetpay.return'))
            ->setCancelUrl(route('cart.checkout'))
            ->setDebug(config('app.debug'));

        // Afficher le formulaire de paiement
        return view('payments.checkout', [
            'payment' => $payment,
            'cinetPay' => $cinetPay,
            'isCheckout' => true,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'currency' => $currency,
        ]);
    }

    /**
     * Initier un paiement pour une commande via CinetPay
     */
    public function initiateOrderPayment(Request $request, Order $order)
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->buyer_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la commande n'est pas déjà payée
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Cette commande est déjà payée');
        }

        // Générer l'ID de transaction unique
        $transactionId = 'VIN-' . date('YmdHis') . '-' . $order->id;

        // Créer l'enregistrement de paiement
        $payment = \App\Models\Payment::create([
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'currency' => 'XOF',
            'designation' => "Paiement commande #{$order->order_number}",
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        // Mettre à jour la commande
        $order->update([
            'payment_transaction_id' => $transactionId,
            'payment_status' => 'pending',
        ]);

        // Initialiser CinetPay
        $cinetPay = new \App\Services\CinetPay(
            config('services.cinetpay.site_id'),
            config('services.cinetpay.api_key'),
            config('services.cinetpay.platform'),
            config('services.cinetpay.version')
        );

        // Configurer le paiement
        $cinetPay->setTransId($transactionId)
            ->setAmount($order->total_amount)
            ->setDesignation($payment->designation)
            ->setCustom(json_encode([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ]))
            ->setNotifyUrl(route('payments.cinetpay.notify'))
            ->setReturnUrl(route('payments.cinetpay.return'))
            ->setCancelUrl(route('orders.show', $order))
            ->setDebug(config('app.debug'));

        // Afficher le formulaire de paiement
        return view('payments.checkout', [
            'order' => $order,
            'payment' => $payment,
            'cinetPay' => $cinetPay,
        ]);
    }

    /**
     * Webhook de notification IPN (appelé par CinetPay)
     * C'est le SEUL endroit où la base de données doit être mise à jour
     */
    public function handleNotification(Request $request)
    {
        Log::info('CinetPay IPN Notification', $request->all());

        try {
            // Récupérer l'ID de transaction
            $transactionId = $request->input('cpm_trans_id');

            if (!$transactionId) {
                Log::error('CinetPay IPN: Transaction ID manquant');
                return response('Transaction ID manquant', 400);
            }

            // Vérifier le statut du paiement via l'API CinetPay
            $cinetPay = new \App\Services\CinetPay(
                config('services.cinetpay.site_id'),
                config('services.cinetpay.api_key'),
                config('services.cinetpay.platform'),
                config('services.cinetpay.version')
            );

            $cinetPay->setTransId($transactionId)->getPayStatus();

            // Récupérer le paiement
            $payment = \App\Models\Payment::where('transaction_id', $transactionId)->firstOrFail();

            // Prévention de fraude : vérifier le montant
            $apiAmount = floatval($cinetPay->_cpm_amount);
            $dbAmount = floatval($payment->amount);

            if (abs($apiAmount - $dbAmount) > 0.01) {
                Log::error("CinetPay Fraud Alert: Montant incohérent", [
                    'transaction_id' => $transactionId,
                    'api_amount' => $apiAmount,
                    'db_amount' => $dbAmount,
                ]);
                return response('Montant incohérent', 400);
            }

            // Éviter le traitement en double
            if ($payment->status === 'completed') {
                Log::info("CinetPay: Paiement déjà traité - {$transactionId}");
                return response('OK', 200);
            }

            \DB::beginTransaction();

            try {
                // Paiement réussi
                if ($cinetPay->_cpm_result == '00') {
                    $payment->markAsCompleted([
                        'cpm_result' => $cinetPay->_cpm_result,
                        'cpm_trans_status' => $cinetPay->_cpm_trans_status,
                        'payment_method' => $cinetPay->_payment_method ?? null,
                        'cpm_amount' => $cinetPay->_cpm_amount,
                    ]);

                    $payment->update([
                        'cpm_trans_id' => $transactionId,
                        'metadata' => [
                            'operator_id' => $cinetPay->_operator_id ?? null,
                            'phone_number' => $cinetPay->_cel_phone_num ?? null,
                            'payment_date' => $cinetPay->_payment_date ?? null,
                        ],
                    ]);

                    // Mettre à jour la commande
                    if ($payment->order_id) {
                        $payment->order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                        ]);
                    }

                    // Si c'est un paiement de checkout (panier), créer les commandes
                    if (!$payment->order_id && isset($payment->metadata['cart_items'])) {
                        $this->createOrdersFromCheckout($payment);
                    }

                    Log::info("CinetPay: Paiement confirmé - {$transactionId}");
                } else {
                    // Paiement échoué
                    $payment->markAsFailed($cinetPay->_cpm_result);

                    if ($payment->order_id) {
                        $payment->order->update([
                            'payment_status' => 'failed',
                        ]);
                    }

                    Log::warning("CinetPay: Paiement échoué - {$transactionId}", [
                        'result' => $cinetPay->_cpm_result,
                        'status' => $cinetPay->_cpm_trans_status,
                    ]);
                }

                \DB::commit();
                return response('OK', 200);
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('CinetPay IPN Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response('Internal Error', 500);
        }
    }

    /**
     * Page de retour après paiement (redirection utilisateur)
     * NE DOIT PAS mettre à jour la base de données
     */
    public function handleReturn(Request $request)
    {
        $transactionId = $request->input('transaction_id') ?? $request->input('cpm_trans_id');

        if (!$transactionId) {
            return redirect()->route('orders.index')
                ->with('error', 'Transaction introuvable');
        }

        $payment = \App\Models\Payment::where('transaction_id', $transactionId)
            ->orWhere('cpm_trans_id', $transactionId)
            ->first();

        if (!$payment) {
            return redirect()->route('orders.index')
                ->with('error', 'Paiement introuvable');
        }

        // Rediriger en fonction du statut
        if ($payment->isCompleted()) {
            return redirect()->route('orders.show', $payment->order_id)
                ->with('success', 'Paiement effectué avec succès !');
        } else {
            return redirect()->route('orders.show', $payment->order_id)
                ->with('warning', 'Paiement en cours de traitement. Vous recevrez une notification.');
        }
    }

    /**
     * Initier un rechargement de wallet via CinetPay
     */
    public function initiateWalletTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5',
        ]);

        $amount = $request->input('amount');
        $transactionId = 'WALLET-' . date('YmdHis') . '-' . auth()->id();

        // Créer le paiement
        $payment = \App\Models\Payment::create([
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'amount' => $amount,
            'currency' => 'XOF',
            'designation' => "Rechargement wallet - " . auth()->user()->name,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        // Initialiser CinetPay
        $cinetPay = new \App\Services\CinetPay(
            config('services.cinetpay.site_id'),
            config('services.cinetpay.api_key'),
            config('services.cinetpay.platform'),
            config('services.cinetpay.version')
        );

        $cinetPay->setTransId($transactionId)
            ->setAmount($amount)
            ->setDesignation($payment->designation)
            ->setCustom(json_encode([
                'user_id' => auth()->id(),
                'payment_id' => $payment->id,
                'type' => 'wallet_topup',
            ]))
            ->setNotifyUrl(route('payments.cinetpay.notify'))
            ->setReturnUrl(route('wallet.index'))
            ->setCancelUrl(route('wallet.index'))
            ->setDebug(config('app.debug'));

        return view('payments.checkout', [
            'payment' => $payment,
            'cinetPay' => $cinetPay,
            'isWalletTopup' => true,
        ]);
    }

    /**
     * Créer les commandes à partir du panier après paiement réussi
     */
    private function createOrdersFromCheckout(\App\Models\Payment $payment)
    {
        $cartItems = $payment->metadata['cart_items'] ?? [];
        $deliveryAddressId = $payment->metadata['delivery_address_id'] ?? null;

        foreach ($cartItems as $item) {
            try {
                // Récupérer l'article
                $itemModel = \App\Models\Item::find($item['id']);
                if (!$itemModel) {
                    Log::warning("Item not found: {$item['id']}");
                    continue;
                }

                // Créer la commande
                $order = \App\Models\Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'buyer_id' => $payment->user_id,
                    'seller_id' => $itemModel->user_id,
                    'item_id' => $itemModel->id,
                    'quantity' => $item['quantity'],
                    'item_price' => $item['price'],
                    'total_amount' => $item['price'] * $item['quantity'],
                    'status' => 'processing',
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $payment->transaction_id,
                    'delivery_address_id' => $deliveryAddressId,
                ]);

                // Lier le paiement à la première commande créée
                if (!$payment->order_id) {
                    $payment->update(['order_id' => $order->id]);
                }

                // TODO: Logique métier
                // - Réduire le stock de l'article
                // - Créer une transaction wallet pour le vendeur
                // - Envoyer notification au vendeur et acheteur

                Log::info("Order created from checkout", [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                ]);
            } catch (\Exception $e) {
                Log::error("Error creating order from checkout: " . $e->getMessage(), [
                    'item' => $item,
                    'payment_id' => $payment->id,
                ]);
            }
        }
    }

    // ========================================================================
    // AFRIBAPAY INTEGRATION
    // ========================================================================

    /**
     * Afficher le formulaire de paiement AfribaPay
     */
    public function showAfribaPaymentForm(Request $request)
    {
        $request->validate([
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'cart_items' => 'required|string',
            'total_amount' => 'required|numeric|min:1',
            'currency' => 'required|in:CDF,USD,XOF,XAF,GNF',
        ]);

        $cartItems = json_decode($request->cart_items, true);
        
        return view('payments.afribapay-form', [
            'cartItems' => $cartItems,
            'totalAmount' => $request->total_amount,
            'currency' => $request->currency,
            'deliveryAddressId' => $request->delivery_address_id,
        ]);
    }

    /**
     * Initier un paiement AfribaPay depuis le checkout
     */
    public function initiateAfribaPayment(Request $request)
    {
        $request->validate([
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'cart_items' => 'required|string',
            'total_amount' => 'required|numeric|min:1',
            'currency' => 'required|in:CDF,USD,XOF,XAF,GNF',
            'phone_number' => 'required|string',
            'operator_code' => 'required|string',
            'country_code' => 'nullable|string|size:2',
        ]);

        $cartItems = json_decode($request->cart_items, true);
        $totalAmount = $request->total_amount;
        $currency = $request->currency;
        $countryCode = $request->country_code ?? 'CD'; // Par défaut RDC

        // Générer la référence de transaction
        $reference = \App\Services\AfribaPay::generateReference('AFRIBA-CHECKOUT');

        // Créer l'enregistrement de paiement
        $payment = \App\Models\Payment::create([
            'transaction_id' => $reference,
            'user_id' => auth()->id(),
            'buyer_id' => auth()->id(),
            'amount' => $totalAmount,
            'currency' => $currency,
            'designation' => "Paiement panier AfribaPay - " . count($cartItems) . " article(s)",
            'status' => 'pending',
            'method' => 'afribapay',
            'ip_address' => $request->ip(),
            'metadata' => [
                'cart_items' => $cartItems,
                'delivery_address_id' => $request->delivery_address_id,
                'phone_number' => $request->phone_number,
                'operator_code' => $request->operator_code,
                'country_code' => $countryCode,
            ],
        ]);

        try {
            // Initialiser AfribaPay
            $afribaPay = new \App\Services\AfribaPay();

            // Formater le numéro de téléphone
            $phoneNumber = $afribaPay->formatPhoneNumber(
                $request->phone_number,
                $countryCode
            );

            // Initier le paiement
            $paymentData = $afribaPay->initiatePayment([
                'reference' => $reference,
                'amount' => $totalAmount,
                'currency' => $currency,
                'country_code' => $countryCode,
                'phone_number' => $phoneNumber,
                'operator_code' => $request->operator_code,
                'description' => $payment->designation,
                'callback_url' => route('payments.afribapay.notify'),
                'return_url' => route('payments.afribapay.return'),
                'customer_name' => auth()->user()->name,
                'customer_email' => auth()->user()->email,
            ]);

            // Mettre à jour le paiement avec les infos AfribaPay
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'afribapay_transaction_id' => $paymentData['data']['transaction_id'] ?? null,
                    'afribapay_response' => $paymentData,
                ]),
            ]);

            // Vérifier si OTP est requis
            if ($afribaPay->requiresOTP($countryCode, $currency, $request->operator_code)) {
                return view('payments.afribapay-otp', [
                    'payment' => $payment,
                    'afribaPay' => $afribaPay,
                    'paymentData' => $paymentData,
                    'ussdCode' => $afribaPay->getUSSDCode($request->country_code, $currency, $request->operator_code),
                ]);
            }

            // Sinon, rediriger vers la page de statut
            return redirect()->route('payments.afribapay.status', ['payment' => $payment->id]);

        } catch (\Exception $e) {
            Log::error('AfribaPay payment initiation failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
            ]);

            $payment->markAsFailed($e->getMessage());

            return back()->with('error', 'Échec de l\'initiation du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier l'OTP AfribaPay
     */
    public function verifyAfribaOTP(Request $request, \App\Models\Payment $payment)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        try {
            $afribaPay = new \App\Services\AfribaPay();

            $afribaTransactionId = $payment->metadata['afribapay_transaction_id'] ?? null;
            
            if (!$afribaTransactionId) {
                throw new \Exception("Transaction AfribaPay introuvable");
            }

            // Vérifier l'OTP
            $otpResult = $afribaPay->verifyOTP($afribaTransactionId, $request->otp);

            // Mettre à jour le paiement
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'otp_verified' => true,
                    'otp_result' => $otpResult,
                ]),
            ]);

            // Vérifier le statut du paiement
            $status = $afribaPay->checkStatus($afribaTransactionId);

            if (($status['data']['status'] ?? '') === 'SUCCESS') {
                $this->processSuccessfulAfribaPayment($payment, $status);
                return redirect()->route('payments.afribapay.return', ['payment' => $payment->id])
                    ->with('success', 'Paiement effectué avec succès !');
            }

            return redirect()->route('payments.afribapay.status', ['payment' => $payment->id]);

        } catch (\Exception $e) {
            Log::error('AfribaPay OTP verification failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
            ]);

            return back()->with('error', 'Vérification OTP échouée: ' . $e->getMessage());
        }
    }

    /**
     * Webhook de notification AfribaPay
     */
    public function handleAfribaNotification(Request $request)
    {
        Log::info('AfribaPay notification received', $request->all());

        try {
            // Récupérer le paiement par référence
            $reference = $request->input('reference');
            $payment = \App\Models\Payment::where('transaction_id', $reference)->first();

            if (!$payment) {
                Log::error('Payment not found for AfribaPay notification', ['reference' => $reference]);
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // Vérifier le statut
            $status = $request->input('status');

            if ($status === 'SUCCESS') {
                $this->processSuccessfulAfribaPayment($payment, $request->all());
            } elseif ($status === 'FAILED') {
                $payment->markAsFailed($request->input('message', 'Payment failed'));
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Error processing AfribaPay notification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Page de retour AfribaPay
     */
    public function handleAfribaReturn(Request $request, ?\App\Models\Payment $payment = null)
    {
        if (!$payment) {
            $paymentId = $request->query('payment');
            $payment = \App\Models\Payment::find($paymentId);
        }

        if (!$payment) {
            return redirect()->route('cart.index')->with('error', 'Paiement introuvable');
        }

        // Vérifier le statut final
        try {
            $afribaPay = new \App\Services\AfribaPay();
            $afribaTransactionId = $payment->metadata['afribapay_transaction_id'] ?? null;

            if ($afribaTransactionId) {
                $status = $afribaPay->checkStatus($afribaTransactionId);
                
                if (($status['data']['status'] ?? '') === 'SUCCESS' && $payment->status !== 'completed') {
                    $this->processSuccessfulAfribaPayment($payment, $status);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error checking AfribaPay status on return: ' . $e->getMessage());
        }

        return view('payments.afribapay-return', [
            'payment' => $payment,
        ]);
    }

    /**
     * Page de statut AfribaPay (polling)
     */
    public function showAfribaStatus(\App\Models\Payment $payment)
    {
        return view('payments.afribapay-status', [
            'payment' => $payment,
        ]);
    }

    /**
     * API pour vérifier le statut AfribaPay (AJAX)
     */
    public function checkAfribaStatus(\App\Models\Payment $payment)
    {
        try {
            $afribaPay = new \App\Services\AfribaPay();
            $afribaTransactionId = $payment->metadata['afribapay_transaction_id'] ?? null;

            if (!$afribaTransactionId) {
                return response()->json([
                    'status' => $payment->status,
                    'message' => 'Transaction ID not found',
                ]);
            }

            $status = $afribaPay->checkStatus($afribaTransactionId);

            if (($status['data']['status'] ?? '') === 'SUCCESS' && $payment->status !== 'completed') {
                $this->processSuccessfulAfribaPayment($payment, $status);
            }

            return response()->json([
                'status' => $payment->fresh()->status,
                'data' => $status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Traiter un paiement AfribaPay réussi
     */
    protected function processSuccessfulAfribaPayment(\App\Models\Payment $payment, array $data)
    {
        if ($payment->isCompleted()) {
            return; // Déjà traité
        }

        // Marquer comme complété
        $payment->markAsCompleted([
            'afribapay_result' => $data,
        ]);

        // Créer les commandes si c'est un checkout
        $cartItems = $payment->metadata['cart_items'] ?? null;
        if ($cartItems) {
            $this->createOrdersFromCheckout($payment, $cartItems);
        }

        Log::info('AfribaPay payment processed successfully', [
            'payment_id' => $payment->id,
        ]);
    }

    // ==================== API Methods ====================

    /**
     * Get payment history via API
     */
    public function apiIndex(Request $request)
    {
        try {
            $payments = \App\Models\Transaction::where('user_id', $request->user()->id)
                ->latest()
                ->paginate($request->per_page ?? 15);

            return $this->paginatedResponse($payments, 'Historique de paiements récupéré');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }

    /**
     * Get payment details via API
     */
    public function apiShow(Request $request, $transactionId)
    {
        try {
            $payment = \App\Models\Transaction::where('transaction_id', $transactionId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            return $this->successResponse($payment, 'Détails du paiement récupérés');
        } catch (\Exception $e) {
            return $this->errorResponse('Paiement introuvable', 404);
        }
    }

    /**
     * Initiate payment via API
     */
    public function apiInitiate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'provider' => 'required|string|in:orange_money,mpesa,airtel_money,africell,illicocash',
            'amount' => 'required|numeric|min:1',
            'phone' => 'required|string|min:9|max:15',
            'purpose' => 'required|string',
            'currency' => 'nullable|in:USD,CDF'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $paymentData = [
                'amount' => $request->amount,
                'phone' => $request->phone,
                'purpose' => $request->purpose,
                'buyer_id' => $request->user()->id
            ];

            $methodName = 'payWith' . str_replace('_', '', ucwords($request->provider, '_'));
            
            if (!method_exists($this->paymentService, $methodName)) {
                return $this->errorResponse('Méthode de paiement non supportée', 400);
            }

            $result = $this->paymentService->{$methodName}($paymentData);

            if ($result['status'] === 'pending') {
                return $this->successResponse($result, 'Paiement initié avec succès');
            }

            return $this->errorResponse($result['message'] ?? 'Erreur lors du paiement', 400);
        } catch (\Exception $e) {
            Log::error('API Payment initiation error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erreur lors de l\'initiation du paiement', 500);
        }
    }

    /**
     * Request refund via API
     */
    public function apiRequestRefund(Request $request, $orderId)
    {
        $validator = \Validator::make($request->all(), [
            'reason' => 'required|string|min:10|max:1000',
            'refund_type' => 'required|in:partial,full',
            'refund_amount' => 'nullable|numeric|min:0',
            'evidence_photos' => 'nullable|array|max:5',
            'evidence_photos.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $order = Order::findOrFail($orderId);

            if ($order->buyer_id !== $request->user()->id) {
                return $this->errorResponse('Non autorisé', 403);
            }

            if (!$this->isRefundEligible($order)) {
                return $this->errorResponse('Commande non éligible au remboursement', 400);
            }

            $evidencePhotos = [];
            if ($request->hasFile('evidence_photos')) {
                foreach ($request->file('evidence_photos') as $photo) {
                    $path = $photo->store('refund_evidence', 'public');
                    StorageSyncService::syncFile($path);
                    $evidencePhotos[] = $path;
                }
            }

            $refundAmount = $request->refund_type === 'full'
                ? $order->total_amount
                : min($request->refund_amount ?? $order->total_amount, $order->total_amount);

            $refund = Refund::create([
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'transaction_id' => $this->getTransactionIdForOrder($order),
                'refund_amount' => $refundAmount,
                'original_amount' => $order->total_amount,
                'currency' => $order->currency,
                'reason' => $request->reason,
                'refund_type' => $request->refund_type,
                'status' => 'pending',
                'evidence_photos' => json_encode($evidencePhotos),
                'requested_at' => now()
            ]);

            return $this->successResponse(
                $refund,
                'Demande de remboursement créée avec succès',
                201
            );
        } catch (\Exception $e) {
            Log::error('API Refund request error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erreur lors de la demande de remboursement', 500);
        }
    }

    /**
     * Get refund status via API
     */
    public function apiRefundStatus(Request $request, $refundId)
    {
        try {
            $refund = Refund::where('id', $refundId)
                ->where('buyer_id', $request->user()->id)
                ->with(['order'])
                ->firstOrFail();

            return $this->successResponse($refund, 'Statut du remboursement');
        } catch (\Exception $e) {
            return $this->errorResponse('Remboursement introuvable', 404);
        }
    }

    /**
     * Get payment statistics via API
     */
    public function apiStats(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $stats = [
                'total_payments' => \App\Models\Transaction::where('user_id', $userId)->count(),
                'successful_payments' => \App\Models\Transaction::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->count(),
                'total_amount' => \App\Models\Transaction::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'pending_refunds' => Refund::where('buyer_id', $userId)
                    ->where('status', 'pending')
                    ->count(),
            ];

            return $this->successResponse($stats, 'Statistiques de paiement');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }

    // ==========================================
    // MAISHAPAY INTEGRATION
    // ==========================================

    /**
     * Checkout via MaishaPay - Affiche le formulaire de paiement MaishaPay
     */
    public function maishapayCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'cart_items' => 'required|string',
            'total_amount' => 'required|numeric|min:1',
            'currency' => 'sometimes|string|in:CDF,USD',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cart = json_decode($request->cart_items, true);
        $total = $request->total_amount;
        $currency = $request->input('currency', 'CDF');
        $deliveryAddressId = $request->delivery_address_id;

        // Récupérer l'adresse de livraison
        $deliveryAddress = \App\Models\DeliveryAddress::findOrFail($deliveryAddressId);

        // Stocker les données en session pour le formulaire MaishaPay
        session([
            'maishapay_checkout' => [
                'cart' => $cart,
                'total' => $total,
                'currency' => $currency,
                'delivery_address_id' => $deliveryAddressId,
                'delivery_address' => $deliveryAddress,
            ]
        ]);

        // Rediriger vers la vue de paiement MaishaPay
        return view('payments.maishapay', [
            'cart' => $cart,
            'total' => $total,
            'currency' => $currency,
            'deliveryAddress' => $deliveryAddress,
        ]);
    }

    /**
     * Initier un paiement MaishaPay via API
     */
    public function initiateMaishaPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'phone' => 'required|string|min:9|max:12',
            'currency' => 'sometimes|string|in:CDF,USD',
            'operator' => 'sometimes|string|in:VODACOM,AIRTEL,ORANGE,AFRICELL,vodacom,airtel,orange,africell',
            'purpose' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $maishaPay = new \App\Services\MaishaPay();

            if (!$maishaPay->isConfigured()) {
                Log::error('MaishaPay non configuré');
                return response()->json([
                    'success' => false,
                    'message' => 'Service de paiement non disponible',
                ], 503);
            }

            $buyerId = $request->user()->id ?? $request->input('buyer_id');

            // Générer un ID de transaction unique
            $transactionId = 'MP-' . strtoupper(\Illuminate\Support\Str::random(12)) . '-' . time();

            // Stocker le panier dans les métadonnées pour le callback
            $cartData = session('cart', []);
            $deliveryAddressId = session('maishapay_checkout.delivery_address_id');

            // Créer la transaction dans la base
            $transaction = Transaction::create([
                'user_id' => $buyerId,
                'buyer_id' => $buyerId,
                'transaction_id' => $transactionId,
                'transaction_ref' => $transactionId, // Pour le callback
                'amount' => $request->amount,
                'currency' => $request->input('currency', 'CDF'),
                'provider' => 'maishapay',
                'status' => 'pending',
                'purpose' => $request->input('purpose', 'Paiement VintApp'),
                'phone' => $request->phone,
                'metadata' => json_encode([
                    'operator' => $request->input('operator'),
                    'gateway' => 'maishapay',
                    'cart' => $cartData,
                    'delivery_address_id' => $deliveryAddressId,
                ]),
            ]);

            // Mode sandbox = simulation
            if (config('services.maishapay.environment') === 'sandbox') {
                $result = $maishaPay->simulatePayment([
                    'amount' => $request->amount,
                    'phone' => $request->phone,
                    'currency' => $request->input('currency', 'CDF'),
                    'operator' => $request->input('operator'),
                ]);

                if ($result['success']) {
                    $transaction->update([
                        'status' => 'completed',
                        'description' => 'Ref: ' . $result['transaction_id'],
                    ]);

                    // Créer les commandes à partir du panier
                    $this->createOrdersFromCart($buyerId, $transaction, $request->phone);

                    return response()->json([
                        'success' => true,
                        'status' => 'success',
                        'transaction_id' => $transaction->id,
                        'reference' => $result['transaction_id'],
                        'message' => $result['message'],
                        'simulated' => true,
                    ]);
                }
            }

            // Mode production
            $result = $maishaPay->initiatePayment([
                'amount' => $request->amount,
                'phone' => $request->phone,
                'currency' => $request->input('currency', 'CDF'),
                'operator' => $request->input('operator'),
                'buyer_id' => $buyerId,
                'description' => $request->input('purpose', 'Paiement VintApp'),
            ]);

            if ($result['success']) {
                $transaction->update([
                    'description' => 'Ref: ' . $result['transaction_id'],
                    'metadata' => json_encode(array_merge(
                        json_decode($transaction->metadata ?? '{}', true),
                        ['maishapay_id' => $result['maishapay_id'] ?? null, 'ref' => $result['transaction_id']]
                    )),
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'transaction_id' => $transaction->id,
                    'reference' => $result['transaction_id'],
                    'message' => $result['message'],
                ]);
            }

            $transaction->update(['status' => 'failed']);

            return response()->json([
                'success' => false,
                'status' => 'failed',
                'message' => $result['message'] ?? 'Erreur lors du paiement',
            ], 400);

        } catch (\Exception $e) {
            Log::error('MaishaPay Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du service de paiement',
            ], 500);
        }
    }

    /**
     * Callback MaishaPay (webhook)
     */
    public function handleMaishaCallback(Request $request)
    {
        Log::info('MaishaPay Callback reçu', $request->all());

        $signature = $request->header('X-MaishaPay-Signature');
        $payload = $request->getContent();

        $maishaPay = new \App\Services\MaishaPay();

        // Vérifier la signature en production
        if (config('services.maishapay.environment') !== 'sandbox') {
            if (!$maishaPay->verifyWebhookSignature($payload, $signature ?? '')) {
                Log::warning('MaishaPay: Signature webhook invalide');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $data = $request->all();
        $reference = $data['reference'] ?? $data['transaction_id'] ?? null;
        $status = strtolower($data['status'] ?? '');

        if (!$reference) {
            return response()->json(['error' => 'Missing reference'], 400);
        }

        // Chercher par transaction_ref ou transaction_id
        $transaction = Transaction::where('transaction_ref', $reference)
            ->orWhere('transaction_id', $reference)
            ->first();

        if (!$transaction) {
            Log::warning('MaishaPay: Transaction non trouvée', ['reference' => $reference]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Mettre à jour le statut
        $newStatus = match($status) {
            'success', 'completed', 'successful' => 'completed',
            'failed', 'declined', 'cancelled' => 'failed',
            default => 'pending',
        };

        $previousStatus = $transaction->status;

        $transaction->update([
            'status' => $newStatus,
            'metadata' => json_encode(array_merge(
                json_decode($transaction->metadata ?? '{}', true),
                ['callback_data' => $data, 'callback_at' => now()->toISOString()]
            )),
        ]);

        // Si le paiement vient d'être confirmé, créer les commandes
        if ($newStatus === 'completed' && $previousStatus !== 'completed') {
            $this->createOrdersFromCallback($transaction);
        }

        Log::info('MaishaPay: Transaction mise à jour', [
            'reference' => $reference,
            'status' => $newStatus,
        ]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    /**
     * Créer les commandes à partir du callback (données stockées dans metadata)
     */
    private function createOrdersFromCallback($transaction)
    {
        $metadata = json_decode($transaction->metadata ?? '{}', true);
        $cart = $metadata['cart'] ?? [];
        $deliveryAddressId = $metadata['delivery_address_id'] ?? null;
        $buyerId = $transaction->buyer_id ?? $transaction->user_id;
        $phone = $transaction->phone ?? null;

        if (empty($cart)) {
            Log::warning('MaishaPay Callback: Panier vide dans les métadonnées', [
                'transaction_id' => $transaction->id
            ]);
            return [];
        }

        $orders = [];
        
        // Récupérer l'adresse de livraison
        $deliveryAddress = $deliveryAddressId 
            ? \App\Models\DeliveryAddress::find($deliveryAddressId)
            : \App\Models\DeliveryAddress::where('user_id', $buyerId)->where('is_default', true)->first();

        foreach ($cart as $itemId => $cartItem) {
            $item = \App\Models\Item::find($itemId);
            
            if (!$item) {
                Log::warning('Article non trouvé dans le panier callback', ['item_id' => $itemId]);
                continue;
            }

            $orderAmount = $item->price * $cartItem['quantity'];
            
            // Créer ou récupérer le wallet "pending" du vendeur
            $seller = \App\Models\User::find($item->user_id);
            if (!$seller) {
                Log::warning('Vendeur non trouvé', ['seller_id' => $item->user_id]);
                continue;
            }

            $sellerPendingWallet = \App\Models\Wallet::firstOrCreate(
                [
                    'user_id' => $seller->id,
                    'type' => 'pending',
                    'currency' => $item->currency
                ],
                [
                    'balance' => 0,
                    'status' => 'active',
                    'is_active' => true
                ]
            );
            
            // Ajouter le montant au wallet pending du vendeur
            $sellerPendingWallet->increment('balance', $orderAmount);
            
            // Préparer les données de commande
            $orderData = [
                'buyer_id' => $buyerId,
                'seller_id' => $item->user_id,
                'item_id' => $item->id,
                'quantity' => $cartItem['quantity'],
                'unit_price' => $item->price,
                'total_amount' => $orderAmount,
                'currency' => $item->currency,
                'status' => 'confirmed',
                'paid_at' => now(),
                'notes' => 'Paiement via MaishaPay - Transaction #' . $transaction->id,
            ];
            
            // Ajouter l'adresse de livraison si disponible
            if ($deliveryAddress) {
                $orderData['delivery_address_id'] = $deliveryAddress->id;
                $orderData['shipping_address'] = $deliveryAddress->address;
                $orderData['shipping_city'] = $deliveryAddress->city;
                $orderData['shipping_phone'] = $deliveryAddress->phone;
            } else {
                $orderData['shipping_address'] = 'À définir';
                $orderData['shipping_city'] = 'À définir';
                $orderData['shipping_phone'] = $phone ?? 'N/A';
            }
            
            // Créer la commande
            $order = \App\Models\Order::create($orderData);
            $orders[] = $order;
            
            // Mettre à jour le stock
            $item->quantity -= $cartItem['quantity'];
            if ($item->quantity <= 0) {
                $item->status = 'sold';
            }
            $item->save();
            
            Log::info("Commande créée via MaishaPay Callback", [
                'order_id' => $order->id,
                'seller_id' => $seller->id,
                'amount' => $orderAmount,
                'currency' => $item->currency,
            ]);
        }
        
        Log::info('Commandes créées via callback', [
            'buyer_id' => $buyerId,
            'transaction_id' => $transaction->id,
            'orders_count' => count($orders),
        ]);
        
        return $orders;
    }

    /**
     * Vérifier le statut d'une transaction MaishaPay
     */
    public function checkMaishaStatus(Request $request, $transactionId)
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction introuvable',
            ], 404);
        }

        // Si déjà complété ou échoué, retourner le statut
        if (in_array($transaction->status, ['completed', 'failed'])) {
            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'transaction_id' => $transaction->id,
            ]);
        }

        // Sinon vérifier auprès de MaishaPay
        if ($transaction->transaction_ref) {
            $maishaPay = new \App\Services\MaishaPay();
            $result = $maishaPay->checkStatus($transaction->transaction_ref);

            if ($result['success'] && isset($result['status'])) {
                $newStatus = match(strtolower($result['status'])) {
                    'success', 'completed', 'successful' => 'completed',
                    'failed', 'declined', 'cancelled' => 'failed',
                    default => 'pending',
                };

                if ($newStatus !== $transaction->status) {
                    $transaction->update(['status' => $newStatus]);
                }

                return response()->json([
                    'success' => true,
                    'status' => $newStatus,
                    'transaction_id' => $transaction->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'transaction_id' => $transaction->id,
        ]);
    }

    /**
     * Créer les commandes à partir du panier après un paiement réussi
     */
    private function createOrdersFromCart($buyerId, $transaction, $phone = null)
    {
        $cart = session('cart', []);
        
        if (empty($cart)) {
            Log::info('Panier vide, aucune commande à créer', ['buyer_id' => $buyerId]);
            return [];
        }

        $orders = [];
        
        // Récupérer l'adresse de livraison par défaut du client
        $defaultDeliveryAddress = \App\Models\DeliveryAddress::where('user_id', $buyerId)
            ->where('is_default', true)
            ->first();

        foreach ($cart as $itemId => $cartItem) {
            $item = \App\Models\Item::find($itemId);
            
            if (!$item) {
                Log::warning('Article non trouvé dans le panier', ['item_id' => $itemId]);
                continue;
            }

            $orderAmount = $item->price * $cartItem['quantity'];
            
            // Créer ou récupérer le wallet "pending" du vendeur
            $seller = \App\Models\User::find($item->user_id);
            if (!$seller) {
                Log::warning('Vendeur non trouvé', ['seller_id' => $item->user_id]);
                continue;
            }

            $sellerPendingWallet = \App\Models\Wallet::firstOrCreate(
                [
                    'user_id' => $seller->id,
                    'type' => 'pending',
                    'currency' => $item->currency
                ],
                [
                    'balance' => 0,
                    'status' => 'active',
                    'is_active' => true
                ]
            );
            
            // Ajouter le montant au wallet pending du vendeur
            $sellerPendingWallet->increment('balance', $orderAmount);
            
            // Préparer les données de commande
            $orderData = [
                'buyer_id' => $buyerId,
                'seller_id' => $item->user_id,
                'item_id' => $item->id,
                'quantity' => $cartItem['quantity'],
                'unit_price' => $item->price,
                'total_amount' => $orderAmount,
                'currency' => $item->currency,
                'status' => 'confirmed',
                'paid_at' => now(),
                'notes' => 'Paiement via MaishaPay - Transaction #' . $transaction->id,
            ];
            
            // Ajouter l'adresse de livraison si disponible
            if ($defaultDeliveryAddress) {
                $orderData['delivery_address_id'] = $defaultDeliveryAddress->id;
                $orderData['shipping_address'] = $defaultDeliveryAddress->address;
                $orderData['shipping_city'] = $defaultDeliveryAddress->city;
                $orderData['shipping_phone'] = $defaultDeliveryAddress->phone;
            } else {
                $orderData['shipping_address'] = 'À définir';
                $orderData['shipping_city'] = 'À définir';
                $orderData['shipping_phone'] = $phone ?? 'N/A';
            }
            
            // Créer la commande
            $order = \App\Models\Order::create($orderData);
            $orders[] = $order;
            
            // Mettre à jour le stock
            $item->quantity -= $cartItem['quantity'];
            if ($item->quantity <= 0) {
                $item->status = 'sold';
            }
            $item->save();
            
            Log::info("Commande créée via MaishaPay", [
                'order_id' => $order->id,
                'seller_id' => $seller->id,
                'amount' => $orderAmount,
                'currency' => $item->currency,
            ]);
        }
        
        // Vider le panier après la création des commandes
        session()->forget('cart');
        
        Log::info('Commandes créées avec succès', [
            'buyer_id' => $buyerId,
            'transaction_id' => $transaction->id,
            'orders_count' => count($orders),
        ]);
        
        return $orders;
    }

}