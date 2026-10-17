<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IntrospectToken
{
    /**
     * Garde les routes internes du service : le token doit être valide,
     * non expiré, et rattaché à une session non révoquée.
     *
     * Un token 2FA en attente est refusé ici — il ne donne accès qu'à
     * POST /v1/two-factor/verify.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié.',
                'data' => null,
                'meta' => (object) [],
            ], 401);
        }

        $token = $user->currentAccessToken();

        if ($token && method_exists($token, 'can') && ! $token->can('*')) {
            return response()->json([
                'success' => false,
                'message' => 'Token 2FA en attente. Vérifiez votre code.',
                'data' => null,
                'meta' => (object) [],
            ], 403);
        }

        if ($token) {
            $revoked = $user->sessions()
                ->where('session_id', 'sanctum-'.$token->id)
                ->where('is_active', false)
                ->exists();

            if ($revoked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session révoquée.',
                    'data' => null,
                    'meta' => (object) [],
                ], 401);
            }
        }

        return $next($request);
    }
}
