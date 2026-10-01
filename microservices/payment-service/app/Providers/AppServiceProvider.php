<?php

namespace App\Providers;

use App\Services\AuthServiceClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthServiceClient::class, fn () => AuthServiceClient::fromConfig());
    }

    public function boot(): void
    {
        //
    }
}
