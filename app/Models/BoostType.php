<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoostType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'color',
        'price_usd',
        'price_cdf',
        'available_durations',
        'visual_config',
        'is_active',
        'sort_order',
        'max_concurrent',
        'admin_notes'
    ];

    protected $casts = [
        'price_usd' => 'decimal:2',
        'price_cdf' => 'decimal:0',
        'available_durations' => 'array',
        'visual_config' => 'array',
        'is_active' => 'boolean'
    ];

    // Relations
    public function productBoosts()
    {
        return $this->hasMany(ProductBoost::class, 'boost_type', 'name');
    }

    public function activeBoosts()
    {
        return $this->productBoosts()->active();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getPriceAttribute($currency = 'USD')
    {
        return $currency === 'USD' ? $this->price_usd : $this->price_cdf;
    }

    public function getPriceFormattedAttribute($currency = 'USD')
    {
        if ($currency === 'USD') {
            return '$' . number_format($this->price_usd, 2);
        }
        
        return number_format($this->price_cdf, 0, ',', ' ') . ' FC';
    }

    public function getVisualConfigAttribute($value)
    {
        $config = json_decode($value, true) ?? [];
        
        return array_merge([
            'badge_text' => strtoupper($this->name),
            'badge_color' => 'bg-blue-500',
            'border_color' => null,
            'border_width' => '1px',
            'shadow_effect' => null,
            'glow_effect' => false,
            'pulse_animation' => false,
            'priority_boost' => 0,
            'homepage_carousel' => false,
            'special_animation' => null
        ], $config);
    }

    // Methods
    public function getPrice($currency)
    {
        return $currency === 'USD' ? $this->price_usd : $this->price_cdf;
    }

    public function getDuration($hours)
    {
        $durations = $this->available_durations ?? [];
        
        if (in_array($hours, $durations)) {
            return $hours;
        }
        
        return $durations[0] ?? 24; // Retourne la première durée disponible ou 24h par défaut
    }

    public function calculatePrice($currency, $hours)
    {
        $basePrice = $this->getPrice($currency);
        $baseDuration = $this->available_durations[0] ?? 24;
        
        return ($basePrice / $baseDuration) * $hours;
    }

    public function canApplyToItem($itemId)
    {
        $activeBoosts = ProductBoost::active()
            ->forItem($itemId)
            ->byType($this->name)
            ->count();
            
        return $activeBoosts < $this->max_concurrent;
    }

    public function getActiveCount()
    {
        return $this->activeBoosts()->count();
    }

    public function getTotalRevenue($currency = 'USD')
    {
        return $this->productBoosts()
            ->where('status', 'active')
            ->where('currency', $currency)
            ->sum('price');
    }
}