<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Vérifie l'authenticité d'un webhook opérateur.
 *
 * Règle absolue : en cas de doute, on refuse. Un secret non configuré ou
 * laissé à un placeholder vaut refus, jamais « on laisse passer en local ».
 * Le comportement est identique quel que soit APP_ENV.
 */
class WebhookSignatureVerifier
{
    /**
     * @return array{ok: bool, reason: ?string}
     */
    public function verify(Request $request, string $provider): array
    {
        $config = config("payments.providers.{$provider}");

        if (! is_array($config)) {
            Log::warning('Webhook : opérateur inconnu', ['provider' => $provider]);

            return $this->fail('unknown_provider');
        }

        $secret = $this->resolveSecret($config);

        if ($secret === null) {
            // C'est le cas le plus important : secret absent ou placeholder.
            Log::warning('Webhook refusé : secret opérateur absent ou placeholder', [
                'provider' => $provider,
                'ip' => $request->ip(),
            ]);

            return $this->fail('secret_not_configured');
        }

        $valid = match ($config['strategy']) {
            'hmac_body' => $this->verifyHmac($request, $secret, $config),
            'api_key_header' => $this->verifyHeader($request, $secret, $config),
            'bearer' => $this->verifyBearer($request, $secret),
            'payload_field' => $this->verifyPayloadField($request, $secret, $config),
            'notify_receipt' => $this->verifyNotifyReceipt($request, $secret),
            default => $this->fail('unknown_strategy'),
        };

        if (! $valid) {
            Log::warning('Webhook refusé : signature invalide', [
                'provider' => $provider,
                'ip' => $request->ip(),
            ]);
        }

        return $valid
            ? ['ok' => true, 'reason' => null]
            : ['ok' => false, 'reason' => 'invalid_signature'];
    }

    /**
     * Le secret attendu, ou null s'il est absent / placeholder.
     */
    private function resolveSecret(array $config): ?string
    {
        $secret = env($config['credential'] ?? '');

        if (! is_string($secret) || trim($secret) === '') {
            return null;
        }

        $placeholders = (array) config('payments.placeholder_secrets', []);

        foreach ($placeholders as $placeholder) {
            if (strcasecmp(trim($secret), (string) $placeholder) === 0) {
                return null;
            }
        }

        return $secret;
    }

    private function verifyHmac(Request $request, string $secret, array $config): bool
    {
        $provided = (string) $request->header($config['header'] ?? 'X-Signature');

        if ($provided === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $this->rawBody($request), $secret);

        return hash_equals($expected, $provided);
    }

    private function verifyHeader(Request $request, string $secret, array $config): bool
    {
        $provided = (string) $request->header($config['header'] ?? 'X-Api-Key');

        return $provided !== '' && hash_equals($secret, $provided);
    }

    private function verifyBearer(Request $request, string $secret): bool
    {
        $provided = (string) $request->header('Authorization');

        return $provided !== '' && hash_equals('Bearer '.$secret, $provided);
    }

    private function verifyPayloadField(Request $request, string $secret, array $config): bool
    {
        $provided = (string) $request->input($config['field'] ?? 'secret');

        return $provided !== '' && hash_equals($secret, $provided);
    }

    /**
     * CinetPay n'envoie pas de HMAC sur son endpoint notify : la preuve est
     * cryptographique (le contenu du callback est signé par la clé du shop).
     * On exige donc que la clé attendue soit présente dans le payload ET que
     * le montant annoncé corresponde à celui de la notification.
     */
    private function verifyNotifyReceipt(Request $request, string $secret): bool
    {
        $notifiedKey = (string) $request->input('key');

        if ($notifiedKey === '' || ! hash_equals($secret, $notifiedKey)) {
            return false;
        }

        return $request->filled('TransactionId') && $request->filled('Amount');
    }

    /**
     * Corps brut, indispensable au HMAC : re-sérialiser $request->all()
     * peut produire une chaîne différente de celle signée par l'opérateur.
     */
    private function rawBody(Request $request): string
    {
        return $request->getContent();
    }

    private function fail(string $reason): array
    {
        return ['ok' => false, 'reason' => $reason];
    }
}
