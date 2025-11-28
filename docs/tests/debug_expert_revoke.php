<?php
/**
 * Script pour débugger la révocation d'expert
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExpertProfile;
use App\Models\ProductAuthenticityCheck;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

try {
    echo "=== DEBUG RÉVOCATION EXPERT ===\n\n";

    // Lister tous les experts
    $experts = ExpertProfile::with('user')->get();
    
    echo "📋 Experts disponibles :\n";
    foreach ($experts as $expert) {
        echo "  - ID: {$expert->id} | User ID: {$expert->user_id} | Nom: {$expert->user->name}\n";
        
        // Vérifier les vérifications en cours
        $pendingVerifications = ProductAuthenticityCheck::where('expert_id', $expert->user_id)
            ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_REVIEW)
            ->count();
        echo "    Vérifications en cours: {$pendingVerifications}\n";
        
        // Vérifier le rôle expert
        $hasExpertRole = $expert->user->roles()->where('slug', 'expert')->exists();
        echo "    A le rôle expert: " . ($hasExpertRole ? "✓" : "✗") . "\n\n";
    }

    // Vérifier si le rôle expert existe
    $expertRole = Role::where('slug', 'expert')->first();
    echo "🔑 Rôle expert :\n";
    if ($expertRole) {
        echo "  ✓ Rôle 'expert' trouvé (ID: {$expertRole->id})\n";
    } else {
        echo "  ✗ Rôle 'expert' introuvable !\n";
    }

} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}