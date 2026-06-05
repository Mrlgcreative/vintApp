<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NewNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private function createAndBroadcast(array $data): Notification
    {
        $notification = Notification::create($data);
        try {
            NewNotification::dispatch($notification);
        } catch (\Exception $e) {
            Log::error('Erreur broadcast notification: ' . $e->getMessage());
        }
        return $notification;
    }
    /**
     * Créer une notification pour un nouveau message
     */
    public function createMessageNotification($senderId, $receiverId, $messageContent)
    {
        try {
            $sender = User::find($senderId);
            
            $this->createAndBroadcast([
                'user_id' => $receiverId,
                'type' => 'new_message',
                'title' => 'Nouveau message',
                'message' => $sender->name . ' vous a envoyé un message',
                'data' => [
                    'sender_id' => $senderId,
                    'sender_name' => $sender->name,
                    'message_preview' => \Str::limit($messageContent, 99),
                    'conversation_id' => $senderId,
                    'url' => '/messages/' . $senderId,
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
            
            $this->createAndBroadcast([
                'user_id' => $sellerId,
                'type' => 'new_order',
                'title' => 'Nouvelle commande',
                'message' => $buyer->name . ' a commandé votre article "' . $itemName . '"',
                'data' => [
                    'buyer_id' => $buyerId,
                    'buyer_name' => $buyer->name,
                    'item_name' => $itemName,
                    'url' => '/orders',
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
            
            $this->createAndBroadcast([
                'user_id' => $userId,
                'type' => 'item_favorited',
                'title' => 'Article ajouté aux favoris',
                'message' => 'Vous avez ajouté "' . $itemName . '" à vos favoris',
                'data' => [
                    'item_name' => $itemName,
                    'url' => '/items',
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour une réduction appliquée
     */
    public function createDiscountNotification($sellerId, $buyerId, $itemName, $discountPercentage, $finalPriceWithCurrency)
    {
        try {
            $seller = User::find($sellerId);
            
            $this->createAndBroadcast([
                'user_id' => $buyerId,
                'type' => 'discount_applied',
                'title' => 'Réduction accordée !',
                'message' => $seller->name . ' vous accorde une réduction de ' . $discountPercentage . '% sur "' . $itemName . '" - Nouveau prix: ' . $finalPriceWithCurrency,
                'data' => [
                    'seller_id' => $sellerId,
                    'seller_name' => $seller->name,
                    'item_name' => $itemName,
                    'discount_percentage' => $discountPercentage,
                    'final_price' => $finalPriceWithCurrency,
                    'url' => '/messages/' . $sellerId,
                ],
                'url' => '/messages/' . $sellerId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification de réduction: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour un remboursement approuvé
     */
    public function createRefundApprovedNotification($refund)
    {
        try {
            // Notification pour l'acheteur
            $this->createAndBroadcast([
                'user_id' => $refund->buyer_id,
                'type' => 'refund_approved',
                'title' => 'Remboursement approuvé ✅',
                'message' => 'Votre demande de remboursement de ' . $refund->formatted_refund_amount . ' pour la commande #' . $refund->order->order_number . ' a été approuvée.',
                'data' => [
                    'refund_id' => $refund->id,
                    'order_id' => $refund->order_id,
                    'order_number' => $refund->order->order_number,
                    'item_name' => $refund->order->item->name,
                    'amount' => $refund->refund_amount,
                    'currency' => $refund->currency,
                    'url' => '/orders/' . $refund->order_id,
                ],
            ]);

            // Notification pour le vendeur
            $this->createAndBroadcast([
                'user_id' => $refund->seller_id,
                'type' => 'refund_approved',
                'title' => 'Remboursement approuvé pour votre article',
                'message' => 'Un remboursement de ' . $refund->formatted_refund_amount . ' a été approuvé pour votre article "' . $refund->order->item->name . '".',
                'data' => [
                    'refund_id' => $refund->id,
                    'order_id' => $refund->order_id,
                    'order_number' => $refund->order->order_number,
                    'item_name' => $refund->order->item->name,
                    'buyer_name' => $refund->buyer->name,
                    'amount' => $refund->refund_amount,
                    'currency' => $refund->currency,
                    'url' => '/orders/' . $refund->order_id,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création des notifications de remboursement approuvé: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour un remboursement rejeté
     */
    public function createRefundRejectedNotification($refund)
    {
        try {
            // Notification pour l'acheteur
            $this->createAndBroadcast([
                'user_id' => $refund->buyer_id,
                'type' => 'refund_rejected',
                'title' => 'Demande de remboursement refusée',
                'message' => 'Votre demande de remboursement pour la commande #' . $refund->order->order_number . ' a été refusée.',
                'data' => [
                    'refund_id' => $refund->id,
                    'order_id' => $refund->order_id,
                    'order_number' => $refund->order->order_number,
                    'item_name' => $refund->order->item->name,
                    'amount' => $refund->refund_amount,
                    'currency' => $refund->currency,
                    'admin_notes' => $refund->admin_notes,
                    'url' => '/orders/' . $refund->order_id,
                ],
            ]);

            // Notification pour le vendeur
            $this->createAndBroadcast([
                'user_id' => $refund->seller_id,
                'type' => 'refund_rejected',
                'title' => 'Demande de remboursement refusée',
                'message' => 'La demande de remboursement pour votre article "' . $refund->order->item->name . '" a été refusée.',
                'data' => [
                    'refund_id' => $refund->id,
                    'order_id' => $refund->order_id,
                    'order_number' => $refund->order->order_number,
                    'item_name' => $refund->order->item->name,
                    'buyer_name' => $refund->buyer->name,
                    'url' => '/orders/' . $refund->order_id,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création des notifications de remboursement rejeté: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour une négociation de remboursement
     */
    public function createRefundNegotiationNotification($refund)
    {
        try {
            // Notification pour l'acheteur
            $this->createAndBroadcast([
                'user_id' => $refund->buyer_id,
                'type' => 'refund_negotiation',
                'title' => 'Contre-offre de remboursement',
                'message' => 'Une contre-offre de ' . $refund->currency . ' ' . number_format($refund->counter_offer_amount, 2) . ' a été proposée pour votre demande de remboursement.',
                'data' => [
                    'refund_id' => $refund->id,
                    'order_id' => $refund->order_id,
                    'order_number' => $refund->order->order_number,
                    'item_name' => $refund->order->item->name,
                    'original_amount' => $refund->refund_amount,
                    'counter_offer' => $refund->counter_offer_amount,
                    'currency' => $refund->currency,
                    'admin_notes' => $refund->admin_notes,
                    'url' => '/orders/' . $refund->order_id,
                ],
            ]);

            // Notification pour le vendeur
            $this->createAndBroadcast([
                'user_id' => $refund->seller_id,
                'type' => 'refund_negotiation',
                'title' => 'Négociation de remboursement',
                'message' => 'Une contre-offre de ' . $refund->currency . ' ' . number_format($refund->counter_offer_amount, 2) . ' a été proposée pour le remboursement de votre article.',
                'data' => [
                    'refund_id' => $refund->id,
                    'order_id' => $refund->order_id,
                    'order_number' => $refund->order->order_number,
                    'item_name' => $refund->order->item->name,
                    'buyer_name' => $refund->buyer->name,
                    'original_amount' => $refund->refund_amount,
                    'counter_offer' => $refund->counter_offer_amount,
                    'currency' => $refund->currency,
                    'url' => '/orders/' . $refund->order_id,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création des notifications de négociation: ' . $e->getMessage());
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

    /**
     * Créer une notification pour une livraison locale proposée
     */
    public function createLocalDeliveryProposedNotification($localDelivery)
    {
        try {
            $this->createAndBroadcast([
                'user_id' => $localDelivery->buyer_id,
                'title' => 'Livraison locale proposée',
                'message' => "Le vendeur {$localDelivery->seller->name} propose une livraison locale pour votre commande #{$localDelivery->order->order_number}",
                'type' => 'local_delivery_proposed',
                'data' => [
                    'local_delivery_id' => $localDelivery->id,
                    'order_id' => $localDelivery->order_id,
                    'order_number' => $localDelivery->order->order_number,
                    'seller_name' => $localDelivery->seller->name,
                    'delivery_type' => $localDelivery->delivery_type_text,
                    'distance_km' => $localDelivery->distance_km,
                    'delivery_fee' => $localDelivery->delivery_fee,
                    'currency' => $localDelivery->currency,
                    'url' => '/local-deliveries/' . $localDelivery->id,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification de livraison proposée: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour une livraison locale acceptée
     */
    public function createLocalDeliveryAcceptedNotification($localDelivery)
    {
        try {
            $this->createAndBroadcast([
                'user_id' => $localDelivery->seller_id,
                'title' => 'Livraison locale acceptée',
                'message' => "L'acheteur {$localDelivery->buyer->name} a accepté votre proposition de livraison locale pour la commande #{$localDelivery->order->order_number}",
                'type' => 'local_delivery_accepted',
                'data' => [
                    'local_delivery_id' => $localDelivery->id,
                    'order_id' => $localDelivery->order_id,
                    'order_number' => $localDelivery->order->order_number,
                    'buyer_name' => $localDelivery->buyer->name,
                    'buyer_phone' => $localDelivery->buyer_phone,
                    'buyer_address' => $localDelivery->buyer_address,
                    'url' => '/local-deliveries/' . $localDelivery->id,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification de livraison acceptée: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour une livraison en transit
     */
    public function createLocalDeliveryInTransitNotification($localDelivery)
    {
        try {
            $this->createAndBroadcast([
                'user_id' => $localDelivery->buyer_id,
                'title' => 'Livraison en transit',
                'message' => "Votre commande #{$localDelivery->order->order_number} est en cours de livraison. Code de vérification: {$localDelivery->delivery_code}",
                'type' => 'local_delivery_in_transit',
                'data' => [
                    'local_delivery_id' => $localDelivery->id,
                    'order_id' => $localDelivery->order_id,
                    'order_number' => $localDelivery->order->order_number,
                    'delivery_code' => $localDelivery->delivery_code,
                    'seller_name' => $localDelivery->seller->name,
                    'seller_phone' => $localDelivery->seller_phone,
                    'url' => '/local-deliveries/' . $localDelivery->id,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification de livraison en transit: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour une livraison terminée
     */
    public function createLocalDeliveryDeliveredNotification($localDelivery)
    {
        try {
            $this->createAndBroadcast([
                'user_id' => $localDelivery->buyer_id,
                'title' => 'Livraison terminée',
                'message' => "Votre commande #{$localDelivery->order->order_number} a été livrée avec succès !",
                'type' => 'local_delivery_delivered',
                'data' => [
                    'local_delivery_id' => $localDelivery->id,
                    'order_id' => $localDelivery->order_id,
                    'order_number' => $localDelivery->order->order_number,
                    'delivery_time' => $localDelivery->actual_delivery_time->format('d/m/Y H:i'),
                    'url' => '/orders/' . $localDelivery->order_id,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification de livraison terminée: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification pour une livraison annulée
     */
    public function createLocalDeliveryCancelledNotification($localDelivery, $recipient = 'both')
    {
        try {
            $message = "La livraison locale pour la commande #{$localDelivery->order->order_number} a été annulée";
            if ($localDelivery->cancellation_reason) {
                $message .= ". Raison: {$localDelivery->cancellation_reason}";
            }

            $recipients = [];
            if ($recipient === 'buyer' || $recipient === 'both') {
                $recipients[] = $localDelivery->buyer_id;
            }
            if ($recipient === 'seller' || $recipient === 'both') {
                $recipients[] = $localDelivery->seller_id;
            }

            foreach ($recipients as $userId) {
                $this->createAndBroadcast([
                    'user_id' => $userId,
                    'title' => 'Livraison locale annulée',
                    'message' => $message,
                    'type' => 'local_delivery_cancelled',
                    'data' => [
                        'local_delivery_id' => $localDelivery->id,
                        'order_id' => $localDelivery->order_id,
                        'order_number' => $localDelivery->order->order_number,
                        'cancellation_reason' => $localDelivery->cancellation_reason,
                        'url' => '/orders/' . $localDelivery->order_id,
                    ],
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification d\'annulation: ' . $e->getMessage());
            return false;
        }
    }
} 