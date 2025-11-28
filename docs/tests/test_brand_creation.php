<?php

require_once 'vendor/autoload.php';

use App\Models\Brand;
use Illuminate\Support\Str;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Test de création d'une marque...\n";

try {
    // Données de test
    $data = [
        'name' => 'Test Brand',
        'slug' => Str::slug('Test Brand'),
        'description' => 'Une marque de test',
        'website' => 'https://test.com',
        'country' => 'France',
        'type' => 'Luxe',
        'is_active' => true,
    ];
    
    echo "Données à insérer:\n";
    print_r($data);
    
    $brand = Brand::create($data);
    
    echo "\nMarque créée avec succès!\n";
    echo "ID: {$brand->id}\n";
    echo "Nom: {$brand->name}\n";
    echo "Slug: {$brand->slug}\n";
    
} catch (Exception $e) {
    echo "Erreur lors de la création: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}