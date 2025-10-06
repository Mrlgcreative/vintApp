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
     * Logique de pré-inscription :
     * 1. Les admins et les routes admin sont TOUJOURS autorisés (même si la pré-inscription est active)
     * 2. Si la pré-inscription est activée, les autres utilisateurs sont redirigés vers /preregistration
     * 3. Certaines routes publiques restent accessibles (login, password setup, etc.)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // PRIORITÉ 1 : Si la route commence par 'admin/', laisser passer TOUJOURS
        // (permet login admin et accès interface admin)
        if (str_starts_with($request->path(), 'admin')) {
            return $next($request);
        }
        
        // PRIORITÉ 2 : Si l'utilisateur est admin authentifié, laisser passer
        // (permet la redirection après login vers n'importe quelle page)
        if (auth()->check()) {
            $user = auth()->user();
            // Rafraîchir les relations pour être sûr
            $user->load('roles');
            
            if ($user->isAdmin()) {
                return $next($request);
            }
        }
        
        // PRIORITÉ 3 : Routes système toujours autorisées
        $systemRoutes = ['login', 'logout', 'register', 'password.request', 'password.reset'];
        $currentRoute = $request->route() ? $request->route()->getName() : '';
        
        if (in_array($currentRoute, $systemRoutes)) {
            return $next($request);
        }
        
        // PRIORITÉ 4 : Vérifier si la pré-inscription est activée
        $preregistrationEnabled = Setting::get('preregistration_enabled', false);
        
        if ($preregistrationEnabled) {
            // Liste des routes autorisées même en mode pré-inscription
            $allowedRoutes = [
                'preregistration.*',  // Toutes les routes de pré-inscription
                'password.setup',     // Page de définition de mot de passe
                'password.setup.store', // Traitement du mot de passe
                'login',              // Connexion
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
            
            // Si ce n'est pas une route autorisée, rediriger vers la pré-inscription
            if (!$isAllowedRoute && !str_starts_with($request->path(), 'preregistration') && !str_starts_with($request->path(), 'set-password')) {
                return redirect()->route('preregistration.index');
            }
        }
        
        return $next($request);
    }
}
