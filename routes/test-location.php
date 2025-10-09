<?php

use Illuminate\Support\Facades\Route;
use Stevebauman\Location\Facades\Location;

// Route de test pour la géolocalisation (À SUPPRIMER EN PRODUCTION)
Route::get('/test-location-debug', function() {
    $ip = request()->ip();
    
    try {
        $position = Location::get($ip);
        
        return response()->json([
            'ip' => $ip,
            'environment' => app()->environment(),
            'position' => $position ? [
                'country' => $position->countryName ?? 'N/A',
                'country_code' => $position->countryCode ?? 'N/A',
                'city' => $position->cityName ?? 'N/A',
                'region' => $position->regionName ?? 'N/A',
                'is_localhost' => $ip === '127.0.0.1',
                'raw_position' => $position,
            ] : null,
            'cache_key' => "location_access_{$ip}",
            'cached_result' => \Cache::get("location_access_{$ip}"),
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (\Exception $e) {
        return response()->json([
            'ip' => $ip,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('test.location.debug');

// Route pour voir votre statut actuel
Route::get('/test-geo-status', function() {
    return view('test-geo-status');
})->name('test.geo.status');
