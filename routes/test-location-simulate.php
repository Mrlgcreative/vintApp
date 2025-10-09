<?php

use Illuminate\Support\Facades\Route;
use Stevebauman\Location\Facades\Location;
use App\Models\AllowedCity;
use App\Models\AllowedRegion;

// Route de test pour simuler une IP étrangère (À SUPPRIMER EN PRODUCTION)
Route::get('/test-location-simulate/{ip}', function($ip) {
    try {
        // Tester la détection avec une IP spécifique
        $position = Location::get($ip);
        
        if (!$position) {
            return response()->json([
                'error' => 'Impossible de détecter la position pour cette IP',
                'ip' => $ip,
            ]);
        }

        // Vérifier si c'est la RDC
        $countryName = $position->countryName ?? '';
        $countryCode = $position->countryCode ?? '';
        $isRDC = str_contains($countryName, 'Congo') 
              || str_contains($countryName, 'Democratic Republic') 
              || $countryCode === 'CD';

        // Vérifier la ville
        $cityName = $position->cityName ?? '';
        $regionName = $position->regionName ?? '';
        $cityAllowed = AllowedCity::isCityAllowed($cityName, $countryName);
        $regionAllowed = AllowedRegion::isRegionAllowed($regionName, $countryName);

        return response()->json([
            'ip' => $ip,
            'position' => [
                'country' => $countryName,
                'country_code' => $countryCode,
                'city' => $cityName,
                'region' => $regionName,
            ],
            'checks' => [
                'is_rdc' => $isRDC,
                'city_allowed' => $cityAllowed,
                'region_allowed' => $regionAllowed,
                'final_access' => $isRDC && ($cityAllowed || $regionAllowed),
            ],
            'verdict' => $isRDC && ($cityAllowed || $regionAllowed) 
                ? '✅ ACCÈS AUTORISÉ' 
                : '❌ ACCÈS BLOQUÉ',
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors de la détection',
            'message' => $e->getMessage(),
            'ip' => $ip,
        ], 500);
    }
})->where('ip', '.*')->name('test.location.simulate');

// Test avec IPs pré-définies
Route::get('/test-location-examples', function() {
    $testIPs = [
        '41.77.11.87' => 'IP de Kinshasa, RDC (exemple)',
        '8.8.8.8' => 'Google DNS - USA (devrait bloquer)',
        '1.1.1.1' => 'Cloudflare DNS - USA (devrait bloquer)',
        '104.26.10.78' => 'IP France (devrait bloquer)',
    ];

    return response()->json([
        'message' => 'Testez ces IPs en accédant à /test-location-simulate/{ip}',
        'examples' => $testIPs,
        'note' => 'Remplacez {ip} par une des IPs ci-dessus',
    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
})->name('test.location.examples');
