<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\MessageController;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Configuration de l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Test de la méthode applyDiscount...\n\n";

try {
    // Simuler l'authentification d'un vendeur
    $seller = \App\Models\User::find(2); // ID 2 comme vendeur
    if (!$seller) {
        echo "❌ Vendeur avec ID 2 non trouvé!\n";
        exit(1);
    }
    
    Auth::login($seller);
    echo "✅ Authentifié en tant que: {$seller->name} (ID: {$seller->id})\n";
    
    // Vérifier si l'article existe et appartient au vendeur
    $item = \App\Models\Item::find(1);
    if (!$item) {
        echo "❌ Article avec ID 1 non trouvé!\n";
        exit(1);
    }
    
    echo "📦 Article trouvé: {$item->name}\n";
    echo "💰 Prix: {$item->formatted_price}\n";
    echo "👤 Propriétaire: {$item->user->name} (ID: {$item->user_id})\n";
    
    // Vérifier si le vendeur est le propriétaire
    if ($item->user_id !== $seller->id) {
        echo "⚠️ Le vendeur connecté (ID: {$seller->id}) n'est pas le propriétaire de l'article (propriétaire ID: {$item->user_id})\n";
        
        // Créer un article de test pour ce vendeur
        echo "🔧 Création d'un article de test pour le vendeur...\n";
        $testItem = \App\Models\Item::create([
            'user_id' => $seller->id,
            'name' => 'Article Test Réduction',
            'description' => 'Article pour tester les réductions',
            'price' => 100.00,
            'currency' => 'USD',
            'quantity' => 1,
            'condition' => 'new',
            'category_id' => 1,
            'status' => 'active'
        ]);
        
        echo "✅ Article de test créé: {$testItem->name} (ID: {$testItem->id})\n";
        $item = $testItem;
    }
    
    // Récupérer un acheteur
    $buyer = \App\Models\User::where('id', '!=', $seller->id)->first();
    if (!$buyer) {
        echo "❌ Aucun acheteur trouvé!\n";
        exit(1);
    }
    
    echo "🛒 Acheteur: {$buyer->name} (ID: {$buyer->id})\n\n";
    
    // Créer une instance du contrôleur
    $notificationService = new NotificationService();
    $controller = new MessageController($notificationService);
    
    // Simuler la requête
    $requestData = [
        'item_id' => $item->id,
        'buyer_id' => $buyer->id,
        'discount_percentage' => 15,
        'expires_hours' => 24
    ];
    
    echo "📋 Données de la requête:\n";
    foreach ($requestData as $key => $value) {
        echo "   {$key}: {$value}\n";
    }
    echo "\n";
    
    // Simuler la requête HTTP
    $request = new Request();
    $request->merge($requestData);
    
    echo "🚀 Appel de la méthode applyDiscount...\n";
    
    $response = $controller->applyDiscount($request);
    $responseData = json_decode($response->getContent(), true);
    
    echo "📤 Réponse HTTP: " . $response->getStatusCode() . "\n";
    echo "📄 Contenu: " . $response->getContent() . "\n";
    
    if ($responseData['success'] ?? false) {
        echo "✅ Réduction appliquée avec succès!\n";
    } else {
        echo "❌ Erreur: " . ($responseData['error'] ?? 'Erreur inconnue') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
    echo "🔍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}