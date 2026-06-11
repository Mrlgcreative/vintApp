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
        'base_price',
        'price_per_day',
        'price_usd',
        'price_cdf',
        'available_durations',
        'visual_config',
        'is_active',
        'is_premium',
        'sort_order',
        'min_duration',
        'max_duration',
        'max_concurrent',
        'benefits',
        'admin_notes'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'price_cdf' => 'decimal:0',
        'available_durations' => 'array',
        'visual_config' => 'array',
        'benefits' => 'array',
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'min_duration' => 'integer',
        'max_duration' => 'integer',
        'sort_order' => 'integer',
        'max_concurrent' => 'integer'
    ];

    // Relations
    public function productBoosts()
    {
        return $this->hasMany(ProductBoost::class, 'boost_type_id');
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
    public function getVisualConfigAttribute($value)
    {
        $config = is_array($value) ? $value : (json_decode($value, true) ?? []);
        
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
        $activeBoosts = ProductBoost::where('boost_type_id', $this->id)
            ->where('item_id', $itemId)
            ->active()
            ->count();

        return $activeBoosts < $this->max_concurrent;
    }

    public function getActiveCount()
    {
        return $this->activeBoosts()->count();
    }

    public function getTotalRevenue()
    {
        return $this->productBoosts()
            ->where('status', 'active')
            ->sum('total_price');
    }
}