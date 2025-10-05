<?php

// Test simple du mode maintenance sans bootstrap Laravel complet
require_once __DIR__ . '/vendor/autoload.php';

// Créer le répertoire manuellement si nécessaire
$storagePath = __DIR__ . '/storage/framework';
if (!file_exists($storagePath)) {
    mkdir($storagePath, 0755, true);
}

// Fonction de test pour storage_path
if (!function_exists('storage_path')) {
    function storage_path($path = '') {
        return __DIR__ . '/storage' . ($path ? '/' . $path : '');
    }
}

use App\Http\Middleware\MaintenanceMode;

echo "=== Test du Mode Maintenance ===\n\n";

echo "1. Test d'activation du mode maintenance...\n";
try {
    $result = MaintenanceMode::enable('Site en maintenance pour test', '15 minutes');
    
    if ($result) {
        echo "✅ Mode maintenance activé avec succès\n";
        
        // Vérifier le fichier
        $maintenanceFile = storage_path('framework/maintenance.json');
        if (file_exists($maintenanceFile)) {
            echo "✅ Fichier créé : $maintenanceFile\n";
            $content = file_get_contents($maintenanceFile);
            echo "Contenu du fichier :\n" . $content . "\n\n";
            
            // Vérifier le statut
            if (MaintenanceMode::isEnabled()) {
                echo "✅ Statut confirmé : maintenance activée\n\n";
            } else {
                echo "❌ Statut non confirmé\n\n";
            }
        } else {
            echo "❌ Fichier non créé\n\n";
        }
    } else {
        echo "❌ Échec de l'activation\n\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n\n";
}

echo "2. Test de désactivation du mode maintenance...\n";
try {
    $result = MaintenanceMode::disable();
    
    if ($result) {
        echo "✅ Mode maintenance désactivé avec succès\n";
        
        if (!MaintenanceMode::isEnabled()) {
            echo "✅ Statut confirmé : maintenance désactivée\n";
        } else {
            echo "❌ Statut non confirmé\n";
        }
    } else {
        echo "❌ Échec de la désactivation\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== Test terminé ===\n";