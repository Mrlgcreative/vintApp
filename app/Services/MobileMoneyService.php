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
 * 
 * Agrégateurs supportés:
 * - MaishaPay (unifié pour tous les opérateurs RDC)
 * - APIs directes des opérateurs (fallback)
 */
class MobileMoneyService
{
    /**
     * Instance MaishaPay pour les payouts unifiés
     */
    protected ?MaishaPay $maishaPay = null;

    /**
     * Instance CinetPay pour les payouts via API de transfert
     */
    protected ?CinetPay $cinetPay = null;

    /**
     * Utiliser CinetPay comme agrégateur de décaissement
     */
    protected bool $useCinetPayAggregator = false;

    /**
     * Utiliser MaishaPay comme agrégateur par défaut
     */
    protected bool $useMaishaPayAggregator = true;

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
     * Constructor - Initialise MaishaPay et CinetPay si configurés
     */
    public function __construct()
    {
        $this->initializeMaishaPay();
        $this->initializeCinetPay();
    }

    /**
     * Initialiser le service CinetPay pour les décaissements
     */
    protected function initializeCinetPay(): void
    {
        try {
            $this->cinetPay = new CinetPay(
                config('services.cinetpay.site_id'),
                config('services.cinetpay.api_key'),
                config('services.cinetpay.platform'),
                config('services.cinetpay.version')
            );

            $this->useCinetPayAggregator = $this->cinetPay->isTransferConfigured();

            if ($this->useCinetPayAggregator) {
                Log::info('MobileMoneyService: CinetPay transfer initialisé', [
                    'enabled' => true,
                ]);
            }
        } catch (Exception $e) {
            Log::warning('MobileMoneyService: Impossible d\'initialiser CinetPay', [
                'error' => $e->getMessage(),
            ]);
            $this->useCinetPayAggregator = false;
        }
    }

    /**
     * Initialiser le service MaishaPay
     */
    protected function initializeMaishaPay(): void
    {
        try {
            if (config('services.maishapay.enabled', false)) {
                $this->maishaPay = new MaishaPay();
                $this->useMaishaPayAggregator = $this->maishaPay->isConfigured();
                
                Log::info('MobileMoneyService: MaishaPay initialisé', [
                    'enabled' => $this->useMaishaPayAggregator,
                ]);
            } else {
                $this->useMaishaPayAggregator = false;
            }
        } catch (Exception $e) {
            Log::warning('MobileMoneyService: Impossible d\'initialiser MaishaPay', [
                'error' => $e->getMessage(),
            ]);
            $this->useMaishaPayAggregator = false;
        }
    }

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
            // Normaliser le numéro de téléphone
            $normalizedPhone = $this->normalizePhoneNumber($phoneNumber);

            // Log de l'initiation
            Log::info("Initiation cash-out", [
                'provider' => $provider,
                'phone' => $normalizedPhone,
                'amount' => $amount,
                'currency' => $currency,
                'transaction_id' => $transaction->id,
                'use_maishapay' => $this->useMaishaPayAggregator,
                'use_cinetpay' => $this->useCinetPayAggregator,
            ]);

            // Si cinetpay est spécifié directement, utiliser l'API de transfert
            if ($provider === 'cinetpay') {
                return $this->cashOutCinetPay($normalizedPhone, $amount, $currency, $transaction);
            }

            // Si maishapay est spécifié directement, utiliser l'API B2C MaishaPay
            if ($provider === 'maishapay') {
                if (!$this->maishaPay) {
                    throw new Exception("MaishaPay n'est pas configuré");
                }
                // MaishaPay détecte automatiquement l'opérateur via le numéro
                $detectedOperator = $this->maishaPay->detectOperator($normalizedPhone);
                $result = $this->cashOutViaMaishaPay($detectedOperator ?? 'VODACOM', $normalizedPhone, $amount, $currency, $transaction);
            } elseif ($this->useMaishaPayAggregator && $this->maishaPay && $this->maishaPay->isOperatorSupported($provider)) {
                // Utiliser MaishaPay comme agrégateur pour les autres providers
                $result = $this->cashOutViaMaishaPay($provider, $normalizedPhone, $amount, $currency, $transaction);
            } else {
                // Validation du provider pour API directe
                if (!isset($this->providers[$provider])) {
                    throw new Exception("Opérateur mobile money invalide : {$provider}");
                }
                // Fallback vers les APIs directes des opérateurs
                $result = match ($provider) {
                    'orange_money' => $this->cashOutOrangeMoney($normalizedPhone, $amount, $currency, $transaction),
                    'airtel_money' => $this->cashOutAirtelMoney($normalizedPhone, $amount, $currency, $transaction),
                    'mpesa' => $this->cashOutMPesa($normalizedPhone, $amount, $currency, $transaction),
                    'africell' => $this->cashOutAfricell($normalizedPhone, $amount, $currency, $transaction),
                    'illicocash' => $this->cashOutIllicocash($normalizedPhone, $amount, $currency, $transaction),
                    default => throw new Exception("Provider non implémenté : {$provider}"),
                };
            }

            // Log du résultat
            Log::info("Cash-out résultat", [
                'provider' => $provider,
                'status' => $result['status'],
                'reference' => $result['provider_reference'] ?? null,
                'aggregator' => $result['aggregator'] ?? 'direct',
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
     * Cash-out via l'agrégateur MaishaPay (unifié pour tous les opérateurs)
     */
    private function cashOutViaMaishaPay(
        string $provider,
        string $phone,
        float $amount,
        string $currency,
        WalletTransaction $transaction
    ): array {
        Log::info("Cash-out via MaishaPay", [
            'provider' => $provider,
            'phone' => substr($phone, 0, 7) . '***',
            'amount' => $amount,
        ]);

        $operator = $this->maishaPay->mapOperator($provider);
        
        if (!$operator) {
            throw new Exception("Opérateur {$provider} non supporté par MaishaPay");
        }

        $result = $this->maishaPay->initiatePayout([
            'phone' => $phone,
            'amount' => $amount,
            'currency' => $currency,
            'operator' => $operator,
            'reference' => $transaction->reference,
            'description' => "Retrait VintApp - {$transaction->reference}",
            'user_id' => $transaction->user_id,
            'transaction_id' => $transaction->id,
            'purpose' => 'withdrawal',
            'callback_url' => route('withdrawals.webhook.provider', ['provider' => 'maishapay']),
        ]);

        if ($result['success']) {
            return [
                'status' => 'processing',
                'message' => $result['message'] ?? 'Retrait en cours via MaishaPay',
                'provider_reference' => $result['provider_reference'] ?? $result['transaction_id'],
                'provider_response' => $result['data'] ?? [],
                'aggregator' => 'maishapay',
            ];
        }

        // En cas d'échec MaishaPay, fallback vers l'API directe
        Log::warning("MaishaPay payout échoué, fallback vers API directe", [
            'provider' => $provider,
            'error' => $result['message'] ?? 'Unknown error',
        ]);

        return match ($provider) {
            'orange_money' => $this->cashOutOrangeMoney($phone, $amount, $currency, $transaction),
            'airtel_money' => $this->cashOutAirtelMoney($phone, $amount, $currency, $transaction),
            'mpesa' => $this->cashOutMPesa($phone, $amount, $currency, $transaction),
            'africell' => $this->cashOutAfricell($phone, $amount, $currency, $transaction),
            default => [
                'status' => 'failed',
                'message' => $result['message'] ?? 'Décaissement échoué',
                'provider_reference' => null,
            ],
        };
    }

    /**
     * Cash-out via l'API de transfert CinetPay (payout)
     */
    private function cashOutCinetPay(string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        if (!$this->cinetPay || !$this->useCinetPayAggregator) {
            throw new Exception("CinetPay n'est pas configuré pour les transferts");
        }

        Log::info("Cash-out via CinetPay", [
            'phone' => substr($phone, 0, 7) . '***',
            'amount' => $amount,
            'currency' => $currency,
        ]);

        [$prefix, $localPhone] = $this->extractPrefixAndPhone($phone);

        $result = $this->cinetPay->initiateTransfer(
            $prefix,
            $localPhone,
            $amount,
            $transaction->reference,
            route('withdrawals.webhook.provider', ['provider' => 'cinetpay'])
        );

        if (!$result['success']) {
            throw new Exception("CinetPay transfert échoué: " . ($result['message'] ?? 'Erreur inconnue'));
        }

        return [
            'status' => $result['status'] ?? 'processing',
            'message' => $result['message'] ?? 'Retrait en cours via CinetPay',
            'provider_reference' => $result['transaction_id'] ?? $result['client_transaction_id'],
            'provider_response' => $result['data'] ?? $result,
            'aggregator' => 'cinetpay',
        ];
    }

    /**
     * Extrait l'indicatif pays (prefix) et le numéro local d'un numéro E.164.
     *
     * @return array [prefix, phone] Ex: ['243', '812345678']
     */
    private function extractPrefixAndPhone(string $phone): array
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);
        $phone = ltrim($phone, '+');

        // Indicatifs pays supportés par CinetPay (3 chiffres)
        $prefixes3 = ['243', '225', '221', '223', '226', '229', '228', '237', '241', '242', '261', '224', '227', '232', '233', '250', '256', '254', '257', '260', '212', '213', '216', '218'];

        if (strlen($phone) >= 3 && in_array(substr($phone, 0, 3), $prefixes3, true)) {
            return [substr($phone, 0, 3), substr($phone, 3)];
        }

        // Indicatifs à 2 chiffres (ex: 27 pour l'Afrique du Sud)
        $prefixes2 = ['27', '20', '94', '95', '91', '92', '93', '81', '84', '96'];
        if (strlen($phone) >= 2 && in_array(substr($phone, 0, 2), $prefixes2, true)) {
            return [substr($phone, 0, 2), substr($phone, 2)];
        }

        // Fallback : sans indicatif, renvoyer le numéro tel quel
        return ['243', $phone];
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
        // Détecter si c'est un numéro RDC
        $isRDC = $this->isRDCNumber($phone);
        
        if ($isRDC) {
            Log::info("RDC number detected, using Vodacom RDC flow", [
                'phone' => substr($phone, 0, 7) . '...',
                'amount' => $amount,
            ]);
        }
        
        // Obtenir le token OAuth (avec détection automatique RDC/Kenya)
        $accessToken = $this->getMPesaAccessToken($phone);
        
        if (!$accessToken) {
            Log::info("No access token available, using simulation", [
                'country' => $isRDC ? 'RDC' : 'Kenya',
                'phone' => substr($phone, 0, 7) . '...',
            ]);
            return $this->simulateRDCCashOut($phone, $amount, $currency, $transaction);
        }

        try {
            $config = $this->providers['mpesa'];
            $shortcode = config('services.mpesa.shortcode');
            $passkey = config('services.mpesa.passkey');
            
            // Timestamp for the request
            $timestamp = now()->format('YmdHis');
            $password = base64_encode($shortcode . $passkey . $timestamp);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->post("{$config['api_url']}/b2c/v1/paymentrequest", [
                'InitiatorName' => config('app.name'),
                'SecurityCredential' => $password,
                'CommandID' => 'BusinessPayment',
                'Amount' => $amount,
                'PartyA' => $shortcode,
                'PartyB' => $phone,
                'Remarks' => "Retrait VintApp - {$transaction->reference}",
                'QueueTimeOutURL' => route('withdrawals.webhook', ['provider' => 'mpesa']),
                'ResultURL' => route('withdrawals.webhook', ['provider' => 'mpesa']),
                'Occasion' => 'Retrait',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => 'processing',
                    'message' => 'Retrait M-Pesa en cours',
                    'provider_reference' => $data['ConversationID'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("M-Pesa API error: " . $response->body());

        } catch (Exception $e) {
            Log::error("M-Pesa cash-out failed", [
                'phone' => $phone,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
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
     * Initier un décaissement via un agent mobile money
     *
     * @param int|null $agentId ID de l'agent (optionnel)
     * @param string $agentPhone Numéro de téléphone de l'agent
     * @param float $amount Montant à retirer
     * @param string $currency Devise (USD ou CDF)
     * @param WalletTransaction $transaction La transaction wallet associée
     * @return array Résultat de l'opération
     */
    public function cashOutAgent(
        ?int $agentId,
        string $agentPhone,
        float $amount,
        string $currency,
        WalletTransaction $transaction
    ): array {
        try {
            // Normaliser le numéro de téléphone de l'agent
            $normalizedAgentPhone = $this->normalizePhoneNumber($agentPhone);

            // Log de l'initiation du décaissement via agent
            Log::info("Initiation cash-out via agent", [
                'agent_id' => $agentId,
                'agent_phone' => $normalizedAgentPhone,
                'amount' => $amount,
                'currency' => $currency,
                'transaction_id' => $transaction->id,
            ]);

            // Déterminer le provider de l'agent selon le préfixe du numéro
            $provider = $this->detectProviderFromPhone($normalizedAgentPhone);
            
            if (!$provider) {
                throw new Exception("Impossible de déterminer l'opérateur pour le numéro agent: {$normalizedAgentPhone}");
            }

            // Validation du provider
            if (!isset($this->providers[$provider])) {
                throw new Exception("Opérateur mobile money invalide pour l'agent : {$provider}");
            }

            // Configuration spécifique pour les agents
            $agentConfig = $this->getAgentConfiguration($provider);

            // Appeler l'API spécifique aux agents du provider
            $result = match ($provider) {
                'orange_money' => $this->cashOutAgentOrangeMoney($normalizedAgentPhone, $amount, $currency, $transaction, $agentId),
                'airtel_money' => $this->cashOutAgentAirtelMoney($normalizedAgentPhone, $amount, $currency, $transaction, $agentId),
                'mpesa' => $this->cashOutAgentMPesa($normalizedAgentPhone, $amount, $currency, $transaction, $agentId),
                'africell' => $this->cashOutAgentAfricell($normalizedAgentPhone, $amount, $currency, $transaction, $agentId),
                'illicocash' => $this->cashOutAgentIllicocash($normalizedAgentPhone, $amount, $currency, $transaction, $agentId),
                default => throw new Exception("Décaissement agent non implémenté pour : {$provider}"),
            };

            // Enrichir le résultat avec les informations de l'agent
            $result['agent_info'] = [
                'agent_id' => $agentId,
                'agent_phone' => $normalizedAgentPhone,
                'detected_provider' => $provider,
            ];

            // Log du résultat
            Log::info("Cash-out agent résultat", [
                'agent_id' => $agentId,
                'provider' => $provider,
                'status' => $result['status'],
                'reference' => $result['provider_reference'] ?? null,
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error("Erreur cash-out agent", [
                'agent_id' => $agentId,
                'agent_phone' => $agentPhone,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage(),
                'provider_reference' => null,
                'agent_info' => [
                    'agent_id' => $agentId,
                    'agent_phone' => $agentPhone,
                ],
            ];
        }
    }

    /**
     * Détecte l'opérateur mobile money à partir du numéro de téléphone
     */
    private function detectProviderFromPhone(string $phone): ?string
    {
        // Retirer le préfixe pays (+243)
        $localNumber = str_replace(['+243', '243'], '', $phone);
        
        // Extraire les 2 premiers chiffres
        $prefix = substr($localNumber, 0, 2);
        
        return match ($prefix) {
            '84', '85', '89' => 'orange_money',
            '81', '82', '83' => 'mpesa',
            '97', '98', '99' => 'airtel_money',
            '90', '91', '92', '93' => 'africell',
            default => null,
        };
    }

    /**
     * Obtient la configuration spécifique aux agents pour un provider
     */
    private function getAgentConfiguration(string $provider): array
    {
        return config("agent_services.{$provider}_agent", []);
    }

    /**
     * Cash-out via agent Orange Money
     */
    private function cashOutAgentOrangeMoney(string $agentPhone, float $amount, string $currency, WalletTransaction $transaction, ?int $agentId): array
    {
        $config = $this->getAgentConfiguration('orange_money');
        
        $agentKey = $config['agent_key'];
        $apiKey = $config['api_key'];

        if (!$agentKey || !$apiKey) {
            throw new Exception("Configuration agent Orange Money manquante");
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'X-Agent-Key' => $agentKey,
            ])->post("{$config['api_url']}/agent-cashout", [
                'agent_msisdn' => $agentPhone,
                'agent_id' => $agentId,
                'currency' => $currency,
                'transaction_id' => $transaction->reference,
                'amount' => $amount,
                'callback_url' => route('wallet.withdrawals.webhook', ['provider' => 'orange_money']),
                'reference' => $transaction->reference,
                'description' => "Décaissement via agent Orange Money - Ref: {$transaction->reference}",
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => $data['status'] === 'success' ? 'processing' : 'failed',
                    'message' => 'Décaissement via agent Orange Money en cours',
                    'provider_reference' => $data['agent_transaction_id'] ?? $data['transaction_id'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Orange Money Agent API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Orange Money agent cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via agent Airtel Money
     */
    private function cashOutAgentAirtelMoney(string $agentPhone, float $amount, string $currency, WalletTransaction $transaction, ?int $agentId): array
    {
        $config = $this->getAgentConfiguration('airtel_money');
        
        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];

        if (!$clientId || !$clientSecret) {
            throw new Exception("Configuration agent Airtel Money manquante");
        }

        try {
            // 1. Obtenir le token d'authentification
            $authResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://openapiuat.airtel.africa/auth/oauth2/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if (!$authResponse->successful()) {
                throw new Exception("Airtel Money agent authentication failed");
            }

            $token = $authResponse->json()['access_token'];

            // 2. Initier le décaissement via agent
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'X-Country' => 'CD',
                'X-Currency' => $currency,
            ])->post("{$config['api_url']}/agent-disbursement", [
                'agent' => [
                    'msisdn' => $agentPhone,
                    'agent_id' => $agentId,
                ],
                'reference' => $transaction->reference,
                'transaction' => [
                    'amount' => $amount,
                    'id' => $transaction->reference,
                    'currency' => $currency,
                ],
                'callback_url' => route('wallet.withdrawals.webhook', ['provider' => 'airtel_money']),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => $data['status']['success'] ? 'processing' : 'failed',
                    'message' => $data['status']['message'] ?? 'Décaissement via agent Airtel en cours',
                    'provider_reference' => $data['data']['transaction']['id'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Airtel Money Agent API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Airtel Money agent cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via agent M-Pesa
     */
    private function cashOutAgentMPesa(string $agentPhone, float $amount, string $currency, WalletTransaction $transaction, ?int $agentId): array
    {
        // Détecter si c'est un agent RDC
        $isRDC = $this->isRDCNumber($agentPhone);
        
        // Obtenir le token OAuth (avec détection automatique RDC/Kenya)
        $accessToken = $this->getMPesaAccessToken($agentPhone);
        
        if (!$accessToken) {
            if ($isRDC) {
                Log::info("Agent RDC detected, using simulation", [
                    'agent_phone' => substr($agentPhone, 0, 7) . '...',
                    'agent_id' => $agentId,
                ]);
                return $this->simulateRDCAgentCashOut($agentPhone, $amount, $currency, $transaction, $agentId);
            }
            throw new Exception("Impossible d'obtenir le token M-Pesa OAuth");
        }

        try {
            $shortcode = config('services.mpesa.shortcode');
            $passkey = config('services.mpesa.passkey');
            
            // Timestamp for the request
            $timestamp = now()->format('YmdHis');
            $password = base64_encode($shortcode . $passkey . $timestamp);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->post('https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest', [
                'InitiatorName' => config('app.name'),
                'SecurityCredential' => $password,
                'CommandID' => 'BusinessPayment',
                'Amount' => $amount,
                'PartyA' => $shortcode,
                'PartyB' => $agentPhone,
                'Remarks' => "Décaissement agent VintApp - {$transaction->reference}",
                'QueueTimeOutURL' => route('withdrawals.webhook', ['provider' => 'mpesa']),
                'ResultURL' => route('withdrawals.webhook', ['provider' => 'mpesa']),
                'Occasion' => "Agent {$agentId}",
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => 'processing',
                    'message' => 'Décaissement via agent M-Pesa en cours',
                    'provider_reference' => $data['ConversationID'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("M-Pesa Agent API error: " . $response->body());

        } catch (Exception $e) {
            Log::error("M-Pesa agent cash-out failed", [
                'agent_phone' => $agentPhone,
                'agent_id' => $agentId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            throw new Exception("M-Pesa agent cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via agent Africell Money
     */
    private function cashOutAgentAfricell(string $agentPhone, float $amount, string $currency, WalletTransaction $transaction, ?int $agentId): array
    {
        $config = $this->getAgentConfiguration('africell');
        
        $agentConfigId = $config['agent_id'];
        $apiSecret = $config['api_secret'];

        if (!$agentConfigId || !$apiSecret) {
            throw new Exception("Configuration agent Africell manquante");
        }

        try {
            $response = Http::withHeaders([
                'X-Agent-ID' => $agentConfigId,
                'X-API-Secret' => $apiSecret,
                'Content-Type' => 'application/json',
            ])->post("{$config['api_url']}/agent-payout", [
                'agent_msisdn' => $agentPhone,
                'agent_id' => $agentId,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $transaction->reference,
                'description' => "Décaissement via agent Africell - Ref: {$transaction->reference}",
                'callback_url' => route('wallet.withdrawals.webhook', ['provider' => 'africell']),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => 'processing',
                    'message' => 'Décaissement via agent Africell en cours',
                    'provider_reference' => $data['agent_transaction_id'] ?? $data['transaction_id'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Africell Agent API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Africell agent cash-out failed: " . $e->getMessage());
        }
    }

    /**
     * Cash-out via agent Illicocash
     */
    private function cashOutAgentIllicocash(string $agentPhone, float $amount, string $currency, WalletTransaction $transaction, ?int $agentId): array
    {
        $config = $this->getAgentConfiguration('illicocash');
        
        $agentCode = $config['agent_code'];
        $apiToken = $config['api_token'];

        if (!$agentCode || !$apiToken) {
            throw new Exception("Configuration agent Illicocash manquante");
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiToken}",
                'Content-Type' => 'application/json',
                'X-Agent-Code' => $agentCode,
            ])->post("{$config['api_url']}/agent-disbursement", [
                'agent_phone' => $agentPhone,
                'agent_id' => $agentId,
                'amount' => $amount,
                'currency' => $currency,
                'transaction_ref' => $transaction->reference,
                'description' => "Décaissement via agent Illicocash - Ref: {$transaction->reference}",
                'webhook_url' => route('wallet.withdrawals.webhook', ['provider' => 'illicocash']),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => $data['status'] === 'success' ? 'processing' : 'failed',
                    'message' => $data['message'] ?? 'Décaissement via agent Illicocash en cours',
                    'provider_reference' => $data['agent_reference'] ?? $data['reference'] ?? null,
                    'provider_response' => $data,
                ];
            }

            throw new Exception("Illicocash Agent API error: " . $response->body());

        } catch (Exception $e) {
            throw new Exception("Illicocash agent cash-out failed: " . $e->getMessage());
        }
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
                'cinetpay' => $this->checkCinetPayStatus($providerReference),
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
    private function checkCinetPayStatus(string $reference): array
    {
        if (!$this->cinetPay || !$this->useCinetPayAggregator) {
            return ['status' => 'processing', 'message' => 'CinetPay non configuré'];
        }

        $result = $this->cinetPay->checkTransferStatus($reference);

        if (!$result['success']) {
            return [
                'status' => 'error',
                'message' => $result['message'] ?? 'Erreur de vérification CinetPay',
            ];
        }

        return [
            'status' => $result['status'] ?? 'processing',
            'message' => $result['comment'] ?? $result['message'] ?? 'Statut CinetPay',
            'provider_reference' => $result['transaction_id'] ?? null,
        ];
    }

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
                'maishapay' => $this->verifyMaishaPayWebhook($request),
                'cinetpay' => $this->verifyCinetPayWebhook($request),
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
            'maishapay' => $request->input('reference') ?? $request->input('data.reference') ?? $request->input('metadata.reference'),
            'cinetpay' => $request->input('client_transaction_id') ?? $request->input('transaction_id'),
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
            'maishapay' => $request->input('status') ?? $request->input('data.status'),
            'cinetpay' => $request->input('treatment_status') ?? $request->input('status'),
            'orange_money' => $request->input('status') ?? $request->input('payment_status'),
            'airtel_money' => $request->input('status.success') ? 'completed' : 'failed',
            'mpesa' => $request->input('output_ResponseCode') === '0' ? 'completed' : 'failed',
            'africell' => $request->input('status'),
            'illicocash' => $request->input('status'),
            default => 'unknown',
        };

        // Normaliser les statuts
        return match (strtolower($status)) {
            'success', 'successful', 'completed', 'paid', 'true', '1', 'val', 'validated' => 'completed',
            'failed', 'failure', 'error', 'declined', 'false', '0', 'rej', 'rejected', 'canc', 'cancelled', 'exp', 'expired' => 'failed',
            'pending', 'processing', 'initiated', 'new' => 'processing',
            default => 'processing',
        };
    }

    /**
     * Extraire la référence du provider du webhook
     */
    public function extractProviderReferenceFromWebhook(string $provider, $request): ?string
    {
        return match ($provider) {
            'maishapay' => $request->input('transaction_id') ?? $request->input('data.transaction_id'),
            'cinetpay' => $request->input('transaction_id') ?? $request->input('lot'),
            'orange_money' => $request->input('payment_token') ?? $request->input('txnid'),
            'airtel_money' => $request->input('data.transaction.id') ?? $request->input('transaction_id'),
            'mpesa' => $request->input('output_ConversationID') ?? $request->input('ConversationID'),
            'africell' => $request->input('transaction_id') ?? $request->input('provider_ref'),
            'illicocash' => $request->input('reference') ?? $request->input('transaction_id'),
            default => null,
        };
    }

    // Méthodes de vérification de signature par provider

    private function verifyCinetPayWebhook($request): bool
    {
        // L'API de transfert CinetPay n'envoie pas de signature sur les callbacks.
        // La validation repose sur la cohérence des données reçues (client_transaction_id
        // correspondant à une transaction de retrait existante) et, si configurée,
        // sur la restriction par IP dans le pool des IP CinetPay.
        $allowedIps = config('services.cinetpay.allowed_ips', '');
        if (empty($allowedIps)) {
            return true;
        }

        $clientIp = $request->ip();
        foreach (array_filter(array_map('trim', explode(',', $allowedIps))) as $range) {
            if ($this->ipMatchesRange($clientIp, $range)) {
                return true;
            }
        }

        Log::warning('CinetPay webhook: IP non autorisée', ['ip' => $clientIp]);
        return false;
    }

    private function ipMatchesRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$subnet, $mask] = explode('/', $range);
        if (!filter_var($subnet, FILTER_VALIDATE_IP) || (int)$mask < 0 || (int)$mask > 32) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);

        return ($ipLong !== false && $subnetLong !== false)
            && ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    private function verifyMaishaPayWebhook($request): bool
    {
        $signature = $request->header('X-MaishaPay-Signature');

        if (!$signature) {
            // Signature obligatoire : plus d'acceptation en mode sandbox
            Log::warning('MaishaPay webhook: signature manquante, refusé');
            return false;
        }

        $secret = config('services.maishapay.secret_key', '');
        if (empty($secret) || $secret === 'DEMO_SECRET') {
            Log::warning('MaishaPay webhook: secret non configuré, refusé');
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    private function verifyOrangeMoneyWebhook($request): bool
    {
        // Orange Money utilise généralement une signature HMAC
        $signature = $request->header('X-Orange-Signature');

        if (!$signature) {
            Log::warning('Orange Money webhook: signature manquante, refusé');
            return false;
        }

        $secret = config('services.orange_money.webhook_secret', '');
        if (empty($secret) || $secret === 'DEMO_SECRET') {
            Log::warning('Orange Money webhook: secret non configuré, refusé');
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    private function verifyAirtelMoneyWebhook($request): bool
    {
        // Airtel Money utilise un token d'authentification
        $token = $request->header('X-Airtel-Webhook-Token');

        if (!$token) {
            Log::warning('Airtel Money webhook: token manquant, refusé');
            return false;
        }

        $expectedToken = config('services.airtel_money.webhook_token', '');
        if (empty($expectedToken) || $expectedToken === 'DEMO_TOKEN') {
            Log::warning('Airtel Money webhook: token non configuré, refusé');
            return false;
        }

        return hash_equals($expectedToken, $token);
    }

    private function verifyMPesaWebhook($request): bool
    {
        // M-Pesa utilise une longueur d'en-tête de signature. Sans la config
        // réelle du secret, on refuse tout webhook pour éviter les faux échecs/remboursements.
        $signature = $request->header('X-M-Signature');
        $secret = config('services.mpesa.webhook_secret', '');

        if (!$signature || empty($secret) || $secret === 'DEMO_SECRET') {
            Log::warning('M-Pesa webhook: signature/secret non configurés, refusé');
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    private function verifyAfricellWebhook($request): bool
    {
        // Africell utilise un secret partagé
        $providedSecret = $request->input('api_secret');

        if (!$providedSecret) {
            Log::warning('Africell webhook: secret manquant, refusé');
            return false;
        }

        $expectedSecret = config('services.africell.webhook_secret', '');
        if (empty($expectedSecret) || $expectedSecret === 'DEMO_SECRET') {
            Log::warning('Africell webhook: secret non configuré, refusé');
            return false;
        }

        return hash_equals($expectedSecret, $providedSecret);
    }

    private function verifyIllicocashWebhook($request): bool
    {
        // Illicocash utilise une signature dans les headers
        $signature = $request->header('X-Illico-Signature');

        if (!$signature) {
            Log::warning('Illicocash webhook: signature manquante, refusé');
            return false;
        }

        $secret = config('services.illicocash.webhook_secret', '');
        if (empty($secret) || $secret === 'DEMO_SECRET') {
            Log::warning('Illicocash webhook: secret non configuré, refusé');
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Simuler un cash-out RDC (en attendant les vraies clés Vodacom RDC)
     */
    private function simulateRDCCashOut(string $phone, float $amount, string $currency, WalletTransaction $transaction): array
    {
        Log::info("Simulating Vodacom RDC cash-out", [
            'phone' => substr($phone, 0, 7) . '...',
            'amount' => $amount,
            'currency' => $currency,
            'transaction_id' => $transaction->id,
        ]);

        return [
            'status' => 'processing',
            'message' => "Retrait M-Pesa RDC en cours (simulé)",
            'provider_reference' => 'VDC-RDC-' . time() . '-' . rand(1000, 9999),
            'provider_response' => [
                'ConversationID' => 'RDC_' . date('YmdHis') . '_' . rand(100000, 999999),
                'OriginatorConversationID' => 'VINTAPP_' . time(),
                'ResponseCode' => '0',
                'ResponseDescription' => 'Request accepted for processing (Vodacom RDC Simulation)',
                'simulation' => true,
                'country' => 'RDC',
                'provider' => 'Vodacom M-Pesa',
            ],
        ];
    }

    /**
     * Simuler un cash-out agent RDC (en attendant les vraies clés Vodacom RDC)
     */
    private function simulateRDCAgentCashOut(string $agentPhone, float $amount, string $currency, WalletTransaction $transaction, ?int $agentId): array
    {
        Log::info("Simulating Vodacom RDC agent cash-out", [
            'agent_phone' => substr($agentPhone, 0, 7) . '...',
            'agent_id' => $agentId,
            'amount' => $amount,
            'currency' => $currency,
            'transaction_id' => $transaction->id,
        ]);

        return [
            'status' => 'processing',
            'message' => "Décaissement agent M-Pesa RDC en cours (simulé)",
            'provider_reference' => 'VDC-AGENT-RDC-' . time() . '-' . rand(1000, 9999),
            'provider_response' => [
                'ConversationID' => 'RDC_AGENT_' . date('YmdHis') . '_' . rand(100000, 999999),
                'OriginatorConversationID' => 'VINTAPP_AGENT_' . time(),
                'ResponseCode' => '0',
                'ResponseDescription' => 'Agent cash-out accepted for processing (Vodacom RDC Simulation)',
                'simulation' => true,
                'country' => 'RDC',
                'provider' => 'Vodacom M-Pesa',
                'agent_id' => $agentId,
                'agent_phone' => $agentPhone,
            ],
        ];
    }

    /**
     * Détecter si un numéro est RDC (Vodacom RDC) ou Kenya (Safaricom)
     */
    private function isRDCNumber(string $normalizedPhone): bool
    {
        // Numéros RDC commencent par +243
        return str_starts_with($normalizedPhone, '+243');
    }

    /**
     * Obtenir un token d'accès OAuth pour M-Pesa
     */
    private function getMPesaAccessToken(string $phoneNumber = ''): ?string
    {
        try {
            $isRDC = $this->isRDCNumber($phoneNumber);
            
            if ($isRDC) {
                // Pour Vodacom RDC - utiliser les clés spécifiques RDC si disponibles
                $consumerKey = config('services.vodacom_rdc.consumer_key') ?? env('VODACOM_RDC_CONSUMER_KEY');
                $consumerSecret = config('services.vodacom_rdc.consumer_secret') ?? env('VODACOM_RDC_CONSUMER_SECRET');
                $environment = config('services.vodacom_rdc.environment', 'sandbox');
                
                if (!$consumerKey || !$consumerSecret) {
                    Log::info("Vodacom RDC credentials not configured, using simulation");
                    return null; // Simulation mode
                }
                
                // URLs Vodacom RDC
                $oauthUrl = $environment === 'production'
                    ? 'https://openapi.m-pesa.com/ipg/v2/token'
                    : 'https://openapi.m-pesa.com/sandbox/ipg/v2/token';
                    
            } else {
                // Pour Safaricom Kenya (numéros +254)
                $consumerKey = config('services.mpesa.consumer_key') ?? env('MPESA_API_KEY');
                $consumerSecret = config('services.mpesa.consumer_secret') ?? env('MPESA_API_SECRET');
                $environment = config('services.mpesa.environment', 'sandbox');

                if (!$consumerKey || !$consumerSecret) {
                    Log::warning("M-Pesa credentials not configured");
                    return null;
                }

                // URLs Safaricom Kenya
                $oauthUrl = $environment === 'production'
                    ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
                    : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
            }

            // Encoder les credentials en base64
            $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

            Log::info("M-Pesa OAuth attempt", [
                'country' => $isRDC ? 'RDC' : 'Kenya',
                'environment' => $environment,
                'phone_prefix' => substr($phoneNumber, 0, 7),
            ]);

            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
                'Content-Type' => 'application/json',
            ])->get($oauthUrl);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'] ?? null;

                if ($accessToken) {
                    Log::info("M-Pesa OAuth token obtained successfully");
                    return $accessToken;
                }
            }

            Log::error("M-Pesa OAuth failed", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;

        } catch (Exception $e) {
            Log::error("M-Pesa OAuth exception", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
