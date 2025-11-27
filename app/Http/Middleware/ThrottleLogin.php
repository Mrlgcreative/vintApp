<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->throttleKey($request);

        // Max 5 tentatives par minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, 60); // 60 secondes

        $response = $next($request);

        // Si authentification réussie, effacer le compteur
        if ($response->getStatusCode() === 200) {
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('email') ?? '') . '|' . $request->ip();
    }
}
