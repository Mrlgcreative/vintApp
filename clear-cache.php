<?php
/**
 * Nettoyage cache Laravel - SUPPRIMER APRÈS UTILISATION
 */
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=UTF-8');

$basePath = __DIR__;
$results = ['timestamp' => date('Y-m-d H:i:s'), 'cleared' => [], 'errors' => []];

// Vider les caches
$cacheDirs = [
    $basePath . '/bootstrap/cache',
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/framework/views',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $count = 0;
        foreach (glob($dir . '/*') as $file) {
            if (is_file($file) && !in_array(basename($file), ['.gitignore', '.gitkeep'])) {
                @unlink($file);
                $count++;
            }
        }
        $results['cleared'][basename($dir)] = $count;
    }
}

// Supprimer cache HTTP spécifique
$cacheFile = $basePath . '/storage/framework/cache/data';
if (is_dir($cacheFile)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheFile, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $httpCacheCount = 0;
    foreach ($iterator as $item) {
        if ($item->isFile()) {
            @unlink($item->getPathname());
            $httpCacheCount++;
        }
    }
    $results['cleared']['http_cache_files'] = $httpCacheCount;
}

// Test JSON simple
$testData = ['test' => true, 'accents' => 'éàùç Électronique Vêtements'];
$results['json_test'] = json_encode($testData, JSON_UNESCAPED_UNICODE) !== false ? 'OK' : 'FAIL';

echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
