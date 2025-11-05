<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AffiliateReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'value',
        'description',
        'reason',
        'is_public',
        'metadata',
        'status',
        'expires_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean',
        'value' => 'decimal:2',
        'expires_at' => 'datetime'
    ];

    /**
     * Types de récompenses disponibles
     */
    const TYPES = [
        'points' => 'Points Bonus',
        'cash' => 'Récompense en Argent',
        'badge' => 'Badge Spécial',
        'level_boost' => 'Boost de Niveau',
        'custom' => 'Récompense Personnalisée'
    ];

    /**
     * Statuts des récompenses
     */
    const STATUSES = [
        'active' => 'Active',
        'expired' => 'Expirée',
        'revoked' => 'Révoquée'
    ];

    /**
     * Relation avec l'utilisateur qui reçoit la récompense
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'admin qui a attribué la récompense
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope pour les récompenses actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope pour les récompenses publiques
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope pour les récompenses par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Vérifier si la récompense est expirée
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Obtenir le nom du type de récompense
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtenir le nom du statut
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Obtenir la valeur formatée selon le type
     */
    public function getFormattedValueAttribute(): string
    {
        return match($this->type) {
            'points' => number_format($this->value) . ' points',
            'cash' => number_format($this->value, 2) . ' ' . ($this->metadata['currency'] ?? 'USD'),
            'level_boost' => '+' . $this->value . ' niveau(x)',
            default => $this->description ?? 'Récompense'
        };
    }

    /**
     * Calculer les jours restants avant expiration
     */
    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(0, $this->expires_at->diffInDays(now(), false));
    }

    /**
     * Révoquer la récompense
     */
    public function revoke(string $reason = null): bool
    {
        $this->update([
            'status' => 'revoked',
            'metadata' => array_merge($this->metadata ?? [], [
                'revoked_at' => now(),
                'revoked_reason' => $reason
            ])
        ]);

        return true;
    }

    /**
     * Vérifier si la récompense peut être utilisée
     */
    public function isUsable(): bool
    {
        return $this->status === 'active' && !$this->is_expired;
    }

    /**
     * Obtenir les récompenses récentes pour un utilisateur
     */
    public static function getRecentForUser($userId, $limit = 5)
    {
        return static::where('user_id', $userId)
                    ->active()
                    ->latest()
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtenir les statistiques des récompenses
     */
    public static function getStats($period = null)
    {
        $query = static::query();

        if ($period) {
            $startDate = match($period) {
                'today' => Carbon::today(),
                'this_week' => Carbon::now()->startOfWeek(),
                'this_month' => Carbon::now()->startOfMonth(),
                'this_year' => Carbon::now()->startOfYear(),
                default => null
            };

            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
        }

        return [
            'total_rewards' => $query->count(),
            'total_value' => $query->sum('value'),
            'by_type' => $query->groupBy('type')
                              ->selectRaw('type, COUNT(*) as count, SUM(value) as total_value')
                              ->get()
                              ->keyBy('type'),
            'active_count' => $query->active()->count(),
            'public_count' => $query->public()->count()
        ];
    }

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-expirer les récompenses temporaires
        static::creating(function ($reward) {
            if ($reward->type === 'badge' && 
                isset($reward->metadata['duration']) && 
                $reward->metadata['duration'] !== 'permanent') {
                
                $reward->expires_at = Carbon::now()->addDays($reward->metadata['duration']);
            }
        });
    }
}