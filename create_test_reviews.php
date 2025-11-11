<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Review;
use App\Models\Item;
use App\Models\User;

echo "=== Création de reviews de test ===\n";

$item = Item::first();
$users = User::take(3)->get();

if ($item && $users->count() >= 2) {
    echo "Article: {$item->name}\n";
    echo "Vendeur: {$item->user_id}\n";
    
    // Créer 2 reviews de test
    Review::create([
        'reviewer_id' => $users[0]->id,
        'seller_id' => $item->user_id,
        'item_id' => $item->id,
        'rating' => 5,
        'comment' => 'Excellent produit, très satisfait de mon achat ! Livraison rapide et conforme à la description.',
        'status' => 'approved'
    ]);
    
    Review::create([
        'reviewer_id' => $users[1]->id,
        'seller_id' => $item->user_id,
        'item_id' => $item->id,
        'rating' => 4,
        'comment' => 'Bon produit, je recommande. Petit défaut sur l\'emballage mais le contenu est parfait.',
        'status' => 'approved'
    ]);
    
    echo "✅ 2 reviews créées avec succès pour l'article '{$item->name}'\n";
    echo "📊 Note moyenne calculée automatiquement\n";
    
} else {
    echo "❌ Pas assez de données pour créer des reviews\n";
    echo "Items: " . Item::count() . "\n";
    echo "Users: " . User::count() . "\n";
}

echo "\n=== Fin du script ===\n";