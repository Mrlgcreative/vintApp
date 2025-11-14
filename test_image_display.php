<?php
require_once 'bootstrap/app.php';

use App\Models\Item;
use Illuminate\Support\Facades\Storage;

echo "=== TEST D'AFFICHAGE DES IMAGES ===\n\n";

// Récupérer les deux articles boostés problématiques
$boostedItems = Item::whereHas('activeBoosts')
    ->with(['activeBoosts.boostType', 'category'])
    ->where('status', 'active')
    ->take(2)
    ->get();

echo "Articles boostés trouvés : " . $boostedItems->count() . "\n\n";

foreach($boostedItems as $item) {
    echo "--- Article: {$item->name} ---\n";
    echo "ID: {$item->id}\n";
    
    $images = $item->images ?? [];
    $firstImage = count($images) > 0 ? $images[0] : null;
    
    echo "Images JSON: " . json_encode($images) . "\n";
    echo "Première image: " . ($firstImage ?? 'AUCUNE') . "\n";
    
    if($firstImage) {
        $exists = Storage::disk('public')->exists($firstImage);
        $url = Storage::url($firstImage);
        echo "Fichier existe: " . ($exists ? 'OUI' : 'NON') . "\n";
        echo "URL: {$url}\n";
        
        // Vérifier la taille du fichier
        if($exists) {
            $size = Storage::disk('public')->size($firstImage);
            echo "Taille du fichier: " . number_format($size / 1024, 2) . " KB\n";
        }
    }
    
    // Vérifier les relations
    $activeBoost = $item->activeBoosts->first();
    echo "Boost actif: " . ($activeBoost ? 'OUI' : 'NON') . "\n";
    if($activeBoost) {
        echo "Type de boost: " . ($activeBoost->boostType->name ?? 'N/A') . "\n";
    }
    
    echo "\n";
}

echo "=== TEST TERMINÉ ===\n";