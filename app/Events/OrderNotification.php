<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $type;
    public $message;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     * @param string $type (new_order, payment_confirmed, shipped, delivered, completed)
     * @param string $message
     * @param int $userId L'ID de l'utilisateur qui doit recevoir la notification
     */
    public function __construct(Order $order, string $type, string $message, int $userId)
    {
        $this->order = $order;
        $this->type = $type;
        $this->message = $message;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * Le nom de l'événement diffusé
     */
    public function broadcastAs(): string
    {
        return 'order.notification';
    }

    /**
     * Les données diffusées avec l'événement
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => $this->type,
            'message' => $this->message,
            'item_name' => $this->order->item->name ?? 'Article',
            'buyer_name' => $this->order->buyer->name ?? 'Acheteur',
            'seller_name' => $this->order->item->user->name ?? 'Vendeur',
            'total_amount' => $this->order->total_amount,
            'currency' => $this->order->currency,
            'status' => $this->order->status,
            'created_at' => $this->order->created_at->toDateTimeString(),
        ];
    }
}
