<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedCity;
use App\Models\AllowedRegion;
use App\Services\LocationCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationAccessController extends Controller
{
    /**
     * Afficher la page de gestion des zones autorisées
     */
    public function index()
    {
        $cities = AllowedCity::orderBy('country')->orderBy('name')->paginate(20, ['*'], 'cities');
        $regions = AllowedRegion::orderBy('name')->paginate(20, ['*'], 'regions');

        $stats = [
            'total_cities' => AllowedCity::count(),
            'active_cities' => AllowedCity::active()->count(),
            'total_regions' => AllowedRegion::count(),
            'active_regions' => AllowedRegion::active()->count(),
            'countries_count' => AllowedCity::distinct('country_code')->count(),
        ];

        // Liste des pays disponibles
        $countries = config('countries.countries');
        
        // Pays avec des villes dans la base de données
        $countriesWithCities = AllowedCity::select('country', 'country_code')
            ->distinct()
            ->orderBy('country')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->country_code => $item->country];
            });

        return view('admin.locations.index', compact('cities', 'regions', 'stats', 'countries', 'countriesWithCities'));
    }

    /**
     * Ajouter une nouvelle ville
     */
    public function storeCity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|size:2', // ISO 3166-1 alpha-2 (canonical)
            'region' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90', // ✅ OBLIGATOIRE pour système GPS
            'longitude' => 'required|numeric|between:-180,180', // ✅ OBLIGATOIRE pour système GPS
            'city_code' => 'nullable|string|max:50|unique:allowed_cities,city_code',
            'population' => 'nullable|integer|min:0',
            'timezone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $city = AllowedCity::create($validated);
        
        // Log pour debug
        \Log::info('📍 Nouvelle ville ajoutée avec GPS', [
            'name' => $city->name,
            'latitude' => $city->latitude,
            'longitude' => $city->longitude,
            'country' => $city->country
        ]);

        // Vider le cache
        LocationCache::clear();

        // Retourner JSON si c'est une requête AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "La ville {$city->name} ({$city->country}) a été ajoutée avec succès.",
                'city' => [
                    'id' => $city->id,
                    'name' => $city->name,
                    'country' => $city->country,
                    'country_code' => $city->country_code,
                    'region' => $city->region,
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'population' => $city->population,
                    'is_active' => $city->is_active,
                ]
            ]);
        }

        return redirect()->route('admin.locations.index')
            ->with('success', "La ville {$city->name} ({$city->country}) a été ajoutée avec succès.");
    }

    /**
     * Mettre à jour une ville
     */
    public function updateCity(Request $request, AllowedCity $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|size:2',
            'region' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city_code' => 'nullable|string|max:50|unique:allowed_cities,city_code,' . $city->id,
            'population' => 'nullable|integer|min:0',
            'timezone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $city->update($validated);

        // Vider le cache
        LocationCache::clear();

        return redirect()->route('admin.locations.index')
            ->with('success', "La ville {$city->name} a été mise à jour.");
    }

    /**
     * Supprimer une ville
     */
    public function destroyCity(AllowedCity $city)
    {
        $cityName = $city->name;
        $city->delete();

        // Vider le cache
        LocationCache::clear();

        return redirect()->route('admin.locations.index')
            ->with('success', "La ville {$cityName} a été supprimée.");
    }

    /**
     * Activer/Désactiver une ville
     */
    public function toggleCityStatus(AllowedCity $city)
    {
        $city->update(['is_active' => !$city->is_active]);

        // Vider le cache
        LocationCache::clear();

        return response()->json([
            'success' => true,
            'is_active' => $city->is_active,
            'message' => $city->is_active ? "Ville activée" : "Ville désactivée",
            'city' => [
                'id' => $city->id,
                'name' => $city->name,
                'country' => $city->country,
                'country_code' => $city->country_code,
                'region' => $city->region,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
                'population' => $city->population,
                'is_active' => $city->is_active,
            ]
        ]);
    }

    /**
     * Ajouter une nouvelle région
     */
    public function storeRegion(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'region_code' => 'nullable|string|max:50|unique:allowed_regions,region_code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $region = AllowedRegion::create($validated);

        // Vider le cache
        LocationCache::clear();

        return redirect()->route('admin.locations.index')
            ->with('success', "La région {$region->name} a été ajoutée avec succès.");
    }

    /**
     * Mettre à jour une région
     */
    public function updateRegion(Request $request, AllowedRegion $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'region_code' => 'nullable|string|max:50|unique:allowed_regions,region_code,' . $region->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $region->update($validated);

        // Vider le cache
        LocationCache::clear();

        return redirect()->route('admin.locations.index')
            ->with('success', "La région {$region->name} a été mise à jour.");
    }

    /**
     * Supprimer une région
     */
    public function destroyRegion(AllowedRegion $region)
    {
        $regionName = $region->name;
        $region->delete();

        // Vider le cache
        LocationCache::clear();

        return redirect()->route('admin.locations.index')
            ->with('success', "La région {$regionName} a été supprimée.");
    }

    /**
     * Activer/Désactiver une région
     */
    public function toggleRegionStatus(AllowedRegion $region)
    {
        $region->update(['is_active' => !$region->is_active]);

        // Vider le cache
        LocationCache::clear();

        return response()->json([
            'success' => true,
            'is_active' => $region->is_active,
            'message' => $region->is_active ? "Région activée" : "Région désactivée"
        ]);
    }

    /**
     * Initialiser avec les principales villes RDC
     */
    public function seedDefaultCities()
    {
        $defaultCities = [
            ['name' => 'Kinshasa', 'region' => 'Kinshasa', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => -4.3276, 'longitude' => 15.3136],
            ['name' => 'Lubumbashi', 'region' => 'Haut-Katanga', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => -11.6795, 'longitude' => 27.4794],
            ['name' => 'Mbuji-Mayi', 'region' => 'Kasaï-Oriental', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => -6.1200, 'longitude' => 23.5900],
            ['name' => 'Kananga', 'region' => 'Kasaï-Central', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => -5.8967, 'longitude' => 22.4169],
            ['name' => 'Kisangani', 'region' => 'Tshopo', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => 0.5150, 'longitude' => 25.1908],
            ['name' => 'Bukavu', 'region' => 'Sud-Kivu', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => -2.5087, 'longitude' => 28.8617],
            ['name' => 'Goma', 'region' => 'Nord-Kivu', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => -1.6792, 'longitude' => 29.2228],
            ['name' => 'Kolwezi', 'region' => 'Lualaba', 'country' => 'Congo (RDC)', 'country_code' => 'CD', 'latitude' => -10.7142, 'longitude' => 25.4731],
        ];

        foreach ($defaultCities as $cityData) {
            AllowedCity::firstOrCreate(
                ['name' => $cityData['name'], 'country' => $cityData['country']],
                $cityData
            );
        }

        // Vider le cache
        LocationCache::clear();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Les villes par défaut ont été ajoutées avec succès.');
    }

    /**
     * Obtenir la liste des pays disponibles
     */
    public function getCountries()
    {
        $countries = config('countries.countries');
        
        return response()->json([
            'success' => true,
            'countries' => $countries
        ]);
    }

    /**
     * Obtenir les villes majeures d'un pays
     */
    public function getMajorCitiesByCountry($countryCode)
    {
        // Accepte indifféremment un code ISO alpha-2 (canonique) ou alpha-3 (clés de la config).
        $alpha2ToAlpha3 = collect(config('countries.countries', []))
            ->pluck('code_alpha2', 'code')
            ->flip()
            ->map(fn ($code) => strtoupper($code));
        $code = $alpha2ToAlpha3[strtoupper($countryCode)] ?? strtoupper($countryCode);

        $cities = collect(config('countries.major_cities', []))
            ->filter(fn ($list, $key) => strtoupper($key) === $code)
            ->flatten(1)
            ->values();

        return response()->json([
            'success' => true,
            'country_code' => $countryCode,
            'total' => $cities->count(),
            'cities' => $cities
        ]);
    }

    /**
     * Obtenir les villes par pays depuis la base de données
     */
    public function getCitiesByCountry($countryCode)
    {
        $cities = AllowedCity::where('country_code', $countryCode)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'country_code' => $countryCode,
            'total' => $cities->count(),
            'cities' => $cities
        ]);
    }

    /**
     * Rechercher les villes à proximité d'une coordonnée GPS
     * Utilise la formule de Haversine pour calculer la distance
     */
    public function searchCitiesNearby(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:1000', // en km
        ]);

        $lat = $validated['latitude'];
        $lng = $validated['longitude'];
        $radius = $validated['radius'] ?? 100; // 100km par défaut

        // Formule de Haversine pour calculer la distance
        $cities = AllowedCity::selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * 
                sin(radians(latitude))
            )) AS distance
        ", [$lat, $lng, $lat])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'center' => ['latitude' => $lat, 'longitude' => $lng],
            'radius_km' => $radius,
            'total' => $cities->count(),
            'cities' => $cities
        ]);
    }

    /**
     * Obtenir toutes les villes avec coordonnées GPS pour la carte
     */
    public function getCitiesForMap()
    {
        $cities = AllowedCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'country', 'country_code', 'latitude', 'longitude', 'population', 'is_active')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $cities->count(),
            'cities' => $cities
        ]);
    }

    /**
     * Valider les coordonnées GPS pour un pays
     * Vérifie si les coordonnées sont dans la zone géographique approximative du pays
     */
    public function validateCoordinatesForCountry(Request $request)
    {
        $validated = $request->validate([
            'country_code' => 'required|string|size:2',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $countryCode = $validated['country_code'];
        $lat = $validated['latitude'];
        $lng = $validated['longitude'];

        // Obtenir le centre du pays depuis la config (alpha-2 en priorité, alpha-3 en secours)
        $countries = collect(config('countries.countries'));
        $country = $countries->firstWhere('code_alpha2', $countryCode)
            ?? $countries->firstWhere('code', $countryCode);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Pays non trouvé'
            ], 404);
        }

        // Calculer la distance entre le point et le centre du pays
        $centerLat = $country['latitude'];
        $centerLng = $country['longitude'];

        $distance = $this->calculateDistance($lat, $lng, $centerLat, $centerLng);

        // Considérer valide si dans un rayon de 1500km du centre
        $isValid = $distance <= 1500;

        return response()->json([
            'success' => true,
            'is_valid' => $isValid,
            'distance_km' => round($distance, 2),
            'country' => $country['name'],
            'message' => $isValid 
                ? 'Coordonnées valides pour ce pays' 
                : 'Coordonnées trop éloignées du centre du pays'
        ]);
    }

    /**
     * Calculer la distance entre deux points GPS (formule de Haversine)
     * @return float Distance en kilomètres
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
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
     * API: Obtenir tous les pays du monde (195 pays)
     */
    public function getAllCountries()
    {
        $countries = config('world_countries');
        
        // Formater pour le select
        $formatted = collect($countries)->map(function ($data, $code) {
            return [
                'code' => $code,
                'name' => $data['name'],
                'flag' => $data['flag'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'currency' => $data['currency']
            ];
        })->sortBy('name')->values();

        return response()->json([
            'success' => true,
            'countries' => $formatted,
            'total' => $formatted->count()
        ]);
    }

    /**
     * API: Rechercher des villes dans le monde entier
     * Utilise OpenStreetMap Nominatim (gratuit, pas de clé API)
     */
    public function searchWorldCities(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'country_code' => 'nullable|string|size:2',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $query = $validated['query'];
        $countryCode = $validated['country_code'] ?? null;
        $limit = $validated['limit'] ?? 10;

        // Utiliser Nominatim OSM (gratuit, pas de clé)
        $cacheKey = 'city_search_' . md5($query . $countryCode . $limit);
        
        $cities = Cache::remember($cacheKey, 3600, function () use ($query, $countryCode, $limit) {
            try {
                $url = 'https://nominatim.openstreetmap.org/search';
                $params = [
                    'q' => $query,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => $limit,
                    'featuretype' => 'city',
                ];

                if ($countryCode) {
                    $params['countrycodes'] = strtolower($countryCode);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'VintApp/1.0');
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode !== 200 || !$response) {
                    return [];
                }

                $data = json_decode($response, true);
                
                if (!is_array($data)) {
                    return [];
                }

                // Formater les résultats
                return collect($data)->map(function ($item) {
                    $address = $item['address'] ?? [];
                    return [
                        'name' => $item['display_name'] ?? $item['name'] ?? 'Ville inconnue',
                        'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? $item['name'] ?? '',
                        'state' => $address['state'] ?? $address['province'] ?? '',
                        'country' => $address['country'] ?? '',
                        'country_code' => strtoupper($address['country_code'] ?? ''),
                        'latitude' => (float) ($item['lat'] ?? 0),
                        'longitude' => (float) ($item['lon'] ?? 0),
                        'importance' => $item['importance'] ?? 0
                    ];
                })->filter(function ($city) {
                    return $city['latitude'] != 0 && $city['longitude'] != 0;
                })->values()->toArray();

            } catch (\Exception $e) {
                \Log::error('Erreur recherche ville: ' . $e->getMessage());
                return [];
            }
        });

        return response()->json([
            'success' => true,
            'cities' => $cities,
            'total' => count($cities),
            'query' => $query
        ]);
    }

    /**
     * API: Géocoder une adresse (obtenir lat/lng depuis nom de ville)
     */
    public function geocodeCity(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string',
            'country_code' => 'nullable|string|size:2'
        ]);

        $city = $validated['city'];
        $countryCode = $validated['country_code'] ?? null;

        $cacheKey = 'geocode_' . md5($city . $countryCode);
        
        $result = Cache::remember($cacheKey, 86400, function () use ($city, $countryCode) {
            try {
                $url = 'https://nominatim.openstreetmap.org/search';
                $params = [
                    'q' => $city,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 1,
                ];

                if ($countryCode) {
                    $params['countrycodes'] = strtolower($countryCode);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'VintApp/1.0');
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode !== 200 || !$response) {
                    return null;
                }

                $data = json_decode($response, true);
                
                if (!is_array($data) || empty($data)) {
                    return null;
                }

                $item = $data[0];
                $address = $item['address'] ?? [];

                return [
                    'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? $city,
                    'state' => $address['state'] ?? $address['province'] ?? '',
                    'country' => $address['country'] ?? '',
                    'country_code' => strtoupper($address['country_code'] ?? ''),
                    'latitude' => (float) ($item['lat'] ?? 0),
                    'longitude' => (float) ($item['lon'] ?? 0),
                    'display_name' => $item['display_name'] ?? ''
                ];

            } catch (\Exception $e) {
                \Log::error('Erreur geocoding: ' . $e->getMessage());
                return null;
            }
        });

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Ville non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
