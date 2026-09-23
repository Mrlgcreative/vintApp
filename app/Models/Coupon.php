<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    const TYPE_PERCENT = 'percent';
    const TYPE_FIXED = 'fixed';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'code',
        'title',
        'type',
        'value',
        'currency',
        'min_amount',
        'starts_at',
        'ends_at',
        'status',
        'max_redemptions',
        'redemption_count',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'max_redemptions' => 'integer',
        'redemption_count' => 'integer',
    ];

    /**
     * Créateur du code (admin).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Codes actifs (statut manuel).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Codes actuellement valides (statut actif + fenêtre de dates courante).
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->active()
            ->where(function (Builder $q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Le code est-il actuellement utilisable ?
     */
    public function isValid(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }
        if ($this->max_redemptions && $this->redemption_count >= $this->max_redemptions) {
            return false;
        }
        return true;
    }

    /**
     * Libellé lisible de la réduction (ex: "-10 %", "-5 $ US").
     */
    public function getDiscountLabelAttribute(): string
    {
        if ($this->type === self::TYPE_PERCENT) {
            return '-' . rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.') . ' %';
        }

        return '-' . number_format((float) $this->value, 2) . ' ' . ($this->currency ?: 'USD');
    }
}