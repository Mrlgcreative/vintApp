<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * K-PAY — Mobile Money Aggregator
 *
 * Encaisse en Mobile Money (USSD) ou via page hébergée (GATEWAY), et reverse
 * vers un compte Mobile Money dans 12 pays africains. Une seule URL de base,
 * deux clés : kpay_test_* (sandbox) et kpay_live_* (production).
 *
 * Authentification : headers X-API-Key + X-Secret-Key.
 * API asynchrone : l'initiation répond PENDING puis le statut final est reçu
 * via webhook (source de vérité) ou en interrogeant GET /payments/:id.
 *
 * Documentation : https://kpay.site/documentation
 */
class KPay
{
    public const MAX_AMOUNT_CDF = 2025313.5;

    protected string $apiKey;
    protected string $secretKey;
    protected string $environment;
    protected string $baseUrl;
    protected string $webhookSecret;
    protected string $gatewaySecret;
    protected string $defaultProvider;
    protected ?int $timeout;

    public function __construct(?array $config = null)
    {
        $config = $config ?? app('config')->get('services.kpay', []);

        $this->apiKey = $config['api_key'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->environment = $config['environment'] ?? 'production';
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://admin.kpay.site', '/');
        $this->webhookSecret = $config['webhook_secret'] ?? '';
        $this->gatewaySecret = $config['gateway_secret'] ?? $this->webhookSecret;
        $this->defaultProvider = $config['default_provider'] ?? 'VODACOM_MPESA_COD';
        $this->timeout = isset($config['timeout']) ? (int) $config['timeout'] : 30;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->secretKey);
    }

    public function isEnabled(): bool
    {
        return (bool) config('services.kpay.enabled', false);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isSandbox(): bool
    {
        return strtolower($this->environment) === 'sandbox'
            || str_starts_with($this->apiKey, 'kpay_test_');
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getDefaultProvider(): string
    {
        return $this->defaultProvider;
    }

    /**
     * Retourne les URLs de callback configurées (pour le dashboard/debug).
     *
     * @return array{generic: ?string, deposit: ?string, withdraw: ?string, refund: ?string}
     */
    public function getCallbackUrls(): array
    {
        $config = config('services.kpay', []);

        return [
            'generic' => $config['callback_url'] ?? null,
            'deposit' => $config['callback_deposit'] ?? null,
            'withdraw' => $config['callback_withdraw'] ?? null,
            'refund' => $config['callback_refund'] ?? null,
        ];
    }

    public function getHeaders(): array
    {
        return [
            'X-API-Key' => $this->apiKey,
            'X-Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Normalise un numéro au format international attendu par K-PAY
     * (chiffres uniquement, indicatif pays, sans '+' ni zéro initial).
     */
    public function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^\d]/', '', $phone);

        // Numéro local congolais avec zéro initial (ex: 0812345678)
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '243' . substr($phone, 1);
        }

        // Numéro local sans indicatif (ex: 812345678 -> RDC)
        if (strlen($phone) === 9 && !str_starts_with($phone, '0')) {
            return '243' . $phone;
        }

        return $phone;
    }

    /**
     * Génère un identifiant métier unique (externalId).
     */
    public function generateExternalId(): string
    {
        return 'KPAY-' . strtoupper(Str::random(10)) . '-' . time();
    }

    /**
     * Mapping des statuts K-PAY vers les statuts internes vintApp.
     */
    public function mapStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'COMPLETED', 'SUCCESS' => 'completed',
            'FAILED', 'REJECTED' => 'failed',
            'CANCELLED', 'CANCELED' => 'cancelled',
            'PROCESSING' => 'processing',
            default => 'pending',
        };
    }

    public function isFinalStatus(string $status): bool
    {
        return in_array($this->mapStatus($status), ['completed', 'failed', 'cancelled'], true);
    }

    /**
     * Initie un paiement (encaissement) — mode USSD ou GATEWAY.
     *
     * USSD : fournir $data['provider'] + $data['phoneNumber'].
     * GATEWAY : fournir $data['returnUrl'] (+ successUrl/cancelUrl optionnels),
     * sans phoneNumber / provider. La réponse contient alors `gatewayUrl`.
     *
     * @return array {success, status, payment_id, mode, gateway_url, data, message}
     */
    public function initiatePayment(array $data): array
    {
        $result = $this->request('POST', '/api/v1/payments/init', $data);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'status' => strtoupper((string) ($result['data']['status'] ?? 'PENDING')),
            'payment_id' => $result['data']['id'] ?? null,
            'reference' => $result['data']['reference'] ?? null,
            'provider_reference' => $result['data']['providerReference'] ?? null,
            'mode' => $result['data']['mode'] ?? 'USSD',
            'gateway_url' => $result['data']['gatewayUrl'] ?? null,
            'expires_at' => $result['data']['expiresAt'] ?? null,
            'metadata' => $result['data'],
            'message' => $result['data']['message'] ?? 'Paiement initié.',
        ];
    }

    /**
     * Vérifie le statut d'un paiement. GET /api/v1/payments/{id}
     */
    public function checkPaymentStatus(string $id): array
    {
        return $this->request('GET', '/api/v1/payments/' . rawurlencode($id));
    }

    /**
     * Initie un retrait (payout) — mode USSD ou GATEWAY.
     * POST /api/v1/payments/withdraw
     *
     * @return array {success, status, withdrawal_id, reference, mode, gateway_url, data, message}
     */
    public function initiateWithdrawal(array $data): array
    {
        $result = $this->request('POST', '/api/v1/payments/withdraw', $data);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'status' => strtoupper((string) ($result['data']['status'] ?? 'PENDING')),
            'withdrawal_id' => $result['data']['id'] ?? null,
            'reference' => $result['data']['reference'] ?? null,
            'provider_reference' => $result['data']['providerReference'] ?? null,
            'mode' => $result['data']['mode'] ?? 'USSD',
            'gateway_url' => $result['data']['gatewayUrl'] ?? null,
            'metadata' => $result['data'],
            'message' => $result['data']['message'] ?? 'Retrait initié.',
        ];
    }

    /**
     * Vérifie le statut d'un retrait. GET /api/v1/payments/withdraw/{id}
     */
    public function checkWithdrawalStatus(string $id): array
    {
        return $this->request('GET', '/api/v1/payments/withdraw/' . rawurlencode($id));
    }

    /**
     * Détecte l'opérateur (provider) et le pays d'un numéro mobile money.
     * POST /api/v1/payments/predict-provider
     *
     * @return array {success, country, provider, phoneNumber, message}
     */
    public function predictProvider(string $phoneNumber): array
    {
        $result = $this->request('POST', '/api/v1/payments/predict-provider', [
            'phoneNumber' => $this->normalizePhoneNumber($phoneNumber),
        ]);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'country' => $result['data']['country'] ?? null,
            'provider' => $result['data']['provider'] ?? null,
            'phoneNumber' => $result['data']['phoneNumber'] ?? $this->normalizePhoneNumber($phoneNumber),
        ];
    }

    /**
     * Retourne le solde du wallet K-PAY. GET /api/v1/payments/balance
     *
     * @return array {success, balances: [{currency, balance, reservedBalance, availableBalance}], message}
     */
    public function getBalance(): array
    {
        $result = $this->request('GET', '/api/v1/payments/balance');

        if (!$result['success']) {
            return $result;
        }

        $balances = $result['data'];
        if (is_array($balances) && isset($balances['currency'])) {
            $balances = [$balances];
        }

        return ['success' => true, 'balances' => $balances ?? [], 'message' => 'Solde récupéré.'];
    }

    /**
     * Vérifie la signature d'un webhook K-PAY entrant.
     *
     * Le header `X-KPAY-Signature` contient un HMAC-SHA256 (hex) calculé sur le
     * body RAW reçu (pas ré-sérialisé). Comparaison en temps constant.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = (string) $request->header('X-KPAY-Signature', '');

        if (empty($signature) || empty($this->webhookSecret)) {
            return false;
        }

        $raw = $request->getContent();
        $expected = hash_hmac('sha256', $raw, $this->webhookSecret);

        if (strlen($signature) !== strlen($expected)) {
            return false;
        }

        return hash_equals($expected, $signature);
    }

    /**
     * Vérifie la signature du retour de la page hébergée (GATEWAY).
     *
     * Le retour est : {returnUrl}?status=...&reference=...&externalId=...&ts=...&sig=...
     * avec HMAC-SHA256(hex) de "status|reference|externalId|ts" signé avec le
     * gateway secret. On rejette si ts date de plus de 10 minutes (anti-replay).
     */
    public function verifyReturnSignature(array $query): bool
    {
        $status = (string) ($query['status'] ?? '');
        $reference = (string) ($query['reference'] ?? '');
        $externalId = (string) ($query['externalId'] ?? '');
        $ts = (string) ($query['ts'] ?? '');
        $sig = (string) ($query['sig'] ?? '');

        if (empty($status) || empty($reference) || empty($ts) || empty($sig) || empty($this->gatewaySecret)) {
            return false;
        }

        // Anti-replay : le timestamp ne doit pas dater de plus de 10 minutes
        if (!ctype_digit($ts) || (time() * 1000 - (int) $ts) > 10 * 60 * 1000) {
            return false;
        }

        $stringToSign = $status . '|' . $reference . '|' . $externalId . '|' . $ts;
        $expected = hash_hmac('sha256', $stringToSign, $this->gatewaySecret);

        if (strlen($sig) !== strlen($expected)) {
            return false;
        }

        return hash_equals($expected, $sig);
    }

    /**
     * Envoie une requête HTTP vers l'API K-PAY et normalise la réponse.
     *
     * @return array {success, status, data (array), message}
     */
    protected function request(string $method, string $path, array $data = []): array
    {
        $url = $this->baseUrl . $path;

        try {
            $client = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->retry(2, 100, throw: false);

            $response = match (strtoupper($method)) {
                'POST' => $client->post($url, $data),
                'GET' => $client->get($url),
                default => throw new \InvalidArgumentException("Méthode HTTP non supportée: {$method}"),
            };

            $body = $response->json() ?: [];

            if ($response->successful()) {
                return $this->normalizeSuccess($body);
            }

            return $this->normalizeFailure($body, $response->status());
        } catch (\Throwable $e) {
            Log::error('K-PAY: échec requête', [
                'method' => $method,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'data' => [],
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function normalizeSuccess(array $body): array
    {
        return [
            'success' => true,
            'status' => (string) ($body['status'] ?? 'OK'),
            'data' => $body,
            'message' => $body['message'] ?? 'OK',
        ];
    }

    protected function normalizeFailure(array $body, int $httpCode): array
    {
        $message = $body['message'] ?? 'Erreur K-PAY (' . $httpCode . ')';

        Log::warning('K-PAY: réponse en échec', [
            'http_code' => $httpCode,
            'body' => $body,
        ]);

        return [
            'success' => false,
            'status' => 'error',
            'http_code' => $httpCode,
            'error' => $body['error'] ?? null,
            'statusCode' => $body['statusCode'] ?? $httpCode,
            'data' => $body,
            'message' => $message,
        ];
    }
}