<?php

namespace App\Http\Controllers;

use App\Models\PaymentCallback;
use App\Services\ProviderPayloadParser;
use App\Services\WebhookProcessor;
use App\Services\WebhookReplayGuard;
use App\Services\WebhookSignatureVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Réception des webhooks opérateurs.
 *
 * Aucun token : l'authentification est la signature de l'opérateur, validée
 * par WebhookSignatureVerifier en fail-closed.
 */
class WebhookController extends ApiController
{
    public function __construct(
        private readonly WebhookSignatureVerifier $verifier,
        private readonly ProviderPayloadParser $parser,
        private readonly WebhookProcessor $processor,
        private readonly WebhookReplayGuard $replayGuard,
    ) {}

    public function handle(Request $request, string $provider): JsonResponse
    {
        if (! array_key_exists($provider, config('payments.providers', []))) {
            return $this->errorResponse("Opérateur inconnu : {$provider}", 404);
        }

        // 1. Signature d'abord : aucun traitement avant.
        $signature = $this->verifier->verify($request, $provider);

        if (! $signature['ok']) {
            return $this->errorResponse(match ($signature['reason']) {
                'secret_not_configured' => 'Webhook non acceptable pour cet opérateur.',
                default => 'Signature invalide.',
            }, 403);
        }

        // 2. Parsing.
        $parsed = $this->parser->parse($request, $provider);

        if ($parsed === null) {
            return $this->errorResponse('Payload illisible ou sans référence exploitable.', 400);
        }

        // 3. Persistance du callback brut avant tout effet.
        $callback = PaymentCallback::create([
            'provider' => $provider,
            'status' => $parsed['status'],
            'amount' => $parsed['amount'] ?? 0,
            'currency' => $parsed['currency'] ?? 'USD',
            'phone_number' => $parsed['phone_number'] ?? '',
            'callback_type' => 'webhook',
            'external_transaction_id' => $parsed['transaction_id'],
            'reference' => $parsed['reference'],
            'raw_payload' => $request->all(),
            'parsed_data' => $parsed,
            'ip_address' => $request->ip(),
            'is_verified' => true,
            'is_processed' => false,
        ]);

        Log::info("Webhook {$provider} reçu", [
            'callback_id' => $callback->id,
            'status' => $parsed['status'],
            'ip' => $request->ip(),
        ]);

        // 4. Rattachement et effets, une seule fois.
        $result = $this->processor->process($callback);
        $this->replayGuard->remember(
            hash('sha256', implode('|', [
                $provider,
                $parsed['transaction_id'] ?? '',
                $parsed['reference'] ?? '',
                $parsed['status'] ?? '',
                (string) ($parsed['amount'] ?? ''),
            ]))
        );

        if ($result['code'] === 'unmatched') {
            // La signature est valide mais rien ne correspond : on le remonte
            // sans faire d'effet, l'opérateur saura qu'il doit rejouer.
            return $this->errorResponse('Aucune transaction correspondante.', 404);
        }

        return $this->successResponse([
            'callback_id' => $callback->public_id,
            'status' => $parsed['status'],
            'outcome' => $result['code'],
        ], 'Callback traité', [], 200);
    }
}
