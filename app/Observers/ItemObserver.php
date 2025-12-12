<?php

namespace App\Observers;

use App\Models\Item;
use App\Services\ExpertNotificationService;

class ItemObserver
{
    private $notificationService;

    public function __construct(ExpertNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Déclencher les notifications quand un article devient en attente de vérification
     */
    public function updated(Item $item)
    {
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
        // Déclencher la notification si l'article est créé avec le statut pending_verification et verification_status pending
        if ($item->status === 'pending_verification' && $item->verification_status === 'pending') {
            $this->notificationService->notifyExpertsForItem($item);
        }
    }
}
