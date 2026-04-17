<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportAgent extends Model
{
    protected $fillable = [
        'user_id',
        'is_active',
        'max_chats',
        'specialties',
        'last_assigned_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_chats' => 'integer',
        'specialties' => 'array',
        'last_assigned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nombre de tickets actifs assignés à cet agent.
     */
    public function activeChatsCount(): int
    {
        return SupportChat::where('admin_id', $this->user_id)
            ->whereNotIn('status', ['closed'])
            ->count();
    }

    /**
     * Vérifie si l'agent peut prendre de nouveaux tickets.
     */
    public function canAcceptChats(): bool
    {
        return $this->is_active && $this->activeChatsCount() < $this->max_chats;
    }

    /**
     * Retourne l'agent le moins chargé parmi les actifs, optionnellement filtré par spécialité.
     */
    public static function leastLoaded(?string $category = null): ?self
    {
        $query = self::where('is_active', true)
            ->with('user');

        if ($category) {
            $query->whereJsonContains('specialties', $category);
        }

        $agents = $query->get()->filter(fn($a) => $a->canAcceptChats());

        return $agents->sortBy(fn($a) => $a->activeChatsCount())->first();
    }

    /**
     * Scope agents actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
