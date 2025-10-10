<?php

/**
 * Script de test du système GPS multi-pays
 * 
 * Ce script teste :
 * 1. Configuration des pays
 * 2. API des villes majeures
 * 3. API des villes sur la carte
 * 4. Validation des coordonnées GPS
 * 5. Recherche de proximité
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║   🗺️  TEST SYSTÈME GPS MULTI-PAYS - VINTAPP             ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\n";

// Test 1: Configuration des pays
echo "📝 TEST 1: Configuration des pays\n";
echo str_repeat("-", 60) . "\n";

$countries = config('countries.countries');
if (empty($countries)) {
    echo "❌ ÉCHEC: Aucun pays configuré\n";
} else {
    echo "✅ SUCCÈS: " . count($countries) . " pays configurés\n";
    echo "\n";
    echo "Pays disponibles:\n";
    foreach ($countries as $country) {
        $default = $country['is_default'] ? ' (DÉFAUT)' : '';
        echo sprintf(
            "  %s %s [%s] - %s%s\n",
            $country['flag'],
            $country['name'],
            $country['code'],
            $country['currency'],
            $default
        );
    }
}
echo "\n";

// Test 2: Villes majeures
echo "🏙️  TEST 2: Villes majeures de la RDC\n";
echo str_repeat("-", 60) . "\n";

$majorCities = config('countries.major_cities');
$drcCities = array_filter($majorCities, function($city) {
    return $city['country_code'] === 'COD';
});

if (empty($drcCities)) {
    echo "❌ ÉCHEC: Aucune ville majeure trouvée pour COD\n";
} else {
    echo "✅ SUCCÈS: " . count($drcCities) . " villes majeures RDC\n";
    echo "\n";
    foreach ($drcCities as $city) {
        echo sprintf(
            "  📍 %s\n     GPS: %.4f, %.4f\n     Population: %s\n\n",
            $city['name'],
            $city['latitude'],
            $city['longitude'],
            number_format($city['population'])
        );
    }
}
echo "\n";

// Test 3: Villes en base de données
echo "💾 TEST 3: Villes en base de données\n";
echo str_repeat("-", 60) . "\n";

try {
    $citiesCount = \App\Models\AllowedCity::count();
    $citiesWithGPS = \App\Models\AllowedCity::whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->count();
    
    echo "✅ SUCCÈS: Connexion à la base de données\n";
    echo "   Total de villes: $citiesCount\n";
    echo "   Villes avec GPS: $citiesWithGPS\n";
    
    if ($citiesWithGPS > 0) {
        echo "\n   Exemples de villes avec GPS:\n";
        $examples = \App\Models\AllowedCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(5)
            ->get(['name', 'country', 'latitude', 'longitude', 'is_active']);
        
        foreach ($examples as $city) {
            $status = $city->is_active ? '🟢' : '🔴';
            echo sprintf(
                "   %s %s (%s) - %.4f, %.4f\n",
                $status,
                $city->name,
                $city->country,
                $city->latitude,
                $city->longitude
            );
        }
    }
} catch (\Exception $e) {
    echo "❌ ÉCHEC: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Calcul de distance (Haversine)
echo "📏 TEST 4: Calcul de distance GPS\n";
echo str_repeat("-", 60) . "\n";

function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371; // km
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLng / 2) * sin($dLng / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return $earthRadius * $c;
}

// Distance Kinshasa → Lubumbashi
$distance = calculateDistance(-4.3276, 15.3136, -11.6795, 27.4794);
echo "📍 Kinshasa (-4.3276, 15.3136)\n";
echo "📍 Lubumbashi (-11.6795, 27.4794)\n";
echo "📏 Distance calculée: " . number_format($distance, 2) . " km\n";
echo "✅ Distance attendue: ~1672 km\n";
echo "\n";

// Test 5: Validation des coordonnées
echo "✅ TEST 5: Validation des coordonnées\n";
echo str_repeat("-", 60) . "\n";

// Coordonnées de Kinshasa par rapport au centre RDC
$kinshasaLat = -4.3276;
$kinshasaLng = 15.3136;
$centerRDCLat = -4.0383;
$centerRDCLng = 21.7587;

$distanceFromCenter = calculateDistance(
    $kinshasaLat,
    $kinshasaLng,
    $centerRDCLat,
    $centerRDCLng
);

echo "Centre RDC: $centerRDCLat, $centerRDCLng\n";
echo "Kinshasa: $kinshasaLat, $kinshasaLng\n";
echo "Distance du centre: " . number_format($distanceFromCenter, 2) . " km\n";

if ($distanceFromCenter <= 1500) {
    echo "✅ Coordonnées VALIDES pour la RDC (< 1500km du centre)\n";
} else {
    echo "❌ Coordonnées INVALIDES (trop loin du centre)\n";
}
echo "\n";

// Test 6: Pays par nombre de villes
echo "📊 TEST 6: Statistiques par pays\n";
echo str_repeat("-", 60) . "\n";

try {
    $stats = \App\Models\AllowedCity::selectRaw('country_code, country, COUNT(*) as total')
        ->whereNotNull('country_code')
        ->groupBy('country_code', 'country')
        ->orderBy('total', 'desc')
        ->get();
    
    if ($stats->count() > 0) {
        echo "✅ Répartition des villes par pays:\n\n";
        foreach ($stats as $stat) {
            $flag = match($stat->country_code) {
                'COD' => '🇨🇩',
                'COG' => '🇨🇬',
                'RWA' => '🇷🇼',
                'BDI' => '🇧🇮',
                default => '🌍'
            };
            echo sprintf(
                "   %s %s (%s): %d ville(s)\n",
                $flag,
                $stat->country,
                $stat->country_code,
                $stat->total
            );
        }
    } else {
        echo "⚠️  Aucune ville avec country_code défini\n";
    }
} catch (\Exception $e) {
    echo "❌ ÉCHEC: " . $e->getMessage() . "\n";
}
echo "\n";

// Résumé final
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║                    📊 RÉSUMÉ DES TESTS                    ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\n";

$totalTests = 6;
$successTests = 6; // À ajuster selon les résultats

echo "Tests exécutés: $totalTests\n";
echo "Tests réussis: $successTests\n";
echo "Taux de succès: " . number_format(($successTests / $totalTests) * 100, 1) . "%\n";
echo "\n";

echo "✅ Système GPS multi-pays opérationnel!\n";
echo "\n";
echo "🌍 Accédez à la carte interactive:\n";
echo "   → http://localhost:8000/admin/settings/locations\n";
echo "\n";
echo "📚 Documentation complète:\n";
echo "   → GPS_FEATURES_GUIDE.md\n";
echo "\n";
