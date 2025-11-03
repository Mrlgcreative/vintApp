<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'title',
        'description',
        'is_active',
        'max_uses',
        'current_uses',
        'bonus_points',
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bonus_points' => 'decimal:2',
        'expires_at' => 'datetime',
        'current_uses' => 'integer',
        'max_uses' => 'integer'
    ];

    /**
     * Relation avec l'utilisateur propriétaire du code
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les parrainages effectués avec ce code
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Génère un code de parrainage unique
     */
    public static function generateUniqueCode($prefix = 'VINT'): string
    {
        do {
            $code = $prefix . strtoupper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Scope pour les codes actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope pour les codes disponibles (pas encore à la limite d'utilisation)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_uses')
              ->orWhereRaw('current_uses < max_uses');
        });
    }

    /**
     * Vérifie si le code peut encore être utilisé
     */
    public function canBeUsed(): bool
    {
        // Vérifier si actif
        if (!$this->is_active) {
            return false;
        }

        // Vérifier l'expiration
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Vérifier la limite d'utilisation
        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Utilise le code (incrémente le compteur)
     */
    public function use(): bool
    {
        if (!$this->canBeUsed()) {
            return false;
        }

        $this->increment('current_uses');
        return true;
    }

    /**
     * Obtient les statistiques d'utilisation
     */
    public function getUsageStats(): array
    {
        $totalReferrals = $this->referrals()->count();
        $activeReferrals = $this->referrals()->where('status', 'active')->count();
        $completedReferrals = $this->referrals()->where('status', 'completed')->count();
        $totalPointsGenerated = $this->referrals()->sum('points_earned');

        return [
            'total_uses' => $this->current_uses,
            'max_uses' => $this->max_uses,
            'remaining_uses' => $this->max_uses ? ($this->max_uses - $this->current_uses) : null,
            'total_referrals' => $totalReferrals,
            'active_referrals' => $activeReferrals,
            'completed_referrals' => $completedReferrals,
            'total_points_generated' => $totalPointsGenerated,
            'conversion_rate' => $totalReferrals > 0 ? ($completedReferrals / $totalReferrals) * 100 : 0,
            'is_expired' => $this->expires_at && $this->expires_at->isPast(),
            'days_until_expiry' => $this->expires_at ? $this->expires_at->diffInDays(now()) : null,
        ];
    }

    /**
     * Formate la date d'expiration
     */
    public function getFormattedExpiryAttribute(): ?string
    {
        if (!$this->expires_at) {
            return 'Aucune expiration';
        }

        return $this->expires_at->format('d/m/Y');
    }

    /**
     * Obtient le statut du code
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactif';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Expiré';
        }

        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return 'Limite atteinte';
        }

        return 'Actif';
    }

    /**
     * Obtient l'URL de partage du code
     */
    public function getShareUrlAttribute(): string
    {
        return route('register') . '?ref=' . $this->code;
    }

    /**
     * Boot method pour générer automatiquement un code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($referralCode) {
            if (!$referralCode->code) {
                $referralCode->code = static::generateUniqueCode();
            }
        });
    }
}