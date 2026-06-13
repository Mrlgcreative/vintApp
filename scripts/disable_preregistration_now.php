<?php

// Charger l'autoloader et l'application Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Bootstrapper l'application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

echo "🔧 Désactivation de la Pré-inscription\n";
echo "=======================================\n\n";

try {
    // Vérifier l'état actuel
    $currentState = Setting::get('preregistration_enabled', false);
    echo "État actuel : " . ($currentState ? "✅ ACTIVÉE" : "❌ DÉSACTIVÉE") . "\n\n";

    if ($currentState) {
        echo "📝 Désactivation en cours...\n";
        
        // Désactiver via UPDATE direct
        DB::table('settings')
            ->where('key', 'preregistration_enabled')
            ->update(['value' => '0']);
        
        // Vérifier
        $newState = Setting::get('preregistration_enabled', false);
        echo "✅ État mis à jour : " . ($newState ? "ACTIVÉE" : "DÉSACTIVÉE") . "\n\n";
    } else {
        echo "ℹ️  La pré-inscription est déjà désactivée.\n\n";
    }

    echo "=======================================\n";
    echo "✅ Opération réussie !\n";
    echo "=======================================\n\n";
    echo "✅ Le site est maintenant en mode NORMAL (pas de pré-inscription).\n";
    echo "✅ Les utilisateurs accèdent directement à l'application.\n";
    echo "✅ Aucune redirection vers /preregistration.\n\n";
    
    echo "🔄 Prochaines étapes :\n";
    echo "   1. php artisan cache:clear\n";
    echo "   2. Actualiser votre navigateur\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
