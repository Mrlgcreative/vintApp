<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Models\VintPass;
use App\Notifications\ItemModerated;
use Illuminate\Support\Facades\Log;

class ItemModerationService
{
    /**
     * Approuver et publier un article (chemin admin OU expert).
     * VintPass créé si absent + notification vendeur (ItemModerated).
     */
    public function approveItem(Item $item, User $moderator, string $source = 'admin'): Item
    {
        $item->update([
            'status' => 'active',
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $moderator->id,
            'authenticity_verified' => true,
            'authenticity_badge_type' => 'vintapp_verified',
        ]);

        Log::info("Article approuvé et publié ({$source})", [
            'item_id' => $item->id,
            'moderator_id' => $moderator->id,
        ]);

        $this->ensureVintPass($item, $moderator);

        $item->user?->notify(new ItemModerated($item, 'approved', null, null, $moderator->name));

        return $item->fresh();
    }

    /**
     * Rejeter un article (chemin admin OU expert).
     */
    public function rejectItem(Item $item, User $moderator, ?string $reason = null, string $source = 'admin'): Item
    {
        $data = [
            'status' => 'inactive',
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => $moderator->id,
        ];

        if ($reason !== null && $reason !== '') {
            $data['rejection_reason'] = $reason;
        }

        $item->update($data);

        Log::info("Article rejeté ({$source})", [
            'item_id' => $item->id,
            'moderator_id' => $moderator->id,
            'reason' => $reason,
        ]);

        $item->user?->notify(new ItemModerated($item, 'rejected', $reason, null, $moderator->name));

        return $item->fresh();
    }

    /**
     * Crée un VintPass si absent (jamais bloquant).
     */
    protected function ensureVintPass(Item $item, User $moderator): void
    {
        try {
            if (!VintPass::where('item_id', $item->id)->exists()) {
                app(VintPassService::class)->createVintPass($item, null, $moderator);
            }
        } catch (\Exception $e) {
            Log::warning("Échec création VintPass", [
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
