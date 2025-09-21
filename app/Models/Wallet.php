<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Devises supportées
    public const CURRENCIES = ['USD', 'CDF'];
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_CDF = 'CDF';

    /**
     * Relation avec l'utilisateur propriétaire du wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les transactions du wallet.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Scope pour récupérer seulement les wallets actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par devise.
     */
    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Formate le solde avec la devise.
     */
    public function getFormattedBalanceAttribute()
    {
        if ($this->currency === 'CDF') {
            return number_format((float)$this->balance, 2, ',', ' ') . ' FC';
        }
        
        return '$' . number_format((float)$this->balance, 2, '.', ',');
    }

    /**
     * Vérifie si le wallet est en USD.
     */
    public function isUSD()
    {
        return $this->currency === self::CURRENCY_USD;
    }

    /**
     * Vérifie si le wallet est en CDF.
     */
    public function isCDF()
    {
        return $this->currency === self::CURRENCY_CDF;
    }
}
