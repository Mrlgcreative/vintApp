<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedCity extends Model
{
    protected $fillable = [
        'name',
        'country',
        'country_code',
        'region',
        'city_code',
        'latitude',
        'longitude',
        'population',
        'timezone',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'population' => 'integer',
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
    public static function isCityAllowed($cityName, $country = null)
    {
        $query = self::active()
            ->where(function($q) use ($cityName) {
                $q->where('name', 'LIKE', "%{$cityName}%")
                  ->orWhere('name', 'LIKE', str_replace(' ', '%', $cityName))
                  ->orWhere('name', '=', $cityName);
            });

        // Si pays spécifié, filtrer par pays
        if ($country) {
            // Normaliser les variations du nom du pays Congo
            $congoVariants = [
                'Congo',
                'Democratic Republic of the Congo',
                'Congo (DRC)',
                'Congo (RDC)',
                'DR Congo',
                'DRC',
                'RDC',
                'Congo-Kinshasa'
            ];
            
            // Si le pays contient "Congo", chercher dans toutes les variantes
            if (stripos($country, 'Congo') !== false) {
                $query->where(function($q) use ($congoVariants) {
                    foreach ($congoVariants as $variant) {
                        $q->orWhere('country', 'LIKE', "%{$variant}%");
                    }
                });
            } else {
                $query->where('country', 'LIKE', "%{$country}%");
            }
        }

        return $query->exists();
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
