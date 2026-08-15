<?php

namespace App\Observers;

use App\Models\Notification;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    public function created(Notification $notification): void
    {
        if (!config('services.firestore.enabled', true)) {
            return;
        }

        try {
            app(FirestoreService::class)->signalUser((int) $notification->user_id);
        } catch (\Exception $e) {
            Log::error('Erreur observer Firestore notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id,
            ]);
        }
    }
}
