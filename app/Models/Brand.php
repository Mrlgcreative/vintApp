<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'is_active',
        'country',
        'type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $brand): void {
            Cache::forget('brands.active');
            Cache::forget('brands.all');
            Cache::forget('api.brands.list');
        });

        static::deleted(function (self $brand): void {
            Cache::forget('brands.active');
            Cache::forget('brands.all');
            Cache::forget('api.brands.list');
        });
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
