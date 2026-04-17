<?php
/**
 * Script de réparation d'urgence - Vider tous les caches Laravel
 * Accéder via: https://vitapp.mykenyastudentprocess.com/fix-cache.php
 * SUPPRIMER IMMEDIATEMENT APRES UTILISATION !
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre style='font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;font-size:14px;'>";
echo "=== REPARATION CACHE VINTAPP ===\n\n";

$base = dirname(__DIR__); // Remonter de public/ vers la racine

// 1. Supprimer config cache
$files = [
    $base . '/bootstrap/cache/config.php',
    $base . '/bootstrap/cache/routes-v7.php',
    $base . '/bootstrap/cache/services.php',
    $base . '/bootstrap/cache/packages.php',
    $base . '/bootstrap/cache/events.php',
];

echo "1. Suppression des fichiers cache:\n";
foreach ($files as $file) {
    $name = str_replace($base . '/', '', $file);
    if (file_exists($file)) {
        if (@unlink($file)) {
            echo "   ✅ Supprimé: $name\n";
        } else {
            echo "   ❌ Impossible de supprimer: $name\n";
        }
    } else {
        echo "   ⏭️  N'existe pas: $name\n";
    }
}

// 2. Vider le cache fichier dans storage
echo "\n2. Nettoyage storage/framework/cache:\n";
$cacheDir = $base . '/storage/framework/cache/data';
if (is_dir($cacheDir)) {
    $count = 0;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $item) {
        if ($item->isFile() && $item->getFilename() !== '.gitignore') {
            @unlink($item->getPathname());
            $count++;
        }
    }
    echo "   ✅ $count fichiers cache supprimés\n";
} else {
    echo "   ⏭️  Pas de cache fichier\n";
}

// 3. Vider les vues compilées
echo "\n3. Nettoyage vues compilées:\n";
$viewsDir = $base . '/storage/framework/views';
if (is_dir($viewsDir)) {
    $count = 0;
    foreach (glob($viewsDir . '/*.php') as $view) {
        @unlink($view);
        $count++;
    }
    echo "   ✅ $count vues compilées supprimées\n";
}

// 4. Vérifier le .env
echo "\n4. Vérification .env:\n";
$envFile = $base . '/.env';
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    
    $checks = [
        'SESSION_DRIVER' => '/^SESSION_DRIVER=(.+)$/m',
        'QUEUE_CONNECTION' => '/^QUEUE_CONNECTION=(.+)$/m',
        'CACHE_STORE' => '/^CACHE_STORE=(.+)$/m',
        'APP_ENV' => '/^APP_ENV=(.+)$/m',
        'APP_DEBUG' => '/^APP_DEBUG=(.+)$/m',
        'DB_DATABASE' => '/^DB_DATABASE=(.+)$/m',
        'DB_HOST' => '/^DB_HOST=(.+)$/m',
    ];
    
    foreach ($checks as $key => $regex) {
        preg_match($regex, $env, $m);
        $val = trim($m[1] ?? 'NON DEFINI');
        $icon = '  ';
        if (in_array($key, ['SESSION_DRIVER', 'QUEUE_CONNECTION', 'CACHE_STORE']) && $val === 'redis') {
            $icon = '❌';
        } else {
            $icon = '✅';
        }
        echo "   $icon $key=$val\n";
    }
    
    // Vérifier les espaces en début de ligne
    if (preg_match('/^ +\w+=/m', $env)) {
        echo "   ❌ ESPACES detectés en début de ligne dans le .env!\n";
    }
    
    // Vérifier doublons
    preg_match_all('/^(\w+)=/m', $env, $allKeys);
    $dupes = array_diff_assoc($allKeys[1], array_unique($allKeys[1]));
    if (!empty($dupes)) {
        echo "   ❌ DOUBLONS: " . implode(', ', array_unique($dupes)) . "\n";
    }
} else {
    echo "   ❌ FICHIER .env INTROUVABLE!\n";
}

// 5. Test connexion DB
echo "\n5. Test connexion MySQL:\n";
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    preg_match('/^DB_HOST=(.+)$/m', $env, $h);
    preg_match('/^DB_PORT=(.+)$/m', $env, $p);
    preg_match('/^DB_DATABASE=(.+)$/m', $env, $d);
    preg_match('/^DB_USERNAME=(.+)$/m', $env, $u);
    preg_match('/^DB_PASSWORD=(.+)$/m', $env, $pw);
    
    try {
        $dsn = 'mysql:host=' . trim($h[1] ?? 'localhost') . ';port=' . trim($p[1] ?? '3306') . ';dbname=' . trim($d[1] ?? '');
        $pdo = new PDO($dsn, trim($u[1] ?? ''), trim($pw[1] ?? ''));
        echo "   ✅ Connexion OK\n";
        
        // Vérifier tables essentielles
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $needed = ['sessions', 'cache', 'jobs', 'users', 'migrations'];
        foreach ($needed as $t) {
            echo "   " . (in_array($t, $tables) ? '✅' : '❌ MANQUANTE') . " table '$t'\n";
        }
    } catch (Exception $e) {
        echo "   ❌ " . $e->getMessage() . "\n";
    }
}

// 6. Dernier log
echo "\n6. Dernières erreurs Laravel:\n";
$logFile = $base . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last = array_slice($lines, -20);
    foreach ($last as $line) {
        $l = trim($line);
        if ($l) echo "   " . substr($l, 0, 150) . "\n";
    }
} else {
    echo "   Pas de log\n";
}

echo "\n=== REPARATION TERMINEE ===\n";
echo "Rechargez le site: https://vitapp.mykenyastudentprocess.com/\n";
echo "\n⚠️  SUPPRIMEZ CE FICHIER APRES UTILISATION!\n";
echo "</pre>";
