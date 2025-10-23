<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;

echo "=== Test QR Code Scan ===\n\n";

$order = Order::whereNotNull('scan_token')->first();

if ($order) {
    echo "✅ Commande trouvée: #{$order->order_number}\n";
    echo "Token: {$order->scan_token}\n";
    echo "URL: " . route('orders.scan', ['token' => $order->scan_token]) . "\n\n";
    
    echo "Pour tester:\n";
    echo "1. Démarrez le serveur: php artisan serve\n";
    echo "2. Ouvrez: http://localhost:8000/order/scan/{$order->scan_token}\n";
} else {
    echo "❌ Aucune commande avec scan_token trouvée\n";
}
