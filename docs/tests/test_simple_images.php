<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST SIMPLE AFFICHAGE PRODUITS ===\n\n";

// Test simple pour récupérer 2-3 produits et leurs images
$items = \App\Models\Item::with(['activeBoosts'])
    ->where('status', 'active')
    ->limit(3)
    ->get();

echo "Produits trouvés: " . $items->count() . "\n\n";

foreach ($items as $item) {
    echo "=== PRODUIT: {$item->name} ===\n";
    
    // Tester l'accès aux images avec la même logique que dans la vue
    $images = $item->images ?? [];
    $firstImage = count($images) > 0 ? $images[0] : null;
    
    echo "Images disponibles: " . count($images) . "\n";
    
    if ($firstImage) {
        echo "Première image: {$firstImage}\n";
        
        // Tester l'existence du fichier
        if (\Storage::disk('public')->exists($firstImage)) {
            echo "✅ Image accessible: " . \Storage::url($firstImage) . "\n";
        } else {
            echo "❌ Image manquante: {$firstImage}\n";
        }
    } else {
        echo "❌ Aucune image\n";
    }
    
    // Vérifier les boosts
    $activeBoosts = $item->activeBoosts;
    $isBoosted = $activeBoosts->count() > 0;
    
    echo "Boosté: " . ($isBoosted ? "OUI" : "NON") . "\n";
    
    if ($isBoosted) {
        echo "Nombre de boosts: " . $activeBoosts->count() . "\n";
    }
    
    echo "\n";
}

echo "=== TEST SIMPLE TERMINÉ ===\n";