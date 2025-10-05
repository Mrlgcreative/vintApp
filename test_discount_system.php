<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Configuration de l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Diagnostic du système de réduction...\n\n";

try {
    // 1. Vérifier si la table discounts existe
    echo "1️⃣ Vérification de la table 'discounts':\n";
    if (Schema::hasTable('discounts')) {
        echo "✅ Table 'discounts' existe\n";
        
        // Lister les colonnes
        $columns = DB::getSchemaBuilder()->getColumnListing('discounts');
        echo "📋 Colonnes: " . implode(', ', $columns) . "\n\n";
        
        // Compter les enregistrements
        $count = DB::table('discounts')->count();
        echo "📊 Nombre de réductions: $count\n\n";
        
    } else {
        echo "❌ Table 'discounts' n'existe pas!\n\n";
    }
    
    // 2. Vérifier la route /discounts/apply
    echo "2️⃣ Test de la route '/discounts/apply':\n";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $discountRoute = null;
    
    foreach ($routes as $route) {
        if ($route->uri() === 'discounts/apply' && in_array('POST', $route->methods())) {
            $discountRoute = $route;
            break;
        }
    }
    
    if ($discountRoute) {
        echo "✅ Route POST /discounts/apply existe\n";
        echo "🎯 Action: " . $discountRoute->getActionName() . "\n\n";
    } else {
        echo "❌ Route POST /discounts/apply n'existe pas!\n\n";
    }
    
    // 3. Vérifier les modèles requis
    echo "3️⃣ Vérification des modèles:\n";
    
    // Modèle Discount
    if (class_exists('App\Models\Discount')) {
        echo "✅ Modèle Discount existe\n";
    } else {
        echo "❌ Modèle Discount n'existe pas!\n";
    }
    
    // Modèle Item
    if (class_exists('App\Models\Item')) {
        echo "✅ Modèle Item existe\n";
        $itemCount = DB::table('items')->count();
        echo "📊 Nombre d'articles: $itemCount\n";
    } else {
        echo "❌ Modèle Item n'existe pas!\n";
    }
    
    // Modèle User
    if (class_exists('App\Models\User')) {
        echo "✅ Modèle User existe\n";
        $userCount = DB::table('users')->count();
        echo "📊 Nombre d'utilisateurs: $userCount\n";
    } else {
        echo "❌ Modèle User n'existe pas!\n";
    }
    
    echo "\n4️⃣ Test de création d'une réduction:\n";
    
    // Simuler les données de test
    $testData = [
        'item_id' => 1,
        'user_id' => 1,
        'seller_id' => 2,
        'original_price' => 100.00,
        'discount_percentage' => 10.00,
        'discount_amount' => 10.00,
        'final_price' => 90.00,
        'status' => 'approved',
        'expires_at' => now()->addDay(),
        'reason' => 'Test'
    ];
    
    echo "🧪 Données de test préparées\n";
    echo "💾 Item ID: {$testData['item_id']}\n";
    echo "💾 User ID: {$testData['user_id']}\n";
    echo "💾 Seller ID: {$testData['seller_id']}\n";
    echo "💰 Prix original: {$testData['original_price']}\n";
    echo "💰 Réduction: {$testData['discount_percentage']}%\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
}