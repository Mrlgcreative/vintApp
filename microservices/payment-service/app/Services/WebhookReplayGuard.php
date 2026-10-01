<?php

namespace App\Services;

use App\Models\ProcessedWebhookEvent;
use Illuminate\Support\Facades\Cache;

/**
 * Empêche qu'un rejeu de webhook ne déclenche un second effet.
 *
 * Deux barrières : le cache bloque les rejeux rapprochés, la table unique
 * bloque les rejeux au-delà de la fenêtre du cache (et après un redémarrage).
 */
class WebhookReplayGuard
{
    public function alreadyProcessed(string $dedupeKey): bool
    {
        $cacheKey = 'webhook_replay_'.$dedupeKey;

        if (Cache::has($cacheKey)) {
            return true;
        }

        $alreadyInDatabase = ProcessedWebhookEvent::where('dedupe_key', $dedupeKey)->exists();

        if ($alreadyInDatabase) {
            Cache::put($cacheKey, true, now()->addMinutes(
                (int) config('payments.replay_window_minutes', 10)
            ));

            return true;
        }

        return false;
    }

    public function remember(string $dedupeKey): void
    {
        Cache::put('webhook_replay_'.$dedupeKey, true, now()->addMinutes(
            (int) config('payments.replay_window_minutes', 10)
        ));
    }
}
