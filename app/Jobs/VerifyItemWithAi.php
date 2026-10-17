<?php

namespace App\Jobs;

use App\Models\Item;
use App\Services\ItemAiVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Analyse IA (vision) d'un article après sa soumission.
 *
 * shortcut: dispatché via dispatchAfterResponse() — tourne après l'envoi de la réponse,
 * sans worker de queue. Passer à dispatch() + `php artisan queue:work` si un worker est en place.
 */
class VerifyItemWithAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(public int $itemId)
    {
    }

    public function handle(ItemAiVerificationService $service): void
    {
        $item = Item::find($this->itemId);

        if (! $item) {
            return;
        }

        try {
            $service->store($item, $service->verify($item));
            Log::info('Vérification IA terminée', ['item_id' => $item->id]);
        } catch (\Throwable $e) {
            Log::error('Vérification IA échouée', ['item_id' => $item->id, 'error' => $e->getMessage()]);
            $service->storeError($item, $e->getMessage());
        }
    }
}
