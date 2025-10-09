<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedCity extends Model
{
    protected $fillable = [
        'name',
        'country',
        'region',
        'city_code',
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

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Vérifier si une ville est autorisée
     */
    public static function isCityAllowed($cityName, $country = 'Congo (RDC)')
    {
        return self::active()
            ->where('name', 'LIKE', "%{$cityName}%")
            ->where('country', $country)
            ->exists();
    }

    /**
     * Obtenir toutes les villes actives d'un pays
     */
    public static function getAllowedCitiesForCountry($country = 'Congo (RDC)')
    {
        return self::active()
            ->byCountry($country)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }
}
