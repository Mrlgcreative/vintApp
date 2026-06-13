<?php
/**
 * Script de diagnostic pour Hostinger
 * Accéder via: https://vitapp.mykenyastudentprocess.com/diagnose.php
 * SUPPRIMER après diagnostic !
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre style='font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;'>";
echo "=== DIAGNOSTIC VINTAPP ===\n\n";

// 1. PHP Version
echo "1. PHP: " . phpversion() . "\n";

// 2. Extensions
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
echo "\n2. Extensions PHP:\n";
foreach ($required as $ext) {
    $ok = extension_loaded($ext) ? '✅' : '❌';
    echo "   $ok $ext\n";
}
echo "   " . (extension_loaded('redis') ? '✅' : '❌ (normal sur mutualisé)') . " redis\n";

// 3. .env check
echo "\n3. Fichier .env:\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "   ✅ .env existe\n";
    $env = file_get_contents($envPath);
    
    // Check Redis references
    $redisIssues = [];
    if (preg_match('/^SESSION_DRIVER=redis/m', $env)) $redisIssues[] = 'SESSION_DRIVER=redis';
    if (preg_match('/^CACHE_STORE=redis/m', $env)) $redisIssues[] = 'CACHE_STORE=redis';
    if (preg_match('/^QUEUE_CONNECTION=redis/m', $env)) $redisIssues[] = 'QUEUE_CONNECTION=redis';
    
    if ($redisIssues) {
        echo "   ❌ PROBLEME REDIS DETECTE:\n";
        foreach ($redisIssues as $issue) {
            echo "      → $issue (changer vers 'database')\n";
        }
    } else {
        echo "   ✅ Pas de dépendance Redis\n";
    }
    
    // Check APP_KEY
    if (preg_match('/^APP_KEY=base64:.+/m', $env)) {
        echo "   ✅ APP_KEY définie\n";
    } else {
        echo "   ❌ APP_KEY manquante ou invalide\n";
    }
    
    // Check APP_DEBUG
    if (preg_match('/^APP_DEBUG=true/m', $env)) {
        echo "   ⚠️  APP_DEBUG=true (activer temporairement pour voir l'erreur)\n";
    } else {
        echo "   ℹ️  APP_DEBUG=false (mettre true temporairement pour debug)\n";
    }
    
    // Check DB
    preg_match('/^DB_HOST=(.+)/m', $env, $m);
    echo "   DB_HOST=" . trim($m[1] ?? '???') . "\n";
    preg_match('/^DB_DATABASE=(.+)/m', $env, $m);
    echo "   DB_DATABASE=" . trim($m[1] ?? '???') . "\n";
} else {
    echo "   ❌ .env MANQUANT!\n";
}

// 4. Storage permissions
echo "\n4. Permissions:\n";
$dirs = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache',
];
foreach ($dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $writable = is_writable($path) ? '✅ writable' : '❌ NOT writable';
        echo "   $writable  $dir\n";
    } else {
        echo "   ❌ MISSING  $dir\n";
    }
}

// 5. Composer autoload
echo "\n5. Autoload:\n";
$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    echo "   ✅ vendor/autoload.php existe\n";
} else {
    echo "   ❌ vendor/autoload.php MANQUANT (composer install requis)\n";
}

// 6. Config cache
echo "\n6. Config cache:\n";
$configCache = __DIR__ . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    $cached = file_get_contents($configCache);
    if (strpos($cached, "'driver' => 'redis'") !== false || strpos($cached, "'default' => 'redis'") !== false) {
        echo "   ❌ CONFIG CACHE contient 'redis'! Supprimer avec: php artisan config:clear\n";
    } else {
        echo "   ✅ Config cache OK (pas de redis)\n";
    }
} else {
    echo "   ℹ️  Pas de config cache (normal)\n";
}

// 7. Try DB connection
echo "\n7. Connexion DB:\n";
if (file_exists($envPath)) {
    $env = file_get_contents($envPath);
    preg_match('/^DB_HOST=(.+)/m', $env, $host);
    preg_match('/^DB_PORT=(.+)/m', $env, $port);
    preg_match('/^DB_DATABASE=(.+)/m', $env, $db);
    preg_match('/^DB_USERNAME=(.+)/m', $env, $user);
    preg_match('/^DB_PASSWORD=(.+)/m', $env, $pass);
    
    try {
        $dsn = 'mysql:host=' . trim($host[1] ?? 'localhost') . ';port=' . trim($port[1] ?? '3306') . ';dbname=' . trim($db[1] ?? '');
        $pdo = new PDO($dsn, trim($user[1] ?? ''), trim($pass[1] ?? ''));
        echo "   ✅ Connexion MySQL OK\n";
        
        // Check tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "   Tables: " . count($tables) . " trouvées\n";
        
        $needed = ['sessions', 'cache', 'jobs', 'users'];
        foreach ($needed as $t) {
            $exists = in_array($t, $tables) ? '✅' : '❌ MANQUANTE';
            echo "   $exists table '$t'\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Erreur DB: " . $e->getMessage() . "\n";
    }
}

// 8. Laravel log
echo "\n8. Dernier log Laravel:\n";
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last = array_slice($lines, -15);
    foreach ($last as $line) {
        echo "   " . substr(trim($line), 0, 120) . "\n";
    }
} else {
    echo "   Pas de fichier log\n";
}

echo "\n=== FIN DIAGNOSTIC ===\n";
echo "</pre>";
