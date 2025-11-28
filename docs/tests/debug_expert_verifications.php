<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProductAuthenticityCheck;
use App\Models\User;

echo "=== DIAGNOSTIC VÉRIFICATIONS EXPERT ===" . PHP_EOL;

try {
    // 1. Lister toutes les vérifications
    echo "1. Toutes les vérifications:" . PHP_EOL;
    $checks = ProductAuthenticityCheck::all(['id', 'status', 'expert_id', 'vendor_id', 'created_at']);
    
    if ($checks->isEmpty()) {
        echo "  Aucune vérification trouvée" . PHP_EOL;
    } else {
        foreach ($checks as $check) {
            echo "  ID: {$check->id} | Statut: {$check->status} | Expert ID: {$check->expert_id} | Vendor: {$check->vendor_id} | Créé: {$check->created_at}" . PHP_EOL;
        }
    }

    // 2. Lister les experts
    echo PHP_EOL . "2. Utilisateurs experts:" . PHP_EOL;
    $experts = User::whereHas('roles', function($q) { 
        $q->where('slug', 'expert'); 
    })->get(['id', 'name', 'email']);
    
    if ($experts->isEmpty()) {
        echo "  Aucun expert trouvé" . PHP_EOL;
    } else {
        foreach ($experts as $expert) {
            echo "  ID: {$expert->id} | Nom: {$expert->name} | Email: {$expert->email}" . PHP_EOL;
        }
    }

    // 3. Vérifications avec statut expert_review
    echo PHP_EOL . "3. Vérifications en attente expert:" . PHP_EOL;
    $expertReviews = ProductAuthenticityCheck::where('status', 'expert_review')->get(['id', 'expert_id', 'vendor_id']);
    
    if ($expertReviews->isEmpty()) {
        echo "  Aucune vérification en attente expert" . PHP_EOL;
    } else {
        foreach ($expertReviews as $review) {
            echo "  ID: {$review->id} | Expert ID: {$review->expert_id} | Vendor: {$review->vendor_id}" . PHP_EOL;
        }
    }

    // 4. Vérifier l'utilisateur actuellement connecté
    echo PHP_EOL . "4. Statuts disponibles dans le modèle:" . PHP_EOL;
    $reflection = new ReflectionClass(ProductAuthenticityCheck::class);
    $constants = $reflection->getConstants();
    foreach ($constants as $name => $value) {
        if (str_contains($name, 'STATUS')) {
            echo "  {$name}: {$value}" . PHP_EOL;
        }
    }

    // 5. Compter par statut
    echo PHP_EOL . "5. Nombre de vérifications par statut:" . PHP_EOL;
    $statusCounts = ProductAuthenticityCheck::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    
    if ($statusCounts->isEmpty()) {
        echo "  Aucun statut trouvé" . PHP_EOL;
    } else {
        foreach ($statusCounts as $statusCount) {
            echo "  {$statusCount->status}: {$statusCount->count}" . PHP_EOL;
        }
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}