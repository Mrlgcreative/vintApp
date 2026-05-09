<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Vérification de la BD\n";
echo "=======================\n\n";

try {
    $result = DB::table('settings')
        ->where('key', 'preregistration_enabled')
        ->first();
    
    if ($result) {
        echo "Clé trouvée :\n";
        echo "  - Key: " . $result->key . "\n";
        echo "  - Value: " . $result->value . "\n";
        echo "  - Type: " . $result->type . "\n\n";
        
        if ($result->value == '0' || $result->value === 0) {
            echo "✅ La préinscription est DÉSACTIVÉE\n";
        } else {
            echo "⚠️  La préinscription est ACTIVÉE. Forçage de la désactivation...\n";
            
            DB::table('settings')
                ->where('key', 'preregistration_enabled')
                ->update(['value' => '0']);
            
            echo "✅ Désactivée\n";
        }
    } else {
        echo "❌ Setting 'preregistration_enabled' non trouvée en BD\n";
        echo "   Création...\n";
        
        DB::table('settings')->insert([
            'key' => 'preregistration_enabled',
            'value' => '0',
            'type' => 'boolean',
            'category' => 'preregistration',
            'description' => 'Active le mode préinscription',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Créée et désactivée\n";
    }
    
    echo "\n" . str_repeat("=", 40) . "\n";
    echo "🔄 Clearing cache...\n";
    system('php artisan cache:clear');
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
