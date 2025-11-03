<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'metadata',
        'status',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime'
    ];

    // Types de transactions
    const TYPE_EARN_REFERRAL = 'earn_referral';
    const TYPE_EARN_SIGNUP_BONUS = 'earn_signup_bonus';
    const TYPE_EARN_PURCHASE = 'earn_purchase';
    const TYPE_EARN_SALE = 'earn_sale';
    const TYPE_EARN_REVIEW = 'earn_review';
    const TYPE_EARN_DAILY_LOGIN = 'earn_daily_login';
    const TYPE_EARN_SOCIAL_SHARE = 'earn_social_share';
    const TYPE_EARN_PROFILE_COMPLETE = 'earn_profile_complete';
    const TYPE_EARN_BONUS = 'earn_bonus';
    const TYPE_REDEEM_CASH = 'redeem_cash';
    const TYPE_REDEEM_DISCOUNT = 'redeem_discount';
    const TYPE_EXPIRE = 'expire';
    const TYPE_REFUND = 'refund';
    const TYPE_ADJUSTMENT = 'adjustment';

    // Statuts
    const STATUS_PENDING = 'pending';
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
     * Relation polymorphique avec l'objet de référence
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Génère un ID de transaction unique
     */
    public static function generateTransactionId(): string
    {
        do {
            $id = 'PT' . now()->format('Ymd') . strtoupper(Str::random(6));
        } while (static::where('transaction_id', $id)->exists());

        return $id;
    }

    /**
     * Scope pour les transactions de gain
     */
    public function scopeEarnings($query)
    {
        return $query->where('amount', '>', 0);
    }

    /**
     * Scope pour les transactions de dépense
     */
    public function scopeSpending($query)
    {
        return $query->where('amount', '<', 0);
    }

    /**
     * Scope par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope par statut
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les transactions complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Obtient la description formatée du type
     */
    public function getTypeDescriptionAttribute(): string
    {
        return match($this->type) {
            self::TYPE_EARN_REFERRAL => 'Gain par parrainage',
            self::TYPE_EARN_SIGNUP_BONUS => 'Bonus d\'inscription',
            self::TYPE_EARN_PURCHASE => 'Gain par achat',
            self::TYPE_EARN_SALE => 'Gain par vente',
            self::TYPE_EARN_REVIEW => 'Gain par avis',
            self::TYPE_EARN_DAILY_LOGIN => 'Connexion quotidienne',
            self::TYPE_EARN_SOCIAL_SHARE => 'Partage sur réseaux sociaux',
            self::TYPE_EARN_PROFILE_COMPLETE => 'Profil complété',
            self::TYPE_EARN_BONUS => 'Bonus',
            self::TYPE_REDEEM_CASH => 'Conversion en argent',
            self::TYPE_REDEEM_DISCOUNT => 'Utilisation pour réduction',
            self::TYPE_EXPIRE => 'Points expirés',
            self::TYPE_REFUND => 'Remboursement',
            self::TYPE_ADJUSTMENT => 'Ajustement',
            default => 'Transaction inconnue'
        };
    }

    /**
     * Obtient le montant formaté avec signe
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->amount >= 0 ? '+' : '';
        return $prefix . number_format((float)$this->amount, 0) . ' pts';
    }

    /**
     * Vérifie si c'est une transaction de gain
     */
    public function isEarning(): bool
    {
        return $this->amount > 0;
    }

    /**
     * Vérifie si c'est une transaction de dépense
     */
    public function isSpending(): bool
    {
        return $this->amount < 0;
    }

    /**
     * Obtient la couleur CSS selon le type de transaction
     */
    public function getColorClassAttribute(): string
    {
        if ($this->isEarning()) {
            return 'text-success';
        } elseif ($this->isSpending()) {
            return 'text-danger';
        }
        return 'text-muted';
    }

    /**
     * Obtient l'icône selon le type de transaction
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_EARN_REFERRAL => 'fas fa-users',
            self::TYPE_EARN_SIGNUP_BONUS => 'fas fa-gift',
            self::TYPE_EARN_PURCHASE => 'fas fa-shopping-cart',
            self::TYPE_EARN_SALE => 'fas fa-dollar-sign',
            self::TYPE_EARN_REVIEW => 'fas fa-star',
            self::TYPE_EARN_DAILY_LOGIN => 'fas fa-calendar-check',
            self::TYPE_EARN_SOCIAL_SHARE => 'fas fa-share-alt',
            self::TYPE_EARN_PROFILE_COMPLETE => 'fas fa-user-check',
            self::TYPE_EARN_BONUS => 'fas fa-trophy',
            self::TYPE_REDEEM_CASH => 'fas fa-money-bill-wave',
            self::TYPE_REDEEM_DISCOUNT => 'fas fa-percentage',
            self::TYPE_EXPIRE => 'fas fa-clock',
            self::TYPE_REFUND => 'fas fa-undo',
            self::TYPE_ADJUSTMENT => 'fas fa-cog',
            default => 'fas fa-circle'
        };
    }

    /**
     * Statistiques par type pour un utilisateur
     */
    public static function getStatsForUser(int $userId, array $options = []): array
    {
        $query = static::where('user_id', $userId)->completed();

        // Filtrer par période si spécifiée
        if (isset($options['period'])) {
            match($options['period']) {
                'today' => $query->whereDate('created_at', today()),
                'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'this_year' => $query->whereYear('created_at', now()->year),
                default => null
            };
        }

        $earnings = clone $query;
        $spendings = clone $query;

        return [
            'total_earnings' => $earnings->earnings()->sum('amount'),
            'total_spendings' => abs($spendings->spending()->sum('amount')),
            'net_points' => $query->sum('amount'),
            'transaction_count' => $query->count(),
            'by_type' => $query->selectRaw('type, count(*) as count, sum(amount) as total')
                               ->groupBy('type')
                               ->get()
                               ->keyBy('type')
                               ->toArray()
        ];
    }

    /**
     * Obtient les détails de la transaction
     */
    public function getDetails(): array
    {
        return [
            'transaction_id' => $this->transaction_id,
            'type' => $this->type_description,
            'amount' => $this->formatted_amount,
            'balance_before' => number_format((float)$this->balance_before, 0) . ' pts',
            'balance_after' => number_format((float)$this->balance_after, 0) . ' pts',
            'description' => $this->description,
            'status' => ucfirst($this->status),
            'date' => $this->created_at->format('d/m/Y H:i'),
            'metadata' => $this->metadata ?? [],
            'icon' => $this->icon,
            'color_class' => $this->color_class
        ];
    }
}