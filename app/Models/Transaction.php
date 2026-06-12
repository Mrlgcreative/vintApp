<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'buyer_id',
        'wallet_id',
        'amount',
        'currency',
        'status',
        'type',
        'payment_method',
        'transaction_ref',
        'description',
        'transaction_id',
        'provider',
        'phone',
        'phone_number',
        'purpose',
        'metadata',
        'receipt_number',
        'receipt_signature',
        'receipt_generated_at',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'receipt_generated_at' => 'datetime',
    ];

    /**
     * Les constantes pour les différents statuts de transaction.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Les constantes pour les différents types de transaction.
     */
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_PURCHASE = 'purchase';

    /**
     * Les constantes pour les différentes méthodes de paiement.
     */
    const METHOD_WALLET = 'wallet';
    const METHOD_AIRTEL = 'airtel_money';
    const METHOD_ORANGE = 'orange_money';
    const METHOD_MPESA = 'mpesa';
    const METHOD_AFRIMONEY = 'afrimoney';
    const METHOD_BANK = 'bank';

    /**
     * Récupère l'utilisateur associé à la transaction.
     */
    public function user(): EloquentBelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère le wallet associé à la transaction.
     */
    public function wallet(): EloquentBelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Scope pour les transactions en attente.
     */
    public function scopePending(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope pour les transactions complétées.
     */
    public function scopeCompleted(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope pour les transactions échouées.
     */
    public function scopeFailed(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope pour les transactions remboursées.
     */
    public function scopeRefunded(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    /**
     * Scope pour les dépôts.
     */
    public function scopeDeposits(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('type', self::TYPE_DEPOSIT);
    }

    /**
     * Scope pour les retraits.
     */
    public function scopeWithdraws(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('type', self::TYPE_WITHDRAW);
    }

    /**
     * Scope pour les transferts.
     */
    public function scopeTransfers(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('type', self::TYPE_TRANSFER);
    }

    /**
     * Scope pour les achats.
     */
    public function scopePurchases(EloquentBuilder $query): EloquentBuilder
    {
        return $query->where('type', self::TYPE_PURCHASE);
    }

    /**
     * Scope pour filtrer par période.
     */
    public function scopeBetweenDates(EloquentBuilder $query, string $startDate, string $endDate): EloquentBuilder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope pour filtrer par méthode de paiement.
     */
    public function scopeByPaymentMethod(EloquentBuilder $query, string $method): EloquentBuilder
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope pour filtrer par devise.
     */
    public function scopeByCurrency(EloquentBuilder $query, string $currency): EloquentBuilder
    {
        return $query->where('currency', $currency);
    }

    /**
     * Vérifie si la transaction est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Vérifie si la transaction est complétée.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Vérifie si la transaction est échouée.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Vérifie si la transaction est remboursée.
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Met à jour le statut de la transaction.
     */
    public function updateStatus(string $status): bool
    {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_COMPLETED, self::STATUS_FAILED, self::STATUS_REFUNDED])) {
            return false;
        }

        $this->status = $status;
        return $this->save();
    }

    /**
     * Formatte le montant avec la devise.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Obtient l'icône correspondant à la méthode de paiement.
     */
    public function getPaymentMethodIconAttribute(): string
    {
        return match ($this->payment_method) {
            self::METHOD_WALLET => 'fas fa-wallet',
            self::METHOD_AIRTEL => 'fas fa-mobile-alt text-danger',
            self::METHOD_ORANGE => 'fas fa-mobile-alt text-warning',
            self::METHOD_MPESA => 'fas fa-mobile-alt text-success',
            self::METHOD_AFRIMONEY => 'fas fa-mobile-alt text-primary',
            self::METHOD_BANK => 'fas fa-university',
            default => 'fas fa-money-bill-wave',
        };
    }

    protected static function booted(): void
    {
        static::saving(function (self $transaction) {
            if ($transaction->isDirty('status') && $transaction->status === self::STATUS_COMPLETED && !$transaction->receipt_number) {
                $transaction->receipt_number = 'REC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
                $transaction->receipt_signature = hash_hmac('sha256',
                    $transaction->id . $transaction->receipt_number . $transaction->amount . $transaction->currency,
                    config('app.key')
                );
                $transaction->receipt_generated_at = now();
            }
        });
    }

    public function getReceiptUrlAttribute(): string
    {
        return $this->receipt_number ? route('payments.receipt', $this->id) : '#';
    }
}
