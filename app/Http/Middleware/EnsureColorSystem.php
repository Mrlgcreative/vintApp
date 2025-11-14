<?php

namespace App\Http\Middleware;

use App\Helpers\ColorSystemHelper;
use Closure;
use Illuminate\Http\Request;

class EnsureColorSystem
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // S'assurer que le système de couleurs est initialisé
        // uniquement pour les requêtes web (pas API)
        if ($request->is('api/*') === false && !$request->ajax()) {
            $this->ensureColorSystemReady();
        }

        return $next($request);
    }

    /**
     * S'assurer que le système de couleurs est prêt
     */
    private function ensureColorSystemReady()
    {
        try {
            $cssPath = resource_path('css/app.css');
            
            // Vérifier si le CSS contient les variables de couleurs
            if (file_exists($cssPath)) {
                $cssContent = file_get_contents($cssPath);
                
                // Si les variables de couleurs ne sont pas présentes
                if (strpos($cssContent, '--color-primary') === false) {
                    ColorSystemHelper::autoInitialize();
                }
            } else {
                // Si le fichier CSS n'existe pas, le créer avec les couleurs de base
                ColorSystemHelper::autoInitialize();
            }
            
        } catch (\Exception $e) {
            // En cas d'erreur, logger mais ne pas faire planter l'app
            logger()->warning('EnsureColorSystem: Erreur lors de la vérification', [
                'error' => $e->getMessage()
            ]);
        }
    }
}