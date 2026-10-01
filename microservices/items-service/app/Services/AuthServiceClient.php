<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * items-service ne possède pas la table users : il demande à auth-service
 * qui est l'appelant via /v1/token/introspect.
 *
 * Fail-closed : si auth-service est injoignable ou mal configuré, la requête
 * est refusée. On ne dégrade jamais vers « utilisateur anonyme ».
 */
class AuthServiceClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $sharedSecret,
        private readonly int $timeout = 5,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: rtrim((string) config('items.auth_service.url'), '/'),
            sharedSecret: config('items.auth_service.shared_secret'),
            timeout: (int) config('items.auth_service.timeout', 5),
        );
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && filled($this->sharedSecret);
    }

    /**
     * @return array{ok: bool, identity: ?array, reason: ?string}
     */
    public function introspect(string $bearerToken): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'identity' => null, 'reason' => 'auth_service_not_configured'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($bearerToken)
                ->withHeader('X-Vintapp-Key', (string) $this->sharedSecret)
                ->acceptJson()
                ->post($this->baseUrl.'/v1/token/introspect');
        } catch (ConnectionException $e) {
            Log::error('auth-service injoignable', ['error' => $e->getMessage()]);

            return ['ok' => false, 'identity' => null, 'reason' => 'auth_service_unreachable'];
        }

        if ($response->status() === 401 || $response->status() === 403) {
            return ['ok' => false, 'identity' => null, 'reason' => 'invalid_token'];
        }

        if (! $response->successful()) {
            Log::error('auth-service a renvoyé une erreur', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'identity' => null, 'reason' => 'auth_service_error'];
        }

        $identity = $response->json('data.identity');

        if (! is_array($identity)) {
            return ['ok' => false, 'identity' => null, 'reason' => 'malformed_identity'];
        }

        return ['ok' => true, 'identity' => $identity, 'reason' => null];
    }
}
