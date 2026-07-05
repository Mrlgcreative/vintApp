<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends BaseDatabaseChannel
{
    protected function buildPayload($notifiable, Notification $notification)
    {
        $data = $notification->toArray($notifiable);

        return [
            'id' => $notification->id,
            'type' => get_class($notification),
            'title' => $data['title'] ?? '',
            'message' => $data['message'] ?? '',
            'data' => $data,
            'action_url' => $data['action_url'] ?? $data['url'] ?? null,
            'read_at' => null,
        ];
    }
}
