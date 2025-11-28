<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Admin\AffiliateController;

echo "=== TEST DU CONTRÔLEUR D'AFFILIATION ===\n\n";

try {
    $controller = new AffiliateController();
    
    // Tester la méthode getReferrersList
    echo "Test de getReferrersList()...\n";
    $result = $controller->getReferrersList();
    $data = json_decode($result->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Succès! Nombre de parrains trouvés: " . count($data['referrers']) . "\n";
        
        foreach ($data['referrers'] as $referrer) {
            echo "  - {$referrer['name']} (ID: {$referrer['id']}) - {$referrer['referrals_count']} parrainages\n";
        }
    } else {
        echo "❌ Échec: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
}

echo "\n=== FIN DU TEST ===\n";