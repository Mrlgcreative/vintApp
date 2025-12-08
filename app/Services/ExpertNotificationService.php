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
        try {
            $this->firebase = (new Factory)
                ->withServiceAccount(config('firebase.service_account'))
                ->create();
        } catch (\Exception $e) {
            Log::warning('Firebase not configured for expert notifications');
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
    public function notifyItemApproved(Item $item, User $expert): void
    {
        try {
            ExpertNotification::create([
                'user_id' => $expert->id,
                'item_id' => $item->id,
                'type' => 'item_approved',
                'title' => "Article approuvé",
                'message' => "L'article '{$item->name}' a été approuvé et publié",
                'icon' => 'fa-check-circle',
                'action_url' => route('expert.items.pending'),
                'data' => ['item_id' => $item->id]
            ]);

            $this->sendFCMNotification(
                $expert,
                "Article approuvé",
                "L'article '{$item->name}' a été approuvé",
                route('expert.items.pending')
            );

        } catch (\Exception $e) {
            Log::error('Error notifying item approved', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifier un expert que son article a été rejeté
     */
    public function notifyItemRejected(Item $item, User $expert, ?string $reason = null): void
    {
        try {
            $message = $reason 
                ? "L'article '{$item->name}' a été rejeté. Raison : {$reason}"
                : "L'article '{$item->name}' a été rejeté";

            ExpertNotification::create([
                'user_id' => $expert->id,
                'item_id' => $item->id,
                'type' => 'item_rejected',
                'title' => "Article rejeté",
                'message' => $message,
                'icon' => 'fa-times-circle',
                'action_url' => route('expert.items.pending'),
                'data' => ['item_id' => $item->id, 'reason' => $reason]
            ]);

            $this->sendFCMNotification(
                $expert,
                "Article rejeté",
                $message,
                route('expert.items.pending')
            );

        } catch (\Exception $e) {
            Log::error('Error notifying item rejected', ['error' => $e->getMessage()]);
        }
    }

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
                $specialties = $expert->specialties ?? [];
                return in_array($item->category?->name, $specialties);
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
