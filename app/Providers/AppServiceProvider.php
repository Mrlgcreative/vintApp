<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Artisan serve = HTTP uniquement. Si APP_URL ou le navigateur pousse du HTTPS,
        // la poignée TLS échoue → net::ERR_CONNECTION_CLOSED sur :8001.
        if ($this->app->environment('local', 'development')) {
            URL::forceScheme('http');
        }

        Vite::usePreloadTagAttributes(function (string $src, string $url, ?array $chunk, ?array $manifest) {
            // Ne pas preload les fichiers CSS (évite le warning navigateur)
            if (str_ends_with($src, '.css')) {
                return false;
            }
            return [];
        });
        Vite::prefetch(concurrency: 3);

        // Enregistrer le provider Apple pour Socialite
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('apple', \SocialiteProviders\Apple\Provider::class);
        });

        // Enregistrer les Observers
        \App\Models\Item::observe(\App\Observers\ItemObserver::class);

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
