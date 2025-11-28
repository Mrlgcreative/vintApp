#!/usr/bin/env php
<?php

/**
 * Script de Test de Distribution des Paiements
 * 
 * Ce script teste le système complet de distribution :
 * 1. Vérifier les settings (commission, transport)
 * 2. Créer une commande test
 * 3. Simuler la confirmation de réception
 * 4. Vérifier la distribution des fonds
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "========================================\n";
echo "TEST DE DISTRIBUTION DES PAIEMENTS\n";
echo "========================================\n\n";

// 1. Vérifier les settings
echo "1️⃣ Vérification des settings...\n";
$commissionSetting = DB::table('settings')
    ->where('key', 'platform_commission_percentage')
    ->first();
$transportSetting = DB::table('settings')
    ->where('key', 'transport_fee_percentage')
    ->first();

if (!$commissionSetting || !$transportSetting) {
    echo "❌ ERREUR : Settings manquants !\n";
    echo "   Exécutez : php artisan migrate\n";
    exit(1);
}

$commissionPercent = (float) $commissionSetting->value;
$transportPercent = (float) $transportSetting->value;

echo "   ✅ Commission plateforme : {$commissionPercent}%\n";
echo "   ✅ Frais de transport : {$transportPercent}%\n\n";

// 2. Exemple de calcul
echo "2️⃣ Calcul de distribution pour 170 USD...\n";
$totalAmount = 170.00;
$commissionAmount = round($totalAmount * ($commissionPercent / 100), 2);
$transportAmount = round($totalAmount * ($transportPercent / 100), 2);
$sellerAmount = $totalAmount - $commissionAmount - $transportAmount;

echo "   Total        : {$totalAmount} USD\n";
echo "   Commission   : {$commissionAmount} USD ({$commissionPercent}%)\n";
echo "   Transport    : {$transportAmount} USD ({$transportPercent}%)\n";
echo "   ─────────────────────────────────\n";
echo "   Vendeur      : {$sellerAmount} USD\n";
echo "   Plateforme   : " . ($commissionAmount + $transportAmount) . " USD\n\n";

// 3. Vérifier la formule
$expectedSeller = 170 - 17 - 8.5; // Avec 10% et 5%
if (abs($sellerAmount - $expectedSeller) < 0.01) {
    echo "   ✅ Calcul correct !\n";
} else {
    echo "   ⚠️ Calcul différent de l'attendu ({$expectedSeller} USD)\n";
}
echo "\n";

// 4. Vérifier les wallets existants
echo "3️⃣ Vérification des wallets...\n";
$pendingWallets = DB::table('wallets')->where('type', 'pending')->count();
$mainWallets = DB::table('wallets')->where('type', 'main')->count();
$enterpriseWallets = DB::table('wallets')->where('type', 'enterprise')->count();

echo "   Wallets Pending : {$pendingWallets}\n";
echo "   Wallets Main    : {$mainWallets}\n";
echo "   Wallets Enterprise : {$enterpriseWallets}\n\n";

// 5. Afficher les commandes en attente de confirmation
echo "4️⃣ Commandes en attente de confirmation...\n";
$pendingOrders = DB::table('orders')
    ->where('status', 'shipped')
    ->orWhere('status', 'delivered')
    ->whereNull('confirmed_by_buyer_at')
    ->get(['id', 'buyer_id', 'seller_id', 'total_amount', 'currency', 'status']);

if ($pendingOrders->count() > 0) {
    echo "   📦 {$pendingOrders->count()} commande(s) en attente :\n";
    foreach ($pendingOrders as $order) {
        echo "      Commande #{$order->id} : {$order->total_amount} {$order->currency} (statut: {$order->status})\n";
        
        // Calculer la distribution pour cette commande
        $total = (float) $order->total_amount;
        $commission = round($total * ($commissionPercent / 100), 2);
        $transport = round($total * ($transportPercent / 100), 2);
        $seller = $total - $commission - $transport;
        
        echo "         → Si confirmée : Vendeur: {$seller} {$order->currency}, Plateforme: " . ($commission + $transport) . " {$order->currency}\n";
    }
} else {
    echo "   ℹ️ Aucune commande en attente de confirmation\n";
}
echo "\n";

// 6. Afficher les dernières transactions
echo "5️⃣ Dernières transactions de distribution...\n";
$recentTransactions = DB::table('transactions')
    ->where(function($query) {
        $query->where('transaction_id', 'LIKE', 'SELLER-%')
              ->orWhere('transaction_id', 'LIKE', 'COMMISSION-%')
              ->orWhere('transaction_id', 'LIKE', 'TRANSPORT-%');
    })
    ->orderBy('created_at', 'desc')
    ->limit(6)
    ->get(['transaction_id', 'amount', 'currency', 'purpose', 'created_at']);

if ($recentTransactions->count() > 0) {
    echo "   📝 {$recentTransactions->count()} transaction(s) récente(s) :\n";
    foreach ($recentTransactions as $txn) {
        $type = '';
        if (str_starts_with($txn->transaction_id, 'SELLER-')) $type = '👤 Vendeur';
        elseif (str_starts_with($txn->transaction_id, 'COMMISSION-')) $type = '💰 Commission';
        elseif (str_starts_with($txn->transaction_id, 'TRANSPORT-')) $type = '🚚 Transport';
        
        echo "      {$type} : {$txn->amount} {$txn->currency} - {$txn->purpose}\n";
        echo "         Date : {$txn->created_at}\n";
    }
} else {
    echo "   ℹ️ Aucune transaction de distribution trouvée\n";
}
echo "\n";

// 7. Statistiques du wallet enterprise
echo "6️⃣ Statistiques du Wallet Enterprise...\n";
$enterpriseWalletUSD = DB::table('wallets')
    ->where('type', 'enterprise')
    ->where('currency', 'USD')
    ->whereNull('user_id')
    ->first();

$enterpriseWalletCDF = DB::table('wallets')
    ->where('type', 'enterprise')
    ->where('currency', 'CDF')
    ->whereNull('user_id')
    ->first();

if ($enterpriseWalletUSD) {
    echo "   💵 USD : {$enterpriseWalletUSD->balance} USD\n";
} else {
    echo "   💵 USD : Wallet non créé\n";
}

if ($enterpriseWalletCDF) {
    echo "   💵 CDF : {$enterpriseWalletCDF->balance} CDF\n";
} else {
    echo "   💵 CDF : Wallet non créé\n";
}

// Calculer le total des commissions
$totalCommissions = DB::table('transactions')
    ->where('transaction_id', 'LIKE', 'COMMISSION-%')
    ->sum('amount');

$totalTransport = DB::table('transactions')
    ->where('transaction_id', 'LIKE', 'TRANSPORT-%')
    ->sum('amount');

echo "   📊 Total commissions collectées : {$totalCommissions}\n";
echo "   📊 Total transport collecté : {$totalTransport}\n";
echo "   📊 Total plateforme : " . ($totalCommissions + $totalTransport) . "\n\n";

echo "========================================\n";
echo "TEST TERMINÉ\n";
echo "========================================\n";
echo "\n";
echo "💡 Pour tester une distribution complète :\n";
echo "   1. Créez une commande via le site\n";
echo "   2. Marquez-la comme expédiée (vendeur)\n";
echo "   3. Confirmez la réception (acheteur)\n";
echo "   4. Re-exécutez ce script pour voir les résultats\n";
echo "\n";
