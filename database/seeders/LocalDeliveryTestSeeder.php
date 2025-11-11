<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\LocalDelivery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalDeliveryTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier s'il y a des commandes existantes
        $existingOrder = Order::with(['buyer', 'seller'])->first();
        
        if (!$existingOrder) {
            $this->command->error('Aucune commande trouvée dans la base de données. Créez d\'abord des commandes.');
            return;
        }

        $this->command->info("Utilisation de la commande #{$existingOrder->order_number}");

        // Créer une livraison locale de test
        $localDelivery = LocalDelivery::firstOrCreate(
            [
                'order_id' => $existingOrder->id,
                'delivery_type' => 'pickup'
            ],
            [
                'seller_id' => $existingOrder->seller_id,
                'buyer_id' => $existingOrder->buyer_id,
                'delivery_type' => 'pickup',
                'status' => 'proposed',
                'seller_latitude' => -4.441931, // Kinshasa - Gombe
                'seller_longitude' => 15.266293,
                'seller_address' => 'Avenue des Forces Armées, Gombe, Kinshasa',
                'seller_phone' => '+243999123456',
                'buyer_latitude' => -4.432774, // Kinshasa - Kintambo
                'buyer_longitude' => 15.251050,
                'buyer_address' => 'Boulevard du 30 Juin, Kintambo, Kinshasa',
                'buyer_phone' => '+243999654321',
                'distance_km' => 2.5,
                'delivery_fee' => 10.00,
                'currency' => 'CDF',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Livraison locale créée avec succès !');
        $this->command->info("ID de la livraison: {$localDelivery->id}");
        $this->command->info("Vendeur: {$existingOrder->seller->name} (ID: {$existingOrder->seller_id})");
        $this->command->info("Acheteur: {$existingOrder->buyer->name} (ID: {$existingOrder->buyer_id})");
        $this->command->info('');
        $this->command->info('Routes de test disponibles:');
        $this->command->info("- Voir la livraison: /local-delivery/{$localDelivery->id}");
        $this->command->info('- Livraisons vendeur: /local-delivery/user/seller');
        $this->command->info('- Livraisons acheteur: /local-delivery/user/buyer');
    }
}
