<?php

namespace App\Events;

use App\Models\User;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public User $buyer;
    public User $seller;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->buyer = $order->buyer;
        $this->seller = $order->seller;
    }
}