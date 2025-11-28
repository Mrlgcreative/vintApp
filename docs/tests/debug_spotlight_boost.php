<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProductBoost;
use App\Models\Item;
use App\Models\BoostType;
use Illuminate\Support\Facades\Auth;

echo "=== VÉRIFICATION BOOST SPOTLIGHT ===\n";

// Simuler l'authentification de l'utilisateur
$userId = 1; // Remplacez par votre ID utilisateur
$user = \App\Models\User::find($userId);
if ($user) {
    Auth::login($user);
    echo "Utilisateur connecté: {$user->name} (ID: {$user->id})\n\n";
}

// 1. Vérifier les boosts actifs de l'utilisateur
echo "1. BOOSTS ACTIFS DE L'UTILISATEUR:\n";
echo "==================================\n";

$activeBoosts = ProductBoost::where('user_id', $userId)
    ->where('status', 'active')
    ->with(['item', 'boostType'])
    ->get();

if ($activeBoosts->count() > 0) {
    foreach ($activeBoosts as $boost) {
        echo "- Produit: {$boost->item->name}\n";
        echo "  Type de boost: {$boost->boostType->name} ({$boost->boostType->display_name})\n";
        echo "  Status: {$boost->status}\n";
        echo "  Activé le: {$boost->activated_at}\n";
        echo "  Expire le: {$boost->expires_at}\n";
        echo "  Durée: {$boost->duration} jour(s)\n";
        echo "  Prix payé: {$boost->total_price} CDF\n\n";
    }
} else {
    echo "Aucun boost actif trouvé.\n\n";
}

// 2. Vérifier spécifiquement le boost Spotlight
echo "2. VÉRIFICATION BOOST SPOTLIGHT:\n";
echo "================================\n";

$spotlightBoostType = BoostType::where('name', 'spotlight')
    ->orWhere('display_name', 'like', '%Spotlight%')
    ->first();

if ($spotlightBoostType) {
    echo "Boost Spotlight trouvé:\n";
    echo "- ID: {$spotlightBoostType->id}\n";
    echo "- Nom: {$spotlightBoostType->name}\n";
    echo "- Nom d'affichage: {$spotlightBoostType->display_name}\n";
    echo "- Description: {$spotlightBoostType->description}\n\n";
    
    // Chercher les produits avec ce boost actif
    $spotlightProducts = ProductBoost::where('boost_type_id', $spotlightBoostType->id)
        ->where('status', 'active')
        ->where('expires_at', '>', now())
        ->with(['item', 'user'])
        ->get();
        
    if ($spotlightProducts->count() > 0) {
        echo "Produits avec boost Spotlight actif:\n";
        foreach ($spotlightProducts as $boost) {
            echo "- {$boost->item->name} (Utilisateur: {$boost->user->name})\n";
            echo "  Expire le: {$boost->expires_at}\n";
        }
    } else {
        echo "Aucun produit avec boost Spotlight actif trouvé.\n";
    }
} else {
    echo "Type de boost Spotlight introuvable dans la base de données.\n";
}

echo "\n3. TOUS LES TYPES DE BOOST DISPONIBLES:\n";
echo "=======================================\n";

$allBoostTypes = BoostType::all();
foreach ($allBoostTypes as $boostType) {
    echo "- ID: {$boostType->id}, Nom: {$boostType->name}, Affichage: {$boostType->display_name}\n";
}

echo "\n=== DIAGNOSTIC TERMINÉ ===\n";