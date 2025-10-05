<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id', // L'acheteur qui a demandé la réduction
        'seller_id', // Le vendeur qui propose la réduction
        'message_id', // Message lié à la demande
        'original_price',
        'discount_percentage',
        'discount_amount',
        'final_price',
        'status',
        'expires_at',
        'reason'
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';
    const STATUS_USED = 'used';

    /**
     * Relation avec l'article
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relation avec l'acheteur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec le vendeur
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Relation avec le message
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Calculer le prix final avec réduction
     */
    public function calculateFinalPrice(): void
    {
        if ($this->discount_percentage > 0) {
            $this->discount_amount = ($this->original_price * $this->discount_percentage) / 100;
        }
        
        $this->final_price = $this->original_price - $this->discount_amount;
    }

    /**
     * Accesseur pour le statut formaté
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'approved' => 'Approuvée',
            'rejected' => 'Refusée',
            'expired' => 'Expirée',
            'used' => 'Utilisée',
            default => 'Inconnu'
        };
    }

    /**
     * Accesseur pour la classe CSS du statut
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'badge bg-warning',
            'approved' => 'badge bg-success',
            'rejected' => 'badge bg-danger',
            'expired' => 'badge bg-secondary',
            'used' => 'badge bg-info',
            default => 'badge bg-light'
        };
    }

    /**
     * Vérifier si la réduction est encore valide
     */
    public function isValid(): bool
    {
        return $this->status === 'approved' && 
               $this->expires_at > now();
    }

    /**
     * Appliquer la réduction
     */
    public function apply(): bool
    {
        if ($this->isValid()) {
            $this->update(['status' => 'used']);
            return true;
        }
        return false;
    }

    /**
     * Scope pour les réductions valides
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'approved')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope pour les réductions d'un acheteur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour les réductions d'un vendeur
     */
    public function scopeFromSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }
}