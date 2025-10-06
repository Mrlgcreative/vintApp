<?php

/**
 * Script de test pour les routes de paiement
 * Usage: php test_payment_routes.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========================================\n";
echo "🧪 TEST DES ROUTES DE PAIEMENT\n";
echo "========================================\n\n";

// Couleurs pour le terminal
function success($message) {
    return "✅ " . $message;
}

function error($message) {
    return "❌ " . $message;
}

function info($message) {
    return "ℹ️  " . $message;
}

function warning($message) {
    return "⚠️  " . $message;
}

// Liste des routes à tester
$routes = [
    'payments.process' => 'POST /payments/process',
    'payments.illicocash' => 'POST /payments/illicocash',
    'payments.orange_money' => 'POST /payments/orange-money',
    'payments.airtel_money' => 'POST /payments/airtel-money',
    'payments.mpesa' => 'POST /payments/mpesa',
    'payments.africell' => 'POST /payments/africell',
    'payments.simulate' => 'POST /payments/simulate',
    'payments.callback' => 'POST /payments/callback',
];

echo info("Vérification des routes enregistrées...\n\n");

$totalRoutes = 0;
$foundRoutes = 0;

foreach ($routes as $name => $uri) {
    $totalRoutes++;
    
    try {
        $url = route($name);
        echo success("Route trouvée: $name\n");
        echo "   URL: $url\n";
        echo "   Pattern: $uri\n\n";
        $foundRoutes++;
    } catch (Exception $e) {
        echo error("Route manquante: $name\n");
        echo "   Erreur: " . $e->getMessage() . "\n\n";
    }
}

echo "\n========================================\n";
echo "📊 RÉSUMÉ DES ROUTES\n";
echo "========================================\n";
echo "Total routes testées: $totalRoutes\n";
echo "Routes trouvées: $foundRoutes\n";
echo "Routes manquantes: " . ($totalRoutes - $foundRoutes) . "\n";

if ($foundRoutes === $totalRoutes) {
    echo "\n" . success("Toutes les routes sont correctement enregistrées!\n");
} else {
    echo "\n" . warning("Certaines routes sont manquantes!\n");
}

echo "\n========================================\n";
echo "🔍 VÉRIFICATION DU CONTRÔLEUR\n";
echo "========================================\n\n";

// Vérifier que le contrôleur existe
$controllerPath = __DIR__ . '/app/Http/Controllers/PaymentController.php';

if (file_exists($controllerPath)) {
    echo success("PaymentController trouvé: $controllerPath\n\n");
    
    // Lire le contenu du contrôleur
    $content = file_get_contents($controllerPath);
    
    // Méthodes à vérifier
    $methods = [
        'processPayment' => 'Route: /payments/process',
        'payWithIllicocash' => 'Route: /payments/illicocash',
        'payWithOrangeMoney' => 'Route: /payments/orange-money',
        'payWithAirtelMoney' => 'Route: /payments/airtel-money',
        'payWithMpesa' => 'Route: /payments/mpesa',
        'payWithAfricell' => 'Route: /payments/africell',
        'simulatePayment' => 'Route: /payments/simulate',
        'handleCallback' => 'Route: /payments/callback',
    ];
    
    echo info("Vérification des méthodes du contrôleur...\n\n");
    
    $totalMethods = 0;
    $foundMethods = 0;
    
    foreach ($methods as $method => $route) {
        $totalMethods++;
        
        if (preg_match("/public function $method\(/", $content)) {
            echo success("Méthode trouvée: $method()\n");
            echo "   $route\n\n";
            $foundMethods++;
        } else {
            echo error("Méthode manquante: $method()\n");
            echo "   $route\n\n";
        }
    }
    
    echo "\n📊 RÉSUMÉ DES MÉTHODES\n";
    echo "Total méthodes: $totalMethods\n";
    echo "Méthodes trouvées: $foundMethods\n";
    echo "Méthodes manquantes: " . ($totalMethods - $foundMethods) . "\n";
    
    if ($foundMethods === $totalMethods) {
        echo "\n" . success("Toutes les méthodes sont implémentées!\n");
    } else {
        echo "\n" . warning("Certaines méthodes sont manquantes!\n");
    }
    
} else {
    echo error("PaymentController non trouvé: $controllerPath\n");
}

echo "\n========================================\n";
echo "⚠️  RECOMMANDATIONS\n";
echo "========================================\n\n";

// Vérifier le middleware CSRF
$csrfMiddlewarePath = __DIR__ . '/app/Http/Middleware/VerifyCsrfToken.php';

if (file_exists($csrfMiddlewarePath)) {
    $csrfContent = file_get_contents($csrfMiddlewarePath);
    
    if (strpos($csrfContent, 'payments/callback') !== false) {
        echo success("CSRF exclu pour 'payments/callback' ✓\n");
    } else {
        echo warning("CSRF non exclu pour 'payments/callback'\n");
        echo "\n📝 Action requise:\n";
        echo "Ajoutez dans app/Http/Middleware/VerifyCsrfToken.php:\n\n";
        echo "protected \$except = [\n";
        echo "    'payments/callback',  // Webhook opérateurs\n";
        echo "];\n\n";
    }
} else {
    echo warning("Fichier VerifyCsrfToken.php non trouvé\n");
}

echo "\n========================================\n";
echo "🧪 TEST DE SIMULATION\n";
echo "========================================\n\n";

echo info("Pour tester manuellement:\n\n");

echo "1. Démarrez le serveur:\n";
echo "   php artisan serve\n\n";

echo "2. Testez la route de simulation (PowerShell):\n";
echo "   \$headers = @{\n";
echo "       'Content-Type' = 'application/json'\n";
echo "       'Accept' = 'application/json'\n";
echo "   }\n";
echo "   \$body = @{\n";
echo "       amount = 1000\n";
echo "       provider = 'orange_money'\n";
echo "       phone = '0812345678'\n";
echo "   } | ConvertTo-Json\n\n";
echo "   Invoke-WebRequest -Uri 'http://localhost:8000/payments/simulate' -Method POST -Headers \$headers -Body \$body\n\n";

echo "3. Ou via cURL (Git Bash):\n";
echo "   curl -X POST http://localhost:8000/payments/simulate \\\n";
echo "     -H 'Content-Type: application/json' \\\n";
echo "     -H 'Accept: application/json' \\\n";
echo "     -d '{\"amount\":1000,\"provider\":\"orange_money\",\"phone\":\"0812345678\"}'\n\n";

echo "========================================\n";
echo "✅ TEST TERMINÉ\n";
echo "========================================\n\n";

// Résumé final
$allGood = ($foundRoutes === $totalRoutes && $foundMethods === $totalMethods);

if ($allGood) {
    echo "🎉 Toutes les vérifications sont passées!\n";
    echo "   Les routes de paiement sont prêtes à être utilisées.\n\n";
    exit(0);
} else {
    echo "⚠️  Certaines vérifications ont échoué.\n";
    echo "   Consultez les détails ci-dessus.\n\n";
    exit(1);
}
