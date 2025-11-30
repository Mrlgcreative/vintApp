<?php

namespace App\Events;

use App\Models\Item;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;
    public $status;
    public $reason;
    public $adminName;

    /**
     * Create a new event instance.
     */
    public function __construct(Item $item, string $status, ?string $reason = null, ?string $adminName = null)
    {
        $this->item = $item;
        $this->status = $status;
        $this->reason = $reason;
        $this->adminName = $adminName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->item->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'item.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'status' => $this->status,
            'reason' => $this->reason,
            'admin_name' => $this->adminName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
