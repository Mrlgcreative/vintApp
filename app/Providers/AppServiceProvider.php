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

        // Partager les variables de couleurs avec toutes les vues
        view()->composer('*', function ($view) {
            // Récupérer le CSS de la palette active depuis le cache
            $activePaletteCSS = Cache::remember('vintapp_active_palette_css', 3600, function () {
                try {
                    $colorService = app(\App\Services\ColorPaletteService::class);
                    $colors = $colorService->getAllColors();
                    
                    // Générer le CSS inline pour les variables CSS
                    $css = ":root {\n";
                    foreach ($colors as $key => $value) {
                        $css .= "    --color-{$key}: {$value};\n";
                    }
                    
                    // Ajouter les classes Tailwind avec les couleurs
                    $primaryColor = $colors['primary'] ?? '#8B4513';
                    $css .= "}\n\n";
                    $css .= ".bg-primary { background-color: {$primaryColor} !important; }\n";
                    $css .= ".text-primary { color: {$primaryColor} !important; }\n";
                    $css .= ".border-primary { border-color: {$primaryColor} !important; }\n";
                    
                    return $css;
                } catch (\Exception $e) {
                    Log::error('Erreur génération palette CSS: ' . $e->getMessage());
                    return '';
                }
            });

            // Partager avec toutes les vues
            $view->with('activePaletteCSS', $activePaletteCSS);
            
            // Partager aussi les informations de l'app
            $view->with('appName', config('app.name', 'VintApp'));
            $view->with('appFavicon', config('app.favicon', '/favicon.ico'));
        });
    }
}
