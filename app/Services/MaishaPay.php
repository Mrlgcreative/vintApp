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
    protected bool $sandbox;

    public function __construct()
    {
        $this->apiKey = config('services.maishapay.api_key', '');
        $this->secretKey = config('services.maishapay.secret_key', '');
        $this->merchantId = config('services.maishapay.merchant_id', '');
        $this->sandbox = config('services.maishapay.environment', 'sandbox') === 'sandbox';
        $this->baseUrl = $this->sandbox 
            ? 'https://sandbox.maishapay.net/api/v2'
            : 'https://api.maishapay.net/api/v2';
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
     * Initier un paiement Mobile Money
     */
    public function initiatePayment(array $data): array
    {
        $transactionId = 'MP-' . strtoupper(Str::random(12));
        $phone = $this->formatPhone($data['phone']);
        $operator = $data['operator'] ?? $this->detectOperator($phone);

        $payload = [
            'amount' => (float) $data['amount'],
            'currency' => $data['currency'] ?? 'CDF',
            'phone' => $phone,
            'operator' => $operator,
            'reference' => $transactionId,
            'description' => $data['description'] ?? 'Paiement VintApp',
            'callback_url' => $data['callback_url'] ?? route('payments.maishapay.callback'),
            'return_url' => $data['return_url'] ?? route('payments.maishapay.return'),
            'cancel_url' => $data['cancel_url'] ?? route('payments.maishapay.cancel'),
            'metadata' => [
                'buyer_id' => $data['buyer_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'purpose' => $data['purpose'] ?? 'purchase',
            ],
        ];

        Log::info('MaishaPay: Initiation paiement', [
            'reference' => $transactionId,
            'amount' => $payload['amount'],
            'currency' => $payload['currency'],
            'phone' => $phone,
            'operator' => $operator,
        ]);

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->post($this->baseUrl . '/payments/mobile-money', $payload);

            $result = $response->json();

            Log::info('MaishaPay: Réponse API', [
                'status' => $response->status(),
                'response' => $result,
            ]);

            if ($response->successful() && isset($result['success']) && $result['success']) {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'maishapay_id' => $result['data']['transaction_id'] ?? null,
                    'status' => 'pending',
                    'message' => $result['message'] ?? 'Paiement initié. Confirmez sur votre téléphone.',
                    'data' => $result['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'failed',
                'message' => $result['message'] ?? 'Erreur lors de l\'initiation du paiement',
                'error' => $result['error'] ?? null,
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
}
