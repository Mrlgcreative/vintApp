<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Simuler une requête POST vers brands.store
use Illuminate\Http\Request;
use App\Http\Controllers\BrandController;

echo "Test de simulation de soumission du formulaire...\n";

try {
    // Créer une instance du contrôleur
    $controller = new BrandController();
    
    // Simuler les données du formulaire
    $requestData = [
        'name' => 'Nike Test',
        'description' => 'Une marque sportive',
        'website' => 'https://nike.com',
        'country' => 'États-Unis',
        'type' => 'Sport',
        'is_active' => '1', // Checkbox cochée
    ];
    
    echo "Données simulées:\n";
    print_r($requestData);
    
    // Note: Pour un test complet, il faudrait simuler une vraie requête HTTP
    // Ici on teste juste que les données peuvent être validées
    
    $rules = [
        'name' => 'required|string|max:100|unique:brands,name',
        'description' => 'nullable|string|max:255',
        'website' => 'nullable|url|max:255',
        'country' => 'nullable|string|max:100',
        'type' => 'nullable|string|max:50',
    ];
    
    $validator = \Illuminate\Support\Facades\Validator::make($requestData, $rules);
    
    if ($validator->fails()) {
        echo "Erreurs de validation:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "- $error\n";
        }
    } else {
        echo "Validation réussie!\n";
        echo "Tentative de création de la marque...\n";
        
        // Simuler la création
        $validated = $validator->validated();
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = true;
        
        $brand = \App\Models\Brand::create($validated);
        echo "Marque créée: ID {$brand->id}, Nom: {$brand->name}\n";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nVérification du nombre total de marques:\n";
$count = \App\Models\Brand::count();
echo "Nombre total: $count\n";