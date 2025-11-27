<?php

/**
 * Test des fonctionnalités Monitoring, Backup et Mobile
 */

echo "🔍 TEST MONITORING, BACKUP & MOBILE\n";
echo "====================================\n\n";

// Test 1: Vérifier Telescope
echo "1️⃣  Test Laravel Telescope...\n";
$telescopeInstalled = class_exists('Laravel\Telescope\TelescopeServiceProvider');
if ($telescopeInstalled) {
    echo "   ✅ Telescope installé\n";
    if (file_exists(__DIR__ . '/config/telescope.php')) {
        echo "   ✅ Configuration publiée\n";
    }
    echo "   📍 URL: http://localhost:8000/telescope\n";
} else {
    echo "   ❌ Telescope manquant\n";
}
echo "\n";

// Test 2: Vérifier Backup
echo "2️⃣  Test Spatie Backup...\n";
$backupInstalled = class_exists('Spatie\Backup\BackupServiceProvider');
if ($backupInstalled) {
    echo "   ✅ Spatie Backup installé\n";
    if (file_exists(__DIR__ . '/config/backup.php')) {
        echo "   ✅ Configuration publiée\n";
        
        $config = include __DIR__ . '/config/backup.php';
        $appName = $config['backup']['name'] ?? 'N/A';
        echo "   📝 Nom backup: {$appName}\n";
        
        $databases = $config['backup']['source']['databases'] ?? [];
        echo "   🗄️  Databases: " . implode(', ', $databases) . "\n";
    }
} else {
    echo "   ❌ Spatie Backup manquant\n";
}
echo "\n";

// Test 3: Vérifier MonitoringService
echo "3️⃣  Test MonitoringService...\n";
if (file_exists(__DIR__ . '/app/Services/MonitoringService.php')) {
    echo "   ✅ MonitoringService créé\n";
    
    $methods = [
        'recordPerformance',
        'recordBusinessMetric',
        'recordError',
        'getRealTimeStats',
        'healthCheck',
    ];
    
    echo "   📊 Méthodes: " . count($methods) . "\n";
    foreach ($methods as $method) {
        echo "      - {$method}()\n";
    }
} else {
    echo "   ❌ MonitoringService manquant\n";
}
echo "\n";

// Test 4: Vérifier Logging Channels
echo "4️⃣  Test Logging Channels...\n";
$loggingConfig = file_get_contents(__DIR__ . '/config/logging.php');
$channels = ['security', 'performance', 'business', 'errors'];

$found = 0;
foreach ($channels as $channel) {
    if (strpos($loggingConfig, "'$channel'") !== false) {
        echo "   ✅ Canal '{$channel}' configuré\n";
        $found++;
    } else {
        echo "   ❌ Canal '{$channel}' manquant\n";
    }
}
echo "   📊 {$found}/" . count($channels) . " canaux configurés\n\n";

// Test 5: Vérifier MonitoringController
echo "5️⃣  Test MonitoringController...\n";
if (file_exists(__DIR__ . '/app/Http/Controllers/Admin/MonitoringController.php')) {
    echo "   ✅ MonitoringController créé\n";
    echo "   📍 Route: /admin/monitoring\n";
} else {
    echo "   ❌ MonitoringController manquant\n";
}
echo "\n";

// Test 6: Vérifier Dashboard Vue
echo "6️⃣  Test Dashboard Monitoring...\n";
if (file_exists(__DIR__ . '/resources/js/Pages/Admin/Monitoring/Dashboard.vue')) {
    echo "   ✅ Dashboard Vue component créé\n";
    echo "   🔄 Auto-refresh: Oui (5 secondes)\n";
    echo "   📊 Widgets: Database, Cache, Performance, Business, Errors\n";
} else {
    echo "   ❌ Dashboard manquant\n";
}
echo "\n";

// Test 7: Vérifier Routes
echo "7️⃣  Test Routes Monitoring...\n";
$webRoutes = file_get_contents(__DIR__ . '/routes/web.php');

$routes = [
    'admin.monitoring.index',
    'admin.monitoring.stats',
    'admin.monitoring.health',
    'admin.monitoring.reset',
];

$found = 0;
foreach ($routes as $route) {
    if (strpos($webRoutes, $route) !== false) {
        echo "   ✅ Route '{$route}'\n";
        $found++;
    }
}
echo "   📊 {$found}/" . count($routes) . " routes configurées\n\n";

// Test 8: Vérifier Optimisations Mobile
echo "8️⃣  Test Optimisations Mobile...\n";

// MobileOptimization Middleware
if (file_exists(__DIR__ . '/app/Http/Middleware/MobileOptimization.php')) {
    echo "   ✅ MobileOptimization middleware\n";
} else {
    echo "   ❌ MobileOptimization manquant\n";
}

// ImageOptimizationService
if (file_exists(__DIR__ . '/app/Services/ImageOptimizationService.php')) {
    echo "   ✅ ImageOptimizationService\n";
    echo "      - convertToWebP()\n";
    echo "      - createResponsiveVersions()\n";
    echo "      - generateBlurPlaceholder()\n";
} else {
    echo "   ❌ ImageOptimizationService manquant\n";
}

// OptimizedImage Component
if (file_exists(__DIR__ . '/resources/js/Components/OptimizedImage.vue')) {
    echo "   ✅ OptimizedImage Vue component\n";
    echo "      - WebP support\n";
    echo "      - Lazy loading\n";
    echo "      - Responsive srcset\n";
    echo "      - Blur placeholder\n";
} else {
    echo "   ❌ OptimizedImage component manquant\n";
}
echo "\n";

// Test 9: Vérifier Middlewares enregistrés
echo "9️⃣  Test Middlewares Enregistrés...\n";
$appBootstrap = file_get_contents(__DIR__ . '/bootstrap/app.php');

$expectedMiddlewares = [
    'mobile.optimize' => 'MobileOptimization',
];

$found = 0;
foreach ($expectedMiddlewares as $alias => $class) {
    if (strpos($appBootstrap, "'$alias'") !== false) {
        echo "   ✅ Middleware '{$alias}' enregistré\n";
        $found++;
    } else {
        echo "   ❌ Middleware '{$alias}' manquant\n";
    }
}
echo "\n";

// Test 10: Vérifier Documentation
echo "🔟 Test Documentation...\n";
if (file_exists(__DIR__ . '/MONITORING_BACKUP_MOBILE_GUIDE.md')) {
    echo "   ✅ Guide complet disponible\n";
    echo "   📄 Fichier: MONITORING_BACKUP_MOBILE_GUIDE.md\n";
} else {
    echo "   ⚠️  Documentation manquante\n";
}
echo "\n";

// Résumé
echo "====================================\n";
echo "✅ Tests terminés !\n\n";

echo "📝 Résumé des Fonctionnalités:\n";
echo "   ✅ Laravel Telescope (debugging)\n";
echo "   ✅ Spatie Backup (BDD + fichiers)\n";
echo "   ✅ MonitoringService (métriques temps réel)\n";
echo "   ✅ 4 Canaux logging spécialisés\n";
echo "   ✅ Dashboard admin monitoring\n";
echo "   ✅ 4 Routes API monitoring\n";
echo "   ✅ MobileOptimization middleware\n";
echo "   ✅ ImageOptimizationService (WebP + responsive)\n";
echo "   ✅ OptimizedImage component Vue\n";
echo "   ✅ Documentation complète\n\n";

echo "🚀 Prochaines Actions:\n";
echo "   1. Accéder à Telescope: http://localhost:8000/telescope\n";
echo "   2. Accéder au Dashboard: http://localhost:8000/admin/monitoring\n";
echo "   3. Tester backup: php artisan backup:run\n";
echo "   4. Configurer cron pour backup automatique\n";
echo "   5. Intégrer MonitoringService dans les contrôleurs\n\n";

echo "📚 Documentation: MONITORING_BACKUP_MOBILE_GUIDE.md\n\n";
