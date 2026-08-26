<?php

namespace App\Observers;

use App\Models\Item;
use App\Services\CacheService;
use App\Services\ExpertNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemObserver
{
    private $notificationService;

    private $cacheService;

    public function __construct(ExpertNotificationService $notificationService, CacheService $cacheService)
    {
        $this->notificationService = $notificationService;
        $this->cacheService = $cacheService;
    }

    /**
     * Invalider le cache public dès qu'un changement de statut affecte la visibilité
     * (approbation, rejet, désactivation, mise en vente).
     */
    public function saved(Item $item)
    {
        if ($item->wasChanged('status') || $item->wasChanged('verification_status')) {
            $this->cacheService->forgetItem($item->id);
            $this->cacheService->forgetHomepage();
            $this->purgePublicHttpCache();
        }
    }

    /**
     * Purge le cache HTTP des listes publiques (api/items, api/categories, api/brands...)
     * pour que les nouveaux statuts soient visibles immédiatement côté API/mobile.
     */
    protected function purgePublicHttpCache(): void
    {
        if (config('cache.default') !== 'database') {
            return;
        }

        try {
            $table = config('cache.stores.database.table', 'cache');
            DB::table($table)
                ->where('key', 'like', '%http_cache:%')
                ->delete();
        } catch (\Exception $e) {
            Log::warning('Échec purge du cache HTTP public', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Déclencher les notifications quand un article devient en attente de vérification
     */
    public function updated(Item $item)
    {
        // Quand l'article passe à 'active' (publié après vérification),
        // broadcast aux clients avec un message personnalisé aléatoire.
        if ($item->wasChanged('status') && $item->status === 'active') {
            try {
                app(\App\Services\NotificationService::class)->notifyClientsOfNewItem($item);
            } catch (\Throwable $e) {
                Log::error('Erreur broadcast article publié (observer): ' . $e->getMessage());
            }

            $this->ensureSellerRole($item->user_id);
        }

        // Vérifier si le statut vient de passer à 'pending' ou 'pending_verification'
        if ($item->wasChanged('verification_status') && $item->verification_status === 'pending') {
            $this->notificationService->notifyExpertsForItem($item);
        }
        
        // Aussi vérifier si le statut est pending_verification et verification_status est pending
        if ($item->wasChanged('status') && $item->status === 'pending_verification' && $item->verification_status === 'pending') {
            $this->notificationService->notifyExpertsForItem($item);
        }
    }

    /**
     * Également vérifier à la création si l'article est en attente
     */
    public function created(Item $item)
    {
        // Notifier le vendeur que son article a bien été publié (in-app + push)
        try {
            app(\App\Services\NotificationService::class)->createItemPublishedNotification($item->user_id, $item);
        } catch (\Throwable $e) {
            Log::error('Erreur notification article publié: ' . $e->getMessage());
        }

        // Déclencher la notification si l'article est créé avec le statut pending_verification et verification_status pending
        if ($item->status === 'pending_verification' && $item->verification_status === 'pending') {
            $this->notificationService->notifyExpertsForItem($item);
        }

        // Dès qu'un produit est créé, l'utilisateur devient vendeur
        $this->ensureSellerRole($item->user_id);
    }

    private function ensureSellerRole(int $userId): void
    {
        $user = \App\Models\User::find($userId);
        if ($user && !$user->hasRole('vendeur')) {
            $vendeurRole = \App\Models\Role::where('slug', 'vendeur')->first();
            if ($vendeurRole) {
                $user->roles()->attach($vendeurRole);
            }
        }
    }
}
