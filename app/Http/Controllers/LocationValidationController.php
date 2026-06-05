<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AllowedCity;

class LocationValidationController extends Controller
{
    /**
     * Endpoint API pour valider la position envoyée par le client.
     * Accepte { city } ou { lat, lng } (lat/lng : ville déduite par proximité GPS, rayon AllowedCity::GEO_MATCH_RADIUS_KM).
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
                session()->forget(['geo_access_via_ip', 'ip_geo_country', 'geo_ip_source']);
                Log::info('🔓 GPS: validation acceptée via city', ['city' => $cityName, 'ip' => $request->ip()]);
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['ok' => true, 'user_city' => $cityName]);
                }

                // Pour les requêtes web classiques, rediriger vers la page d'accueil
                session()->flash('geo_success', 'Localisation enregistrée — bienvenue !');
                return redirect()->intended('/');
            }

            // Pour les requêtes web, afficher la page de ville non autorisée
            $unauthorizedUrl = route('location.unauthorized', ['reason' => 'city', 'city' => $cityName]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Ville non autorisée',
                    'allowed' => AllowedCity::active()->pluck('name'),
                    'redirect' => $unauthorizedUrl,
                ], 422);
            }

            return redirect()->to($unauthorizedUrl);
        }

        // Si lat/lng fournis : associer à la ville autorisée la plus proche (rayon fixe), sinon page « indisponible »
        if (!empty($data['lat']) && !empty($data['lng'])) {
            $lat = (float) $data['lat'];
            $lng = (float) $data['lng'];
            $coords = sprintf('%s,%s', $lat, $lng);

            $hasReferenceCities = AllowedCity::active()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->exists();

            if (!$hasReferenceCities) {
                Log::warning('🔒 GPS: aucune ville avec coordonnées — impossible de valider automatiquement', ['ip' => $request->ip()]);
                $unauthorizedUrl = route('location.unauthorized', ['reason' => 'coverage']);

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'La vérification automatique par position n\'est pas disponible. Saisissez une ville autorisée.',
                        'redirect' => $unauthorizedUrl,
                    ], 422);
                }

                return redirect()->to($unauthorizedUrl);
            }

            $nearest = AllowedCity::nearestActiveWithinRadius($lat, $lng);
            if ($nearest === null) {
                Log::info('🔒 GPS: position hors zone desservie', ['coords' => $coords, 'ip' => $request->ip()]);
                $unauthorizedUrl = route('location.unauthorized', ['reason' => 'geo']);

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Aucune zone desservie ne correspond à votre position.',
                        'redirect' => $unauthorizedUrl,
                    ], 422);
                }

                return redirect()->to($unauthorizedUrl);
            }

            session([
                'gps_location_validated' => true,
                'user_city' => $nearest->name,
                'validated_at' => now()->toDateTimeString(),
                'gps_coords' => $coords,
            ]);
            session()->forget(['geo_access_via_ip', 'ip_geo_country', 'geo_ip_source']);
            Log::info('🔓 GPS: validation acceptée via coords (ville déduite)', [
                'coords' => $coords,
                'user_city' => $nearest->name,
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['ok' => true, 'gps_coords' => $coords, 'user_city' => $nearest->name]);
            }

            session()->flash('geo_success', 'Localisation enregistrée — bienvenue !');
            return redirect()->intended('/');
        }

        // Rien reçu : renvoyer la liste des villes autorisées pour aider le client
        $allowed = AllowedCity::active()->orderBy('name')->pluck('name');
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'ok' => false,
                'message' => 'Aucune donnée de localisation fournie',
                'allowed' => $allowed
            ], 422);
        }

        // Pour les requêtes web, rediriger vers la page de validation avec la liste
        return redirect()->route('location.validate')->with('allowed_cities', $allowed)->withErrors(['location' => 'Aucune donnée de localisation fournie']);
    }

    /**
     * Affiche la page de validation de localisation (web)
     */
    public function showValidatePage(Request $request)
    {
        $allowed = session('allowed_cities') ?? AllowedCity::active()->orderBy('name')->pluck('name');
        $allowed = collect($allowed)->sort(SORT_NATURAL)->values();
        $hint = session('geo_hint');
        $success = session('geo_success');
        return view('location-validate', compact('allowed', 'hint', 'success'));
    }

    /**
     * Affiche la page de ville non autorisée avec la liste des villes autorisées
     */
    public function showUnauthorizedPage(Request $request)
    {
        $cityName = $request->query('city', '');
        $reason = $request->query('reason', $cityName !== '' ? 'city' : 'geo');
        $allowedReasons = ['city', 'geo', 'coverage'];
        if (!in_array($reason, $allowedReasons, true)) {
            $reason = $cityName !== '' ? 'city' : 'geo';
        }
        $allowedCities = AllowedCity::active()->pluck('name');

        return view('location.unauthorized', compact('cityName', 'allowedCities', 'reason'));
    }
}