<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Si l'utilisateur n'est pas connecté, laisser passer (le middleware auth s'en chargera)
        if (!$user) {
            return $next($request);
        }
        
        // Si l'utilisateur a 2FA activé mais n'a pas vérifié le code
        if ($user->google2fa_enabled && !session('2fa_verified')) {
            // Si l'utilisateur doit passer par le challenge 2FA
            if (session('2fa_required')) {
                // Autoriser uniquement l'accès à la page de challenge et logout
                if (!$request->routeIs('two-factor.challenge') && 
                    !$request->routeIs('two-factor.verify') && 
                    !$request->routeIs('logout')) {
                    return redirect()->route('two-factor.challenge');
                }
            } else {
                // L'utilisateur n'a pas encore passé par le processus de connexion avec 2FA
                // Le rediriger vers la page de challenge
                session(['2fa_required' => true, '2fa_user_id' => $user->id]);
                return redirect()->route('two-factor.challenge');
            }
        }
        
        return $next($request);
    }
}
