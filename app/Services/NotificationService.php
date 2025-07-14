<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Créer une notification pour un nouveau message
     */
    public function createMessageNotification($senderId, $receiverId, $messageContent)
    {
        try {
            $sender = User::find($senderId);
            
            Notification::create([
                'user_id' => $receiverId,
                'type' => 'new_message',
                'title' => 'Nouveau message',
                'message' => $sender->name . ' vous a envoyé un message',
                'data' => [
                    'sender_id' => $senderId,
                    'sender_name' => $sender->name,
                    'message_preview' => \Str::limit($messageContent, 99),
                    'conversation_id' => $senderId, // Pour la messagerie, l'ID de conversation est l'ID de l'expéditeur
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour une nouvelle commande
     */
    public function createOrderNotification($buyerId, $sellerId, $itemName)
    {
        try {
            $buyer = User::find($buyerId);
            
            Notification::create([
                'user_id' => $sellerId,
                'type' => 'new_order',
                'title' => 'Nouvelle commande',
                'message' => $buyer->name . ' a commandé votre article "' . $itemName . '"',
                'data' => [
                    'buyer_id' => $buyerId,
                    'buyer_name' => $buyer->name,
                    'item_name' => $itemName,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour un article favori
     */
    public function createFavoriteNotification($userId, $itemName)
    {
        try {
            $user = User::find($userId);
            
            Notification::create([
                'user_id' => $userId,
                'type' => 'item_favorited',
                'title' => 'Article ajouté aux favoris',
                'message' => 'Vous avez ajouté "' . $itemName . '" à vos favoris',
                'data' => [
                    'item_name' => $itemName,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['read_at' => now()]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Obtenir les notifications non lues d'un utilisateur
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
} 