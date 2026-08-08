<?php

namespace App\Services;

use App\Models\ExpertNotification;
use App\Models\User;
use App\Models\Item;
use App\Events\ItemPendingForVerification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

class ExpertNotificationService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = null;

        try {
            $serviceAccount = config('firebase.service_account');
            
            // Vérifier si Firebase est configuré
            if (empty($serviceAccount)) {
                Log::warning('Firebase service account not configured');
                return;
            }

            // Si c'est un chemin de fichier JSON, le charger
            if (is_string($serviceAccount) && file_exists($serviceAccount)) {
                $serviceAccount = json_decode(file_get_contents($serviceAccount), true);
            }

            if (!is_array($serviceAccount)) {
                Log::warning('Firebase service account must be array or file path');
                return;
            }

            $this->firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();
                
        } catch (\Exception $e) {
            Log::warning('Firebase initialization failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notifier les experts qu'un nouvel article est en attente
     */
    public function notifyExpertsForItem(Item $item): void
    {
        try {
            // Récupérer les experts correspondant aux catégories
            $experts = $this->getExpertsForItem($item);

            if ($experts->isEmpty()) {
                Log::info('No experts found for item', ['item_id' => $item->id]);
                return;
            }

            $expertIds = $experts->pluck('user_id')->toArray();

            // Créer les notifications en base de données
            foreach ($experts as $expertProfile) {
                ExpertNotification::create([
                    'user_id' => $expertProfile->user_id,
                    'item_id' => $item->id,
                    'type' => 'item_pending',
                    'title' => "Nouvel article à vérifier",
                    'message' => "Un nouvel article '{$item->name}' en catégorie '{$item->category?->name}' attend votre vérification",
                    'icon' => 'fa-list-check',
                    'action_url' => route('expert.items.show-for-verification', $item),
                    'data' => [
                        'item_id' => $item->id,
                        'category' => $item->category?->name,
                        'price' => $item->price
                    ]
                ]);

                // Envoyer notification FCM si token disponible
                $this->sendFCMNotification(
                    $expertProfile->user,
                    "Nouvel article à vérifier",
                    "Un nouvel article '{$item->name}' attend votre vérification",
                    route('expert.items.show-for-verification', $item)
                );
            }

            // Broadcaster l'événement en temps réel
            broadcast(new ItemPendingForVerification($item, $expertIds))->toOthers();

            Log::info('Expert notifications sent', [
                'item_id' => $item->id,
                'expert_count' => count($expertIds)
            ]);

        } catch (\Exception $e) {
            Log::error('Error notifying experts', [
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notifier un expert que son article a été approuvé
     */
    /**
     * Envoyer une notification FCM
     */
    public function sendFCMNotification(User $user, string $title, string $body, string $clickAction = null): void
    {
        try {
            if (!$user->fcm_token || !$this->firebase) {
                return;
            }

            $messaging = $this->firebase->createMessaging();

            $notification = \Kreait\Firebase\Messaging\Notification::create($title, $body);
            
            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification($notification)
                ->withData([
                    'click_action' => $clickAction ?? route('expert.dashboard'),
                    'timestamp' => now()->toIso8601String()
                ]);

            $messaging->send($message);

            Log::info('FCM notification sent', ['user_id' => $user->id]);

        } catch (\Exception $e) {
            Log::warning('Error sending FCM notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Récupérer les experts pour un article
     */
    private function getExpertsForItem(Item $item): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\ExpertProfile::query()
            ->where('is_active', true)
            ->with('user')
            ->get()
            ->filter(function ($expert) use ($item) {
                // Vérifier si l'expert a la catégorie parmi ses spécialités
                // Les specialties sont stockées comme des slugs (ex: 'mode_luxe', 'electronique')
                $specialties = $expert->specialties ?? [];
                $categorySlug = $item->category?->slug;
                
                // Si l'expert n'a pas de spécialités, il accepte tous les articles (généraliste)
                if (empty($specialties)) {
                    return true;
                }
                
                return in_array($categorySlug, $specialties);
            });
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(int $notificationId): void
    {
        ExpertNotification::findOrFail($notificationId)->markAsRead();
    }

    /**
     * Marquer toutes les notifications comme lues pour un expert
     */
    public function markAllAsRead(int $expertId): void
    {
        ExpertNotification::where('user_id', $expertId)
            ->unread()
            ->update(['read' => true, 'read_at' => now()]);
    }

    /**
     * Obtenir les notifications non lues pour un expert
     */
    public function getUnreadNotifications(int $expertId): \Illuminate\Database\Eloquent\Collection
    {
        return ExpertNotification::where('user_id', $expertId)
            ->unread()
            ->recent()
            ->limit(10)
            ->get();
    }
}
