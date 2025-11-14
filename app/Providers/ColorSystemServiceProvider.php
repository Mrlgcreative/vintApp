<?php

namespace App\Providers;

use App\Helpers\ColorSystemHelper;
use Illuminate\Support\ServiceProvider;

class ColorSystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Initialiser automatiquement le système de couleurs
        // uniquement si on n'est pas en mode console (artisan commands)
        if (!app()->runningInConsole()) {
            $this->initializeColorSystem();
        }
    }

    /**
     * Initialise le système de couleurs automatiquement
     */
    private function initializeColorSystem()
    {
        try {
            // Vérifier si le CSS a besoin d'être mis à jour
            $lastUpdatePath = storage_path('framework/cache/color_palette.json');
            $cssPath = resource_path('css/app.css');
            
            $needsUpdate = false;
            
            // Si le fichier de cache n'existe pas
            if (!file_exists($lastUpdatePath)) {
                $needsUpdate = true;
            } else {
                // Si le CSS a été modifié plus récemment que notre cache
                $cacheTime = filemtime($lastUpdatePath);
                $cssTime = file_exists($cssPath) ? filemtime($cssPath) : 0;
                
                if ($cssTime > $cacheTime) {
                    $needsUpdate = true;
                }
            }
            
            // Mettre à jour si nécessaire
            if ($needsUpdate) {
                ColorSystemHelper::autoInitialize();
            }
            
        } catch (\Exception $e) {
            // En cas d'erreur, ne pas faire planter l'app
            logger()->warning('ColorSystemServiceProvider: Erreur lors de l\'initialisation', [
                'error' => $e->getMessage()
            ]);
        }
    }
}