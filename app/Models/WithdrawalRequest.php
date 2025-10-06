<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_transaction_id',
        'phone_number',
        'payment_method',
        'amount',
        'currency',
        'status',
        'provider_reference',
        'provider_response',
        'failure_reason',
        'retry_count',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'provider_response' => 'array',
        'retry_count' => 'integer',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Relation avec la transaction wallet
     */
    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    /**
     * Vérifier si le retrait est en attente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifier si le retrait est en cours de traitement
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Vérifier si le retrait est complété
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifier si le retrait a échoué
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Vérifier si le retrait a été annulé
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Marquer comme en traitement
     */
    public function markAsProcessing(string $providerReference = null): void
    {
        $this->update([
            'status' => 'processing',
            'provider_reference' => $providerReference ?? $this->provider_reference,
        ]);
    }

    /**
     * Marquer comme complété
     */
    public function markAsCompleted(array $providerResponse = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'provider_response' => $providerResponse ?? $this->provider_response,
        ]);
    }

    /**
     * Marquer comme échoué
     */
    public function markAsFailed(string $reason, array $providerResponse = null): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'failed_at' => now(),
            'provider_response' => $providerResponse ?? $this->provider_response,
        ]);
    }

    /**
     * Incrémenter le compteur de retry
     */
    public function incrementRetryCount(): void
    {
        $this->increment('retry_count');
    }

    /**
     * Scope pour les retraits en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les retraits en cours
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope pour les retraits complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les retraits échoués
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Obtenir le nom lisible du provider
     */
    public function getProviderNameAttribute(): string
    {
        return match($this->payment_method) {
            'orange_money' => '🟠 Orange Money',
            'airtel_money' => '🔴 Airtel Money',
            'mpesa' => '🟢 M-Pesa',
            'africell' => '🔵 Africell Money',
            'illicocash' => '💳 Illicocash',
            default => $this->payment_method,
        };
    }

    /**
     * Obtenir le badge de statut
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">En attente</span>',
            'processing' => '<span class="badge bg-info">En cours</span>',
            'completed' => '<span class="badge bg-success">Complété</span>',
            'failed' => '<span class="badge bg-danger">Échec</span>',
            'cancelled' => '<span class="badge bg-secondary">Annulé</span>',
            default => '<span class="badge bg-light">' . $this->status . '</span>',
        };
    }
}
