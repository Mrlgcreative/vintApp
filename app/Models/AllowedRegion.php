<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedRegion extends Model
{
    protected $fillable = [
        'name',
        'country',
        'region_code',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Vérifier si une région est autorisée
     */
    public static function isRegionAllowed($regionName, $country = 'Congo (RDC)')
    {
        return self::active()
            ->where('name', 'LIKE', "%{$regionName}%")
            ->where('country', $country)
            ->exists();
    }

    /**
     * Obtenir toutes les régions actives d'un pays
     */
    public static function getAllowedRegionsForCountry($country = 'Congo (RDC)')
    {
        return self::active()
            ->byCountry($country)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }
}
