<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Événement en attente de publication sur le bus.
 *
 * Écrit dans la transaction métier, publiée après coup : c'est ce qui évite
 * de perdre `payment.completed` quand Redis est au moment du webhook indisponible.
 */
class OutboxMessage extends Model
{
    protected $fillable = [
        'event_id',
        'type',
        'stream',
        'payload',
        'attempts',
        'published_at',
        'available_at',
        'last_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'published_at' => 'datetime',
        'available_at' => 'datetime',
    ];

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }
}
