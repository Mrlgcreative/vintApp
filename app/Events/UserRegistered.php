<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public ?string $referralCode;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, ?string $referralCode = null)
    {
        $this->user = $user;
        $this->referralCode = $referralCode;
    }
}