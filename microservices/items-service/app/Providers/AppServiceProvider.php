<?php

namespace App\Providers;

use App\Contracts\EventPublisher;
use App\Services\AuthServiceClient;
use App\Services\LogEventPublisher;
use App\Services\NullEventPublisher;
use App\Services\RedisStreamEventPublisher;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthServiceClient::class, fn () => AuthServiceClient::fromConfig());

        // Le constructeur prend des scalaires : ils ne sont pas résolvables,
        // on fournit donc la factory explicitement.
        $this->app->singleton(RedisStreamEventPublisher::class, fn () => new RedisStreamEventPublisher(
            (string) config('events.redis_connection', 'default'),
            (int) config('events.stream_max_length', 10000),
        ));

        $this->app->singleton(EventPublisher::class, function ($app) {
            $driver = (string) config('events.publisher', 'redis-stream');

            return match ($driver) {
                'log' => $app->make(LogEventPublisher::class),
                'null' => $app->make(NullEventPublisher::class),
                'redis-stream' => $app->make(RedisStreamEventPublisher::class),
                // Un driver mal orthographié ne doit pas laisser croire que
                // les événements partent : on échoue au démarrage.
                default => throw new RuntimeException("EVENT_PUBLISHER inconnu : {$driver}"),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
