#!/usr/bin/env php
<?php

/**
 * Script de Vérification et Correction des Statuts de Commandes
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "VÉRIFICATION DES STATUTS DE COMMANDES\n";
echo "========================================\n\n";

// 1. Trouver les commandes avec statut incorrect
echo "1️⃣ Recherche de commandes avec statut incorrect...\n";

$wrongStatusOrders = DB::table('orders')
    ->where('status', 'confirmed')
    ->whereNull('confirmed_by_buyer_at')
    ->whereNotNull('paid_at')
    ->get(['id', 'buyer_id', 'seller_id', 'total_amount', 'currency', 'status', 'paid_at', 'created_at']);

if ($wrongStatusOrders->count() > 0) {
    echo "   ⚠️ {$wrongStatusOrders->count()} commande(s) trouvée(s) avec statut incorrect :\n\n";
    
    foreach ($wrongStatusOrders as $order) {
        echo "   📦 Commande #{$order->id}\n";
        echo "      Montant: {$order->total_amount} {$order->currency}\n";
        echo "      Status actuel: {$order->status}\n";
        echo "      Payée le: {$order->paid_at}\n";
        echo "      Confirmée par acheteur: NON (NULL)\n";
        echo "      → Devrait avoir status = 'pending'\n\n";
    }
    
    // Demander confirmation pour corriger
    echo "========================================\n";
    echo "Voulez-vous corriger ces commandes ? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($line) === 'y' || strtolower($line) === 'yes' || strtolower($line) === 'o' || strtolower($line) === 'oui') {
        echo "\n2️⃣ Correction en cours...\n";
        
        $updated = DB::table('orders')
            ->where('status', 'confirmed')
            ->whereNull('confirmed_by_buyer_at')
            ->whereNotNull('paid_at')
            ->update([
                'status' => 'pending',
                'updated_at' => now()
            ]);
        
        echo "   ✅ {$updated} commande(s) corrigée(s) !\n";
        echo "   Status changé de 'confirmed' → 'pending'\n\n";
        
        // Afficher les commandes après correction
        echo "3️⃣ Vérification après correction...\n";
        $pendingOrders = DB::table('orders')
            ->where('status', 'pending')
            ->whereNotNull('paid_at')
            ->get(['id', 'total_amount', 'currency', 'paid_at']);
        
        echo "   📊 {$pendingOrders->count()} commande(s) avec status 'pending' (payées, en attente d'expédition)\n";
    } else {
        echo "\n❌ Correction annulée.\n";
    }
} else {
    echo "   ✅ Aucune commande avec statut incorrect !\n";
    echo "   Toutes les commandes sont cohérentes.\n";
}

echo "\n========================================\n";
echo "STATISTIQUES DES COMMANDES\n";
echo "========================================\n\n";

// Statistiques par statut
$statuses = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded'];

foreach ($statuses as $status) {
    $count = DB::table('orders')->where('status', $status)->count();
    $icon = match($status) {
        'pending' => '⏳',
        'processing' => '📦',
        'shipped' => '🚚',
        'delivered' => '📬',
        'completed' => '✅',
        'cancelled' => '❌',
        'refunded' => '💸',
        default => '📋'
    };
    echo "   {$icon} {$status}: {$count} commande(s)\n";
}

// Commandes confirmées par l'acheteur
echo "\n";
$confirmedByBuyer = DB::table('orders')->whereNotNull('confirmed_by_buyer_at')->count();
echo "   ✅ Confirmées par l'acheteur: {$confirmedByBuyer}\n";

$notConfirmedByBuyer = DB::table('orders')
    ->whereNotNull('paid_at')
    ->whereNull('confirmed_by_buyer_at')
    ->count();
echo "   ⏰ En attente de confirmation: {$notConfirmedByBuyer}\n";

echo "\n========================================\n";
echo "VÉRIFICATION TERMINÉE\n";
echo "========================================\n";
