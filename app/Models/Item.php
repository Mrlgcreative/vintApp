<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Item extends Model
{
    use HasFactory;

    /**
     * Cache des offres courantes (résolues une fois par requête).
     */
    protected static ?Collection $runningOffersCache = null;

    public static function clearRunningOffersCache(): void
    {
        static::$runningOffersCache = null;
    }

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'currency',
        'quantity',
        'condition',
        'category_id',
        'brand_id',
        'status',
        'specifications',
        'images',
        'views',
        'color',
        'size',
        'item_number',
        'authenticity_requested',
        'authenticity_verified',
        'authenticity_verified_at',
        'authenticity_badge_type',
        'verification_status',
        'verification_score',
        'verification_details',
        'verified_at',
        'verified_by',
        'is_blocked',
        'blocked_at',
        'blocked_by',
        'block_reason',
        'is_suspended',
        'suspended_at',
        'suspended_until',
        'suspended_by',
        'suspend_reason',
        'rejection_reason',
    ];

    protected $casts = [
        'specifications' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'authenticity_requested' => 'boolean',
        'authenticity_verified' => 'boolean',
        'authenticity_verified_at' => 'datetime',
        'verification_details' => 'array',
        'verification_score' => 'decimal:2',
        'verified_at' => 'datetime',
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
        'is_suspended' => 'boolean',
        'suspended_at' => 'datetime',
        'suspended_until' => 'datetime',
    ];

    /**
     * Attributs à ajouter automatiquement au JSON
     */
    protected $appends = ['image_urls', 'first_image_url'];

    /**
     * Accesseur pour les URLs complètes des images
     */
    public function getImageUrlsAttribute(): array
    {
        return $this->getImageUrls();
    }

    /**
     * Accesseur pour la première image URL
     */
    public function getFirstImageUrlAttribute(): ?string
    {
        return $this->getFirstImageUrl();
    }

    /**
     * Get the formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : 'FC';
        return $symbol . ' ' . number_format((float) $this->price, 2);
    }

    /**
     * Expositions numériques contenant ce produit.
     */
    public function expositions()
    {
        return $this->belongsToMany(Exposition::class, 'exposition_item');
    }

    /**
     * Meilleure offre active (pourcentage le plus élevé préféré) applicable au produit.
     */
    public function activeOffer(): ?Offer
    {
        return $this->applicableOffers()->first();
    }

    /**
     * Collection triée (meilleure d'abord) des offres applicables à ce produit.
     */
    public function applicableOffers(): Collection
    {
        $offers = $this->runningOffers();
        $itemId = $this->id;
        $categoryId = $this->category_id;

        $applicable = $offers->filter(function (Offer $o) use ($itemId, $categoryId) {
            if ($o->scope === 'global') {
                return true;
            }
            if ($o->scope === 'items') {
                return $o->items()->where('item_id', $itemId)->exists();
            }
            if ($o->scope === 'categories') {
                return $categoryId && $o->categories()->where('category_id', $categoryId)->exists();
            }
            return false;
        })->sortByDesc(function (Offer $o) {
            // Meilleure offre = réduction en % ; à valeur égale, la plus récente d'abord.
            $pct = $o->type === 'percent'
                ? (float) $o->value
                : ((float) $o->value / max((float) $this->price, 1)) * 100;
            return [$pct, $o->created_at ? $o->created_at->timestamp : 0];
        });

        return $applicable->values();
    }

    /**
     * Toutes les offres actuellement valides dans la boutique (mises en cache par requête).
     */
    public static function runningOffers(): Collection
    {
        if (static::$runningOffersCache === null) {
            static::$runningOffersCache = Offer::running()->get();
        }
        return static::$runningOffersCache;
    }

    /**
     * Prix final après réduction (null si aucune offre applicable).
     */
    public function salePrice(): ?float
    {
        $offer = $this->activeOffer();
        return $offer ? $offer->discountPriceFor($this) : null;
    }

    /**
     * Ce produit a-t-il une offre active ?
     */
    public function getHasOfferAttribute(): bool
    {
        return $this->activeOffer() !== null;
    }

    /**
     * Label de l'offre active (ex : "-20 %").
     */
    public function getOfferLabelAttribute(): ?string
    {
        $offer = $this->activeOffer();
        return $offer?->discountLabel();
    }

    /**
     * Offre active (objet) pour Blade.
     */
    public function getOfferAttribute(): ?Offer
    {
        return $this->activeOffer();
    }

    /**
     * Prix final après réduction (pour Blade), null si aucune offre.
     */
    public function getSalePriceAttribute(): ?float
    {
        return $this->salePrice();
    }

    /**
     * Get the currency symbol
     */
    public function getCurrencySymbolAttribute()
    {
        return $this->currency === 'USD' ? '$' : 'FC';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'expert/admin qui a vérifié l'article
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function boosts()
    {
        return $this->hasMany(ProductBoost::class);
    }

    public function activeBoosts()
    {
        return $this->boosts()->active();
    }

    public function currentBoosts()
    {
        return $this->boosts()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>', now());
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relation avec les utilisateurs qui ont mis l'article en favori
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id');
    }

    /**
     * Alias pour la relation favoritedBy (pour compatibilité)
     */
    public function favorites()
    {
        return $this->favoritedBy();
    }

    /**
     * Relation avec les réductions
     */
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    /**
     * Relation avec les messages liés à cet item
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relation avec la vérification d'authenticité
     */
    public function authenticityCheck()
    {
        return $this->hasOne(ProductAuthenticityCheck::class);
    }

    /**
     * Méthodes pour la vérification d'authenticité
     */
    public function isVerified(): bool
    {
        // Un article est vérifié si:
        // 1. authenticity_verified est true ET verification_status est approved (vérifié par expert)
        // OU
        // 2. Il a un authenticityCheck approuvé (ancien système)
        if ($this->authenticity_verified && $this->verification_status === 'approved') {
            return true;
        }
        
        return $this->authenticity_verified && $this->authenticityCheck && $this->authenticityCheck->isApproved();
    }

    public function needsVerification(): bool
    {
        return !$this->authenticityCheck || $this->authenticityCheck->isPending();
    }

    public function canRequestVerification(): bool
    {
        // Ne peut pas demander si déjà vérifié
        if ($this->isVerified()) {
            return false;
        }
        
        // Tous les articles peuvent demander une vérification s'ils n'en ont pas déjà une
        // et s'ils ont une catégorie active
        return !$this->authenticity_requested && 
               !$this->authenticity_verified &&
               $this->category && 
               $this->category->is_active;
    }

    public function getAuthenticityBadgeHtml(): string
    {
        if (!$this->isVerified()) {
            return '';
        }

        $badgeText = match($this->authenticity_badge_type) {
            'vintapp_verified' => 'Vérifié VintApp',
            'expert_certified' => 'Certifié Expert',
            default => 'Authentifié'
        };

        $badgeIcon = match($this->authenticity_badge_type) {
            'vintapp_verified' => '✓',
            'expert_certified' => '🏆',
            default => '✓'
        };

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ' . $badgeIcon . ' ' . $badgeText . '
                </span>';
    }

    // Méthodes pour les boosts
    public function isBoosted(): bool
    {
        return $this->activeBoosts()->exists();
    }

    public function getActiveBoostTypes(): array
    {
        return $this->activeBoosts()->pluck('boost_type')->toArray();
    }

    public function hasBoostType($type): bool
    {
        return $this->activeBoosts()->where('boost_type', $type)->exists();
    }

    public function getBoostPriority(): int
    {
        return ProductBoost::getBoostPriority($this->id);
    }

    public function getBadgesHtml(): string
    {
        $html = '';
        
        // Badge authenticité d'abord
        $html .= $this->getAuthenticityBadgeHtml();
        
        // Ensuite les badges de boost
        $activeBoosts = $this->activeBoosts;
        
        foreach ($activeBoosts as $boost) {
            $boostType = $boost->boostType;
            if (!$boostType) continue;
            
            $config = $boostType->visual_config;
            $classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-1 ';
            $classes .= $config['badge_color'] ?? 'bg-blue-500 text-white';
            
            if ($config['pulse_animation'] ?? false) {
                $classes .= ' animate-pulse';
            }
            
            $icon = $boostType->icon ? '<i class="' . $boostType->icon . ' mr-1"></i>' : '';
            $text = $config['badge_text'] ?? strtoupper($boost->boost_type);
            
            $html .= '<span class="' . $classes . '">' . $icon . $text . '</span>';
        }
        
        return $html;
    }

    public function getBoostStyles(): string
    {
        $styles = [];
        $activeBoosts = $this->activeBoosts;
        
        foreach ($activeBoosts as $boost) {
            $boostType = $boost->boostType;
            if (!$boostType) continue;
            
            $config = $boostType->visual_config;
            
            if ($config['border_color'] ?? false) {
                $styles[] = 'border: ' . ($config['border_width'] ?? '1px') . ' solid ' . $config['border_color'];
            }
            
            if ($config['shadow_effect'] ?? false) {
                $styles[] = 'box-shadow: ' . $config['shadow_effect'];
            }
            
            if ($config['glow_effect'] ?? false) {
                $styles[] = 'box-shadow: 0 0 10px rgba(59, 130, 246, 0.5)';
            }
        }
        
        return implode('; ', $styles);
    }

    public function canBeBoostWith($boostType): bool
    {
        $type = BoostType::where('name', $boostType)->first();
        return $type ? $type->canApplyToItem($this->id) : false;
    }

    /**
     * Obtenir les URLs complètes des images
     */
    public function getImageUrls(): array
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        return array_map(function ($imagePath) {
            return asset('storage/' . $imagePath);
        }, $this->images);
    }

    /**
     * Obtenir la première image formatée en URL
     */
    public function getFirstImageUrl(): ?string
    {
        $urls = $this->getImageUrls();
        return !empty($urls) ? $urls[0] : null;
    }

    // Relations de modération
    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    // Scopes de modération
    public function scopeNotBlocked($query)
    {
        return $query->where('is_blocked', false);
    }

    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    public function scopeNotSuspended($query)
    {
        return $query->where('is_suspended', false);
    }

    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_blocked', false)
            ->where(function ($q) {
                $q->where('is_suspended', false)
                  ->orWhere('suspended_until', '<', now());
            });
    }

    // Méthodes de modération
    public function isAvailable(): bool
    {
        return $this->status === 'active'
            && !$this->is_blocked
            && !$this->isCurrentlySuspended();
    }

    public function isCurrentlySuspended(): bool
    {
        if (!$this->is_suspended) return false;
        if (!$this->suspended_until) return true;
        return now()->lt($this->suspended_until);
    }

    // Scope pour les items boostés
    public function scopeBoosted($query)
    {
        return $query->whereHas('activeBoosts');
    }

    public function scopeOrderByBoostPriority($query)
    {
        return $query->leftJoin('product_boosts', function($join) {
            $join->on('items.id', '=', 'product_boosts.item_id')
                 ->where('product_boosts.status', '=', 'active')
                 ->where('product_boosts.starts_at', '<=', now())
                 ->where('product_boosts.expires_at', '>', now());
        })
        ->selectRaw('items.*, COALESCE(
            CASE product_boosts.boost_type
                WHEN "spotlight" THEN 1000
                WHEN "premium" THEN 500  
                WHEN "top" THEN 300
                WHEN "featured" THEN 200
                WHEN "urgent" THEN 100
                ELSE 0
            END, 0
        ) as boost_priority')
        ->orderByDesc('boost_priority')
        ->groupBy('items.id');
    }
}
