<?php

/**
 * Script pour générer les scan_token pour les commandes existantes
 * Usage: php update_existing_orders_tokens.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use Illuminate\Support\Str;

echo "=== Génération des tokens pour les commandes existantes ===\n\n";

// Récupérer toutes les commandes sans scan_token
$ordersWithoutToken = Order::whereNull('scan_token')->get();

echo "Commandes trouvées sans scan_token : " . $ordersWithoutToken->count() . "\n\n";

if ($ordersWithoutToken->isEmpty()) {
    echo "✅ Toutes les commandes ont déjà un scan_token!\n";
    exit(0);
}

$updated = 0;
$failed = 0;

foreach ($ordersWithoutToken as $order) {
    try {
        $token = Str::random(32);
        $order->update(['scan_token' => $token]);
        echo "✓ Commande #{$order->order_number} - Token généré : {$token}\n";
        $updated++;
    } catch (\Exception $e) {
        echo "✗ Erreur pour commande #{$order->order_number} : " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=== Résumé ===\n";
echo "Commandes mises à jour : {$updated}\n";
echo "Échecs : {$failed}\n";

if ($updated > 0) {
    echo "\n✅ Tokens générés avec succès !\n";
    echo "Vous pouvez maintenant générer les factures avec QR codes.\n";
}
