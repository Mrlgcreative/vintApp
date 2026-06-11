<?php

namespace App\Notifications;

use App\Models\Item;
use App\Services\FirebasePushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ItemRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $item;
    protected $reason;
    protected $adminName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Item $item, string $reason, string $adminName)
    {
        $this->item = $item;
        $this->reason = $reason;
        $this->adminName = $adminName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Après envoi de la notification, envoyer aussi via FCM
     */
    public function afterCommit()
    {
        // Envoyer notification push Firebase
        $this->sendFCMNotification();
    }

    /**
     * Envoyer notification push via Firebase Cloud Messaging
     */
    protected function sendFCMNotification()
    {
        try {
            $user = $this->item->user;
            
            if (!$user || !$user->fcm_token) {
                Log::info('Pas de token FCM pour l\'utilisateur', ['user_id' => $this->item->user_id]);
                return;
            }

            $fcmService = app(FirebasePushService::class);
            
            $itemData = [
                'item_id' => $this->item->id,
                'item_name' => $this->item->name,
                'item_image' => $this->item->images[0] ?? null,
                'reason' => $this->reason
            ];

            $fcmService->sendItemRejectedNotification($user->fcm_token, $itemData);
            
            Log::info('Notification FCM envoyée (rejet)', [
                'user_id' => $user->id,
                'item_id' => $this->item->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification FCM (rejet)', [
                'error' => $e->getMessage(),
                'item_id' => $this->item->id
            ]);
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('❌ Votre article a été rejeté')
            ->greeting('Information importante')
            ->line("Votre article \"{$this->item->name}\" n'a pas pu être approuvé.")
            ->line("Raison : {$this->reason}")
            ->action('Modifier mon article', route('items.edit', $this->item))
            ->line('Vous pouvez modifier votre article et le soumettre à nouveau pour vérification.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'item_rejected',
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_image' => $this->item->images[0] ?? null,
            'reason' => $this->reason,
            'admin_name' => $this->adminName,
            'message' => "Votre article \"{$this->item->name}\" a été rejeté. Raison : {$this->reason}",
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'item_rejected',
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_image' => $this->item->images[0] ?? null,
            'reason' => $this->reason,
            'admin_name' => $this->adminName,
            'message' => "Votre article \"{$this->item->name}\" a été rejeté. Raison : {$this->reason}",
        ]);
    }
}
