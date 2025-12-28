<?php
/**
 * Script pour corriger les articles bloqués
 * Remet verified_at à NULL pour les articles en attente de vérification expert
 * Exécuter: php fix-pending-items.php
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;

echo "=== CORRECTION DES ARTICLES EN ATTENTE ===\n\n";

// Trouver les articles qui ont verification_status=pending mais verified_at rempli
$itemsToFix = Item::where('status', 'pending_verification')
    ->where('verification_status', 'pending')
    ->whereNotNull('verified_at')
    ->get();

echo "Articles à corriger: " . $itemsToFix->count() . "\n\n";

foreach ($itemsToFix as $item) {
    echo "Correction de l'article ID: {$item->id} - {$item->name}\n";
    echo "  Ancien verified_at: {$item->verified_at}\n";
    
    $item->verified_at = null;
    $item->save();
    
    echo "  Nouveau verified_at: NULL ✓\n\n";
}

echo "=== VÉRIFICATION APRÈS CORRECTION ===\n";
$pendingCount = Item::where('status', 'pending_verification')
    ->where('verification_status', 'pending')
    ->whereNull('verified_at')
    ->count();

echo "Articles maintenant visibles par les experts: {$pendingCount}\n";
echo "\n=== CORRECTION TERMINÉE ===\n";
