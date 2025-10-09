<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedCity;
use App\Models\AllowedRegion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationAccessController extends Controller
{
    /**
     * Afficher la page de gestion des zones autorisées
     */
    public function index()
    {
        $cities = AllowedCity::orderBy('name')->paginate(20, ['*'], 'cities');
        $regions = AllowedRegion::orderBy('name')->paginate(20, ['*'], 'regions');

        $stats = [
            'total_cities' => AllowedCity::count(),
            'active_cities' => AllowedCity::active()->count(),
            'total_regions' => AllowedRegion::count(),
            'active_regions' => AllowedRegion::active()->count(),
        ];

        return view('admin.locations.index', compact('cities', 'regions', 'stats'));
    }

    /**
     * Ajouter une nouvelle ville
     */
    public function storeCity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'city_code' => 'nullable|string|max:50|unique:allowed_cities,city_code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $city = AllowedCity::create($validated);

        // Vider le cache
        Cache::flush();

        return redirect()->route('admin.locations.index')
            ->with('success', "La ville {$city->name} a été ajoutée avec succès.");
    }

    /**
     * Mettre à jour une ville
     */
    public function updateCity(Request $request, AllowedCity $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'city_code' => 'nullable|string|max:50|unique:allowed_cities,city_code,' . $city->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $city->update($validated);

        // Vider le cache
        Cache::flush();

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
        Cache::flush();

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
        Cache::flush();

        return response()->json([
            'success' => true,
            'is_active' => $city->is_active,
            'message' => $city->is_active ? "Ville activée" : "Ville désactivée"
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
        Cache::flush();

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
        Cache::flush();

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
        Cache::flush();

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
        Cache::flush();

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
            ['name' => 'Kinshasa', 'region' => 'Kinshasa', 'country' => 'Congo (RDC)'],
            ['name' => 'Lubumbashi', 'region' => 'Haut-Katanga', 'country' => 'Congo (RDC)'],
            ['name' => 'Mbuji-Mayi', 'region' => 'Kasaï-Oriental', 'country' => 'Congo (RDC)'],
            ['name' => 'Kananga', 'region' => 'Kasaï-Central', 'country' => 'Congo (RDC)'],
            ['name' => 'Kisangani', 'region' => 'Tshopo', 'country' => 'Congo (RDC)'],
            ['name' => 'Bukavu', 'region' => 'Sud-Kivu', 'country' => 'Congo (RDC)'],
            ['name' => 'Goma', 'region' => 'Nord-Kivu', 'country' => 'Congo (RDC)'],
            ['name' => 'Kolwezi', 'region' => 'Lualaba', 'country' => 'Congo (RDC)'],
        ];

        foreach ($defaultCities as $cityData) {
            AllowedCity::firstOrCreate(
                ['name' => $cityData['name'], 'country' => $cityData['country']],
                $cityData
            );
        }

        // Vider le cache
        Cache::flush();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Les villes par défaut ont été ajoutées avec succès.');
    }
}
