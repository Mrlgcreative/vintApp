<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Item;

try {
    // Trouver un utilisateur
    $user = User::first();
    
    if (!$user) {
        echo "❌ Aucun utilisateur trouvé dans la base de données\n";
        exit(1);
    }
    
    echo "✓ Utilisateur trouvé: {$user->name} (ID: {$user->id})\n";
    
    // Trouver une marque valide
    $brand = \App\Models\Brand::first();
    if (!$brand) {
        echo "❌ Aucune marque trouvée\n";
        exit(1);
    }
    
    // Créer un item de test
    $item = Item::create([
        'user_id' => $user->id,
        'name' => 'iPhone 13 Pro Max - Test Authenticité',
        'description' => 'Item de test pour vérifier les routes API d\'authenticité. Produit neuf avec tous ses accessoires.',
        'price' => 450000,
        'category_id' => 1,
        'brand_id' => $brand->id,
        'condition' => 'new',  // Valeurs valides: new, like_new, good, fair, poor
        'status' => 'active',
        'quantity' => 1
    ]);
    
    echo "✓ Item créé avec succès!\n";
    echo "  ID: {$item->id}\n";
    echo "  Nom: {$item->name}\n";
    echo "  Prix: {$item->price} FCFA\n";
    echo "  Statut: {$item->status}\n";
    
    echo "\n🎉 Vous pouvez maintenant tester avec l'ID: {$item->id}\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
