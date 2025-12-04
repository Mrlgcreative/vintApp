<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\SettingService::class, function ($app) {
            return new \App\Services\SettingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Enregistrer le provider Apple pour Socialite
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('apple', \SocialiteProviders\Apple\Provider::class);
        });

        // Partager les informations de l'app avec toutes les vues
        view()->composer('*', function ($view) {
            $view->with('appName', config('app.name', 'VintApp'));
            $view->with('appFavicon', config('app.favicon', '/favicon.ico'));
        });
        
        // Générer automatiquement le fichier CSS dynamique au démarrage
        $this->generateDynamicCSS();
    }
    
    /**
     * Générer le fichier CSS dynamique avec les couleurs actives
     */
    protected function generateDynamicCSS(): void
    {
        try {
            $colorService = app(\App\Services\ColorPaletteService::class);
            $colors = $colorService->getAllColors();
            
            // Générer le contenu CSS
            $css = "/* Généré automatiquement par VintApp - Ne pas modifier manuellement */\n";
            $css .= ":root {\n";
            foreach ($colors as $key => $value) {
                $css .= "    --color-{$key}: {$value};\n";
            }
            $css .= "}\n";
            
            // Écrire dans le fichier public/css/vintapp-dynamic.css
            $cssPath = public_path('css/vintapp-dynamic.css');
            $cssDir = dirname($cssPath);
            
            if (!file_exists($cssDir)) {
                mkdir($cssDir, 0755, true);
            }
            
            file_put_contents($cssPath, $css);
            
        } catch (\Exception $e) {
            Log::error('Erreur génération CSS dynamique: ' . $e->getMessage());
        }
    }
}
