<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST AFFICHAGE SPOTLIGHT ===\n\n";

// Test du WelcomeController
try {
    $controller = new App\Http\Controllers\WelcomeController();
    
    // Simuler une requête
    $request = Illuminate\Http\Request::create('/');
    
    // Appeler la méthode index
    $response = $controller->index($request);
    
    echo "✅ WelcomeController->index() fonctionne\n";
    
    // Vérifier les données passées à la vue
    $viewData = $response->getData();
    
    if (isset($viewData['spotlightItems'])) {
        $spotlightCount = $viewData['spotlightItems']->count();
        echo "✅ spotlightItems trouvés: {$spotlightCount}\n";
        
        if ($spotlightCount > 0) {
            foreach ($viewData['spotlightItems'] as $item) {
                echo "  - Item: {$item->name}\n";
                
                // Tester l'accès aux images
                try {
                    $images = $item->images ?? [];
                    $imageCount = count($images);
                    echo "    Images: {$imageCount}\n";
                    
                    if ($imageCount > 0) {
                        echo "    Première image: {$images[0]}\n";
                    }
                    
                    // Tester les boosts actifs
                    $activeBoosts = $item->activeBoosts;
                    echo "    Boosts actifs: {$activeBoosts->count()}\n";
                    
                    if ($activeBoosts->count() > 0) {
                        $boost = $activeBoosts->first();
                        echo "    Type boost: {$boost->boost_type}\n";
                        echo "    Expire: {$boost->expires_at}\n";
                    }
                    
                } catch (Exception $e) {
                    echo "    ❌ Erreur accès images: {$e->getMessage()}\n";
                }
            }
        }
    } else {
        echo "❌ spotlightItems non trouvés dans les données de la vue\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: {$e->getMessage()}\n";
}

echo "\n=== TEST TERMINÉ ===\n";