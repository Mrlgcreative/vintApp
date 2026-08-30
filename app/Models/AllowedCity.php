<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedCity extends Model
{
    /**
     * Rayon maximal (km) entre la position GPS et le point de référence d'une ville autorisée.
     */
    public const GEO_MATCH_RADIUS_KM = 120;

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
     * Normalise le code pays en ISO alpha-2 à l'écriture (canonical), quelle que
     * soit la forme reçue (CD, COD, ZR...).
     */
    public function setCountryCodeAttribute($value): void
    {
        $this->attributes['country_code'] = $value === null || $value === ''
            ? null
            : self::toAlpha2CountryCode((string) $value);
    }

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

    /**
     * Distance orthodromique entre deux points WGS84 (km).
     */
    public static function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthKm = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Ville active la plus proche ayant des coordonnées, si elle est dans le rayon (km).
     */
    public static function nearestActiveWithinRadius(float $lat, float $lng, ?float $maxKm = null): ?self
    {
        $maxKm = $maxKm ?? (float) self::GEO_MATCH_RADIUS_KM;

        $cities = self::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'name', 'latitude', 'longitude']);

        if ($cities->isEmpty()) {
            return null;
        }

        $nearest = null;
        $minKm = PHP_FLOAT_MAX;

        foreach ($cities as $city) {
            $d = self::haversineKm($lat, $lng, (float) $city->latitude, (float) $city->longitude);
            if ($d < $minKm) {
                $minKm = $d;
                $nearest = $city;
            }
        }

        if ($nearest === null || $minKm > $maxKm) {
            return null;
        }

        return $nearest;
    }

    /**
     * Indique si au moins une ville active a un code pays (filtre « pays » pour la géoloc IP).
     */
    public static function hasCountryRestrictionConfigured(): bool
    {
        return self::active()
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->exists();
    }

    /**
     * Normalise un code pays (alpha-2 / alpha-3) vers ISO alpha-2 pour comparaison avec les APIs IP.
     */
    public static function toAlpha2CountryCode(string $code): string
    {
        $c = strtoupper(trim($code));

        return match ($c) {
            'COD', 'ZR' => 'CD',
            'COG' => 'CG',
            default => strlen($c) === 2 ? $c : $c,
        };
    }

    /**
     * Le code pays (tel que renvoyé par une API IP) correspond-il à un pays desservi ?
     */
    public static function countryCodeIsAllowed(?string $code): bool
    {
        if ($code === null || $code === '') {
            return false;
        }

        $needle = self::toAlpha2CountryCode($code);
        $allowedAlpha2 = self::active()
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->pluck('country_code')
            ->map(fn ($raw) => self::toAlpha2CountryCode((string) $raw))
            ->unique()
            ->values()
            ->all();

        if ($allowedAlpha2 === []) {
            return false;
        }

        return in_array($needle, $allowedAlpha2, true);
    }
}
