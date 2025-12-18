<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MaishaPay
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $merchantId;
    protected string $baseUrl;
    protected string $collectUrl;
    protected string $payoutUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $this->apiKey = config('services.maishapay.api_key') ?? '';
        $this->secretKey = config('services.maishapay.secret_key') ?? '';
        $this->merchantId = config('services.maishapay.merchant_id') ?? '';
        $this->sandbox = config('services.maishapay.environment', 'sandbox') === 'sandbox';
        
        // URLs de l'API MaishaPay
        $this->baseUrl = $this->sandbox 
            ? 'https://sandbox.maishapay.net/api/v2'
            : 'https://marchand.maishapay.online/api';
            
        // URL spécifique pour la collecte Mobile Money v2
        $this->collectUrl = $this->sandbox
            ? 'https://sandbox.maishapay.net/api/v2/payments/mobile-money'
            : 'https://marchand.maishapay.online/api/collect/v2/store/mobileMoney';
            
        // URL spécifique pour les transferts B2C (décaissement/payout)
        $this->payoutUrl = $this->sandbox
            ? 'https://sandbox.maishapay.net/api/v2/payouts/mobile-money'
            : 'https://marchand.maishapay.online/api/b2c/store/transfert/mobilemoney';
    }

    /**
     * Vérifier si MaishaPay est configuré
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->secretKey);
    }

    /**
     * Obtenir les headers d'authentification
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Détecter l'opérateur depuis le numéro de téléphone
     */
    public function detectOperator(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Supprimer le préfixe 243 si présent
        if (str_starts_with($phone, '243')) {
            $phone = substr($phone, 3);
        }
        
        $prefix = substr($phone, 0, 2);
        
        $operators = [
            '81' => 'VODACOM',
            '82' => 'VODACOM', 
            '83' => 'VODACOM',
            '84' => 'ORANGE',
            '85' => 'ORANGE',
            '89' => 'ORANGE',
            '97' => 'AIRTEL',
            '98' => 'AIRTEL',
            '99' => 'AIRTEL',
            '90' => 'AFRICELL',
            '91' => 'AFRICELL',
        ];

        return $operators[$prefix] ?? null;
    }

    /**
     * Formater le numéro de téléphone pour MaishaPay
     */
    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Si le numéro commence par 243, on le garde
        if (str_starts_with($phone, '243')) {
            return $phone;
        }
        
        // Sinon on ajoute 243
        return '243' . $phone;
    }

    /**
     * Initier un paiement Mobile Money (Collecte v2)
     */
    public function initiatePayment(array $data): array
    {
        // Utiliser l'ID fourni ou en générer un
        $transactionId = $data['transaction_id'] ?? ('MP-' . strtoupper(Str::random(12)));
        $phone = $this->formatPhone($data['phone']);
        $operator = $data['operator'] ?? $this->detectOperator($phone);

        // Mapper l'opérateur vers le format MaishaPay
        $providerMap = [
            'VODACOM' => 'MPESA',
            'ORANGE' => 'ORANGE',
            'AIRTEL' => 'AIRTEL',
            'AFRICELL' => 'AFRICELL',
        ];
        $provider = $providerMap[strtoupper($operator)] ?? strtoupper($operator);

        // Payload selon la documentation MaishaPay Collect v2 Production
        $payload = [
            'transactionReference' => $transactionId,
            'gatewayMode' => $this->sandbox ? '0' : '1',
            'publicApiKey' => $this->apiKey,
            'secretApiKey' => $this->secretKey,
            'order' => [
                'amount' => (string) $data['amount'],
                'currency' => strtoupper($data['currency'] ?? 'USD'),
                'customerFullName' => $data['customer_name'] ?? ($data['user_name'] ?? 'Client VintApp'),
                'customerEmailAdress' => $data['customer_email'] ?? '',
            ],
            'paymentChannel' => [
                'channel' => 'MOBILEMONEY',
                'provider' => $provider,
                'walletID' => '+' . $phone,
                'callbackUrl' => $data['callback_url'] ?? route('payments.maishapay.callback', ['reference' => $transactionId]),
            ],
        ];

        Log::info('MaishaPay: Initiation paiement v2', [
            'reference' => $transactionId,
            'amount' => $payload['order']['amount'],
            'currency' => $payload['order']['currency'],
            'phone' => '+' . $phone,
            'provider' => $provider,
            'url' => $this->collectUrl,
        ]);

        try {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(30)
                ->post($this->collectUrl, $payload);

            $result = $response->json();

            Log::info('MaishaPay: Réponse API v2', [
                'status' => $response->status(),
                'response' => $result,
            ]);

            // Succès si status 200/201 et pas d'erreurs
            if ($response->successful() && !isset($result['errors'])) {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'maishapay_id' => $result['data']['transactionId'] ?? $result['transactionId'] ?? null,
                    'status' => 'pending',
                    'message' => $result['message'] ?? 'Paiement initié. Confirmez sur votre téléphone.',
                    'data' => $result['data'] ?? $result,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'failed',
                'message' => $result['title'] ?? $result['message'] ?? 'Erreur lors de l\'initiation du paiement',
                'error' => $result['errors'] ?? $result['error'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('MaishaPay: Exception', [
                'message' => $e->getMessage(),
                'reference' => $transactionId,
            ]);

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'error',
                'message' => 'Erreur de connexion au service de paiement',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifier le statut d'une transaction
     */
    public function checkStatus(string $transactionId): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->get($this->baseUrl . '/payments/' . $transactionId . '/status');

            $result = $response->json();

            Log::info('MaishaPay: Vérification statut', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'response' => $result,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'status' => $result['data']['status'] ?? 'unknown',
                    'message' => $result['message'] ?? '',
                    'data' => $result['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'unknown',
                'message' => $result['message'] ?? 'Impossible de vérifier le statut',
            ];

        } catch (\Exception $e) {
            Log::error('MaishaPay: Erreur vérification statut', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'error',
                'message' => 'Erreur de connexion',
            ];
        }
    }

    /**
     * Vérifier la signature du webhook
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->secretKey);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Obtenir le solde du compte marchand
     */
    public function getBalance(): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->get($this->baseUrl . '/merchant/balance');

            $result = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'balance' => $result['data']['balance'] ?? 0,
                    'currency' => $result['data']['currency'] ?? 'CDF',
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Erreur',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Simuler un paiement (mode sandbox uniquement)
     */
    public function simulatePayment(array $data): array
    {
        if (!$this->sandbox) {
            return [
                'success' => false,
                'message' => 'Simulation disponible uniquement en mode sandbox',
            ];
        }

        $transactionId = 'MP-SIM-' . strtoupper(Str::random(8));
        
        // Simuler un délai de traitement
        usleep(500000); // 0.5 seconde

        Log::info('MaishaPay: Simulation paiement', [
            'transaction_id' => $transactionId,
            'amount' => $data['amount'],
            'phone' => $data['phone'],
        ]);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => 'completed',
            'message' => 'Paiement simulé avec succès (sandbox)',
            'data' => [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'CDF',
                'phone' => $data['phone'],
                'operator' => $data['operator'] ?? $this->detectOperator($data['phone']),
                'simulated' => true,
            ],
        ];
    }

    /**
     * Initier un décaissement (payout/transfert sortant) - API B2C MaishaPay
     * 
     * @param array $data Données du décaissement
     * @return array Résultat de l'opération
     */
    public function initiatePayout(array $data): array
    {
        $transactionId = 'MP-OUT-' . strtoupper(Str::random(10));
        $phone = $this->formatPhone($data['phone']);
        $operator = $data['operator'] ?? $this->detectOperator($phone);

        // Mapper l'opérateur vers le format MaishaPay
        $providerMap = [
            'VODACOM' => 'MPESA',
            'ORANGE' => 'ORANGE',
            'AIRTEL' => 'AIRTEL',
            'AFRICELL' => 'AFRICELL',
        ];
        $provider = $providerMap[strtoupper($operator)] ?? strtoupper($operator);

        // Payload selon la documentation MaishaPay B2C (transfert sortant)
        $payload = [
            'transactionReference' => $transactionId,
            'gatewayMode' => $this->sandbox ? '0' : '1',
            'publicApiKey' => $this->apiKey,
            'secretApiKey' => $this->secretKey,
            'order' => [
                'motif' => $data['description'] ?? 'Retrait VintApp',
                'amount' => (string) $data['amount'],
                'currency' => strtoupper($data['currency'] ?? 'USD'),
                'customerFullName' => $data['customer_name'] ?? 'Client VintApp',
                'customerEmailAdress' => $data['customer_email'] ?? '',
            ],
            'paymentChannel' => [
                'provider' => $provider,
                'walletID' => '+' . $phone,
                'callbackUrl' => $data['callback_url'] ?? route('withdrawals.webhook.provider', ['provider' => 'maishapay']),
            ],
        ];

        Log::info('MaishaPay: Initiation décaissement B2C', [
            'reference' => $transactionId,
            'amount' => $payload['order']['amount'],
            'currency' => $payload['order']['currency'],
            'phone' => '+' . substr($phone, 0, 7) . '***',
            'provider' => $provider,
            'url' => $this->payoutUrl,
        ]);

        // En mode sandbox, simuler le payout
        if ($this->sandbox) {
            return $this->simulatePayout($payload, $transactionId);
        }

        try {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(30)
                ->post($this->payoutUrl, $payload);

            $result = $response->json();

            Log::info('MaishaPay: Réponse payout API B2C', [
                'status' => $response->status(),
                'response' => $result,
            ]);

            // Succès si status 200/201 et pas d'erreurs
            if ($response->successful() && !isset($result['errors'])) {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'maishapay_id' => $result['data']['transactionId'] ?? $result['transactionId'] ?? null,
                    'provider_reference' => $result['data']['transactionId'] ?? $transactionId,
                    'status' => 'processing',
                    'message' => $result['message'] ?? 'Décaissement initié avec succès',
                    'data' => $result['data'] ?? $result,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'failed',
                'message' => $result['title'] ?? $result['message'] ?? 'Erreur lors du décaissement',
                'error' => $result['errors'] ?? $result['error'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('MaishaPay: Exception payout B2C', [
                'message' => $e->getMessage(),
                'reference' => $transactionId,
            ]);

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'error',
                'message' => 'Erreur de connexion au service de paiement',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Simuler un décaissement (mode sandbox uniquement)
     */
    protected function simulatePayout(array $payload, string $transactionId): array
    {
        Log::info('MaishaPay: Simulation décaissement', [
            'transaction_id' => $transactionId,
            'amount' => $payload['amount'],
            'phone' => $payload['phone'],
        ]);

        // Simuler un délai de traitement
        usleep(300000); // 0.3 seconde

        // 95% de réussite en simulation
        $success = rand(1, 100) <= 95;

        if ($success) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'provider_reference' => 'MP-SIM-OUT-' . strtoupper(Str::random(8)),
                'status' => 'processing',
                'message' => 'Décaissement simulé en cours (sandbox)',
                'data' => [
                    'amount' => $payload['amount'],
                    'currency' => $payload['currency'],
                    'phone' => $payload['phone'],
                    'operator' => $payload['operator'],
                    'simulated' => true,
                ],
            ];
        }

        return [
            'success' => false,
            'transaction_id' => $transactionId,
            'status' => 'failed',
            'message' => 'Simulation échec décaissement (test)',
        ];
    }

    /**
     * Vérifier le statut d'un décaissement
     */
    public function checkPayoutStatus(string $transactionId): array
    {
        // Si c'est une simulation
        if (str_starts_with($transactionId, 'MP-SIM-')) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'completed',
                'message' => 'Décaissement simulé complété',
            ];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->get($this->baseUrl . '/payouts/' . $transactionId . '/status');

            $result = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'status' => $result['data']['status'] ?? 'unknown',
                    'message' => $result['message'] ?? '',
                    'data' => $result['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'unknown',
                'message' => $result['message'] ?? 'Impossible de vérifier le statut',
            ];

        } catch (\Exception $e) {
            Log::error('MaishaPay: Erreur vérification statut payout', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'error',
                'message' => 'Erreur de connexion',
            ];
        }
    }

    /**
     * Mapper un opérateur VintApp vers un opérateur MaishaPay
     */
    public function mapOperator(string $provider): ?string
    {
        $mapping = [
            // Format VintApp (lowercase avec underscore)
            'orange_money' => 'ORANGE',
            'airtel_money' => 'AIRTEL',
            'mpesa' => 'MPESA',
            'africell' => 'AFRICELL',
            'illicocash' => null, // Non supporté par MaishaPay
            // Format détecté (uppercase)
            'ORANGE' => 'ORANGE',
            'AIRTEL' => 'AIRTEL',
            'VODACOM' => 'MPESA',
            'MPESA' => 'MPESA',
            'AFRICELL' => 'AFRICELL',
            // Format mixte
            'vodacom' => 'MPESA',
            'orange' => 'ORANGE',
            'airtel' => 'AIRTEL',
        ];

        return $mapping[strtolower($provider)] ?? $mapping[strtoupper($provider)] ?? $mapping[$provider] ?? null;
    }

    /**
     * Vérifier si un opérateur est supporté pour les payouts
     */
    public function isOperatorSupported(string $provider): bool
    {
        return $this->mapOperator($provider) !== null;
    }
}
