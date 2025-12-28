<?php
/**
 * Script de diagnostic pour les articles d'experts
 * Exécuter: php test-expert-items.php
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use App\Models\ExpertProfile;
use App\Models\Category;
use App\Models\User;

echo "=== DIAGNOSTIC EXPERT ITEMS ===\n\n";

// 1. Vérifier les experts
echo "1. EXPERTS DISPONIBLES:\n";
$experts = ExpertProfile::with('user')->get();
foreach ($experts as $profile) {
    echo "   - " . ($profile->user->name ?? 'N/A') . " (ID: " . $profile->user_id . ")\n";
    echo "     Spécialités: " . json_encode($profile->specialties) . "\n";
    echo "     Status: " . $profile->status . "\n\n";
}

// 2. Vérifier les articles en attente
echo "\n2. ARTICLES EN ATTENTE DE VÉRIFICATION:\n";
$pendingItems = Item::where('status', 'pending_verification')
    ->where('verification_status', 'pending')
    ->whereNull('verified_at')
    ->with(['category'])
    ->get();

if ($pendingItems->isEmpty()) {
    echo "   Aucun article en attente!\n";
    
    // Voyons tous les articles et leur statut
    echo "\n   Détail des derniers articles:\n";
    $allItems = Item::with('category')->latest()->limit(10)->get();
    foreach ($allItems as $item) {
        echo "   - ID: {$item->id}, Nom: " . substr($item->name, 0, 30) . "\n";
        echo "     Status: {$item->status}\n";
        echo "     Verification Status: {$item->verification_status}\n";
        echo "     Verified At: " . ($item->verified_at ?? 'NULL') . "\n";
        echo "     Category: " . ($item->category->slug ?? 'N/A') . "\n\n";
    }
} else {
    foreach ($pendingItems as $item) {
        echo "   - ID: {$item->id}, Nom: " . substr($item->name, 0, 30) . "\n";
        echo "     Category: " . ($item->category->slug ?? 'N/A') . "\n";
    }
}

// 3. Vérifier les catégories disponibles
echo "\n3. CATÉGORIES DISPONIBLES:\n";
$categories = Category::where('is_active', true)->get();
foreach ($categories as $cat) {
    echo "   - {$cat->slug}: {$cat->name}\n";
}

// 4. Simulation du filtre pour chaque expert
echo "\n4. SIMULATION FILTRE PAR EXPERT:\n";
foreach ($experts as $profile) {
    echo "\n   Expert: " . ($profile->user->name ?? 'N/A') . "\n";
    $specialties = $profile->specialties ?? [];
    
    $specialtyToCategoryMap = [
        'mode_luxe' => ['vetements', 'beaute'],
        'electronique' => ['electronique', 'informatique'],
        'bijoux' => ['beaute', 'collection'],
        'montres' => ['collection', 'beaute'],
        'sacs_maroquinerie' => ['vetements', 'beaute'],
        'vetements-femmes' => ['vetements'],
        'vetements-hommes' => ['vetements'],
        'vareuse' => ['vetements'],
        'general' => [],
    ];
    
    $categorySlugs = [];
    $isGeneralist = false;
    
    foreach ($specialties as $specialty) {
        if ($specialty === 'general') {
            $isGeneralist = true;
            break;
        }
        if (isset($specialtyToCategoryMap[$specialty])) {
            $categorySlugs = array_merge($categorySlugs, $specialtyToCategoryMap[$specialty]);
        } else {
            // Slug direct
            $categorySlugs[] = $specialty;
        }
    }
    
    echo "   Spécialités: " . json_encode($specialties) . "\n";
    echo "   Catégories filtrées: " . json_encode(array_unique($categorySlugs)) . "\n";
    echo "   Est généraliste: " . ($isGeneralist ? 'OUI' : 'NON') . "\n";
    
    // Compter les articles visibles
    $query = Item::where('status', 'pending_verification')
        ->where('verification_status', 'pending')
        ->whereNull('verified_at');
    
    if (!$isGeneralist && !empty($categorySlugs)) {
        $categorySlugs = array_unique($categorySlugs);
        $query->whereHas('category', function($q) use ($categorySlugs) {
            $q->whereIn('slug', $categorySlugs);
        });
    }
    
    $count = $query->count();
    echo "   Articles visibles: {$count}\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
