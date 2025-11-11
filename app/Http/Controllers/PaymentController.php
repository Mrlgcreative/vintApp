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

class PaymentController extends Controller
{
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
        $transaction = \App\Models\Transaction::where('transaction_id', $transaction_id)->first();
        
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
}

