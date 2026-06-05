<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $userId;
    public array $message;

    public function __construct(Message $msg)
    {
        $this->userId = $msg->receiver_id;
        $this->message = [
            'id' => $msg->id,
            'sender_id' => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'content' => $msg->content,
            'attachment' => $msg->attachment ? \Storage::url($msg->attachment) : null,
            'type' => $msg->type,
            'duration' => $msg->duration,
            'created_at' => $msg->created_at->toDateTimeString(),
            'time' => $msg->created_at->format('H:i'),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return $this->message;
    }
}
