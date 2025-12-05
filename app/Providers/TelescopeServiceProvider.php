<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class TelescopeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope n'est disponible qu'en environnement local
        if (!$this->app->environment('local')) {
            return;
        }

        // Vérifier si Telescope est installé
        if (!class_exists(\Laravel\Telescope\Telescope::class)) {
            return;
        }

        $this->registerTelescope();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!$this->app->environment('local')) {
            return;
        }

        if (class_exists(\Laravel\Telescope\Telescope::class)) {
            $this->gate();
        }
    }

    /**
     * Enregistrer Telescope (uniquement en local)
     */
    protected function registerTelescope(): void
    {
        $Telescope = \Laravel\Telescope\Telescope::class;
        
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $Telescope::filter(function ($entry) {
            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        $Telescope = \Laravel\Telescope\Telescope::class;

        $Telescope::hideRequestParameters(['_token']);

        $Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return $user && $user->admin_role == 1;
        });
    }
}
