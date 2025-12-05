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

        // Injecter la palette de couleurs active
        view()->composer('*', function ($view) {
            // Récupérer le service de palette
            $colorPaletteService = app(\App\Services\ColorPaletteService::class);
            $activePalette = $colorPaletteService->getActivePalette();
            
            // Partager avec toutes les vues
            $view->with('appName', config('app.name', 'VintApp'));
            $view->with('appFavicon', config('app.favicon', '/favicon.png'));
            $view->with('activePalette', $activePalette);
            $view->with('colorPrimary', $activePalette['primary'] ?? '#8B4513');
            $view->with('colorSecondary', $activePalette['secondary'] ?? '#696969');
        });
    }
}
