<?php
/**
 * Script de diagnostic et correction de la corruption JSON
 * BLOQUÉ EN PRODUCTION - Exécuter uniquement en local via: php public/fix-json-corruption.php
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo 'Accès interdit. Exécutez ce script en ligne de commande.';
    exit(1);
}

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => [],
    'fixes' => [],
    'errors' => []
];

// 1. Vérifier les fichiers PHP pour BOM ou caractères parasites
function checkFileForBOM($filepath) {
    if (!file_exists($filepath)) return null;
    $content = file_get_contents($filepath, false, null, 0, 10);
    $bom = pack('CCC', 0xEF, 0xBB, 0xBF);
    return strpos($content, $bom) === 0;
}

function checkFileForWhitespace($filepath) {
    if (!file_exists($filepath)) return null;
    $content = file_get_contents($filepath);
    
    // Vérifier espaces/newlines avant <?php
    if (preg_match('/^[\s\n\r]+<\?php/', $content)) {
        return 'whitespace_before_php';
    }
    
    // Vérifier après 
    if (preg_match('/\?>\s*\S/', $content)) {
        return 'content_after_closing_tag';
    }
    
    // Vérifier  suivi d'espaces/newlines
    if (preg_match('/\?>[\s\n\r]+$/', $content)) {
        return 'whitespace_after_closing_tag';
    }
    
    return false;
}

// Trouver le chemin racine Laravel
$basePath = dirname(__DIR__);
if (!file_exists($basePath . '/artisan')) {
    $basePath = __DIR__;
    if (!file_exists($basePath . '/artisan')) {
        $basePath = dirname(__DIR__, 1);
    }
}

$results['checks']['base_path'] = $basePath;
$results['checks']['artisan_exists'] = file_exists($basePath . '/artisan');

// 2. Fichiers critiques à vérifier
$criticalFiles = [
    $basePath . '/bootstrap/app.php',
    $basePath . '/config/app.php',
    $basePath . '/app/Traits/ApiResponses.php',
    $basePath . '/app/Http/Controllers/CategoryController.php',
    $basePath . '/app/Http/Middleware/CompressResponse.php',
    $basePath . '/app/Http/Middleware/CacheResponse.php',
    __DIR__ . '/index.php',
];

$results['checks']['files'] = [];
foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        $hasBOM = checkFileForBOM($file);
        $whitespace = checkFileForWhitespace($file);
        $results['checks']['files'][basename($file)] = [
            'exists' => true,
            'has_bom' => $hasBOM,
            'whitespace_issue' => $whitespace
        ];
        
        // Corriger automatiquement les problèmes
        if ($hasBOM || $whitespace) {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Supprimer BOM
            $bom = pack('CCC', 0xEF, 0xBB, 0xBF);
            $content = str_replace($bom, '', $content);
            
            // Nettoyer les espaces avant <?php
            $content = preg_replace('/^[\s\n\r]+(<\?php)/', '$1', $content);
            
            // Supprimer en fin de fichier (recommandé PSR)
            $content = preg_replace('/\?>\s*$/', '', $content);
            
            if ($content !== $originalContent) {
                if (is_writable($file)) {
                    file_put_contents($file, $content);
                    $results['fixes'][] = "Fixed: " . basename($file);
                } else {
                    $results['errors'][] = "Cannot write to: " . basename($file);
                }
            }
        }
    } else {
        $results['checks']['files'][basename($file)] = ['exists' => false];
    }
}

// 3. Vider TOUS les caches Laravel
$cacheCleared = [];

// Cache fichiers
$cacheDirs = [
    $basePath . '/bootstrap/cache',
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/framework/views',
    $basePath . '/storage/framework/sessions',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file) && !in_array(basename($file), ['.gitignore', '.gitkeep'])) {
                @unlink($file);
                $count++;
            }
        }
        $cacheCleared[basename($dir)] = $count . ' files cleared';
    }
}

$results['fixes']['cache_cleared'] = $cacheCleared;

// 4. Supprimer le cache de routes
$routeCacheFile = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCacheFile)) {
    @unlink($routeCacheFile);
    $results['fixes'][] = 'Route cache cleared';
}

// 5. Supprimer le cache de config
$configCacheFile = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCacheFile)) {
    @unlink($configCacheFile);
    $results['fixes'][] = 'Config cache cleared';
}

// 6. Test de génération JSON propre
$testData = [
    'test' => true,
    'message' => 'Test avec accents: éàùç',
    'categories' => [
        ['id' => 1, 'name' => 'Électronique'],
        ['id' => 2, 'name' => 'Vêtements'],
        ['id' => 3, 'name' => 'Chaussures'],
    ]
];

$jsonTest = json_encode($testData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$jsonDecoded = json_decode($jsonTest, true);
$results['checks']['json_test'] = [
    'encode_success' => $jsonTest !== false,
    'decode_success' => $jsonDecoded !== null,
    'json_error' => json_last_error_msg(),
    'sample_output' => $jsonTest
];

// 7. Test de connexion DB et récupération catégories
try {
    // Charger .env manuellement
    $envFile = $basePath . '/.env';
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        $lines = explode("\n", $envContent);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $value = trim($value, '"\'');
                putenv("$key=$value");
            }
        }
    }
    
    $host = getenv('DB_HOST') ?: 'localhost';
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
    
    // Récupérer les catégories directement
    $stmt = $pdo->query("SELECT id, name, description FROM categories ORDER BY name LIMIT 5");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results['checks']['database'] = [
        'connection' => 'success',
        'categories_count' => count($categories),
        'raw_data' => $categories
    ];
    
    // Tester l'encodage JSON des données DB
    $dbJson = json_encode(['categories' => $categories], JSON_UNESCAPED_UNICODE);
    $results['checks']['database']['json_encode_success'] = $dbJson !== false;
    $results['checks']['database']['json_error'] = json_last_error_msg();
    
    // Vérifier chaque champ pour des caractères problématiques
    foreach ($categories as $cat) {
        foreach ($cat as $field => $value) {
            if (is_string($value)) {
                // Vérifier si la chaîne contient des caractères de contrôle
                if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
                    $results['errors'][] = "Control character found in category {$cat['id']}, field: $field";
                }
                // Vérifier les espaces bizarres (non-breaking space, etc.)
                if (preg_match('/[\xC2\xA0]/', $value)) {
                    $results['errors'][] = "Non-breaking space found in category {$cat['id']}, field: $field";
                }
            }
        }
    }
    
} catch (Exception $e) {
    $results['checks']['database'] = [
        'connection' => 'failed',
        'error' => $e->getMessage()
    ];
}

// 8. Vérifier le middleware de compression
$compressMiddlewarePath = $basePath . '/app/Http/Middleware/CompressResponse.php';
if (file_exists($compressMiddlewarePath)) {
    $content = file_get_contents($compressMiddlewarePath);
    $results['checks']['compress_middleware'] = [
        'exists' => true,
        'has_gzencode' => strpos($content, 'gzencode') !== false,
        'has_gzdeflate' => strpos($content, 'gzdeflate') !== false,
    ];
}

// 9. Vérifier ApiResponses trait
$apiResponsesPath = $basePath . '/app/Traits/ApiResponses.php';
if (file_exists($apiResponsesPath)) {
    $content = file_get_contents($apiResponsesPath);
    $results['checks']['api_responses_trait'] = [
        'exists' => true,
        'has_cleanUtf8' => strpos($content, 'cleanUtf8') !== false,
        'has_mb_convert_encoding' => strpos($content, 'mb_convert_encoding') !== false,
        'uses_JSON_UNESCAPED_UNICODE' => strpos($content, 'JSON_UNESCAPED_UNICODE') !== false,
    ];
}

// 10. Recommandations
$results['recommendations'] = [];

if (!empty($results['errors'])) {
    $results['recommendations'][] = "Des caractères problématiques ont été détectés dans la base de données. Un nettoyage SQL est recommandé.";
}

$results['recommendations'][] = "Après exécution de ce script, testez l'API: /api/v1/categories";
$results['recommendations'][] = "SUPPRIMEZ CE FICHIER après utilisation!";

// Output final
echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
