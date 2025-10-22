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
    ];

    protected $casts = [
        'specifications' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
    ];

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
}
