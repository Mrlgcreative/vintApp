<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Setting;

echo "🔧 Désactivation de la Pré-inscription\n";
echo "=======================================\n\n";

// Vérifier l'état actuel
$currentState = Setting::get('preregistration_enabled', false);

echo "État actuel : " . ($currentState ? "✅ ACTIVÉE" : "❌ DÉSACTIVÉE") . "\n\n";

if ($currentState) {
    echo "📝 Désactivation en cours...\n";
    
    // Désactiver la pré-inscription
    Setting::set('preregistration_enabled', false);
    
    // Vérifier que c'est bien désactivé
    $newState = Setting::get('preregistration_enabled', false);
    
    if (!$newState) {
        echo "✅ Pré-inscription DÉSACTIVÉE avec succès !\n\n";
    } else {
        echo "❌ Erreur lors de la désactivation\n\n";
    }
} else {
    echo "ℹ️  La pré-inscription est déjà désactivée.\n\n";
}

echo "=======================================\n";
echo "📊 État Final\n";
echo "=======================================\n\n";

$finalState = Setting::get('preregistration_enabled', false);
echo "Pré-inscription : " . ($finalState ? "✅ ACTIVÉE" : "❌ DÉSACTIVÉE") . "\n\n";

if (!$finalState) {
    echo "✅ Le site est maintenant accessible à tous les visiteurs.\n";
    echo "✅ Aucune redirection vers /preregistration.\n";
    echo "✅ Les utilisateurs peuvent naviguer normalement.\n\n";
    
    echo "🔄 Prochaines étapes :\n";
    echo "   1. Videz le cache : php artisan cache:clear\n";
    echo "   2. Testez l'accès : http://localhost:8000/\n";
} else {
    echo "⚠️  La pré-inscription est toujours active.\n";
}

echo "\n";
