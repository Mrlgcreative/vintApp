<?php
/**
 * Script de diagnostic pour le bouton d'authenticité
 * Exécuter: php test-auth-button.php
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use App\Models\Category;

echo "=== DIAGNOSTIC BOUTON AUTHENTICITÉ ===\n\n";

// Récupérer les derniers articles
$items = Item::with('category')->latest()->limit(5)->get();

foreach ($items as $item) {
    echo "Article ID: {$item->id} - {$item->name}\n";
    echo "  - user_id: {$item->user_id}\n";
    echo "  - authenticity_requested: " . ($item->authenticity_requested ? 'true' : 'false') . "\n";
    echo "  - authenticity_verified: " . ($item->authenticity_verified ? 'true' : 'false') . "\n";
    echo "  - category: " . ($item->category ? $item->category->slug : 'NULL') . "\n";
    echo "  - category->is_active: " . ($item->category && $item->category->is_active ? 'true' : 'false') . "\n";
    echo "  - canRequestVerification(): " . ($item->canRequestVerification() ? 'true' : 'false') . "\n";
    echo "  - authenticityCheck exists: " . ($item->authenticityCheck ? 'true' : 'false') . "\n";
    echo "\n";
}

echo "\n=== CATÉGORIES ACTIVES ===\n";
$categories = Category::where('is_active', true)->get();
foreach ($categories as $cat) {
    echo "  - {$cat->slug}: {$cat->name} (is_active: " . ($cat->is_active ? 'true' : 'false') . ")\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
