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

        // Si l'utilisateur est connecté et son email n'est pas vérifié
        if ($user && is_null($user->email_verified_at)) {
            // Routes qui ne nécessitent PAS la vérification d'email
            $allowedRoutes = [
                // Routes de vérification d'email
                'verification.code',
                'verification.code.verify',
                'verification.code.resend',
                // Routes d'authentification
                'logout',
                'login',
                'register',
                // Routes publiques
                'home',
                'splash',
                'offline',
                'location.validate',
                'api.fcm-token',
                // Routes de débogage
                'debug.check-admin',
                'debug.test-verification-access',
                'test.lazy-loading',
                'test.navigation-skeleton',
                'test.push',
                'pwa.debug',
                'test.background.sync',
            ];

            $currentRoute = $request->route() ? $request->route()->getName() : null;

            // Autoriser les routes listées ci-dessus
            if ($currentRoute && in_array($currentRoute, $allowedRoutes)) {
                return $next($request);
            }

            // Autoriser les routes qui commencent par certains préfixes
            if ($currentRoute && (
                str_starts_with($currentRoute, 'verification.') ||
                str_starts_with($currentRoute, 'auth.') ||
                str_starts_with($currentRoute, 'password.') ||
                str_starts_with($currentRoute, 'api.')
            )) {
                return $next($request);
            }

            // Bloquer toutes les autres routes et rediriger vers la vérification d'email
            Log::info('Utilisateur bloqué - email non vérifié', [
                'user_id' => $user->id,
                'email' => $user->email,
                'route' => $currentRoute,
                'path' => $request->path()
            ]);

            return redirect()->route('verification.code')
                ->with('warning', 'Veuillez vérifier votre email avant d\'accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
