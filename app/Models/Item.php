<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

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
     * Accesseur pour s'assurer que specifications est toujours un tableau
     */
    public function getSpecificationsAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return is_array($value) ? $value : [];
    }

    /**
     * Accesseur pour s'assurer que images est toujours un tableau
     */
    public function getImagesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return is_array($value) ? $value : [];
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
        return $this->authenticity_verified && $this->authenticityCheck && $this->authenticityCheck->isApproved();
    }

    public function needsVerification(): bool
    {
        return !$this->authenticityCheck || $this->authenticityCheck->isPending();
    }

    public function canRequestVerification(): bool
    {
        // Vérifier si le produit est dans une catégorie éligible
        $eligibleCategories = [
            'mode_luxe',
            'electronique', 
            'bijoux',
            'montres',
            'sacs_maroquinerie',
            'vetements-femmes',
            'vetements-hommes',
            'vareuse'
        ];
        
        return !$this->authenticity_requested && 
               $this->category && 
               in_array($this->category->slug ?? '', $eligibleCategories);
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
            if (config('app.env') === 'local') {
                return asset('storage/' . $imagePath);
            }

            if (file_exists(public_path('storage'))) {
                return asset('storage/' . $imagePath);
            }

            return asset('storage/app/public/' . $imagePath);
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
