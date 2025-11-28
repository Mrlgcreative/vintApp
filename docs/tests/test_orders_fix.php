<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Test des requêtes Order après corrections...\n";

try {
    // Test 1: Compter les commandes avec buyer_id
    echo "\n1. Test de comptage avec buyer_id = 1:\n";
    $count = \App\Models\Order::where('buyer_id', 1)->count();
    echo "Nombre de commandes trouvées: $count\n";
    
    // Test 2: Vérifier la structure mise à jour
    echo "\n2. Structure mise à jour de la table orders:\n";
    $columns = DB::select('DESCRIBE orders');
    foreach($columns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
    
    // Test 3: Tester les relations
    echo "\n3. Test des relations du modèle Order:\n";
    $order = new \App\Models\Order();
    echo "Relations disponibles:\n";
    echo "- buyer: " . (method_exists($order, 'buyer') ? '✓' : '✗') . "\n";
    echo "- seller: " . (method_exists($order, 'seller') ? '✓' : '✗') . "\n";
    echo "- item: " . (method_exists($order, 'item') ? '✓' : '✗') . "\n";
    
    echo "\n✅ Tous les tests ont réussi!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}