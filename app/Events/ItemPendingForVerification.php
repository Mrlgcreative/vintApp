<?php

namespace App\Events;

use App\Models\Item;
use App\Models\ExpertProfile;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemPendingForVerification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Item $item,
        public array $expertIds = []
    ) {}

    public function broadcastOn(): array
    {
        $channels = [];
        
        // Broadcast to expert-specific channels
        foreach ($this->expertIds as $expertId) {
            $channels[] = new PrivateChannel("expert.{$expertId}");
        }

        // Fallback to general expert channel if no specific experts
        if (empty($this->expertIds)) {
            $channels[] = new PrivateChannel('expert.notifications');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'item.pending';
    }

    public function broadcastWith(): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_category' => $this->item->category?->name,
            'item_image' => !empty($this->item->images) ? asset('storage/' . $this->item->images[0]) : null,
            'item_price' => $this->item->price,
            'seller_name' => $this->item->user?->name,
            'message' => "Nouvel article en attente de vérification : {$this->item->name}",
            'timestamp' => now()->toIso8601String()
        ];
    }
}
