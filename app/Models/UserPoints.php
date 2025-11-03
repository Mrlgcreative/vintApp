<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoints extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'available_points',
        'pending_points',
        'redeemed_points',
        'level',
        'level_multiplier',
        'last_activity_at'
    ];

    protected $casts = [
        'total_points' => 'decimal:2',
        'available_points' => 'decimal:2',
        'pending_points' => 'decimal:2',
        'redeemed_points' => 'decimal:2',
        'level_multiplier' => 'decimal:2',
        'last_activity_at' => 'datetime',
        'level' => 'integer'
    ];

    // Paliers de niveaux (points nécessaires pour chaque niveau)
    const LEVEL_THRESHOLDS = [
        1 => 0,       // Niveau 1: 0 points
        2 => 500,     // Niveau 2: 500 points
        3 => 1500,    // Niveau 3: 1500 points
        4 => 3000,    // Niveau 4: 3000 points
        5 => 5000,    // Niveau 5: 5000 points
        6 => 8000,    // Niveau 6: 8000 points
        7 => 12000,   // Niveau 7: 12000 points
        8 => 18000,   // Niveau 8: 18000 points
        9 => 25000,   // Niveau 9: 25000 points
        10 => 35000   // Niveau 10: 35000 points
    ];

    // Multiplicateurs par niveau
    const LEVEL_MULTIPLIERS = [
        1 => 1.0,
        2 => 1.1,
        3 => 1.2,
        4 => 1.3,
        5 => 1.5,
        6 => 1.7,
        7 => 2.0,
        8 => 2.3,
        9 => 2.7,
        10 => 3.0
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les transactions de points
     */
    public function transactions()
    {
        return $this->hasMany(PointTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Crédite des points au compte
     */
    public function credit(float $amount, string $type = 'earn_bonus', string $description = 'Ajout de points', array $metadata = []): PointTransaction
    {
        $balanceBefore = $this->available_points;
        $this->available_points += $amount;
        $this->total_points += $amount;
        $this->last_activity_at = now();
        
        // Vérifier et mettre à jour le niveau
        $this->updateLevel();
        
        $this->save();

        // Créer la transaction
        return PointTransaction::create([
            'user_id' => $this->user_id,
            'transaction_id' => PointTransaction::generateTransactionId(),
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->available_points,
            'description' => $description,
            'metadata' => $metadata,
            'status' => 'completed',
            'processed_at' => now()
        ]);
    }

    /**
     * Débite des points du compte
     */
    public function debit(float $amount, string $type = 'redeem_cash', string $description = 'Utilisation de points', array $metadata = []): ?PointTransaction
    {
        if ($this->available_points < $amount) {
            return null; // Pas assez de points
        }

        $balanceBefore = $this->available_points;
        $this->available_points -= $amount;
        $this->redeemed_points += $amount;
        $this->last_activity_at = now();
        $this->save();

        // Créer la transaction
        return PointTransaction::create([
            'user_id' => $this->user_id,
            'transaction_id' => PointTransaction::generateTransactionId(),
            'type' => $type,
            'amount' => -$amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->available_points,
            'description' => $description,
            'metadata' => $metadata,
            'status' => 'completed',
            'processed_at' => now()
        ]);
    }

    /**
     * Met à jour le niveau selon les points accumulés
     */
    public function updateLevel(): bool
    {
        $newLevel = $this->calculateLevel();
        
        if ($newLevel !== $this->level) {
            $oldLevel = $this->level;
            $this->level = $newLevel;
            $this->level_multiplier = self::LEVEL_MULTIPLIERS[$newLevel];
            
            // Créer une transaction pour le changement de niveau
            $this->credit(0, 'earn_bonus', "Passage au niveau $newLevel (depuis niveau $oldLevel)", [
                'old_level' => $oldLevel,
                'new_level' => $newLevel,
                'multiplier' => $this->level_multiplier
            ]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Calcule le niveau selon les points totaux
     */
    protected function calculateLevel(): int
    {
        $points = (float) $this->total_points;
        
        foreach (array_reverse(self::LEVEL_THRESHOLDS, true) as $level => $threshold) {
            if ($points >= $threshold) {
                return $level;
            }
        }
        
        return 1;
    }

    /**
     * Obtient les points nécessaires pour le prochain niveau
     */
    public function getPointsToNextLevel(): ?int
    {
        if ($this->level >= 10) {
            return null; // Niveau max atteint
        }
        
        $nextLevelThreshold = self::LEVEL_THRESHOLDS[$this->level + 1];
        return $nextLevelThreshold - (int) $this->total_points;
    }

    /**
     * Obtient le pourcentage de progression vers le prochain niveau
     */
    public function getLevelProgress(): float
    {
        if ($this->level >= 10) {
            return 100.0;
        }
        
        $currentThreshold = self::LEVEL_THRESHOLDS[$this->level];
        $nextThreshold = self::LEVEL_THRESHOLDS[$this->level + 1];
        $progress = ((float) $this->total_points - $currentThreshold) / ($nextThreshold - $currentThreshold);
        
        return min(100.0, max(0.0, $progress * 100));
    }

    /**
     * Obtient les statistiques détaillées
     */
    public function getStats(): array
    {
        return [
            'total_points' => $this->total_points,
            'available_points' => $this->available_points,
            'pending_points' => $this->pending_points,
            'redeemed_points' => $this->redeemed_points,
            'level' => $this->level,
            'level_name' => $this->getLevelName(),
            'level_multiplier' => $this->level_multiplier,
            'points_to_next_level' => $this->getPointsToNextLevel(),
            'level_progress_percentage' => $this->getLevelProgress(),
            'last_activity_at' => $this->last_activity_at?->format('d/m/Y H:i'),
            'transactions_count' => $this->transactions()->count(),
            'this_month_earned' => $this->getMonthlyEarned(),
            'this_month_redeemed' => $this->getMonthlyRedeemed(),
        ];
    }

    /**
     * Obtient le nom du niveau
     */
    public function getLevelName(): string
    {
        return match($this->level) {
            1 => 'Bronze',
            2 => 'Bronze+',
            3 => 'Argent',
            4 => 'Argent+',
            5 => 'Or',
            6 => 'Or+',
            7 => 'Platine',
            8 => 'Platine+',
            9 => 'Diamant',
            10 => 'Légende',
            default => 'Inconnu'
        };
    }

    /**
     * Points gagnés ce mois
     */
    protected function getMonthlyEarned(): float
    {
        return $this->transactions()
                   ->where('amount', '>', 0)
                   ->whereMonth('created_at', now()->month)
                   ->whereYear('created_at', now()->year)
                   ->sum('amount');
    }

    /**
     * Points utilisés ce mois
     */
    protected function getMonthlyRedeemed(): float
    {
        return abs($this->transactions()
                       ->where('amount', '<', 0)
                       ->whereMonth('created_at', now()->month)
                       ->whereYear('created_at', now()->year)
                       ->sum('amount'));
    }

    /**
     * Créer automatiquement un enregistrement pour un nouvel utilisateur
     */
    public static function createForUser(int $userId): self
    {
        return self::create([
            'user_id' => $userId,
            'total_points' => 0,
            'available_points' => 0,
            'pending_points' => 0,
            'redeemed_points' => 0,
            'level' => 1,
            'level_multiplier' => 1.0,
            'last_activity_at' => now()
        ]);
    }
}