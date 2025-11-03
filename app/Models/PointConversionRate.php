<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointConversionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'points_per_unit',
        'minimum_conversion',
        'maximum_conversion',
        'conversion_fee_percentage',
        'conversion_fee_fixed',
        'is_active',
        'conditions',
        'effective_from',
        'effective_until',
        'notes'
    ];

    protected $casts = [
        'points_per_unit' => 'decimal:2',
        'minimum_conversion' => 'decimal:2',
        'maximum_conversion' => 'decimal:2',
        'conversion_fee_percentage' => 'decimal:2',
        'conversion_fee_fixed' => 'decimal:2',
        'is_active' => 'boolean',
        'conditions' => 'array',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime'
    ];

    /**
     * Scope pour les taux actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('effective_from', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('effective_until')
                          ->orWhere('effective_until', '>', now());
                    });
    }

    /**
     * Scope par devise
     */
    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Obtient le taux de conversion actuel pour une devise
     */
    public static function getCurrentRate(string $currency): ?self
    {
        return static::active()
                     ->byCurrency($currency)
                     ->orderBy('effective_from', 'desc')
                     ->first();
    }

    /**
     * Calcule le montant d'argent pour des points donnés
     */
    public function calculateCashAmount(float $points): array
    {
        if ($points < $this->minimum_conversion) {
            return [
                'valid' => false,
                'error' => "Minimum {$this->minimum_conversion} points requis"
            ];
        }

        if ($this->maximum_conversion && $points > $this->maximum_conversion) {
            return [
                'valid' => false,
                'error' => "Maximum {$this->maximum_conversion} points autorisés"
            ];
        }

        // Calcul du montant de base
        $baseAmount = $points / $this->points_per_unit;

        // Calcul des frais
        $percentageFee = ($baseAmount * $this->conversion_fee_percentage) / 100;
        $totalFees = $percentageFee + $this->conversion_fee_fixed;

        // Montant final
        $finalAmount = $baseAmount - $totalFees;

        return [
            'valid' => true,
            'points_used' => $points,
            'base_amount' => round($baseAmount, 2),
            'fees' => round($totalFees, 2),
            'final_amount' => round($finalAmount, 2),
            'currency' => $this->currency,
            'conversion_rate' => $this->points_per_unit,
            'fee_breakdown' => [
                'percentage_fee' => round($percentageFee, 2),
                'fixed_fee' => $this->conversion_fee_fixed,
                'total_fee' => round($totalFees, 2)
            ]
        ];
    }

    /**
     * Calcule le nombre de points nécessaires pour un montant donné
     */
    public function calculatePointsNeeded(float $cashAmount): array
    {
        // Ajouter les frais au montant souhaité
        $totalWithFees = $cashAmount + $this->conversion_fee_fixed;
        $amountBeforeFees = $totalWithFees / (1 - $this->conversion_fee_percentage / 100);
        
        $pointsNeeded = $amountBeforeFees * $this->points_per_unit;

        return [
            'points_needed' => ceil($pointsNeeded),
            'cash_amount' => $cashAmount,
            'total_fees' => round($amountBeforeFees - $cashAmount, 2),
            'currency' => $this->currency
        ];
    }

    /**
     * Vérifie si une conversion est valide
     */
    public function canConvert(float $points, int $userId = null): array
    {
        $result = ['valid' => true, 'errors' => []];

        // Vérifier le minimum
        if ($points < $this->minimum_conversion) {
            $result['valid'] = false;
            $result['errors'][] = "Minimum {$this->minimum_conversion} points requis";
        }

        // Vérifier le maximum
        if ($this->maximum_conversion && $points > $this->maximum_conversion) {
            $result['valid'] = false;
            $result['errors'][] = "Maximum {$this->maximum_conversion} points autorisés";
        }

        // Vérifier les conditions spéciales
        if ($this->conditions && $userId) {
            $conditionsResult = $this->checkSpecialConditions($userId);
            if (!$conditionsResult['valid']) {
                $result['valid'] = false;
                $result['errors'] = array_merge($result['errors'], $conditionsResult['errors']);
            }
        }

        return $result;
    }

    /**
     * Vérifie les conditions spéciales
     */
    protected function checkSpecialConditions(int $userId): array
    {
        $result = ['valid' => true, 'errors' => []];
        
        if (!$this->conditions) {
            return $result;
        }

        $user = User::find($userId);
        if (!$user) {
            return ['valid' => false, 'errors' => ['Utilisateur introuvable']];
        }

        // Vérifier le niveau minimum
        if (isset($this->conditions['min_level'])) {
            $userPoints = $user->points;
            if (!$userPoints || $userPoints->level < $this->conditions['min_level']) {
                $result['valid'] = false;
                $result['errors'][] = "Niveau {$this->conditions['min_level']} requis";
            }
        }

        // Vérifier le nombre minimum de parrainages
        if (isset($this->conditions['min_referrals'])) {
            $referralCount = $user->referrals()->completed()->count();
            if ($referralCount < $this->conditions['min_referrals']) {
                $result['valid'] = false;
                $result['errors'][] = "{$this->conditions['min_referrals']} parrainages requis";
            }
        }

        // Vérifier la limite quotidienne
        if (isset($this->conditions['daily_limit'])) {
            $todayRedemptions = PointRedemption::where('user_id', $userId)
                                              ->where('type', 'cash_conversion')
                                              ->where('currency', $this->currency)
                                              ->whereDate('created_at', today())
                                              ->sum('points_used');
            
            if ($todayRedemptions >= $this->conditions['daily_limit']) {
                $result['valid'] = false;
                $result['errors'][] = "Limite quotidienne de {$this->conditions['daily_limit']} points atteinte";
            }
        }

        return $result;
    }

    /**
     * Obtient les détails du taux
     */
    public function getDetails(): array
    {
        return [
            'currency' => $this->currency,
            'rate' => "{$this->points_per_unit} pts = 1 {$this->currency}",
            'minimum_conversion' => $this->minimum_conversion,
            'maximum_conversion' => $this->maximum_conversion,
            'conversion_fee_percentage' => $this->conversion_fee_percentage,
            'conversion_fee_fixed' => $this->conversion_fee_fixed,
            'effective_from' => $this->effective_from->format('d/m/Y'),
            'effective_until' => $this->effective_until?->format('d/m/Y') ?? 'Pas d\'expiration',
            'conditions' => $this->conditions ?? [],
            'is_active' => $this->is_active,
            'notes' => $this->notes
        ];
    }

    /**
     * Formate le taux pour l'affichage
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format((float)$this->points_per_unit, 0) . " pts = 1 {$this->currency}";
    }

    /**
     * Obtient l'exemple de conversion
     */
    public function getConversionExampleAttribute(): array
    {
        $examplePoints = max($this->minimum_conversion, 1000);
        return $this->calculateCashAmount($examplePoints);
    }
}