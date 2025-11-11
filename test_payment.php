<?php

require_once 'vendor/autoload.php';

// Charger Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Http;

echo "=== Test de Simulation de Paiement ===\n";

// 1. Récupérer un utilisateur et des articles
$user = User::first();
$items = Item::where('status', 'active')->take(2)->get();

if (!$user) {
    echo "❌ Aucun utilisateur trouvé\n";
    exit(1);
}

if ($items->isEmpty()) {
    echo "❌ Aucun article trouvé\n";
    exit(1);
}

echo "👤 Utilisateur: {$user->name} (ID: {$user->id})\n";
echo "🛍️  Articles sélectionnés:\n";

$totalAmount = 0;
foreach ($items as $item) {
    echo "   - {$item->name}: {$item->price} {$item->currency}\n";
    $totalAmount += $item->price;
}

echo "💰 Montant total: {$totalAmount} USD\n\n";

// 2. Simuler l'ajout au panier (normalement fait via session)
echo "📋 Simulation du panier...\n";

// 3. Préparer les données de paiement
$paymentData = [
    'buyer_id' => $user->id,
    'amount' => $totalAmount,
    'currency' => 'USD',
    'phone' => '850123456', // Numéro Orange Money fictif
    'purpose' => 'Test de paiement simulé',
    'provider' => 'orange_money'
];

echo "📤 Données de paiement:\n";
echo "   - Acheteur: {$user->name}\n";
echo "   - Montant: {$paymentData['amount']} {$paymentData['currency']}\n";
echo "   - Téléphone: {$paymentData['phone']}\n";
echo "   - Opérateur: Orange Money\n\n";

// 4. Appeler l'API de simulation
echo "🚀 Lancement du paiement simulé...\n";

try {
    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ])->post('http://127.0.0.1:8000/payments/simulate', $paymentData);

    if ($response->successful()) {
        $result = $response->json();
        
        if ($result['status'] === 'success') {
            echo "✅ Paiement réussi !\n";
            echo "   - Transaction ID: {$result['transaction_id']}\n";
            echo "   - Montant: {$result['amount']} {$result['currency']}\n";
            echo "   - Message: {$result['message']}\n";
            
            if (isset($result['distribution'])) {
                echo "📊 Répartition des fonds:\n";
                foreach ($result['distribution'] as $dist) {
                    echo "   - {$dist['beneficiary_type']}: {$dist['amount']}\n";
                }
            }
            
            echo "\n🔗 URL de succès: http://127.0.0.1:8000/payments/success/{$result['transaction_id']}\n";
            
        } else {
            echo "❌ Paiement échoué: {$result['message']}\n";
        }
    } else {
        echo "❌ Erreur HTTP: " . $response->status() . "\n";
        echo "   Réponse: " . $response->body() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur d'exception: " . $e->getMessage() . "\n";
}

echo "\n=== Test terminé ===\n";