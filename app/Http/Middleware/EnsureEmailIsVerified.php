<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureEmailIsVerified
{
    /**
     * Vérifie que l'utilisateur a vérifié son email
     * 
     * Si l'email n'est pas vérifié, redirige vers la page de saisie du code
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        Log::info('Middleware EnsureEmailIsVerified appelé', [
            'user_id' => $user ? $user->id : null,
            'email_verified_at' => $user ? $user->email_verified_at : null,
            'route' => $request->route() ? $request->route()->getName() : null
        ]);

        // Si l'utilisateur est connecté et son email n'est pas vérifié
        if ($user && is_null($user->email_verified_at)) {
            // Ne pas rediriger si on est déjà sur les routes de vérification
            if (!$request->routeIs('verification.*') && !$request->routeIs('logout')) {
                Log::info('Redirection vers verification.code car email non vérifié');
                return redirect()->route('verification.code')
                    ->with('warning', 'Veuillez vérifier votre email avant d\'accéder à cette fonctionnalité.');
            }
        }

        return $next($request);
    }
}
