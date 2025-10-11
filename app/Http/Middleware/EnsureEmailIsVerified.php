<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    /**
     * Vérifie que l'utilisateur a vérifié son email
     * 
     * Si l'email n'est pas vérifié, redirige vers la page de notification
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si l'utilisateur est connecté et son email n'est pas vérifié
        if ($user && is_null($user->email_verified_at)) {
            // Ne pas rediriger si on est déjà sur les routes de vérification
            if (!$request->routeIs('verification.*') && !$request->routeIs('logout')) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Veuillez vérifier votre email avant d\'accéder à cette fonctionnalité.');
            }
        }

        return $next($request);
    }
}
