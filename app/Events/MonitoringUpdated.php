<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement temps réel — met à jour le dashboard de monitoring.
 *
 * Diffusé sur un canal public `monitoring.updates` après chaque
 * capture de statistiques, pour que toutes les fenêtres d'admin
 * ouvertes soient rafraîchies instantanément (via Pusher/Echo).
 */
class MonitoringUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public array $stats;
    public array $health;
    public string $timestamp;
    public array $alerts;

    public function __construct(array $stats, array $health, array $alerts = [])
    {
        $this->stats = $stats;
        $this->health = $health;
        $this->timestamp = now()->toIso8601String();
        $this->alerts = $alerts;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Canal public : le dashboard admin n'a pas besoin d'être privé,
        // mais il est derrière l'authentification admin au niveau HTTP.
        return [
            new Channel('monitoring.updates'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'monitoring.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'stats' => $this->stats,
            'health' => $this->health,
            'timestamp' => $this->timestamp,
            'alerts' => $this->alerts,
        ];
    }
}
