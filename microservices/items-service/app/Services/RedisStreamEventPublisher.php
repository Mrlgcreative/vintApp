<?php

namespace App\Services;

use App\Contracts\EventPublisher;
use App\Models\OutboxMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use RuntimeException;

/**
 * Publication sur un Redis Stream.
 *
 * Redis Streams plutôt que Pub/Sub : avec Pub/Sub un message émis pendant que
 * `order-service` est arrêté est perdu sans trace. Un stream conserve les
 * entrées jusqu'à `XACK` du côté consommateur, ce qui compte pour des
 * commandes déjà payées.
 */
class RedisStreamEventPublisher implements EventPublisher
{
    public function __construct(
        private readonly string $connection,
        private readonly int $maxLength,
    ) {}

    public function publish(OutboxMessage $message): void
    {
        $payload = [
            'event_id' => $message->event_id,
            'type' => $message->type,
            'occurred_at' => now()->toIso8601String(),
            'data' => $message->payload,
        ];

        try {
            // La connexion `events` est configurée sans préfixe : le nom du
            // stream est le nom du contrat, tel que le lit un consommateur.
            Redis::connection($this->connection)->xadd(
                $message->stream,
                '*',
                ['payload' => json_encode($payload, JSON_THROW_ON_ERROR)],
                $this->maxLength,
                'approximate',
            );
        } catch (\Throwable $e) {
            Log::error('Publication événement impossible', [
                'event_id' => $message->event_id,
                'type' => $message->type,
                'stream' => $message->stream,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Publication Redis échouée: '.$e->getMessage(), 0, $e);
        }
    }

    public function name(): string
    {
        return 'redis-stream';
    }
}
