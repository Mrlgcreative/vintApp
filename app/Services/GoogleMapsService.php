<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleMapsService
{
    protected $apiKey;
    protected $enabled;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
        $this->enabled = config('services.google_maps.enabled', true);
    }

    /**
     * Vérifier si Google Maps est configuré
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Obtenir la clé API pour le frontend
     */
    public function getApiKey(): ?string
    {
        return $this->isEnabled() ? $this->apiKey : null;
    }

    /**
     * Geocoding: Obtenir les coordonnées d'une adresse
     * 
     * @param string $address Adresse ou nom de ville
     * @param string|null $region Code pays pour biaiser les résultats (ex: 'CD' pour RDC)
     * @return array|null ['lat' => float, 'lng' => float, 'formatted_address' => string, ...]
     */
    public function geocode(string $address, ?string $region = null): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $cacheKey = 'geocode_' . md5($address . $region);
        
        return Cache::remember($cacheKey, 86400, function () use ($address, $region) {
            try {
                $params = [
                    'address' => $address,
                    'key' => $this->apiKey,
                    'language' => config('services.google_maps.language', 'fr'),
                ];

                if ($region) {
                    $params['region'] = $region;
                }

                $response = Http::get("{$this->baseUrl}/geocode/json", $params);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && !empty($data['results'])) {
                        $result = $data['results'][0];
                        
                        return [
                            'lat' => $result['geometry']['location']['lat'],
                            'lng' => $result['geometry']['location']['lng'],
                            'formatted_address' => $result['formatted_address'],
                            'place_id' => $result['place_id'] ?? null,
                            'types' => $result['types'] ?? [],
                            'address_components' => $result['address_components'] ?? [],
                        ];
                    }

                    Log::warning('Google Maps Geocoding: No results', [
                        'address' => $address,
                        'status' => $data['status']
                    ]);
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Google Maps Geocoding Error', [
                    'message' => $e->getMessage(),
                    'address' => $address
                ]);
                return null;
            }
        });
    }

    /**
     * Reverse Geocoding: Obtenir l'adresse depuis les coordonnées
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @return array|null ['formatted_address' => string, 'city' => string, 'country' => string, ...]
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $cacheKey = 'reverse_geocode_' . md5("{$lat}_{$lng}");
        
        return Cache::remember($cacheKey, 86400, function () use ($lat, $lng) {
            try {
                $params = [
                    'latlng' => "{$lat},{$lng}",
                    'key' => $this->apiKey,
                    'language' => config('services.google_maps.language', 'fr'),
                ];

                $response = Http::get("{$this->baseUrl}/geocode/json", $params);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && !empty($data['results'])) {
                        $result = $data['results'][0];
                        $addressComponents = $result['address_components'] ?? [];
                        
                        // Extraire ville et pays
                        $city = null;
                        $country = null;
                        $countryCode = null;

                        foreach ($addressComponents as $component) {
                            if (in_array('locality', $component['types'])) {
                                $city = $component['long_name'];
                            }
                            if (in_array('country', $component['types'])) {
                                $country = $component['long_name'];
                                $countryCode = $component['short_name'];
                            }
                        }

                        return [
                            'formatted_address' => $result['formatted_address'],
                            'city' => $city,
                            'country' => $country,
                            'country_code' => $countryCode,
                            'place_id' => $result['place_id'] ?? null,
                            'address_components' => $addressComponents,
                        ];
                    }

                    Log::warning('Google Maps Reverse Geocoding: No results', [
                        'lat' => $lat,
                        'lng' => $lng,
                        'status' => $data['status']
                    ]);
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Google Maps Reverse Geocoding Error', [
                    'message' => $e->getMessage(),
                    'lat' => $lat,
                    'lng' => $lng
                ]);
                return null;
            }
        });
    }

    /**
     * Valider des coordonnées GPS
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @return bool
     */
    public function validateCoordinates(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }

    /**
     * Calculer la distance entre deux points (formule de Haversine)
     * 
     * @param float $lat1 Latitude point 1
     * @param float $lng1 Longitude point 1
     * @param float $lat2 Latitude point 2
     * @param float $lng2 Longitude point 2
     * @return float Distance en kilomètres
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Rayon de la Terre en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Rechercher des villes avec autocomplete
     * Utilise Places API Autocomplete
     * 
     * @param string $input Texte de recherche
     * @param string|null $types Types de lieux ('(cities)' par défaut)
     * @param string|null $components Composants pour filtrer par pays (ex: 'country:cd')
     * @return array Liste de suggestions
     */
    public function autocomplete(string $input, ?string $types = '(cities)', ?string $components = null): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        try {
            $params = [
                'input' => $input,
                'key' => $this->apiKey,
                'language' => config('services.google_maps.language', 'fr'),
            ];

            if ($types) {
                $params['types'] = $types;
            }

            if ($components) {
                $params['components'] = $components;
            }

            $response = Http::get("{$this->baseUrl}/place/autocomplete/json", $params);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK' && !empty($data['predictions'])) {
                    return array_map(function ($prediction) {
                        return [
                            'description' => $prediction['description'],
                            'place_id' => $prediction['place_id'],
                            'types' => $prediction['types'] ?? [],
                            'structured_formatting' => $prediction['structured_formatting'] ?? null,
                        ];
                    }, $data['predictions']);
                }
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Google Maps Autocomplete Error', [
                'message' => $e->getMessage(),
                'input' => $input
            ]);
            return [];
        }
    }

    /**
     * Obtenir les détails d'un lieu à partir de son place_id
     * 
     * @param string $placeId Place ID Google
     * @return array|null Détails du lieu
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $cacheKey = 'place_details_' . $placeId;
        
        return Cache::remember($cacheKey, 86400, function () use ($placeId) {
            try {
                $params = [
                    'place_id' => $placeId,
                    'key' => $this->apiKey,
                    'language' => config('services.google_maps.language', 'fr'),
                    'fields' => 'name,formatted_address,geometry,address_components,types,place_id',
                ];

                $response = Http::get("{$this->baseUrl}/place/details/json", $params);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && !empty($data['result'])) {
                        $result = $data['result'];
                        
                        return [
                            'name' => $result['name'] ?? null,
                            'formatted_address' => $result['formatted_address'] ?? null,
                            'lat' => $result['geometry']['location']['lat'] ?? null,
                            'lng' => $result['geometry']['location']['lng'] ?? null,
                            'place_id' => $result['place_id'] ?? null,
                            'types' => $result['types'] ?? [],
                            'address_components' => $result['address_components'] ?? [],
                        ];
                    }
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Google Maps Place Details Error', [
                    'message' => $e->getMessage(),
                    'place_id' => $placeId
                ]);
                return null;
            }
        });
    }

    /**
     * Générer l'URL d'une carte statique (pour aperçu/thumbnails)
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param int $zoom Niveau de zoom
     * @param string $size Taille de l'image (ex: '600x300')
     * @return string|null URL de l'image
     */
    public function getStaticMapUrl(float $lat, float $lng, int $zoom = 12, string $size = '600x300'): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $params = http_build_query([
            'center' => "{$lat},{$lng}",
            'zoom' => $zoom,
            'size' => $size,
            'markers' => "color:red|{$lat},{$lng}",
            'key' => $this->apiKey,
        ]);

        return "{$this->baseUrl}/staticmap?{$params}";
    }
}
