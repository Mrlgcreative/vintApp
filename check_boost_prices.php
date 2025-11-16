<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Prix des types de boost ===\n\n";

$boostTypes = DB::table('boost_types')->get();

foreach ($boostTypes as $boost) {
    echo "ID: {$boost->id}\n";
    echo "Nom: {$boost->display_name}\n";
    echo "Prix CDF: " . number_format($boost->price_cdf ?? $boost->base_price ?? 0, 0, ',', ' ') . " CDF\n";
    echo "Prix USD: $" . number_format($boost->price_usd ?? 0, 2) . "\n";
    echo "Prix par jour: " . number_format($boost->price_per_day ?? 0, 0, ',', ' ') . " CDF\n";
    echo "Min: {$boost->min_duration} jours | Max: {$boost->max_duration} jours\n";
    echo "Durées disponibles: {$boost->available_durations}\n";
    echo "\n--- Nouveau calcul pour ce boost ---\n";
    
    $basePrice = $boost->price_cdf ?? $boost->base_price ?? 1000;
    
    // 1 jour
    $price1 = $basePrice * 1;
    echo "1 jour:  " . number_format($price1, 0, ',', ' ') . " CDF\n";
    
    // 3 jours (10% remise)
    $price3 = ($basePrice * 3) * 0.90;
    echo "3 jours: " . number_format($price3, 0, ',', ' ') . " CDF (remise 10%)\n";
    
    // 7 jours (15% remise)
    $price7 = ($basePrice * 7) * 0.85;
    echo "7 jours: " . number_format($price7, 0, ',', ' ') . " CDF (remise 15%)\n";
    
    // 14 jours (20% remise)
    $price14 = ($basePrice * 14) * 0.80;
    echo "14 jours: " . number_format($price14, 0, ',', ' ') . " CDF (remise 20%)\n";
    
    // 30 jours (30% remise)
    $price30 = ($basePrice * 30) * 0.70;
    echo "30 jours: " . number_format($price30, 0, ',', ' ') . " CDF (remise 30%)\n";
    
    echo "\n" . str_repeat("=", 60) . "\n\n";
}
