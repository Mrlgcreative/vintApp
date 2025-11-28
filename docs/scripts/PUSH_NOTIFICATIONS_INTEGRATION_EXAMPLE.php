<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

/**
 * EXEMPLE D'INTÉGRATION: Push Notifications dans OrderController
 * 
 * Ce fichier montre comment intégrer les notifications push
 * dans les différents événements d'une commande
 */
class OrderControllerPushExample extends Controller
{
    protected $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Créer une nouvelle commande
     * → Notifier le VENDEUR de la nouvelle commande
     */
    public function store(Request $request)
    {
        // Validation et création de la commande
        $order = Order::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $request->seller_id,
            'item_id' => $request->item_id,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            // ...
        ]);

        // 🔔 NOTIFICATION PUSH: Nouvelle commande pour le vendeur
        $this->pushService->notifyNewOrder($order->seller, $order);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Confirmer une commande (paiement reçu)
     * → Notifier l'ACHETEUR que sa commande est confirmée
     */
    public function confirm($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Vérifier que l'utilisateur est bien le vendeur
        if ($order->seller_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        // 🔔 NOTIFICATION PUSH: Commande confirmée pour l'acheteur
        $this->pushService->notifyOrderConfirmed($order->buyer, $order);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Expédier une commande
     * → Notifier l'ACHETEUR que sa commande est en route
     */
    public function ship($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Vérifier que l'utilisateur est bien le vendeur
        if ($order->seller_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->update([
            'status' => 'shipped',
            'shipped_at' => now()
        ]);

        // 🔔 NOTIFICATION PUSH: Commande expédiée
        $this->pushService->notifyOrderShipped($order->buyer, $order);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Marquer une commande comme livrée
     * → Notifier le VENDEUR que la commande est complétée
     */
    public function complete($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Vérifier que l'utilisateur est bien l'acheteur
        if ($order->buyer_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        // 🔔 NOTIFICATION PUSH: Commande livrée (informer le vendeur)
        $this->pushService->sendToUser($order->seller, [
            'title' => '✅ Commande livrée',
            'body' => "La commande #{$order->id} a été reçue par {$order->buyer->name}",
            'icon' => '/images/icons/icon-192x192.png',
            'tag' => "order-delivered-{$order->id}"
        ], [
            'url' => "/orders/{$order->id}",
            'orderId' => (string) $order->id,
            'type' => 'order_delivered'
        ]);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Annuler une commande
     * → Notifier les 2 PARTIES de l'annulation
     */
    public function cancel($orderId, Request $request)
    {
        $order = Order::findOrFail($orderId);

        // Vérifier que l'utilisateur est l'acheteur ou le vendeur
        if ($order->buyer_id !== auth()->id() && $order->seller_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason
        ]);

        // 🔔 NOTIFICATION PUSH: Annulation pour l'acheteur (si vendeur annule)
        if (auth()->id() === $order->seller_id) {
            $this->pushService->sendToUser($order->buyer, [
                'title' => '❌ Commande annulée',
                'body' => "Le vendeur a annulé votre commande #{$order->id}",
                'icon' => '/images/icons/icon-192x192.png',
                'tag' => "order-cancelled-{$order->id}",
                'requireInteraction' => true
            ], [
                'url' => "/orders/{$order->id}",
                'orderId' => (string) $order->id,
                'type' => 'order_cancelled'
            ]);
        }

        // 🔔 NOTIFICATION PUSH: Annulation pour le vendeur (si acheteur annule)
        if (auth()->id() === $order->buyer_id) {
            $this->pushService->sendToUser($order->seller, [
                'title' => '❌ Commande annulée',
                'body' => "L'acheteur a annulé la commande #{$order->id}",
                'icon' => '/images/icons/icon-192x192.png',
                'tag' => "order-cancelled-{$order->id}",
                'requireInteraction' => true
            ], [
                'url' => "/orders/{$order->id}",
                'orderId' => (string) $order->id,
                'type' => 'order_cancelled'
            ]);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * BONUS: Notification personnalisée
     */
    public function sendCustomNotification($orderId, Request $request)
    {
        $order = Order::findOrFail($orderId);

        // Déterminer le destinataire
        $recipient = $request->recipient === 'buyer' ? $order->buyer : $order->seller;

        // Envoyer notification personnalisée
        $this->pushService->sendToUser($recipient, [
            'title' => $request->title,
            'body' => $request->message,
            'icon' => '/images/icons/icon-192x192.png',
            'tag' => "order-custom-{$order->id}",
            'requireInteraction' => $request->require_interaction ?? false
        ], [
            'url' => $request->url ?? "/orders/{$order->id}",
            'orderId' => (string) $order->id,
            'type' => 'custom'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification envoyée'
        ]);
    }
}

/**
 * INTÉGRATION DANS LE OrderController EXISTANT
 * ============================================
 * 
 * 1. Injecter PushNotificationService dans le constructeur:
 * 
 *    protected $pushService;
 * 
 *    public function __construct(PushNotificationService $pushService)
 *    {
 *        $this->pushService = $pushService;
 *    }
 * 
 * 
 * 2. Ajouter les notifications dans les méthodes existantes:
 * 
 *    public function store(Request $request)
 *    {
 *        // ... création de la commande ...
 *        
 *        // Ajouter cette ligne:
 *        $this->pushService->notifyNewOrder($order->seller, $order);
 *        
 *        return response()->json($order);
 *    }
 * 
 * 
 * 3. Tester avec curl:
 * 
 *    curl -X POST http://localhost:8000/api/orders \
 *      -H "Authorization: Bearer TOKEN" \
 *      -H "Content-Type: application/json" \
 *      -d '{"item_id": 1, "quantity": 1}'
 * 
 * 
 * 4. Vérifier les logs:
 * 
 *    tail -f storage/logs/laravel.log
 * 
 * 
 * POINTS D'INTÉGRATION RECOMMANDÉS:
 * ==================================
 * 
 * ✅ store()      → notifyNewOrder()         (vendeur)
 * ✅ confirm()    → notifyOrderConfirmed()   (acheteur)
 * ✅ ship()       → notifyOrderShipped()     (acheteur)
 * ✅ complete()   → notification personnalisée (vendeur)
 * ✅ cancel()     → notification personnalisée (2 parties)
 * ✅ refund()     → notification personnalisée (acheteur)
 */
