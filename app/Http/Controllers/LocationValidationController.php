<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\AllowedCity;

class LocationValidationController extends Controller
{
    /**
     * Endpoint API pour valider la position envoyée par le client.
     * Accepte { city } ou { lat, lng } (lat/lng traitement minimal).
     */
    public function validateLocation(Request $request)
    {
        $data = $request->only(['city', 'lat', 'lng']);

        // Si la ville est fournie, vérifier qu'elle est autorisée
        if (!empty($data['city'])) {
            $cityName = trim($data['city']);
            $exists = AllowedCity::active()->where('name', $cityName)->exists();
            if ($exists) {
                session(['gps_location_validated' => true, 'user_city' => $cityName, 'validated_at' => now()->toDateTimeString()]);
                Log::info('🔓 GPS: validation acceptée via city', ['city' => $cityName, 'ip' => $request->ip()]);
                return response()->json(['ok' => true, 'user_city' => $cityName]);
            }

            return response()->json([
                'ok' => false,
                'message' => 'Ville non autorisée',
                'allowed' => AllowedCity::active()->pluck('name')
            ], 422);
        }

        // Si lat/lng fournis, on accepte et mémorise (implémentation légère)
        if (!empty($data['lat']) && !empty($data['lng'])) {
            $coords = sprintf('%s,%s', $data['lat'], $data['lng']);
            session(['gps_location_validated' => true, 'user_city' => null, 'validated_at' => now()->toDateTimeString(), 'gps_coords' => $coords]);
            Log::info('🔓 GPS: validation acceptée via coords', ['coords' => $coords, 'ip' => $request->ip()]);
            return response()->json(['ok' => true, 'gps_coords' => $coords]);
        }

        // Rien reçu : renvoyer la liste des villes autorisées pour aider le client
        return response()->json([
            'ok' => false,
            'message' => 'Aucune donnée de localisation fournie',
            'allowed' => AllowedCity::active()->pluck('name')
        ], 422);
    }
}
