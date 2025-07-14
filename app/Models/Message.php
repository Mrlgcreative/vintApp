<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'item_id',
        'subject',
        'content',
        'attachment', // Ajouté pour permettre l'upload de fichiers
        'type',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relation avec l'expéditeur
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relation avec le destinataire
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Relation avec l'article (optionnel)
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Accesseur pour le type de message formaté
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'general' => 'Général',
            'item_inquiry' => 'Demande d\'article',
            'order_related' => 'Commande',
            default => 'Général'
        };
    }

    /**
     * Accesseur pour le statut de lecture formaté
     */
    public function getReadStatusAttribute(): string
    {
        return $this->is_read ? 'Lu' : 'Non lu';
    }

    /**
     * Accesseur pour la classe CSS du statut
     */
    public function getReadStatusClassAttribute(): string
    {
        return $this->is_read ? 'text-muted' : 'text-primary fw-bold';
    }

    /**
     * Scope pour les messages non lus
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope pour les messages reçus par un utilisateur
     */
    public function scopeReceivedBy($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    /**
     * Scope pour les messages envoyés par un utilisateur
     */
    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }
}
