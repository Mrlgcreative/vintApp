<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test simple pour vérifier l'accès direct aux images
echo "=== TEST DIRECT IMAGES ===\n\n";

// Chemins d'images connus d'après notre diagnostic précédent
$images = [
    'items/1761564651_7V3hVjFEDa.jpg', // New Balance
    'items/1758545223_Eble78AkRk.jpg', // iPhone
    'items/1761844283_BN5ALAyOCM.jpg'  // Gucci
];

foreach ($images as $imagePath) {
    echo "Image: $imagePath\n";
    
    $fullPath = storage_path('app/public/' . $imagePath);
    echo "Chemin complet: $fullPath\n";
    
    if (file_exists($fullPath)) {
        echo "✅ Fichier existe physiquement\n";
        echo "Taille: " . formatBytes(filesize($fullPath)) . "\n";
        echo "URL Laravel: /storage/$imagePath\n";
    } else {
        echo "❌ Fichier physique manquant\n";
    }
    
    echo "\n";
}

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

echo "=== VÉRIFICATION SYMLINK ===\n";
$publicStoragePath = public_path('storage');
if (is_link($publicStoragePath)) {
    echo "✅ Symlink 'storage' existe dans public/\n";
    echo "Pointe vers: " . readlink($publicStoragePath) . "\n";
} else {
    echo "❌ Symlink 'storage' manquant dans public/\n";
    echo "Lancez: php artisan storage:link\n";
}

echo "\n=== TEST TERMINÉ ===\n";