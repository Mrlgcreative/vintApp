<?php

namespace App\Http\Middleware;

use App\Services\TokenIntrospectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentifie les appels venus des autres microservices.
 *
 * Fail-closed : sans SERVICE_SHARED_SECRET configuré, tout appel est
 * refusé plutôt que d'être accepté.
 */
class RequireServiceSignature
{
    public function __construct(private readonly TokenIntrospectionService $introspection) {}

    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('identity.service_shared_secret');

        if (! $secret) {
            return response()->json([
                'success' => false,
                'message' => 'Service non configuré.',
                'data' => null,
                'meta' => (object) [],
            ], 503);
        }

        $provided = (string) $request->header('X-Vintapp-Key');

        if (! hash_equals($secret, $provided)) {
            return response()->json([
                'success' => false,
                'message' => 'Signature de service invalide.',
                'data' => null,
                'meta' => (object) [],
            ], 401);
        }

        $token = (string) $request->bearerToken();

        if ($token === '') {
            return response()->json([
                'success' => false,
                'message' => 'Token manquant.',
                'data' => null,
                'meta' => (object) [],
            ], 400);
        }

        $identity = $this->introspection->resolve($token);

        if ($identity === null) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide ou expiré.',
                'data' => null,
                'meta' => (object) [],
            ], 401);
        }

        $request->attributes->set('vintapp_identity', $identity);

        return $next($request);
    }
}
