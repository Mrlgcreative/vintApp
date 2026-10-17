<?php

namespace App\Services;

use App\Contracts\EventPublisher;
use App\Models\OutboxMessage;
use Illuminate\Support\Facades\Log;

/**
 * Trace l'événement sans bus. Sert au développement local sans Redis, et
 * garde l'outbox comme source de vérité : le message y reste écrit.
 */
class LogEventPublisher implements EventPublisher
{
    public function publish(OutboxMessage $message): void
    {
        Log::info('Événement publié (publisher: log)', [
            'event_id' => $message->event_id,
            'type' => $message->type,
            'stream' => $message->stream,
            'payload' => $message->payload,
        ]);
    }

    public function name(): string
    {
        return 'log';
    }
}
