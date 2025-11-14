<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Services\ColorPaletteService;
use App\Services\CSSInjectionService;

class ColorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ColorPaletteService::class, function ($app) {
            return new ColorPaletteService();
        });
        
        $this->app->singleton(CSSInjectionService::class, function ($app) {
            return new CSSInjectionService($app->make(ColorPaletteService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Partager les couleurs avec toutes les vues
        View::composer('*', function ($view) {
            try {
                $colorService = app(ColorPaletteService::class);
                $cssService = app(CSSInjectionService::class);
                
                // Injecter les variables utiles dans toutes les vues
                $view->with([
                    'activeColors' => $colorService->getAllColors(),
                    'activePaletteName' => $colorService->getActivePaletteName(),
                    'activePaletteCSS' => $colorService->generateActivePaletteCSS(),
                    'customCSSUrl' => $cssService->getCustomCSSUrl(),
                    'colorService' => $colorService,
                    'cssService' => $cssService
                ]);
            } catch (\Exception $e) {
                // En cas d'erreur, utiliser les valeurs par défaut
                Log::error('ColorServiceProvider error: ' . $e->getMessage());
                $view->with([
                    'activeColors' => config('colors.palettes.default', []),
                    'activePaletteName' => 'default',
                    'activePaletteCSS' => ':root { --color-primary: #3B82F6; --color-secondary: #6B7280; }',
                    'customCSSUrl' => '',
                    'colorService' => null,
                    'cssService' => null
                ]);
            }
        });
    }
}