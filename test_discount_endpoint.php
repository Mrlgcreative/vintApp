<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Simuler une requête HTTP POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_SERVER['REQUEST_URI'] = '/discounts/apply';

// Simuler les données POST
$_POST = [
    'item_id' => '4', // Article de test créé précédemment
    'buyer_id' => '1',
    'discount_percentage' => '15',
    'expires_hours' => '24',
    '_token' => 'test-token'
];

// Démarrer la session et authentifier un utilisateur
session_start();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Authentifier l'utilisateur ID 2 (vendeur)
$seller = User::find(2);
if ($seller) {
    Auth::login($seller);
    echo "✅ Authentifié en tant que: " . $seller->name . " (ID: " . $seller->id . ")\n";
} else {
    echo "❌ Utilisateur vendeur non trouvé\n";
    exit;
}

// Simuler l'appel à l'endpoint
use App\Http\Controllers\MessageController;
use App\Services\NotificationService;
use Illuminate\Http\Request;

$controller = new MessageController(new NotificationService());

// Créer une requête
$request = new Request($_POST);

try {
    echo "🚀 Test de l'endpoint /discounts/apply...\n";
    
    $response = $controller->applyDiscount($request);
    
    echo "📤 Type de réponse: " . get_class($response) . "\n";
    echo "📄 Contenu de la réponse:\n";
    
    if (method_exists($response, 'getContent')) {
        echo $response->getContent() . "\n";
    } else {
        echo json_encode($response) . "\n";
    }
    
    echo "📊 Status HTTP: ";
    if (method_exists($response, 'getStatusCode')) {
        echo $response->getStatusCode() . "\n";
    } else {
        echo "N/A (pas une réponse HTTP)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
}