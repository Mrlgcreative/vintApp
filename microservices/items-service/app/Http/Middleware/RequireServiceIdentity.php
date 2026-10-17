<?php

namespace App\Http\Middleware;

use App\Services\AuthServiceClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentifie l'appelant en déléguant à auth-service.
 *
 * L'identité obtenue est placée dans les attributs de requête ; aucun
 * contrôleur de ce service ne lit une table users.
 */
class RequireServiceIdentity
{
    public function __construct(private readonly AuthServiceClient $auth) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) $request->bearerToken();

        if ($token === '') {
            return $this->deny('Token manquant.', 401);
        }

        $result = $this->auth->introspect($token);

        if ($result['ok'] === false) {
            return match ($result['reason']) {
                'auth_service_not_configured' => $this->deny('Service non configuré.', 503),
                'auth_service_unreachable' => $this->deny('Service d’authentification injoignable.', 503),
                'invalid_token' => $this->deny('Token invalide ou expiré.', 401),
                default => $this->deny('Authentification impossible.', 503),
            };
        }

        $request->attributes->set('vintapp_identity', $result['identity']);

        return $next($request);
    }

    private function deny(string $message, int $status): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => (object) [],
        ], $status);
    }
}
