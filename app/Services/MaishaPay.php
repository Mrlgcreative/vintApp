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

    public function __construct(?array $config = null)
    {
        $serviceConfig = $config;

        if (empty($serviceConfig) && function_exists('app') && app()->bound('config')) {
            $serviceConfig = app('config')->get('services.maishapay', []);
        }

        if (empty($serviceConfig)) {
            $serviceConfig = [
                'api_key' => getenv('MAISHAPAY_API_KEY') ?: '',
                'secret_key' => getenv('MAISHAPAY_SECRET_KEY') ?: '',
                'merchant_id' => getenv('MAISHAPAY_MERCHANT_ID') ?: '',
            ];
        }

        $this->apiKey = $serviceConfig['api_key'] ?? '';
        $this->secretKey = $serviceConfig['secret_key'] ?? '';
        $this->merchantId = $serviceConfig['merchant_id'] ?? '';

        $this->baseUrl = 'https://marchand.maishapay.online/api';
        $this->collectUrl = 'https://marchand.maishapay.online/api/collect/v2/store/mobileMoney';
        $this->payoutUrl = 'https://marchand.maishapay.online/api/b2c/store/transfert/mobilemoney';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->secretKey);
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function detectOperator(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

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

    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '243')) {
            return $phone;
        }

        return '243' . $phone;
    }

    public function initiatePayment(array $data): array
    {
        $transactionId = $data['transaction_id'] ?? ('MP-' . strtoupper(Str::random(12)));
        $phone = $this->formatPhone($data['phone']);
        $operator = $data['operator'] ?? $this->detectOperator($phone);

        $providerMap = [
            'VODACOM' => 'MPESA',
            'ORANGE' => 'ORANGE',
            'AIRTEL' => 'AIRTEL',
            'AFRICELL' => 'AFRICELL',
        ];
        $provider = $providerMap[strtoupper($operator)] ?? strtoupper($operator);

        $payload = [
            'transactionReference' => $transactionId,
            'gatewayMode' => '1',
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

            Log::info('MaishaPay: Reponse API v2', [
                'status' => $response->status(),
                'response' => $result,
            ]);

            if ($response->successful() && !isset($result['errors'])) {
                $payloadData = $result['data'] ?? $result;
                $providerTransactionId = $payloadData['transactionId'] ?? $payloadData['id'] ?? null;
                $statusReference = $payloadData['originatingTransactionId']
                    ?? $payloadData['transactionReference']
                    ?? $payloadData['reference']
                    ?? $payloadData['id']
                    ?? $transactionId;

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'maishapay_id' => $providerTransactionId,
                    'status_reference' => $statusReference,
                    'status' => 'pending',
                    'message' => $result['message'] ?? 'Paiement initie. Confirmez sur votre telephone.',
                    'data' => $payloadData,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'failed',
                'message' => $result['title'] ?? $result['message'] ?? "Erreur lors de l'initiation du paiement",
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

    public function resolveStatusReference(array $result): ?string
    {
        $data = $result['data'] ?? $result;

        foreach (['originatingTransactionId', 'transactionReference', 'transactionId', 'reference', 'id'] as $key) {
            $value = $data[$key] ?? null;
            if (!empty($value) && is_string($value)) {
                return $value;
            }

            if (!empty($value) && !is_array($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    public function buildStatusUrl(string $transactionId, ?array $result = null): string
    {
        $reference = $result ? $this->resolveStatusReference($result) : null;

        if ($reference) {
            return $this->baseUrl . '/payments/' . urlencode($reference) . '/status';
        }

        return $this->baseUrl . '/payments/' . urlencode($transactionId) . '/status';
    }

    public function checkStatus(string $transactionId): array
    {
        try {
            $payload = [
                'gatewayMode' => 1,
                'publicApiKey' => $this->apiKey,
                'secretApiKey' => $this->secretKey,
                'transactionId' => $transactionId,
            ];

            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(15)
                ->post($this->baseUrl . '/transaction/rest/v2/check?useRef=1', $payload);

            $result = $response->json();

            Log::info('MaishaPay: Verification statut', [
                'transaction_id' => $transactionId,
                'status_code' => $response->status(),
                'response' => $result,
            ]);

            if ($response->successful()) {
                $status = $result['data']['transactionStatus']
                    ?? $result['data']['status']
                    ?? $result['transactionStatus']
                    ?? $result['status']
                    ?? $result['state']
                    ?? 'unknown';

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'status' => $status,
                    'message' => $result['message'] ?? $result['data']['message'] ?? '',
                    'data' => $result['data'] ?? $result,
                ];
            }

            if (in_array($response->status(), [404, 500], true)) {
                return [
                    'success' => false,
                    'transaction_id' => $transactionId,
                    'status' => 'pending',
                    'message' => 'Statut MaishaPay indisponible pour le moment',
                    'retryable' => false,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => $result['data']['transactionStatus'] ?? $result['data']['status'] ?? $result['status'] ?? 'unknown',
                'message' => $result['message'] ?? 'Impossible de verifier le statut',
            ];

        } catch (\Exception $e) {
            Log::error('MaishaPay: Erreur verification statut', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'message' => 'Erreur temporaire lors de la vérification du statut',
                'retryable' => false,
            ];
        }
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->secretKey);
        return hash_equals($expectedSignature, $signature);
    }

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

    public function initiatePayout(array $data): array
    {
        $transactionId = 'MP-OUT-' . strtoupper(Str::random(10));
        $phone = $this->formatPhone($data['phone']);
        $operator = $data['operator'] ?? $this->detectOperator($phone);

        $providerMap = [
            'VODACOM' => 'MPESA',
            'ORANGE' => 'ORANGE',
            'AIRTEL' => 'AIRTEL',
            'AFRICELL' => 'AFRICELL',
        ];
        $provider = $providerMap[strtoupper($operator)] ?? strtoupper($operator);

        $payload = [
            'transactionReference' => $transactionId,
            'gatewayMode' => '1',
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

        Log::info('MaishaPay: Initiation decaissement B2C', [
            'reference' => $transactionId,
            'amount' => $payload['order']['amount'],
            'currency' => $payload['order']['currency'],
            'phone' => '+' . substr($phone, 0, 7) . '***',
            'provider' => $provider,
            'url' => $this->payoutUrl,
        ]);

        try {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(30)
                ->post($this->payoutUrl, $payload);

            $result = $response->json();

            Log::info('MaishaPay: Reponse payout API B2C', [
                'status' => $response->status(),
                'response' => $result,
            ]);

            if ($response->successful() && !isset($result['errors'])) {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'maishapay_id' => $result['data']['transactionId'] ?? $result['data']['reference'] ?? $result['data']['id'] ?? $result['transactionId'] ?? $result['transactionReference'] ?? $result['reference'] ?? $result['id'] ?? null,
                    'provider_reference' => $result['data']['transactionId'] ?? $transactionId,
                    'status' => 'processing',
                    'message' => $result['message'] ?? 'Decaissement initie avec succes',
                    'data' => $result['data'] ?? $result,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'failed',
                'message' => $result['title'] ?? $result['message'] ?? "Erreur lors du decaissement",
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

    public function checkPayoutStatus(string $transactionId): array
    {
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
                'message' => $result['message'] ?? 'Impossible de verifier le statut',
            ];

        } catch (\Exception $e) {
            Log::error('MaishaPay: Erreur verification statut payout', [
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

    public function mapOperator(string $provider): ?string
    {
        $mapping = [
            'orange_money' => 'ORANGE',
            'airtel_money' => 'AIRTEL',
            'mpesa' => 'MPESA',
            'africell' => 'AFRICELL',
            'illicocash' => null,
            'ORANGE' => 'ORANGE',
            'AIRTEL' => 'AIRTEL',
            'VODACOM' => 'MPESA',
            'MPESA' => 'MPESA',
            'AFRICELL' => 'AFRICELL',
            'vodacom' => 'MPESA',
            'orange' => 'ORANGE',
            'airtel' => 'AIRTEL',
        ];

        return $mapping[strtolower($provider)] ?? $mapping[strtoupper($provider)] ?? $mapping[$provider] ?? null;
    }

    public function isOperatorSupported(string $provider): bool
    {
        return $this->mapOperator($provider) !== null;
    }
}
