<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // Types de transactions
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    /**
     * Relation avec le wallet.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Scope pour les crédits.
     */
    public function scopeCredits($query)
    {
        return $query->where('type', self::TYPE_CREDIT);
    }

    /**
     * Scope pour les débits.
     */
    public function scopeDebits($query)
    {
        return $query->where('type', self::TYPE_DEBIT);
    }

    /**
     * Scope pour une référence spécifique.
     */
    public function scopeByReference($query, $reference)
    {
        return $query->where('reference', $reference);
    }

    /**
     * Formate le montant avec la devise du wallet.
     */
    public function getFormattedAmountAttribute()
    {
        if ($this->wallet->currency === 'CDF') {
            return number_format((float)$this->amount, 2, ',', ' ') . ' FC';
        }
        
        return '$' . number_format((float)$this->amount, 2, '.', ',');
    }

    /**
     * Formate le solde après transaction avec la devise.
     */
    public function getFormattedBalanceAfterAttribute()
    {
        if ($this->wallet->currency === 'CDF') {
            return number_format((float)$this->balance_after, 2, ',', ' ') . ' FC';
        }
        
        return '$' . number_format((float)$this->balance_after, 2, '.', ',');
    }

    /**
     * Vérifie si c'est un crédit.
     */
    public function isCredit()
    {
        return $this->type === self::TYPE_CREDIT;
    }

    /**
     * Vérifie si c'est un débit.
     */
    public function isDebit()
    {
        return $this->type === self::TYPE_DEBIT;
    }
}
