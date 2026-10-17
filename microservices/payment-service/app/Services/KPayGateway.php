<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client sortant K-PAY.
 *
 * K-PAY est un agrégateur Mobile Money : une seule URL de base, deux clés
 * (X-API-Key + X-Secret-Key) et deux modes d'encaissement :
 *  - USSD    : on fournit l'opérateur et le numéro, le client valide par USSD ;
 *  - GATEWAY : on fournit une URL de retour, le client paie sur la page hébergée.
 *
 * L'API est asynchrone : l'initiation répond PENDING/PROCESSING puis le statut
 * final arrive par webhook (source de vérité) ou via GET /payments/{id}.
 */
class KPayGateway
{
    public const MAX_AMOUNT_CDF = 2025313.5;

    /**
     * Correspondance opérateur local -> code K-PAY (RDC).
     */
    private const OPERATOR_MAP = [
        'VODACOM' => 'VODACOM_MPESA_COD',
        'AIRTEL' => 'AIRTEL_COD',
        'ORANGE' => 'ORANGE_COD',
    ];

    public function __construct(private readonly array $config) {}

    public static function fromConfig(): self
    {
        return new self((array) config('kpay', []));
    }

    public function isConfigured(): bool
    {
        return $this->apiKey() !== '' && $this->secretKey() !== '';
    }

    public function isEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }

    public function isSandbox(): bool
    {
        return strtolower((string) ($this->config['environment'] ?? 'production')) === 'sandbox'
            || str_starts_with($this->apiKey(), 'kpay_test_');
    }

    public function defaultProvider(): string
    {
        return (string) ($this->config['default_provider'] ?? 'VODACOM_MPESA_COD');
    }

    /**
     * Numéro au format international attendu par K-PAY : chiffres uniquement,
     * indicatif pays, sans '+' ni zéro initial.
     */
    public function normalizePhoneNumber(string $phone): string
    {
        $digits = (string) preg_replace('/[^\d]/', '', $phone);

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '243'.substr($digits, 1);
        }

        if (strlen($digits) === 9 && ! str_starts_with($digits, '0')) {
            return '243'.$digits;
        }

        return $digits;
    }

    public function operatorCode(?string $operator): string
    {
        return self::OPERATOR_MAP[strtoupper((string) $operator)] ?? $this->defaultProvider();
    }

    /**
     * Initie un encaissement. `externalId` est notre référence : elle revient
     * telle quelle dans les webhooks et sert au rattachement.
     *
     * @param  array{mode?: ?string, operator?: ?string, phone_number?: ?string, return_url?: ?string, cancel_url?: ?string, description?: ?string}  $options
     * @return array{ok: bool, external_id: string, payment_id: ?string, reference: ?string, provider_reference: ?string, status: ?string, mode: string, gateway_url: ?string, raw: array, message: string}
     */
    public function initiate(Payment $payment, array $options = []): array
    {
        $externalId = (string) ($payment->reference ?: $payment->public_id);
        $phone = $options['phone_number'] ?? $payment->phone_number;
        $mode = strtoupper((string) ($options['mode'] ?? ($phone ? 'USSD' : 'GATEWAY')));

        $body = [
            'amount' => (int) $payment->amount,
            'currency' => $payment->currency,
            'externalId' => $externalId,
            'description' => $options['description'] ?? $payment->designation ?? ('Paiement '.$externalId),
        ];

        if ($mode === 'GATEWAY') {
            $returnUrl = $options['return_url'] ?? ($this->config['return_url'] ?? null);
            $cancelUrl = $options['cancel_url'] ?? ($this->config['cancel_url'] ?? null);

            if (! empty($returnUrl)) {
                $body['returnUrl'] = $returnUrl;
            }

            if (! empty($cancelUrl)) {
                $body['cancelUrl'] = $cancelUrl;
            }
        } else {
            if (empty($phone)) {
                return $this->failure($externalId, $mode, 'Numéro Mobile Money requis pour un paiement USSD.');
            }

            $body['provider'] = $this->operatorCode($options['operator'] ?? null);
            $body['phoneNumber'] = $this->normalizePhoneNumber((string) $phone);
        }

        $response = Http::withHeaders($this->headers())
            ->timeout($this->timeout())
            ->acceptJson()
            ->retry(2, 100, throw: false)
            ->post($this->baseUrl().'/api/v1/payments/init', $body);

        $data = $response->json() ?: [];

        if (! $response->successful()) {
            Log::warning('K-PAY: échec initiation', [
                'http_code' => $response->status(),
                'external_id' => $externalId,
            ]);

            return $this->failure(
                $externalId,
                $mode,
                (string) ($data['message'] ?? ('Erreur K-PAY ('.$response->status().')')),
                $data,
            );
        }

        return [
            'ok' => true,
            'external_id' => $externalId,
            'payment_id' => isset($data['id']) ? (string) $data['id'] : null,
            'reference' => isset($data['reference']) ? (string) $data['reference'] : null,
            'provider_reference' => isset($data['providerReference']) ? (string) $data['providerReference'] : null,
            'status' => strtoupper((string) ($data['status'] ?? 'PENDING')),
            'mode' => (string) ($data['mode'] ?? $mode),
            'gateway_url' => isset($data['gatewayUrl']) ? (string) $data['gatewayUrl'] : null,
            'raw' => $data,
            'message' => (string) ($data['message'] ?? 'Paiement initié.'),
        ];
    }

    /**
     * Interroge le statut d'un paiement. GET /api/v1/payments/{id}
     *
     * @return array{ok: bool, raw: array, message: string}
     */
    public function checkStatus(string $paymentId): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout($this->timeout())
            ->acceptJson()
            ->retry(2, 100, throw: false)
            ->get($this->baseUrl().'/api/v1/payments/'.rawurlencode($paymentId));

        $data = $response->json() ?: [];

        return [
            'ok' => $response->successful(),
            'raw' => $data,
            'message' => (string) ($data['message'] ?? ($response->successful() ? 'OK' : 'Erreur K-PAY')),
        ];
    }

    private function headers(): array
    {
        return [
            'X-API-Key' => $this->apiKey(),
            'X-Secret-Key' => $this->secretKey(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    private function baseUrl(): string
    {
        return rtrim((string) ($this->config['base_url'] ?? 'https://admin.kpay.site'), '/');
    }

    private function apiKey(): string
    {
        return (string) ($this->config['api_key'] ?? '');
    }

    private function secretKey(): string
    {
        return (string) ($this->config['secret_key'] ?? '');
    }

    private function timeout(): int
    {
        return (int) ($this->config['timeout'] ?? 30);
    }

    private function failure(string $externalId, string $mode, string $message, array $raw = []): array
    {
        return [
            'ok' => false,
            'external_id' => $externalId,
            'payment_id' => null,
            'reference' => null,
            'provider_reference' => null,
            'status' => null,
            'mode' => $mode,
            'gateway_url' => null,
            'raw' => $raw,
            'message' => $message,
        ];
    }
}
