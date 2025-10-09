<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'balance',
        'is_active',
        'type',
        'commission_rate',
        'status',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    // Devises supportées
    public const CURRENCIES = ['USD', 'CDF'];
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_CDF = 'CDF';

    // Types de wallets
    public const TYPE_MAIN = 'main';
    public const TYPE_PENDING = 'pending';
    public const TYPE_ENTERPRISE = 'enterprise';

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

    /**
     * Vérifie si c'est un wallet principal
     */
    public function isMain()
    {
        return $this->type === 'main';
    }

    /**
     * Vérifie si c'est un wallet en attente
     */
    public function isPending()
    {
        return $this->type === 'pending';
    }

    /**
     * Scope pour les wallets principaux
     */
    public function scopeMain($query)
    {
        return $query->where('type', 'main');
    }

    /**
     * Scope pour les wallets en attente
     */
    public function scopePending($query)
    {
        return $query->where('type', 'pending');
    }

    /**
     * Vérifie si c'est un wallet entreprise
     */
    public function isEnterprise()
    {
        return $this->type === self::TYPE_ENTERPRISE;
    }

    /**
     * Scope pour les wallets entreprise
     */
    public function scopeEnterprise($query)
    {
        return $query->where('type', 'enterprise')->whereNull('user_id');
    }

    /**
     * Obtenir le wallet entreprise pour une devise donnée
     */
    public static function getEnterpriseWallet(string $currency = 'USD')
    {
        return static::enterprise()
            ->where('currency', $currency)
            ->first();
    }

    /**
     * Calculer le montant de commission basé sur le taux
     */
    public function calculateCommission(float $amount): float
    {
        if (!$this->isEnterprise()) {
            return 0.00;
        }
        
        return round(($amount * $this->commission_rate) / 100, 2);
    }

    /**
     * Crédite le wallet du montant spécifié
     */
    public function credit($amount)
    {
        $newBalance = $this->balance + $amount;
        $this->update(['balance' => $newBalance]);
        return $this;
    }

    /**
     * Débite le wallet du montant spécifié
     */
    public function debit($amount)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Solde insuffisant');
        }
        
        $newBalance = $this->balance - $amount;
        $this->update(['balance' => $newBalance]);
        return $this;
    }

    /**
     * Transfert un montant vers un autre wallet
     */
    public function transferTo(Wallet $destination, $amount)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Solde insuffisant pour le transfert');
        }

        DB::transaction(function () use ($destination, $amount) {
            $this->debit($amount);
            $destination->credit($amount);
        });

        return true;
    }
}
