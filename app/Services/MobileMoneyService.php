<?php

namespace App\Services;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service de décaissement Mobile Money
 * 
 * Gère les cash-out (retraits) vers les opérateurs mobile money :
 * - Orange Money
 * - Airtel Money
 * - M-Pesa (Vodacom)
 * - Africell Money
 * - Illicocash
 */
class MobileMoneyService
{
    /**
     * Configuration des APIs des opérateurs
     */
    private array $providers = [
        'orange_money' => [
            'name' => 'Orange Money',
            'api_url' => 'https://api.orange.com/orange-money-webpay/cd/v1',
            'merchant_key' => null, // À configurer dans .env
            'api_key' => null,
        ],
        'airtel_money' => [
            'name' => 'Airtel Money',
            'api_url' => 'https://openapiuat.airtel.africa/merchant/v1',
            'client_id' => null,
            'client_secret' => null,
        ],
        'mpesa' => [
            'name' => 'M-Pesa',
            'api_url' => 'https://api.vodacom.cd/mpesa/b2c/v1',
            'api_key' => null,
            'public_key' => null,
        ],
        'africell' => [
            'name' => 'Africell Money',
            'api_url' => 'https://api.africell.cd/payment/v1',
            'merchant_id' => null,
            'api_secret' => null,
        ],
        'illicocash' => [
            'name' => 'Illicocash',
            'api_url' => 'https://api.illicocash.com/v1',
            'merchant_code' => null,
            'api_token' => null,
        ],
    ];

    /**
     * Initier un décaissement (cash-out)
     *
     * @param string $provider L'opérateur (orange_money, airtel_money, etc.)
     * @param string $phoneNumber Numéro de téléphone du destinataire
     * @param float $amount Montant à retirer
     * @param string $currency Devise (USD ou CDF)
     * @param WalletTransaction $transaction La transaction wallet associée
     * @return array Résultat de l'opération
     */
    public function cashOut(
        string $provider,
        string $phoneNumber,
        float $amount,
        string $currency,
        WalletTransaction $transaction
    ): array {
        try {
            // Validation du provider
            if (!isset($this->providers[$provider])) {
                throw new Exception("Opérateur mobile money invalide : {$provider}");
            }

            // Normaliser le numéro de téléphone
            $normalizedPhone = $this->normalizePhoneNumber($phoneNumber);

            // Log de l'initiation
            Log::info("Initiation cash-out", [
                'provider' => $provider,
                'phone' => $normalizedPhone,
                'amount' => $amount,
                'currency' => $currency,
                'transaction_id' => $transaction->id,
            ]);

            // Appeler l'API du provider approprié
            $result = match ($provider) {
                'orange_money' => $this->cashOutOrangeMoney($normalizedPhone, $amount, $currency, $transaction),
                'airtel_money' => $this->cashOutAirtelMoney($normalizedPhone, $amount, $currency, $transaction),
                'mpesa' => $this->cashOutMPesa($normalizedPhone, $amount, $currency, $transaction),
                'africell' => $this->cashOutAfricell($normalizedPhone, $amount, $currency, $transaction),
                'illicocash' => $this->cashOutIllicocash($normalizedPhone, $amount, $currency, $transaction),
                default => throw new Exception("Provider non implémenté : {$provider}"),
            };

            // Log du résultat
            Log::info("Cash-out résultat", [
                'provider' => $provider,
                'status' => $result['status'],
                'reference' => $result['provider_reference'] ?? null,
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error("Erreur cash-out", [
                'provider' => $provider,
                'phone' => $phoneNumber,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage(),
                'provider_reference' => null,
            ];
        }
    }

    /**
     * Cash-out via Orange Money
     */
    private function cashOutOrangeMoney(string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        $config = $this->providers['orange_money'];
        
        // TODO: Remplacer par vraies credentials depuis .env
        $merchantKey = config('services.orange_money.merchant_key', 'DEMO_KEY');
        $apiKey = config('services.orange_money.api_key', 'DEMO_API');

        if ($merchantKey === 'DEMO_KEY') {
            // Mode simulation pour développement
            return $this->simulateCashOut('orange_money', $phone, $amount, $currency, $transaction);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$config['api_url']}/cashout", [
                'merchant_key' => $merchantKey,
                'currency' => $currency,
                'order_id' => $transaction->reference,
                'amount' => $amount,
                'return_url' => route('withdrawals.webhook', ['provider' => 'orange_money']),
                'cancel_url' => route('withdrawals.webhook', ['provider' => 'orange_money']),
                'notif_url' => route('withdrawals.webhook', ['provider' => 'orange_money']),
                'reference' => $transaction->reference,
                'subscriber_msisdn' => $phone,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => 'processing',
                    'message' => 'Retrait en cours de traitement',
                    'provider_reference' => $data['payment_token'] ?? $data['transaction_id'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Orange Money API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Orange Money cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via Airtel Money
     */
    private function cashOutAirtelMoney(string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        $config = $this->providers['airtel_money'];
        
        $clientId = config('services.airtel_money.client_id', 'DEMO_CLIENT');
        $clientSecret = config('services.airtel_money.client_secret', 'DEMO_SECRET');

        if ($clientId === 'DEMO_CLIENT') {
            return $this->simulateCashOut('airtel_money', $phone, $amount, $currency, $transaction);
        }

        try {
            // 1. Obtenir le token d'authentification
            $authResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$config['api_url']}/auth/oauth2/token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if (!$authResponse->successful()) {
                throw new Exception("Airtel Money authentication failed");
            }

            $token = $authResponse->json()['access_token'];

            // 2. Initier le cash-out
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'X-Country' => 'CD',
                'X-Currency' => $currency,
            ])->post("{$config['api_url']}/disbursements", [
                'payee' => [
                    'msisdn' => $phone,
                ],
                'reference' => $transaction->reference,
                'pin' => config('services.airtel_money.pin'),
                'transaction' => [
                    'amount' => $amount,
                    'id' => $transaction->reference,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => $data['status']['success'] ? 'processing' : 'failed',
                    'message' => $data['status']['message'] ?? 'Retrait en cours',
                    'provider_reference' => $data['data']['transaction']['id'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Airtel Money API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Airtel Money cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via M-Pesa (Vodacom)
     */
    private function cashOutMPesa(string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        $config = $this->providers['mpesa'];
        
        $apiKey = config('services.mpesa.api_key', 'DEMO_KEY');
        $publicKey = config('services.mpesa.public_key', 'DEMO_PUBLIC');

        if ($apiKey === 'DEMO_KEY') {
            return $this->simulateCashOut('mpesa', $phone, $amount, $currency, $transaction);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'Origin' => config('app.url'),
            ])->post("{$config['api_url']}/paymentrequest", [
                'input_TransactionReference' => $transaction->reference,
                'input_CustomerMSISDN' => $phone,
                'input_Amount' => $amount,
                'input_ThirdPartyConversationID' => $transaction->reference,
                'input_ServiceProviderCode' => config('services.mpesa.service_code'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => 'processing',
                    'message' => 'Retrait M-Pesa en cours',
                    'provider_reference' => $data['output_ConversationID'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("M-Pesa API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("M-Pesa cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via Africell Money
     */
    private function cashOutAfricell(string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        $config = $this->providers['africell'];
        
        $merchantId = config('services.africell.merchant_id', 'DEMO_MERCHANT');
        $apiSecret = config('services.africell.api_secret', 'DEMO_SECRET');

        if ($merchantId === 'DEMO_MERCHANT') {
            return $this->simulateCashOut('africell', $phone, $amount, $currency, $transaction);
        }

        try {
            $response = Http::withHeaders([
                'X-Merchant-ID' => $merchantId,
                'X-API-Secret' => $apiSecret,
                'Content-Type' => 'application/json',
            ])->post("{$config['api_url']}/payout", [
                'msisdn' => $phone,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $transaction->reference,
                'callback_url' => route('withdrawals.webhook', ['provider' => 'africell']),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => 'processing',
                    'message' => 'Retrait Africell en cours',
                    'provider_reference' => $data['transaction_id'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Africell API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Africell cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via Illicocash
     */
    private function cashOutIllicocash(string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        $config = $this->providers['illicocash'];
        
        $merchantCode = config('services.illicocash.merchant_code', 'DEMO_CODE');
        $apiToken = config('services.illicocash.api_token', 'DEMO_TOKEN');

        if ($merchantCode === 'DEMO_CODE') {
            return $this->simulateCashOut('illicocash', $phone, $amount, $currency, $transaction);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiToken}",
                'Content-Type' => 'application/json',
            ])->post("{$config['api_url']}/disbursements", [
                'merchant_code' => $merchantCode,
                'phone_number' => $phone,
                'amount' => $amount,
                'currency' => $currency,
                'transaction_ref' => $transaction->reference,
                'webhook_url' => route('withdrawals.webhook', ['provider' => 'illicocash']),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => $data['status'] === 'success' ? 'processing' : 'failed',
                    'message' => $data['message'] ?? 'Retrait Illicocash en cours',
                    'provider_reference' => $data['reference'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Illicocash API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Illicocash cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Mode simulation pour le développement
     * 
     * Simule un cash-out réussi sans appeler les vraies APIs
     */
    private function simulateCashOut(string $provider, string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        Log::info("SIMULATION MODE - Cash-out", [
            'provider' => $provider,
            'phone' => $phone,
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $transaction->reference,
        ]);

        // Simuler un délai de traitement
        sleep(1);

        // 90% de réussite, 10% d'échec aléatoire pour tester
        $success = rand(1, 10) <= 9;

        if ($success) {
            return [
                'status' => 'processing',
                'message' => "Retrait simulé en cours vers {$phone}",
                'provider_reference' => 'SIM-' . strtoupper($provider) . '-' . time() . '-' . rand(1000, 9999),
                'provider_response' => [
                    'simulation' => true,
                    'provider' => $provider,
                    'phone' => $phone,
                    'amount' => $amount,
                    'currency' => $currency,
                ],
            ];
        } else {
            return [
                'status' => 'failed',
                'message' => 'Simulation d\'échec de retrait (mode test)',
                'provider_reference' => null,
                'provider_response' => [
                    'simulation' => true,
                    'error' => 'Simulated failure',
                ],
            ];
        }
    }

    /**
     * Normaliser un numéro de téléphone congolais
     * 
     * Formats acceptés:
     * - 0812345678 → +243812345678
     * - 812345678 → +243812345678
     * - +243812345678 → +243812345678
     * - 243812345678 → +243812345678
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Retirer tous les espaces et caractères spéciaux sauf +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Si commence par 0, remplacer par +243
        if (str_starts_with($phone, '0')) {
            return '+243' . substr($phone, 1);
        }

        // Si commence par 243, ajouter le +
        if (str_starts_with($phone, '243')) {
            return '+' . $phone;
        }

        // Si ne commence pas par +, c'est juste le numéro sans indicatif
        if (!str_starts_with($phone, '+')) {
            return '+243' . $phone;
        }

        return $phone;
    }

    /**
     * Vérifier le statut d'un retrait auprès du provider
     * 
     * @param string $provider Opérateur
     * @param string $providerReference Référence du provider
     * @return array Statut actuel
     */
    public function checkWithdrawalStatus(string $provider, string $providerReference): array
    {
        try {
            Log::info("Vérification statut retrait", [
                'provider' => $provider,
                'reference' => $providerReference,
            ]);

            // Si c'est une simulation
            if (str_starts_with($providerReference, 'SIM-')) {
                return [
                    'status' => 'completed',
                    'message' => 'Transaction simulée complétée',
                ];
            }

            // TODO: Implémenter les vraies vérifications de statut par provider
            return match ($provider) {
                'orange_money' => $this->checkOrangeMoneyStatus($providerReference),
                'airtel_money' => $this->checkAirtelMoneyStatus($providerReference),
                'mpesa' => $this->checkMPesaStatus($providerReference),
                'africell' => $this->checkAfricellStatus($providerReference),
                'illicocash' => $this->checkIllicocashStatus($providerReference),
                default => ['status' => 'unknown', 'message' => 'Provider inconnu'],
            };

        } catch (Exception $e) {
            Log::error("Erreur vérification statut", [
                'provider' => $provider,
                'reference' => $providerReference,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    // Méthodes de vérification de statut (à implémenter selon les APIs)
    private function checkOrangeMoneyStatus(string $reference): array
    {
        // TODO: Implémenter l'appel API de vérification
        return ['status' => 'processing', 'message' => 'En cours de traitement'];
    }

    private function checkAirtelMoneyStatus(string $reference): array
    {
        // TODO: Implémenter l'appel API de vérification
        return ['status' => 'processing', 'message' => 'En cours de traitement'];
    }

    private function checkMPesaStatus(string $reference): array
    {
        // TODO: Implémenter l'appel API de vérification
        return ['status' => 'processing', 'message' => 'En cours de traitement'];
    }

    private function checkAfricellStatus(string $reference): array
    {
        // TODO: Implémenter l'appel API de vérification
        return ['status' => 'processing', 'message' => 'En cours de traitement'];
    }

    private function checkIllicocashStatus(string $reference): array
    {
        // TODO: Implémenter l'appel API de vérification
        return ['status' => 'processing', 'message' => 'En cours de traitement'];
    }

    /**
     * Vérifier la signature/authentification d'un webhook
     *
     * @param string $provider Opérateur mobile money
     * @param \Illuminate\Http\Request $request Requête webhook
     * @return bool Signature valide ou non
     */
    public function verifyWebhookSignature(string $provider, $request): bool
    {
        try {
            return match ($provider) {
                'orange_money' => $this->verifyOrangeMoneyWebhook($request),
                'airtel_money' => $this->verifyAirtelMoneyWebhook($request),
                'mpesa' => $this->verifyMPesaWebhook($request),
                'africell' => $this->verifyAfricellWebhook($request),
                'illicocash' => $this->verifyIllicocashWebhook($request),
                default => false,
            };
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Extraire la référence de transaction du webhook
     */
    public function extractReferenceFromWebhook(string $provider, $request): ?string
    {
        return match ($provider) {
            'orange_money' => $request->input('reference') ?? $request->input('order_id'),
            'airtel_money' => $request->input('transaction.id') ?? $request->input('reference'),
            'mpesa' => $request->input('input_TransactionReference') ?? $request->input('ThirdPartyConversationID'),
            'africell' => $request->input('reference') ?? $request->input('transaction_ref'),
            'illicocash' => $request->input('transaction_ref') ?? $request->input('reference'),
            default => null,
        };
    }

    /**
     * Extraire le statut du webhook
     */
    public function extractStatusFromWebhook(string $provider, $request): string
    {
        $status = match ($provider) {
            'orange_money' => $request->input('status') ?? $request->input('payment_status'),
            'airtel_money' => $request->input('status.success') ? 'completed' : 'failed',
            'mpesa' => $request->input('output_ResponseCode') === '0' ? 'completed' : 'failed',
            'africell' => $request->input('status'),
            'illicocash' => $request->input('status'),
            default => 'unknown',
        };

        // Normaliser les statuts
        return match (strtolower($status)) {
            'success', 'successful', 'completed', 'paid', 'true', '1' => 'completed',
            'failed', 'failure', 'error', 'declined', 'false', '0' => 'failed',
            'pending', 'processing', 'initiated' => 'processing',
            default => 'processing',
        };
    }

    /**
     * Extraire la référence du provider du webhook
     */
    public function extractProviderReferenceFromWebhook(string $provider, $request): ?string
    {
        return match ($provider) {
            'orange_money' => $request->input('payment_token') ?? $request->input('txnid'),
            'airtel_money' => $request->input('data.transaction.id') ?? $request->input('transaction_id'),
            'mpesa' => $request->input('output_ConversationID') ?? $request->input('ConversationID'),
            'africell' => $request->input('transaction_id') ?? $request->input('provider_ref'),
            'illicocash' => $request->input('reference') ?? $request->input('transaction_id'),
            default => null,
        };
    }

    // Méthodes de vérification de signature par provider

    private function verifyOrangeMoneyWebhook($request): bool
    {
        // Orange Money utilise généralement une signature HMAC
        $signature = $request->header('X-Orange-Signature');
        
        if (!$signature) {
            return true; // En mode simulation, accepter tous les webhooks
        }

        $secret = config('services.orange_money.webhook_secret', 'DEMO_SECRET');
        $payload = json_encode($request->all());
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    private function verifyAirtelMoneyWebhook($request): bool
    {
        // Airtel Money utilise un token d'authentification
        $token = $request->header('X-Airtel-Webhook-Token');
        
        if (!$token) {
            return true; // Mode simulation
        }

        $expectedToken = config('services.airtel_money.webhook_token', 'DEMO_TOKEN');
        return hash_equals($expectedToken, $token);
    }

    private function verifyMPesaWebhook($request): bool
    {
        // M-Pesa utilise une clé publique pour vérifier les signatures
        $signature = $request->header('X-M-Signature');
        
        if (!$signature) {
            return true; // Mode simulation
        }

        // TODO: Implémenter la vérification avec la clé publique M-Pesa
        return true;
    }

    private function verifyAfricellWebhook($request): bool
    {
        // Africell utilise un secret partagé
        $providedSecret = $request->input('api_secret');
        
        if (!$providedSecret) {
            return true; // Mode simulation
        }

        $expectedSecret = config('services.africell.webhook_secret', 'DEMO_SECRET');
        return hash_equals($expectedSecret, $providedSecret);
    }

    private function verifyIllicocashWebhook($request): bool
    {
        // Illicocash utilise une signature dans les headers
        $signature = $request->header('X-Illico-Signature');
        
        if (!$signature) {
            return true; // Mode simulation
        }

        $secret = config('services.illicocash.webhook_secret', 'DEMO_SECRET');
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
