<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG ARTICLES BOOSTÉS vs NORMAUX ===\n\n";

// Reproduire la même logique que dans WelcomeController
$boostedItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
    ->whereHas('activeBoosts')
    ->where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(8)
    ->get();

$regularItems = \App\Models\Item::with(['category', 'brand', 'user'])
    ->whereDoesntHave('activeBoosts')
    ->where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(8)
    ->get();

$latestItems = $boostedItems->concat($regularItems)->take(12);

echo "=== ARTICLES BOOSTÉS ({$boostedItems->count()}) ===\n";
foreach ($boostedItems as $item) {
    echo "ID: {$item->id} - {$item->name}\n";
    
    $images = $item->images ?? [];
    $firstImage = count($images) > 0 ? $images[0] : null;
    
    echo "  Images: " . count($images) . "\n";
    
    if ($firstImage) {
        echo "  Première image: {$firstImage}\n";
        echo "  URL: " . \Storage::url($firstImage) . "\n";
        if (\Storage::disk('public')->exists($firstImage)) {
            echo "  ✅ Fichier OK\n";
        } else {
            echo "  ❌ Fichier manquant\n";
        }
    } else {
        echo "  ❌ Aucune image\n";
    }
    
    echo "  Boosts: " . $item->activeBoosts->count() . "\n";
    echo "\n";
}

echo "=== ARTICLES NORMAUX ({$regularItems->count()}) ===\n";
foreach ($regularItems->take(3) as $item) {
    echo "ID: {$item->id} - {$item->name}\n";
    
    $images = $item->images ?? [];
    $firstImage = count($images) > 0 ? $images[0] : null;
    
    echo "  Images: " . count($images) . "\n";
    
    if ($firstImage) {
        echo "  Première image: {$firstImage}\n";
        echo "  URL: " . \Storage::url($firstImage) . "\n";
        if (\Storage::disk('public')->exists($firstImage)) {
            echo "  ✅ Fichier OK\n";
        } else {
            echo "  ❌ Fichier manquant\n";
        }
    } else {
        echo "  ❌ Aucune image\n";
    }
    
    echo "\n";
}

echo "=== ARTICLES COMBINÉS (latestItems - {$latestItems->count()}) ===\n";
foreach ($latestItems as $index => $item) {
    $isBoosted = $item->activeBoosts && $item->activeBoosts->count() > 0;
    echo ($index + 1) . ". {$item->name} - " . ($isBoosted ? "BOOSTÉ" : "NORMAL") . "\n";
}

echo "\n=== TEST TERMINÉ ===\n";