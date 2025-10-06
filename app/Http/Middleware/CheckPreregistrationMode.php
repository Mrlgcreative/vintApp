<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CheckPreregistrationMode
{
    /**
     * Handle an incoming request.
     *
     * Si la pré-inscription est activée, redirige tous les utilisateurs non-admins
     * vers la page de pré-inscription (sauf s'ils sont déjà sur cette page).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si la pré-inscription est activée
        $preregistrationEnabled = Setting::get('preregistration_enabled', false);
        
        if ($preregistrationEnabled) {
            // Liste des routes autorisées même en mode pré-inscription
            $allowedRoutes = [
                'preregistration.*',  // Toutes les routes de pré-inscription
                'password.setup',     // Page de définition de mot de passe
                'password.setup.store', // Traitement du mot de passe
                'login',              // Connexion admin
                'logout',             // Déconnexion
            ];
            
            // Vérifier si la route actuelle est autorisée
            $currentRoute = $request->route() ? $request->route()->getName() : '';
            
            $isAllowedRoute = false;
            foreach ($allowedRoutes as $pattern) {
                if (fnmatch($pattern, $currentRoute)) {
                    $isAllowedRoute = true;
                    break;
                }
            }
            
            // Si l'utilisateur est admin, il peut accéder à tout
            if (auth()->check() && auth()->user()->isAdmin()) {
                return $next($request);
            }
            
            // Si ce n'est pas une route autorisée, rediriger vers la pré-inscription
            if (!$isAllowedRoute && !str_starts_with($request->path(), 'preregistration') && !str_starts_with($request->path(), 'set-password')) {
                return redirect()->route('preregistration.index');
            }
        }
        
        return $next($request);
    }
}
