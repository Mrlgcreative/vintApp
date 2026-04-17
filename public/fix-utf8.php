<?php
/**
 * Script de diagnostic et réparation UTF-8 pour VintApp
 * BLOQUÉ EN PRODUCTION - Exécuter uniquement en local via: php public/fix-utf8.php
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo 'Accès interdit. Exécutez ce script en ligne de commande.';
    exit(1);
}

echo "=== VintApp UTF-8 Diagnostic & Fix Script ===\n\n";

// 1. Vérifier le fichier .env
echo "1. Vérification du fichier .env...\n";
$envPath = dirname(__DIR__) . '/.env';

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Vérifier CRLF
    if (strpos($envContent, "\r\n") !== false) {
        echo "   ⚠️  PROBLÈME: Le fichier .env contient des fins de ligne Windows (CRLF)\n";
        echo "   🔧 Correction en cours...\n";
        
        // Convertir CRLF en LF
        $fixedContent = str_replace("\r\n", "\n", $envContent);
        $fixedContent = str_replace("\r", "\n", $fixedContent); // Au cas où il y a des CR seuls
        
        // Sauvegarder une copie de backup
        file_put_contents($envPath . '.backup', $envContent);
        
        // Écrire le fichier corrigé
        if (file_put_contents($envPath, $fixedContent)) {
            echo "   ✅ Fichier .env corrigé ! (backup créé: .env.backup)\n";
        } else {
            echo "   ❌ Impossible d'écrire le fichier .env\n";
        }
    } else {
        echo "   ✅ Fichier .env OK (fins de ligne Unix)\n";
    }
    
    // Vérifier l'encodage UTF-8
    if (!mb_check_encoding($envContent, 'UTF-8')) {
        echo "   ⚠️  PROBLÈME: Le fichier .env contient des caractères non-UTF-8\n";
    }
} else {
    echo "   ❌ Fichier .env non trouvé!\n";
}

echo "\n";

// 2. Vider les caches Laravel
echo "2. Vidage des caches Laravel...\n";

$cachePaths = [
    dirname(__DIR__) . '/bootstrap/cache/config.php',
    dirname(__DIR__) . '/bootstrap/cache/routes-v7.php',
    dirname(__DIR__) . '/bootstrap/cache/services.php',
    dirname(__DIR__) . '/bootstrap/cache/packages.php',
];

foreach ($cachePaths as $cachePath) {
    if (file_exists($cachePath)) {
        if (unlink($cachePath)) {
            echo "   ✅ Supprimé: " . basename($cachePath) . "\n";
        } else {
            echo "   ❌ Impossible de supprimer: " . basename($cachePath) . "\n";
        }
    }
}

// Vider le cache des vues compilées
$viewsCachePath = dirname(__DIR__) . '/storage/framework/views';
if (is_dir($viewsCachePath)) {
    $files = glob($viewsCachePath . '/*.php');
    $count = 0;
    foreach ($files as $file) {
        if (unlink($file)) {
            $count++;
        }
    }
    echo "   ✅ Supprimé: $count fichiers de cache de vues\n";
}

// Vider le cache d'application
$appCachePath = dirname(__DIR__) . '/storage/framework/cache/data';
if (is_dir($appCachePath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appCachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $count = 0;
    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getFilename() !== '.gitignore') {
            if (unlink($item->getPathname())) {
                $count++;
            }
        }
    }
    echo "   ✅ Supprimé: $count fichiers de cache d'application\n";
}

echo "\n";

// 3. Tester la connexion à la base de données et l'encodage
echo "3. Test de la base de données...\n";

try {
    // Charger les variables d'environnement depuis .env
    $envContent = file_get_contents($envPath);
    $envVars = [];
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value, '"\'');
        }
    }
    
    $host = $envVars['DB_HOST'] ?? '127.0.0.1';
    $database = $envVars['DB_DATABASE'] ?? '';
    $username = $envVars['DB_USERNAME'] ?? '';
    $password = $envVars['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "   ✅ Connexion à la base de données OK\n";
    
    // Vérifier l'encodage de la connexion
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'character_set%'");
    $charsets = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo "   📊 Encodages:\n";
    foreach ($charsets as $var => $value) {
        $status = (strpos($value, 'utf8') !== false) ? '✅' : '⚠️';
        echo "      $status $var: $value\n";
    }
    
    // Tester les catégories
    echo "\n   📊 Test des données (catégories):\n";
    $stmt = $pdo->query("SELECT id, name FROM categories LIMIT 5");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasIssues = false;
    foreach ($categories as $cat) {
        $name = $cat['name'];
        $isValidUtf8 = mb_check_encoding($name, 'UTF-8');
        $status = $isValidUtf8 ? '✅' : '❌';
        if (!$isValidUtf8) $hasIssues = true;
        
        // Essayer d'encoder en JSON
        $jsonTest = json_encode(['name' => $name]);
        $jsonStatus = ($jsonTest !== false) ? '✅' : '❌';
        if ($jsonTest === false) $hasIssues = true;
        
        echo "      ID {$cat['id']}: $status UTF8 | $jsonStatus JSON | \"$name\"\n";
    }
    
    if ($hasIssues) {
        echo "\n   ⚠️  Des problèmes d'encodage ont été détectés dans les données!\n";
    } else {
        echo "\n   ✅ Toutes les catégories testées sont en UTF-8 valide\n";
    }
    
} catch (PDOException $e) {
    echo "   ❌ Erreur de connexion: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Test API simple
echo "4. Test d'encodage JSON...\n";

$testData = [
    'status' => 'success',
    'message' => 'Test UTF-8: éàù中文🎉',
    'categories' => isset($categories) ? $categories : []
];

$jsonResult = json_encode($testData, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

if ($jsonResult === false) {
    echo "   ❌ Erreur JSON: " . json_last_error_msg() . "\n";
} else {
    echo "   ✅ Encodage JSON OK\n";
    echo "   📊 Résultat: " . substr($jsonResult, 0, 200) . "...\n";
}

echo "\n";

// 5. Vérifier les fichiers PHP pour BOM
echo "5. Vérification des fichiers pour BOM UTF-8...\n";

$filesToCheck = [
    dirname(__DIR__) . '/app/Traits/ApiResponses.php',
    dirname(__DIR__) . '/app/Http/Controllers/CategoryController.php',
    dirname(__DIR__) . '/app/Http/Controllers/BrandController.php',
    dirname(__DIR__) . '/routes/api.php',
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $hasBom = (substr($content, 0, 3) === "\xEF\xBB\xBF");
        
        if ($hasBom) {
            echo "   ⚠️  BOM détecté: " . basename($file) . "\n";
            // Supprimer le BOM
            $content = substr($content, 3);
            file_put_contents($file, $content);
            echo "   🔧 BOM supprimé de: " . basename($file) . "\n";
        } else {
            echo "   ✅ OK: " . basename($file) . "\n";
        }
    } else {
        echo "   ⚠️  Non trouvé: " . basename($file) . "\n";
    }
}

echo "\n";

// 6. Créer un endpoint de test API
echo "6. Création d'un endpoint de test API...\n";

$testApiContent = '<?php
// Test API endpoint - À SUPPRIMER APRÈS USAGE
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

try {
    // Charger .env
    $envPath = dirname(__DIR__) . "/.env";
    $envContent = file_get_contents($envPath);
    $envVars = [];
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, "#") === 0) continue;
        if (strpos($line, "=") !== false) {
            list($key, $value) = explode("=", $line, 2);
            $envVars[trim($key)] = trim($value, "\"\'");
        }
    }
    
    $pdo = new PDO(
        "mysql:host=" . ($envVars["DB_HOST"] ?? "127.0.0.1") . ";dbname=" . ($envVars["DB_DATABASE"] ?? "") . ";charset=utf8mb4",
        $envVars["DB_USERNAME"] ?? "",
        $envVars["DB_PASSWORD"] ?? "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $stmt = $pdo->query("SELECT id, name, slug, icon, is_active FROM categories WHERE is_active = 1 ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Nettoyer UTF-8
    array_walk_recursive($categories, function(&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, "UTF-8", "UTF-8");
        }
    });
    
    echo json_encode([
        "status" => "success",
        "data" => $categories,
        "count" => count($categories)
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
';

$testApiPath = dirname(__DIR__) . '/public/test-api-direct.php';
if (file_put_contents($testApiPath, $testApiContent)) {
    echo "   ✅ Endpoint de test créé: /test-api-direct.php\n";
    echo "   📝 Testez avec: curl https://vitapp.mykenyastudentprocess.com/test-api-direct.php\n";
} else {
    echo "   ❌ Impossible de créer l'endpoint de test\n";
}

echo "\n";
echo "=== Diagnostic terminé ===\n";
echo "\n⚠️  IMPORTANT: Supprimez ce fichier et test-api-direct.php après usage!\n";
echo "   rm public/fix-utf8.php public/test-api-direct.php\n";
