<?php
/**
 * Vider opcache + tester Laravel directement
 * SUPPRIMER APRÈS UTILISATION
 */

// Désactiver output buffering
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=UTF-8');

$results = ['timestamp' => date('Y-m-d H:i:s')];

// 1. Vider opcache
if (function_exists('opcache_reset')) {
    $results['opcache_reset'] = opcache_reset() ? 'SUCCESS' : 'FAILED';
} else {
    $results['opcache_reset'] = 'NOT_AVAILABLE';
}

// 2. Vider le cache de fichiers
$basePath = __DIR__;
$cacheFiles = [
    $basePath . '/bootstrap/cache/config.php',
    $basePath . '/bootstrap/cache/routes-v7.php',
    $basePath . '/bootstrap/cache/services.php',
    $basePath . '/bootstrap/cache/packages.php',
];

$results['cache_files_deleted'] = [];
foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        $deleted = @unlink($file);
        $results['cache_files_deleted'][basename($file)] = $deleted ? 'DELETED' : 'FAILED';
    }
}

// 3. Vider storage/framework/cache
$cacheDir = $basePath . '/storage/framework/cache/data';
if (is_dir($cacheDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $count = 0;
    foreach ($iterator as $item) {
        if ($item->isFile()) {
            @unlink($item->getPathname());
            $count++;
        }
    }
    $results['framework_cache_cleared'] = $count;
}

// 4. Vider storage/framework/views
$viewsDir = $basePath . '/storage/framework/views';
if (is_dir($viewsDir)) {
    $count = 0;
    foreach (glob($viewsDir . '/*.php') as $file) {
        @unlink($file);
        $count++;
    }
    $results['views_cleared'] = $count;
}

// 5. Test direct avec Laravel bootstrap minimal
$results['laravel_test'] = 'STARTING';

try {
    // Charger l'autoloader
    require $basePath . '/vendor/autoload.php';
    
    // Charger .env
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();
    
    // Connexion DB directe via PDO (pas Eloquent)
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $stmt = $pdo->query("SELECT id, name, slug, image, is_active FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results['laravel_test'] = 'SUCCESS';
    $results['categories_count'] = count($categories);
    $results['data'] = $categories;
    
} catch (Exception $e) {
    $results['laravel_test'] = 'FAILED';
    $results['error'] = $e->getMessage();
}

// Output
echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
