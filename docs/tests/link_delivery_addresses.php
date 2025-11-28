<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DeliveryAddress;
use App\Models\Order;

// Associer les adresses par défaut aux commandes des utilisateurs
$users = DeliveryAddress::where('is_default', true)->get();

foreach ($users as $address) {
    $updated = Order::where('buyer_id', $address->user_id)
        ->whereNull('delivery_address_id')
        ->update(['delivery_address_id' => $address->id]);
    
    echo "Utilisateur {$address->user_id} ({$address->full_name}): {$updated} commande(s) mise(s) à jour\n";
}

echo "\n✅ Terminé!\n";
