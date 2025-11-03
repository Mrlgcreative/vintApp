<?php

namespace App\Events;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredWithReferral
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $referral;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, ?Referral $referral = null)
    {
        $this->user = $user;
        $this->referral = $referral;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}