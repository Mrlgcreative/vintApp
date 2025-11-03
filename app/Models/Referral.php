<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code_id',
        'status',
        'points_earned',
        'bonus_points',
        'activated_at',
        'completed_at',
        'conditions_met',
        'notes'
    ];

    protected $casts = [
        'points_earned' => 'decimal:2',
        'bonus_points' => 'decimal:2',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'conditions_met' => 'array'
    ];

    // Statuts possibles
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relation avec l'utilisateur parrain
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Relation avec l'utilisateur filleul
     */
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Relation avec le code de parrainage utilisé
     */
    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class);
    }

    /**
     * Scope pour les parrainages actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope pour les parrainages complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope pour les parrainages en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Active le parrainage
     */
    public function activate(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'activated_at' => now()
        ]);

        return true;
    }

    /**
     * Complète le parrainage et attribue les points
     */
    public function complete($pointsEarned = null): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        // Calculer les points si non spécifiés
        if ($pointsEarned === null) {
            $pointsEarned = $this->calculatePoints();
        }

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'points_earned' => $pointsEarned
        ]);

        // Ajouter les points au parrain
        $this->referrer->points()->credit($pointsEarned, 'earn_referral', "Parrainage de {$this->referred->name}");

        return true;
    }

    /**
     * Calcule les points à attribuer selon les règles
     */
    protected function calculatePoints(): float
    {
        $basePoints = 50.0; // Points de base pour un parrainage
        $bonusPoints = $this->referralCode->bonus_points ?? 0;
        $levelMultiplier = $this->referrer->points->level_multiplier ?? 1.0;

        return ($basePoints + $bonusPoints) * $levelMultiplier;
    }

    /**
     * Vérifie si les conditions de completion sont remplies
     */
    public function checkCompletionConditions(): bool
    {
        $conditions = [
            'email_verified' => $this->referred->email_verified_at !== null,
            'profile_completed' => $this->hasCompletedProfile(),
            'first_purchase' => $this->hasFirstPurchase(),
        ];

        // Mettre à jour les conditions remplies
        $this->update(['conditions_met' => $conditions]);

        // Vérifier si toutes les conditions sont remplies
        return !in_array(false, $conditions, true);
    }

    /**
     * Vérifie si le profil est complété
     */
    protected function hasCompletedProfile(): bool
    {
        $user = $this->referred;
        return !empty($user->name) && 
               !empty($user->phone) && 
               !empty($user->location);
    }

    /**
     * Vérifie si l'utilisateur a fait son premier achat
     */
    protected function hasFirstPurchase(): bool
    {
        return $this->referred->ordersAsBuyer()
                              ->where('status', 'completed')
                              ->exists();
    }

    /**
     * Obtient le statut formaté
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_ACTIVE => 'Actif',
            self::STATUS_COMPLETED => 'Complété',
            self::STATUS_CANCELLED => 'Annulé',
            default => 'Inconnu'
        };
    }

    /**
     * Obtient les détails du parrainage
     */
    public function getDetails(): array
    {
        return [
            'referrer_name' => $this->referrer->name,
            'referred_name' => $this->referred->name,
            'code_used' => $this->referralCode->code,
            'status' => $this->formatted_status,
            'points_earned' => $this->points_earned,
            'bonus_points' => $this->bonus_points,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'activated_at' => $this->activated_at?->format('d/m/Y H:i'),
            'completed_at' => $this->completed_at?->format('d/m/Y H:i'),
            'conditions_met' => $this->conditions_met ?? [],
            'days_since_referral' => $this->created_at->diffInDays(now()),
        ];
    }
}