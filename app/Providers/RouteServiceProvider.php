<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * Les limiter d'authentification sont ciblés par email+IP (et non IP seule)
     * pour éviter qu'un attaquant épuise le quota de tous les utilisateurs
     * partageant la même IP (notamment derrière le edge / proxy Hostinger).
     *
     * Fenêtre de verrouillage : 3 heures (180 min). Après 5 échecs,
     * l'utilisateur est bloqué jusqu'à expiration de la fenêtre.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('auth.login', function (Request $request) {
            return Limit::perMinutes(180, 5) // 5 tentatives / 3 heures
                ->by(
                    Str::lower((string) ($request->input('email') ?? '')) . '|' . $request->ip()
                );
        });

        RateLimiter::for('auth.register', function (Request $request) {
            return Limit::perMinutes(180, 5) // 5 tentatives / 3 heures
                ->by(
                    Str::lower((string) ($request->input('email') ?? '')) . '|' . $request->ip()
                );
        });

        RateLimiter::for('auth.password', function (Request $request) {
            return Limit::perMinutes(180, 5) // 5 tentatives / 3 heures
                ->by(
                    Str::lower((string) ($request->input('email') ?? '')) . '|' . $request->ip()
                );
        });
    }
}
