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
                    $css .= "}\n\n";
                    
                    // Générer toutes les classes Tailwind pour primary (toutes les nuances)
                    $shades = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];
                    
                    foreach ($shades as $shade) {
                        $colorKey = "primary-{$shade}";
                        if (isset($colors[$colorKey])) {
                            $color = $colors[$colorKey];
                            
                            // Background
                            $css .= ".bg-primary-{$shade} { background-color: {$color} !important; }\n";
                            $css .= ".hover\\:bg-primary-{$shade}:hover { background-color: {$color} !important; }\n";
                            
                            // Text
                            $css .= ".text-primary-{$shade} { color: {$color} !important; }\n";
                            $css .= ".hover\\:text-primary-{$shade}:hover { color: {$color} !important; }\n";
                            
                            // Border
                            $css .= ".border-primary-{$shade} { border-color: {$color} !important; }\n";
                            $css .= ".hover\\:border-primary-{$shade}:hover { border-color: {$color} !important; }\n";
                            
                            // Ring (pour focus states)
                            $css .= ".ring-primary-{$shade} { --tw-ring-color: {$color} !important; }\n";
                            $css .= ".focus\\:ring-primary-{$shade}:focus { --tw-ring-color: {$color} !important; }\n";
                        }
                    }
                    
                    // Classes de base pour primary (sans nuance)
                    $primaryColor = $colors['primary'] ?? $colors['primary-500'] ?? '#8B4513';
                    $css .= "\n/* Classes de base primary */\n";
                    $css .= ".bg-primary { background-color: {$primaryColor} !important; }\n";
                    $css .= ".hover\\:bg-primary:hover { background-color: {$primaryColor} !important; }\n";
                    $css .= ".text-primary { color: {$primaryColor} !important; }\n";
                    $css .= ".hover\\:text-primary:hover { color: {$primaryColor} !important; }\n";
                    $css .= ".border-primary { border-color: {$primaryColor} !important; }\n";
                    $css .= ".hover\\:border-primary:hover { border-color: {$primaryColor} !important; }\n";
                    
                    // Repeat pour secondary, success, danger, warning, info
                    $colorNames = ['secondary', 'success', 'danger', 'warning', 'info'];
                    foreach ($colorNames as $colorName) {
                        foreach ($shades as $shade) {
                            $colorKey = "{$colorName}-{$shade}";
                            if (isset($colors[$colorKey])) {
                                $color = $colors[$colorKey];
                                $css .= ".bg-{$colorName}-{$shade} { background-color: {$color} !important; }\n";
                                $css .= ".text-{$colorName}-{$shade} { color: {$color} !important; }\n";
                                $css .= ".border-{$colorName}-{$shade} { border-color: {$color} !important; }\n";
                            }
                        }
                        
                        // Classe de base
                        if (isset($colors[$colorName])) {
                            $color = $colors[$colorName];
                            $css .= ".bg-{$colorName} { background-color: {$color} !important; }\n";
                            $css .= ".text-{$colorName} { color: {$color} !important; }\n";
                            $css .= ".border-{$colorName} { border-color: {$color} !important; }\n";
                        }
                    }
                    
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
