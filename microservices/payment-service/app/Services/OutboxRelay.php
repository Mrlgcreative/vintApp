<?php

namespace App\Services;

use App\Contracts\EventPublisher;
use App\Models\OutboxMessage;
use Illuminate\Support\Facades\Log;

/**
 * Publie les messages en attente, puis les marque.
 *
 * L'ordre est délibéré : on publie d'abord, on marque ensuite. Si le process
 * meurt entre les deux, le message sera republié — un doublon que le
 * consommateur doit dédupliquer sur `event_id`, conformément à EVENTS.md.
 * L'inverse (marquer puis publier) perdrait des événements.
 */
class OutboxRelay
{
    public function __construct(private readonly EventPublisher $publisher) {}

    /**
     * @return array{published: int, failed: int}
     */
    public function flush(?int $batch = null): array
    {
        $batch ??= (int) config('events.outbox.batch', 100);

        $published = 0;
        $failed = 0;

        $messages = OutboxMessage::query()
            ->whereNull('published_at')
            ->where(function ($query) {
                $query->whereNull('available_at')->orWhere('available_at', '<=', now());
            })
            ->orderBy('id')
            ->limit($batch)
            ->get();

        foreach ($messages as $message) {
            try {
                $this->publisher->publish($message);

                $message->forceFill(['published_at' => now(), 'last_error' => null])->save();

                $published++;
            } catch (\Throwable $e) {
                $message->forceFill([
                    'attempts' => $message->attempts + 1,
                    'last_error' => $e->getMessage(),
                    'available_at' => now()->addSeconds((int) config('events.outbox.backoff_seconds', 30)),
                ])->save();

                $failed++;

                Log::warning('Message outbox non publié, reprogrammé', [
                    'event_id' => $message->event_id,
                    'type' => $message->type,
                    'attempts' => $message->attempts,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['published' => $published, 'failed' => $failed];
    }
}
