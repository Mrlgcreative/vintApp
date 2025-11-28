<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Http\Middleware\MaintenanceMode;

// Test d'activation du mode maintenance
echo "Test d'activation du mode maintenance...\n";
$result = MaintenanceMode::enable('Site en maintenance pour test', '15 minutes');

if ($result) {
    echo "✅ Mode maintenance activé avec succès\n";
    
    // Vérifier le statut
    if (MaintenanceMode::isEnabled()) {
        echo "✅ Statut confirmé : maintenance activée\n";
        
        // Lire le fichier créé
        $maintenanceFile = storage_path('framework/maintenance.json');
        if (file_exists($maintenanceFile)) {
            echo "✅ Fichier de maintenance créé : $maintenanceFile\n";
            echo "Contenu :\n";
            echo file_get_contents($maintenanceFile) . "\n";
        } else {
            echo "❌ Fichier de maintenance non trouvé\n";
        }
    } else {
        echo "❌ Statut non confirmé\n";
    }
} else {
    echo "❌ Échec de l'activation du mode maintenance\n";
}

// Test de désactivation
echo "\nTest de désactivation du mode maintenance...\n";
$result = MaintenanceMode::disable();

if ($result) {
    echo "✅ Mode maintenance désactivé avec succès\n";
    
    if (!MaintenanceMode::isEnabled()) {
        echo "✅ Statut confirmé : maintenance désactivée\n";
    } else {
        echo "❌ Statut non confirmé\n";
    }
} else {
    echo "❌ Échec de la désactivation du mode maintenance\n";
}