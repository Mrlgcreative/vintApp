<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCallback extends Model
{
    protected $fillable = [
        'transaction_id',
        'external_transaction_id',
        'provider',
        'status',
        'amount',
        'currency',
        'phone_number',
        'callback_type',
        'raw_payload',
        'parsed_data',
        'signature',
        'ip_address',
        'is_verified',
        'is_processed',
        'processed_at',
        'processing_error',
        'retry_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'parsed_data' => 'array',
        'is_verified' => 'boolean',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Relation avec la transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Marquer comme traité
     */
    public function markAsProcessed(): void
    {
        $this->update([
            'is_processed' => true,
            'processed_at' => now(),
        ]);
    }

    /**
     * Marquer comme vérifié
     */
    public function markAsVerified(): void
    {
        $this->update(['is_verified' => true]);
    }

    /**
     * Incrémenter le compteur de tentatives
     */
    public function incrementRetry(): void
    {
        $this->increment('retry_count');
    }

    /**
     * Enregistrer une erreur de traitement
     */
    public function recordError(string $error): void
    {
        $this->update(['processing_error' => $error]);
    }

    /**
     * Scope pour les callbacks non traités
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Scope pour les callbacks vérifiés
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope pour un provider spécifique
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope pour un statut spécifique
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
