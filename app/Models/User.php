<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'bio',
        'location',
        'avatar',
        'theme_preference',
        'password',
        'newsletter_subscribed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'newsletter_subscribed' => 'boolean',
        ];
    }

    /**
     * Relation avec les items de l'utilisateur
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Relation avec les commandes où l'utilisateur est acheteur
     */
    public function ordersAsBuyer()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Relation avec les commandes où l'utilisateur est vendeur
     */
    public function ordersAsSeller()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    /**
     * Relation avec les messages envoyés
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Relation avec les messages reçus
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Relation avec les notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relation avec les réductions demandées (en tant qu'acheteur)
     */
    public function requestedDiscounts()
    {
        return $this->hasMany(Discount::class, 'user_id');
    }

    /**
     * Relation avec les réductions proposées (en tant que vendeur)
     */
    public function offeredDiscounts()
    {
        return $this->hasMany(Discount::class, 'seller_id');
    }

    /**
     * Relation avec les avis donnés
     */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Relation avec les avis reçus
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    /**
     * Relation avec les paiements effectués
     */
    public function paymentsAsBuyer()
    {
        return $this->hasMany(Payment::class, 'buyer_id');
    }

    /**
     * Relation avec les paiements reçus
     */
    public function paymentsAsSeller()
    {
        return $this->hasMany(Payment::class, 'seller_id');
    }

     


    /**
     * Relation avec les articles favoris
     */
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id');
    }

    /**
     * Relation avec les wallets de l'utilisateur
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Obtient le wallet USD de l'utilisateur
     */
    public function usdWallet()
    {
        return $this->wallets()->where('currency', 'USD')->first();
    }

    /**
     * Obtient le wallet CDF de l'utilisateur
     */
    public function cdfWallet()
    {
        return $this->wallets()->where('currency', 'CDF')->first();
    }

    /**
     * Relation avec le wallet principal
     */
    public function mainWallet()
    {
        return $this->hasOne(Wallet::class)->where('type', 'main');
    }

    /**
     * Relation avec le wallet en attente
     */
    public function pendingWallet()
    {
        return $this->hasOne(Wallet::class)->where('type', 'pending');
    }

    /**
     * Relations avec les transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Récupère le solde total de l'utilisateur
     */
    public function getTotalBalance($currency = 'USD')
    {
        return $this->wallets()
            ->where('currency', $currency)
            ->sum('balance');
    }

    /**
     * Récupère le solde disponible (main wallet)
     */
    public function getAvailableBalance($currency = 'USD')
    {
        return $this->mainWallet()
            ->where('currency', $currency)
            ->value('balance') ?? 0;
    }

    /**
     * Récupère le solde en attente (pending wallet)
     */
    public function getPendingBalance($currency = 'USD')
    {
        return $this->pendingWallet()
            ->where('currency', $currency)
            ->value('balance') ?? 0;
    }

    /**
     * Vérifie si l'utilisateur a assez de fonds disponibles
     */
    public function hasAvailableFunds($amount, $currency = 'USD')
    {
        return $this->getAvailableBalance($currency) >= $amount;
    }

    /**
     * Obtient ou crée le wallet USD
     */
    public function getOrCreateUsdWallet()
    {
        $wallet = $this->usdWallet();
        if (!$wallet) {
            $wallet = $this->wallets()->create([
                'currency' => 'USD',
                'balance' => 0.00,
                'is_active' => true,
            ]);
        }
        return $wallet;
    }

    /**
     * Obtient ou crée le wallet CDF
     */
    public function getOrCreateCdfWallet()
    {
        $wallet = $this->cdfWallet();
        if (!$wallet) {
            $wallet = $this->wallets()->create([
                'currency' => 'CDF',
                'balance' => 0.00,
                'is_active' => true,
            ]);
        }
        return $wallet;
    }

    /**
     * Obtenir l'URL de l'avatar
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Obtenir l'initiale pour l'avatar
     */
    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Obtenir le nom complet formaté
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Vérifier si l'utilisateur a un avatar personnalisé
     */
    public function hasCustomAvatar()
    {
        return !empty($this->avatar);
    }

    /**
     * Obtenir les statistiques de l'utilisateur
     */
    public function getStats()
    {
        return [
            'total_items' => $this->items()->count(),
            'active_items' => $this->items()->where('status', 'active')->count(),
            'sold_items' => $this->items()->where('status', 'sold')->count(),
            'total_orders' => $this->ordersAsBuyer()->count(),
            'completed_orders' => $this->ordersAsBuyer()->where('status', 'completed')->count(),
            'total_revenue' => $this->ordersAsSeller()->where('status', 'completed')->sum('total_amount'),
            'total_messages' => $this->sentMessages()->count() + $this->receivedMessages()->count(),
            'unread_messages' => $this->receivedMessages()->where('is_read', false)->count(),
            'favorites_count' => $this->favorites()->count(),
            'reviews_count' => $this->reviewsReceived()->count(),
            'average_rating' => $this->reviewsReceived()->avg('rating') ?? 0,
            'usd_balance' => $this->usdWallet() ? $this->usdWallet()->balance : 0.00,
            'cdf_balance' => $this->cdfWallet() ? $this->cdfWallet()->balance : 0.00,
        ];
    }

    /**
     * Vérifie si l'utilisateur est en ligne (last_seen < 2 min)
     */
    public function isOnline(): bool
    {
        return $this->last_seen && $this->last_seen->gt(now()->subMinutes(2));
    }

    /**
     * Les rôles de l'utilisateur.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Vérifie si l'utilisateur a l'un des rôles donnés.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Vérifie si l'utilisateur a tous les rôles donnés.
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() === count($roles);
    }

    /**
     * Vérifie si l'utilisateur est un admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Vérifie si l'utilisateur est un utilisateur standard.
     */
    public function isUser(): bool
    {
        return $this->hasRole('user');
    }
}
