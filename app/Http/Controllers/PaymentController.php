<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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
                            'status' => 'pending', // Paiement effectué, en attente d'expédition
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
        
        return view('payments.success', compact('transaction'));
    }
    
    public function paymentError(Request $request)
    {
        $error = $request->query('error', 'Une erreur est survenue lors du paiement');
        $amount = $request->query('amount', 0);
        $provider = $request->query('provider', 'Mobile Money');
        $currency = $request->query('currency', 'USD');
        
        return view('payments.error', compact('error', 'amount', 'provider', 'currency'));
    }
}

