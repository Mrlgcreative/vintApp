<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Log;

class OrderProximityService
{
    public function __construct(protected FirebasePushService $pushService)
    {
    }

    /**
     * Alert the buyer via push FCM when the courier is close to the
     * delivery destination. Sends at most once per order.
     */
    public function check(Order $order, OrderTracking $tracking): bool
    {
        if ($order->proximity_notified_at) {
            return false;
        }

        if (in_array($order->status, ['delivered', 'completed', 'cancelled', 'refunded'])) {
            return false;
        }

        if (!$tracking->latitude || !$tracking->longitude) {
            return false;
        }

        $customerLat = $tracking->customer_latitude ?? $order->deliveryAddress?->effective_latitude;
        $customerLng = $tracking->customer_longitude ?? $order->deliveryAddress?->effective_longitude;

        if (!$customerLat || !$customerLng) {
            return false;
        }

        $distance = OrderTracking::calculateDistance(
            (float) $tracking->latitude,
            (float) $tracking->longitude,
            (float) $customerLat,
            (float) $customerLng
        );

        if ($distance === null || $distance > OrderTracking::PROXIMITY_THRESHOLD_KM) {
            return false;
        }

        $buyer = $order->buyer;
        $sent = ($buyer && $buyer->fcm_token)
            ? $this->pushService->notifyOrderNearBuyer($buyer, $order, $distance)
            : false;

        // On mémorise l'envoi (même en cas d'échec) pour éviter les doublons à chaque mise à jour de position.
        $order->proximity_notified_at = now();
        $order->save();

        Log::info('📡 Notification proximité commande envoyée', [
            'order_id' => $order->id,
            'distance_km' => $distance,
            'push_sent' => $sent,
        ]);

        return true;
    }
}