#!/usr/bin/env php
<?php

/**
 * Script de vérification de l'intégration CinetPay
 * Execute: php check_cinetpay_integration.php
 */

echo "🔍 Vérification de l'intégration CinetPay\n";
echo str_repeat('=', 60) . "\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Vérifier que le SDK est copié
echo "1️⃣  Vérification du SDK CinetPay...\n";
if (file_exists(__DIR__ . '/app/Services/CinetPay.php')) {
    $success[] = "✅ SDK CinetPay trouvé dans app/Services/CinetPay.php";
} else {
    $errors[] = "❌ SDK CinetPay manquant dans app/Services/CinetPay.php";
}

// 2. Vérifier la migration
echo "\n2️⃣  Vérification de la migration...\n";
$migrationFile = glob(__DIR__ . '/database/migrations/*_create_payments_table.php');
if (!empty($migrationFile)) {
    $success[] = "✅ Migration payments trouvée: " . basename($migrationFile[0]);
} else {
    $errors[] = "❌ Migration payments manquante";
}

// 3. Vérifier le modèle Payment
echo "\n3️⃣  Vérification du modèle Payment...\n";
if (file_exists(__DIR__ . '/app/Models/Payment.php')) {
    $success[] = "✅ Modèle Payment trouvé";
    
    // Vérifier les méthodes importantes
    $paymentContent = file_get_contents(__DIR__ . '/app/Models/Payment.php');
    if (strpos($paymentContent, 'isCompleted') !== false) {
        $success[] = "✅ Méthode isCompleted() présente";
    } else {
        $warnings[] = "⚠️  Méthode isCompleted() manquante";
    }
    
    if (strpos($paymentContent, 'markAsCompleted') !== false) {
        $success[] = "✅ Méthode markAsCompleted() présente";
    } else {
        $warnings[] = "⚠️  Méthode markAsCompleted() manquante";
    }
} else {
    $errors[] = "❌ Modèle Payment manquant";
}

// 4. Vérifier le controller
echo "\n4️⃣  Vérification du PaymentController...\n";
if (file_exists(__DIR__ . '/app/Http/Controllers/PaymentController.php')) {
    $success[] = "✅ PaymentController trouvé";
    
    $controllerContent = file_get_contents(__DIR__ . '/app/Http/Controllers/PaymentController.php');
    
    $methods = [
        'initiateOrderPayment' => 'Initiation paiement commande',
        'handleNotification' => 'Webhook IPN',
        'handleReturn' => 'Page de retour',
        'initiateWalletTopup' => 'Rechargement wallet',
    ];
    
    foreach ($methods as $method => $description) {
        if (strpos($controllerContent, "function $method") !== false) {
            $success[] = "✅ Méthode $method() présente ($description)";
        } else {
            $warnings[] = "⚠️  Méthode $method() manquante ($description)";
        }
    }
} else {
    $errors[] = "❌ PaymentController manquant";
}

// 5. Vérifier les routes
echo "\n5️⃣  Vérification des routes...\n";
if (file_exists(__DIR__ . '/routes/web.php')) {
    $routesContent = file_get_contents(__DIR__ . '/routes/web.php');
    
    $routes = [
        'payments.order.initiate' => 'Paiement commande',
        'payments.cinetpay.notify' => 'Webhook IPN',
        'payments.cinetpay.return' => 'Retour utilisateur',
        'payments.wallet.topup' => 'Rechargement wallet',
    ];
    
    foreach ($routes as $routeName => $description) {
        if (strpos($routesContent, $routeName) !== false) {
            $success[] = "✅ Route '$routeName' configurée ($description)";
        } else {
            $errors[] = "❌ Route '$routeName' manquante ($description)";
        }
    }
} else {
    $errors[] = "❌ Fichier routes/web.php introuvable";
}

// 6. Vérifier la vue checkout
echo "\n6️⃣  Vérification des vues...\n";
if (file_exists(__DIR__ . '/resources/views/payments/checkout.blade.php')) {
    $success[] = "✅ Vue checkout.blade.php trouvée";
} else {
    $errors[] = "❌ Vue checkout.blade.php manquante";
}

// 7. Vérifier la configuration
echo "\n7️⃣  Vérification de la configuration...\n";
if (file_exists(__DIR__ . '/config/services.php')) {
    $servicesContent = file_get_contents(__DIR__ . '/config/services.php');
    
    if (strpos($servicesContent, 'cinetpay') !== false) {
        $success[] = "✅ Configuration CinetPay ajoutée dans config/services.php";
    } else {
        $errors[] = "❌ Configuration CinetPay manquante dans config/services.php";
    }
} else {
    $errors[] = "❌ Fichier config/services.php introuvable";
}

// 8. Vérifier les variables d'environnement
echo "\n8️⃣  Vérification des variables d'environnement...\n";
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    
    $envVars = [
        'CINETPAY_SITE_ID',
        'CINETPAY_API_KEY',
        'CINETPAY_PLATFORM',
        'CINETPAY_VERSION',
    ];
    
    foreach ($envVars as $var) {
        if (strpos($envContent, $var) !== false) {
            $success[] = "✅ Variable $var configurée";
        } else {
            $warnings[] = "⚠️  Variable $var manquante dans .env";
        }
    }
} else {
    $warnings[] = "⚠️  Fichier .env introuvable (normal en développement)";
}

// 9. Vérifier la documentation
echo "\n9️⃣  Vérification de la documentation...\n";
$docs = [
    'CINETPAY_INTEGRATION_GUIDE.md' => 'Guide complet',
    'CINETPAY_QUICK_START.md' => 'Quick start',
    '.env.cinetpay.example' => 'Template .env',
];

foreach ($docs as $file => $description) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $success[] = "✅ Documentation '$file' présente ($description)";
    } else {
        $warnings[] = "⚠️  Documentation '$file' manquante ($description)";
    }
}

// Affichage du résumé
echo "\n" . str_repeat('=', 60) . "\n";
echo "📊 RÉSUMÉ DE LA VÉRIFICATION\n";
echo str_repeat('=', 60) . "\n\n";

if (!empty($success)) {
    echo "✅ SUCCÈS (" . count($success) . ")\n";
    echo str_repeat('-', 60) . "\n";
    foreach ($success as $msg) {
        echo $msg . "\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  AVERTISSEMENTS (" . count($warnings) . ")\n";
    echo str_repeat('-', 60) . "\n";
    foreach ($warnings as $msg) {
        echo $msg . "\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERREURS (" . count($errors) . ")\n";
    echo str_repeat('-', 60) . "\n";
    foreach ($errors as $msg) {
        echo $msg . "\n";
    }
    echo "\n";
}

// Conclusion
echo str_repeat('=', 60) . "\n";
if (empty($errors)) {
    echo "🎉 INSTALLATION RÉUSSIE !\n\n";
    echo "Prochaines étapes:\n";
    echo "1. php artisan migrate\n";
    echo "2. Configurer les variables dans .env (voir .env.cinetpay.example)\n";
    echo "3. Ajouter le bouton de paiement aux vues\n";
    echo "4. Tester avec une transaction de test\n\n";
    echo "📖 Consultez CINETPAY_QUICK_START.md pour démarrer\n";
} else {
    echo "❌ DES ERREURS ONT ÉTÉ DÉTECTÉES\n\n";
    echo "Veuillez corriger les erreurs ci-dessus avant de continuer.\n";
}
echo str_repeat('=', 60) . "\n";

exit(empty($errors) ? 0 : 1);
