<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class TrackUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Web (session) ou API mobile (token Sanctum)
        $user = Auth::user() ?: Auth::guard('sanctum')->user();

        if ($user) {
            UserSession::trackSession(
                $user->id,
                $this->resolveSessionId($request, $user),
                $request
            );
        }

        return $next($request);
    }

    /**
     * Identifier la session : token Sanctum pour l'API, session PHP pour le web.
     */
    protected function resolveSessionId(Request $request, $user): string
    {
        $token = $user->currentAccessToken();

        if ($token) {
            return 'sanctum-' . $token->id;
        }

        return $request->session()->getId();
    }
}
