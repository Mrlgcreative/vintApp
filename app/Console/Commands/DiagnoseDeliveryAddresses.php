<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\DeliveryAddress;

class DiagnoseDeliveryAddresses extends Command
{
    protected $signature = 'diagnose:delivery-addresses {order_id?}';
    protected $description = 'Diagnostique les problèmes d\'affichage des adresses de livraison';

    public function handle()
    {
        $this->info("🔍 Diagnostic des Adresses de Livraison");
        
        $orderId = $this->argument('order_id');
        
        if ($orderId) {
            $this->diagnoseSpecificOrder($orderId);
        } else {
            $this->diagnoseRecentOrders();
        }
    }
    
    private function diagnoseSpecificOrder($orderId)
    {
        $this->info("📋 Diagnostic pour la commande ID: {$orderId}");
        
        $order = Order::find($orderId);
        
        if (!$order) {
            $this->error("❌ Commande ID {$orderId} non trouvée");
            return;
        }
        
        $this->line("✅ Commande trouvée:");
        $this->line("   - ID: {$order->id}");
        $this->line("   - Buyer ID: {$order->buyer_id}");
        $this->line("   - Delivery Address ID: " . ($order->delivery_address_id ?: 'NULL'));
        $this->line("   - Status: {$order->status}");
        
        // Test du chargement de la relation
        $order->load('deliveryAddress');
        
        if ($order->deliveryAddress) {
            $this->info("✅ Adresse de livraison chargée:");
            $this->line("   - Nom: {$order->deliveryAddress->full_name}");
            $this->line("   - Téléphone: {$order->deliveryAddress->phone}");
            $this->line("   - Ville: {$order->deliveryAddress->city}");
            $this->line("   - Commune: {$order->deliveryAddress->commune}");
            $this->line("   - Adresse: {$order->deliveryAddress->address}");
        } else {
            $this->warn("⚠️  Adresse de livraison non trouvée");
            
            if ($order->delivery_address_id) {
                // Vérifier si l'adresse existe dans la table
                $address = DeliveryAddress::find($order->delivery_address_id);
                if ($address) {
                    $this->error("❌ L'adresse existe mais la relation ne fonctionne pas");
                } else {
                    $this->error("❌ L'adresse ID {$order->delivery_address_id} n'existe pas dans delivery_addresses");
                }
            } else {
                $this->error("❌ Aucun delivery_address_id défini pour cette commande");
            }
        }
        
        // Vérifier les adresses disponibles pour cet utilisateur
        $userAddresses = DeliveryAddress::where('user_id', $order->buyer_id)->get();
        $this->info("📍 Adresses disponibles pour l'utilisateur {$order->buyer_id}:");
        foreach ($userAddresses as $addr) {
            $this->line("   - ID: {$addr->id} | {$addr->full_name} | {$addr->city}");
        }
    }
    
    private function diagnoseRecentOrders()
    {
        $this->info("📋 Diagnostic des commandes récentes:");
        
        $orders = Order::with('deliveryAddress')->latest()->take(5)->get();
        
        foreach ($orders as $order) {
            $this->line("");
            $this->info("Commande ID: {$order->id}");
            $this->line("   - Buyer: {$order->buyer_id}");
            $this->line("   - Delivery Address ID: " . ($order->delivery_address_id ?: 'NULL'));
            
            if ($order->deliveryAddress) {
                $this->line("   - ✅ Adresse: {$order->deliveryAddress->full_name}, {$order->deliveryAddress->city}");
            } else {
                $this->line("   - ❌ Pas d'adresse de livraison");
            }
        }
        
        $this->line("");
        $this->info("💡 Pour diagnostiquer une commande spécifique:");
        $this->line("php artisan diagnose:delivery-addresses ORDRE_ID");
    }
}