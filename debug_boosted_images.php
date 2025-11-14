<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC IMAGES PRODUITS BOOSTÉS ===\n\n";

// Récupérer les articles avec boost prioritaires (tous types) comme dans WelcomeController
$boostedItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
    ->whereHas('activeBoosts')
    ->where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(8)
    ->get();

echo "Produits boostés trouvés: " . $boostedItems->count() . "\n\n";

foreach ($boostedItems as $item) {
    echo "=== PRODUIT: {$item->name} ===\n";
    echo "ID: {$item->id}\n";
    echo "Status: {$item->status}\n";
    
    // Vérifier les images
    $images = $item->images ?? [];
    echo "Images (raw): " . json_encode($item->getAttributes()['images']) . "\n";
    echo "Images (processed): " . json_encode($images) . "\n";
    echo "Nombre d'images: " . count($images) . "\n";
    
    if (count($images) > 0) {
        $firstImage = $images[0];
        echo "Première image: {$firstImage}\n";
        
        // Vérifier si le fichier existe
        if (\Storage::disk('public')->exists($firstImage)) {
            echo "✅ Fichier existe: " . \Storage::url($firstImage) . "\n";
        } else {
            echo "❌ Fichier n'existe pas: {$firstImage}\n";
            
            // Lister les fichiers dans le dossier items
            $itemsDir = 'items';
            if (\Storage::disk('public')->exists($itemsDir)) {
                $files = \Storage::disk('public')->files($itemsDir);
                echo "Fichiers disponibles dans {$itemsDir}:\n";
                foreach ($files as $file) {
                    if (strpos($file, (string)$item->id) !== false || strpos($file, $item->name) !== false) {
                        echo "  - {$file}\n";
                    }
                }
            }
        }
    } else {
        echo "❌ Aucune image\n";
    }
    
    // Vérifier les boosts
    $activeBoosts = $item->activeBoosts;
    echo "Boosts actifs: " . $activeBoosts->count() . "\n";
    
    foreach ($activeBoosts as $boost) {
        echo "  - Type: {$boost->boost_type}\n";
        echo "  - Status: {$boost->status}\n";
        echo "  - Expire: {$boost->expires_at}\n";
        
        if ($boost->boostType) {
            echo "  - Couleur: " . ($boost->boostType->color ?? 'N/A') . "\n";
        }
    }
    
    echo "\n";
}

// Vérifier aussi les articles récents (non-boostés)
$regularItems = \App\Models\Item::with(['category', 'brand', 'user'])
    ->whereDoesntHave('activeBoosts')
    ->where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

echo "\n=== PRODUITS NON-BOOSTÉS (pour comparaison) ===\n";
foreach ($regularItems as $item) {
    echo "Produit: {$item->name}\n";
    $images = $item->images ?? [];
    echo "Images: " . count($images) . " - " . json_encode($images) . "\n";
    
    if (count($images) > 0) {
        $firstImage = $images[0];
        if (\Storage::disk('public')->exists($firstImage)) {
            echo "✅ Image OK: " . \Storage::url($firstImage) . "\n";
        } else {
            echo "❌ Image manquante: {$firstImage}\n";
        }
    }
    echo "\n";
}

echo "=== DIAGNOSTIC TERMINÉ ===\n";