<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PointRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'redemption_id',
        'type',
        'points_used',
        'currency',
        'cash_amount',
        'conversion_rate',
        'fees_charged',
        'redemption_code',
        'status',
        'description',
        'details',
        'processed_by',
        'processed_at',
        'expires_at',
        'failure_reason'
    ];

    protected $casts = [
        'points_used' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'fees_charged' => 'decimal:2',
        'details' => 'array',
        'processed_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    // Types de rachats
    const TYPE_CASH_CONVERSION = 'cash_conversion';
    const TYPE_DISCOUNT_CODE = 'discount_code';
    const TYPE_GIFT_CARD = 'gift_card';
    const TYPE_SPECIAL_OFFER = 'special_offer';

    // Statuts
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'admin qui a traité
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Génère un ID de rachat unique
     */
    public static function generateRedemptionId(): string
    {
        do {
            $id = 'RD' . now()->format('Ymd') . strtoupper(Str::random(6));
        } while (static::where('redemption_id', $id)->exists());

        return $id;
    }

    /**
     * Génère un code de réduction unique
     */
    public static function generateRedemptionCode(): string
    {
        do {
            $code = 'VINT' . strtoupper(Str::random(8));
        } while (static::where('redemption_code', $code)->exists());

        return $code;
    }

    /**
     * Scope pour les rachats en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope pour les rachats complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Crée un rachat de points contre argent
     */
    public static function createCashRedemption(int $userId, float $points, string $currency): self
    {
        $conversionRate = PointConversionRate::getCurrentRate($currency);
        if (!$conversionRate) {
            throw new \Exception("Aucun taux de conversion disponible pour {$currency}");
        }

        $calculation = $conversionRate->calculateCashAmount($points);
        if (!$calculation['valid']) {
            throw new \Exception($calculation['error']);
        }

        return static::create([
            'user_id' => $userId,
            'redemption_id' => static::generateRedemptionId(),
            'type' => self::TYPE_CASH_CONVERSION,
            'points_used' => $points,
            'currency' => $currency,
            'cash_amount' => $calculation['final_amount'],
            'conversion_rate' => $conversionRate->points_per_unit,
            'fees_charged' => $calculation['fees'],
            'status' => self::STATUS_PENDING,
            'description' => "Conversion de {$points} points en {$calculation['final_amount']} {$currency}",
            'details' => $calculation
        ]);
    }

    /**
     * Crée un code de réduction
     */
    public static function createDiscountCode(int $userId, float $points, array $options = []): self
    {
        $discountValue = $options['discount_value'] ?? ($points / 100); // 100 points = 1% de réduction
        $expiresAt = $options['expires_at'] ?? now()->addDays(30);

        return static::create([
            'user_id' => $userId,
            'redemption_id' => static::generateRedemptionId(),
            'type' => self::TYPE_DISCOUNT_CODE,
            'points_used' => $points,
            'redemption_code' => static::generateRedemptionCode(),
            'status' => self::STATUS_COMPLETED,
            'description' => "Code de réduction de {$discountValue}% généré",
            'expires_at' => $expiresAt,
            'details' => [
                'discount_percentage' => $discountValue,
                'min_order_amount' => $options['min_order_amount'] ?? 0,
                'max_discount_amount' => $options['max_discount_amount'] ?? null,
                'usage_limit' => $options['usage_limit'] ?? 1
            ]
        ]);
    }

    /**
     * Traite le rachat (pour les admins)
     */
    public function process(int $adminId, string $status = self::STATUS_COMPLETED, string $notes = null): bool
    {
        if ($this->status !== self::STATUS_PENDING && $this->status !== self::STATUS_PROCESSING) {
            return false;
        }

        $this->update([
            'status' => $status,
            'processed_by' => $adminId,
            'processed_at' => now(),
            'failure_reason' => $status === self::STATUS_FAILED ? $notes : null
        ]);

        // Si complété et c'est une conversion en argent, créditer le wallet
        if ($status === self::STATUS_COMPLETED && $this->type === self::TYPE_CASH_CONVERSION) {
            $this->creditUserWallet();
        }

        return true;
    }

    /**
     * Crédite le wallet de l'utilisateur
     */
    protected function creditUserWallet(): bool
    {
        try {
            $wallet = $this->user->wallets()
                                ->where('currency', $this->currency)
                                ->where('type', 'main')
                                ->first();

            if (!$wallet) {
                throw new \Exception("Wallet {$this->currency} introuvable");
            }

            $wallet->credit($this->cash_amount);

            // Créer une transaction de wallet
            Transaction::create([
                'user_id' => $this->user_id,
                'type' => 'points_conversion',
                'amount' => $this->cash_amount,
                'currency' => $this->currency,
                'status' => 'completed',
                'description' => "Conversion de {$this->points_used} points",
                'reference_type' => 'point_redemption',
                'reference_id' => $this->id
            ]);

            return true;
        } catch (\Exception $e) {
            $this->update([
                'status' => self::STATUS_FAILED,
                'failure_reason' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Annule le rachat
     */
    public function cancel(string $reason = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'failure_reason' => $reason
        ]);

        // Rembourser les points à l'utilisateur
        $this->user->points()->credit(
            $this->points_used,
            'refund',
            "Remboursement rachat #{$this->redemption_id}"
        );

        return true;
    }

    /**
     * Obtient la description du type
     */
    public function getTypeDescriptionAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CASH_CONVERSION => 'Conversion en argent',
            self::TYPE_DISCOUNT_CODE => 'Code de réduction',
            self::TYPE_GIFT_CARD => 'Carte cadeau',
            self::TYPE_SPECIAL_OFFER => 'Offre spéciale',
            default => 'Type inconnu'
        };
    }

    /**
     * Obtient le statut formaté
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PROCESSING => 'En cours',
            self::STATUS_COMPLETED => 'Complété',
            self::STATUS_FAILED => 'Échoué',
            self::STATUS_CANCELLED => 'Annulé',
            default => 'Inconnu'
        };
    }

    /**
     * Vérifie si le rachat peut être annulé
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Vérifie si le rachat peut être traité
     */
    public function canBeProcessed(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Obtient les détails complets
     */
    public function getFullDetails(): array
    {
        return [
            'redemption_id' => $this->redemption_id,
            'type' => $this->type_description,
            'points_used' => number_format((float)$this->points_used, 0) . ' pts',
            'cash_amount' => $this->cash_amount ? number_format((float)$this->cash_amount, 2) . ' ' . $this->currency : null,
            'fees_charged' => number_format((float)$this->fees_charged, 2) . ' ' . ($this->currency ?? ''),
            'redemption_code' => $this->redemption_code,
            'status' => $this->formatted_status,
            'description' => $this->description,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'processed_at' => $this->processed_at?->format('d/m/Y H:i'),
            'expires_at' => $this->expires_at?->format('d/m/Y H:i'),
            'processed_by' => $this->processedBy?->name,
            'failure_reason' => $this->failure_reason,
            'details' => $this->details ?? [],
            'can_be_cancelled' => $this->canBeCancelled(),
            'can_be_processed' => $this->canBeProcessed()
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($redemption) {
            if (!$redemption->redemption_id) {
                $redemption->redemption_id = static::generateRedemptionId();
            }
        });
    }
}