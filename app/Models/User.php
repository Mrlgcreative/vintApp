<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

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
        'avatar_url',
        'theme_preference',
        'locale',
        'password',
        'newsletter_subscribed',
        'google_id',
        'apple_id',
        'firebase_uid',
        'provider_data',
        'verification_code',
        'verification_code_expires_at',
        'email_verified_at',
        'wallet_balance',
        'google2fa_enabled',
        'latitude',
        'longitude',
        'city',
        'commune',
        'location_updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_token',
        'google_refresh_token',
        'fcm_token',
        'google2fa_secret',
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
            'verification_code_expires_at' => 'datetime',
            'newsletter_subscribed' => 'boolean',
            'google2fa_enabled' => 'boolean',
            'last_seen' => 'datetime',
            'location_updated_at' => 'datetime',
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
     * Relation avec toutes les commandes de l'utilisateur (acheteur + vendeur)
     */
    public function orders()
    {
        return $this->ordersAsBuyer();
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
     * Alias pour reviewsReceived (compatibilité)
     */
    public function receivedReviews()
    {
        return $this->reviewsReceived();
    }

    /**
     * Alias pour reviewsGiven (compatibilité)
     */
    public function givenReviews()
    {
        return $this->reviewsGiven();
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
     * Relation avec les sessions de l'utilisateur
     */
    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Obtenir les sessions actives de l'utilisateur
     */
    public function activeSessions()
    {
        return $this->hasMany(UserSession::class)
            ->where('is_active', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->orderBy('last_activity', 'desc');
    }

    /**
     * Relation avec les livraisons locales en tant que vendeur
     */
    public function localDeliveriesAsSeller()
    {
        return $this->hasMany(LocalDelivery::class, 'seller_id');
    }

    /**
     * Relation avec les livraisons locales en tant qu'acheteur
     */
    public function localDeliveriesAsBuyer()
    {
        return $this->hasMany(LocalDelivery::class, 'buyer_id');
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
        // Priorité à la colonne avatar_url stockée en base (ex: OAuth)
        $storedUrl = $this->attributes['avatar_url'] ?? null;
        if ($storedUrl) {
            return $storedUrl;
        }

        if ($this->avatar) {
            // Si c'est déjà une URL complète (Google, Facebook, etc.), retourner telle quelle
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            // Sinon, c'est un chemin local dans storage
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Mutateur pour avatar_url
     */
    public function setAvatarUrlAttribute($value)
    {
        $this->attributes['avatar_url'] = $value;
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

    /**
     * Vérifie si l'utilisateur est un vendeur (rôle persistant).
     */
    public function isSeller(): bool
    {
        return $this->hasRole('vendeur');
    }

    /**
     * Chats de support créés par l'utilisateur
     */
    public function supportChats()
    {
        return $this->hasMany(SupportChat::class);
    }

    /**
     * Chats de support assignés à l'admin
     */
    public function assignedSupportChats()
    {
        return $this->hasMany(SupportChat::class, 'admin_id');
    }

    /**
     * Messages de support envoyés par l'utilisateur
     */
    public function supportMessages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    /**
     * Adresses de livraison de l'utilisateur
     */
    public function deliveryAddresses()
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    /**
     * Envoyer la notification de vérification d'email personnalisée
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }

    /**
     * === RELATIONS SYSTEME D'AFFILIATION ===
     */

    /**
     * Relation avec les points de l'utilisateur
     */
    public function points()
    {
        return $this->hasOne(UserPoints::class);
    }

    /**
     * Codes de parrainage créés par l'utilisateur
     */
    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class);
    }

    /**
     * Code de parrainage principal de l'utilisateur
     */
    public function mainReferralCode()
    {
        return $this->hasOne(ReferralCode::class)->where('is_active', true)->oldest();
    }

    /**
     * Parrainages effectués par cet utilisateur (en tant que parrain)
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Parrainage de cet utilisateur (en tant que filleul)
     */
    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * Utilisateur qui a parrainé cet utilisateur
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Transactions de points de l'utilisateur
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Rachats de points de l'utilisateur
     */
    public function pointRedemptions()
    {
        return $this->hasMany(PointRedemption::class);
    }

    /**
     * Récompenses d'affiliation reçues par l'utilisateur
     */
    public function affiliateRewards()
    {
        return $this->hasMany(AffiliateReward::class);
    }

    /**
     * Récompenses d'affiliation actives
     */
    public function activeAffiliateRewards()
    {
        return $this->hasMany(AffiliateReward::class)->active();
    }

    /**
     * === METHODES SYSTEME D'AFFILIATION ===
     */

    /**
     * Génère un code de parrainage unique pour l'utilisateur
     */
    public function generateReferralCode(): ReferralCode
    {
        return $this->referralCodes()->create([
            'title' => 'Code principal',
            'description' => 'Code de parrainage principal',
            'is_active' => true,
        ]);
    }

    /**
     * Obtient ou crée le système de points pour l'utilisateur
     */
    public function getOrCreatePoints(): UserPoints
    {
        $points = $this->points;
        if (!$points) {
            $points = UserPoints::createForUser($this->id);
            $this->setRelation('points', $points);
        }
        return $points;
    }

    /**
     * Ajoute des points bonus d'inscription
     */
    public function addSignupBonus(float $points = 100): void
    {
        $this->getOrCreatePoints()->credit(
            $points,
            'earn_signup_bonus',
            'Bonus d\'inscription à VintApp'
        );
    }

    /**
     * Ajoute des points pour un achat
     */
    public function addPurchasePoints(float $orderAmount, float $percentage = 2.0): void
    {
        $points = ($orderAmount * $percentage) / 100;
        $this->getOrCreatePoints()->credit(
            $points,
            'earn_purchase',
            "Points d'achat pour commande de {$orderAmount} USD"
        );
    }

    /**
     * Ajoute des points pour une vente
     */
    public function addSalePoints(float $saleAmount, float $percentage = 1.0): void
    {
        $points = ($saleAmount * $percentage) / 100;
        $this->getOrCreatePoints()->credit(
            $points,
            'earn_sale',
            "Points de vente pour {$saleAmount} USD"
        );
    }

    /**
     * Vérifie si l'utilisateur peut être parrainé
     */
    public function canBeReferred(): bool
    {
        return $this->referred_by === null && $this->referredBy === null;
    }

    /**
     * Applique un code de parrainage à l'utilisateur
     */
    public function applyReferralCode(string $code): ?Referral
    {
        if (!$this->canBeReferred()) {
            return null;
        }

        $referralCode = ReferralCode::where('code', $code)
                                   ->active()
                                   ->available()
                                   ->first();

        if (!$referralCode || $referralCode->user_id === $this->id) {
            return null;
        }

        // Utiliser le code
        if (!$referralCode->use()) {
            return null;
        }

        // Créer la relation de parrainage
        $referral = Referral::create([
            'referrer_id' => $referralCode->user_id,
            'referred_id' => $this->id,
            'referral_code_id' => $referralCode->id,
            'status' => 'pending',
            'bonus_points' => $referralCode->bonus_points
        ]);

        // Mettre à jour l'utilisateur
        $this->update(['referred_by' => $referralCode->user_id]);

        return $referral;
    }

    /**
     * Active le parrainage (appelé lors de la validation email)
     */
    public function activateReferral(): void
    {
        $referral = $this->referredBy;
        if ($referral && $referral->status === 'pending') {
            $referral->activate();
            
            // Ajouter le bonus au filleul si applicable
            if ($referral->bonus_points > 0) {
                $this->getOrCreatePoints()->credit(
                    $referral->bonus_points,
                    'earn_signup_bonus',
                    'Bonus de parrainage'
                );
            }
        }
    }

    /**
     * Obtient les statistiques d'affiliation de l'utilisateur
     */
    public function getAffiliateStats(): array
    {
        $points = $this->getOrCreatePoints();
        
        return [
            'points' => $points->getStats(),
            'referrals' => [
                'total' => $this->referrals()->count(),
                'active' => $this->referrals()->active()->count(),
                'completed' => $this->referrals()->completed()->count(),
                'pending' => $this->referrals()->pending()->count(),
                'total_points_earned' => $this->referrals()->sum('points_earned'),
            ],
            'referral_codes' => [
                'total' => $this->referralCodes()->count(),
                'active' => $this->referralCodes()->active()->count(),
                'total_uses' => $this->referralCodes()->sum('current_uses'),
            ],
            'redemptions' => [
                'total' => $this->pointRedemptions()->count(),
                'completed' => $this->pointRedemptions()->completed()->count(),
                'total_redeemed_value' => $this->pointRedemptions()->completed()->sum('cash_amount'),
            ],
            'referred_by' => $this->referrer?->name,
            'referral_activated' => $this->referral_activated_at !== null,
        ];
    }

    /**
     * Générer un code de vérification à 6 chiffres
     */
    public function generateVerificationCode(): string
    {
        $code = sprintf('%06d', random_int(100000, 999999));
        
        $this->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(15), // Expire après 15 minutes
        ]);

        return $code;
    }

    /**
     * Vérifier si le code de vérification est valide
     */
    public function isValidVerificationCode(string $code): bool
    {
        return $this->verification_code === $code 
            && $this->verification_code_expires_at 
            && $this->verification_code_expires_at->isFuture();
    }

    /**
     * Marquer l'email comme vérifié et supprimer le code
     */
    public function markEmailAsVerifiedWithCode(): bool
    {
        $this->update([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Vérifier si le code de vérification a expiré
     */
    public function hasExpiredVerificationCode(): bool
    {
        return $this->verification_code_expires_at && $this->verification_code_expires_at->isPast();
    }

    /**
     * Relation avec le profil expert
     */
    public function expertProfile()
    {
        return $this->hasOne(ExpertProfile::class);
    }

    /**
     * Vérifier si l'utilisateur est un expert
     */
    public function isExpert(): bool
    {
        return $this->expertProfile && $this->expertProfile->is_active;
    }

    /**
     * Relation avec les boosts de produits
     */
    public function productBoosts()
    {
        return $this->hasMany(ProductBoost::class);
    }

    /**
     * Boosts actifs de l'utilisateur
     */
    public function activeBoosts()
    {
        return $this->productBoosts()->where('status', 'active');
    }
}
