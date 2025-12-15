<?php
/**
 * Script de diagnostic et réparation UTF-8 pour VintApp
 * Uploadez ce fichier DIRECTEMENT dans public_html/ (racine du site)
 * Accédez via: https://vitapp.mykenyastudentprocess.com/fix-utf8.php
 * SUPPRIMEZ CE FICHIER APRÈS UTILISATION !
 */

// Désactiver l'affichage HTML pour avoir du texte brut
header('Content-Type: text/plain; charset=UTF-8');

echo "=== VintApp UTF-8 Diagnostic & Fix Script ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Répertoire courant: " . __DIR__ . "\n\n";

// Sur cet hébergement, tout est dans public_html/ directement
$basePath = __DIR__;

// 1. Vérifier le fichier .env
echo "1. Vérification du fichier .env...\n";
$envPath = $basePath . '/.env';

echo "   Chemin recherché: $envPath\n";

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    echo "   ✅ Fichier .env trouvé (" . strlen($envContent) . " octets)\n";
    
    // Vérifier CRLF
    $hasCRLF = strpos($envContent, "\r\n") !== false;
    $hasCR = strpos($envContent, "\r") !== false;
    
    if ($hasCRLF || $hasCR) {
        echo "   ⚠️  PROBLÈME: Le fichier .env contient des fins de ligne Windows (CRLF)\n";
        echo "   🔧 Correction en cours...\n";
        
        // Convertir CRLF en LF
        $fixedContent = str_replace("\r\n", "\n", $envContent);
        $fixedContent = str_replace("\r", "\n", $fixedContent);
        
        // Sauvegarder une copie de backup
        if (file_put_contents($envPath . '.backup.' . time(), $envContent)) {
            echo "   ✅ Backup créé\n";
        }
        
        // Écrire le fichier corrigé
        if (file_put_contents($envPath, $fixedContent)) {
            echo "   ✅ Fichier .env corrigé !\n";
            $envContent = $fixedContent; // Utiliser le contenu corrigé
        } else {
            echo "   ❌ Impossible d'écrire le fichier .env (permissions?)\n";
        }
    } else {
        echo "   ✅ Fichier .env OK (fins de ligne Unix)\n";
    }
    
    // Vérifier l'encodage UTF-8
    if (!mb_check_encoding($envContent, 'UTF-8')) {
        echo "   ⚠️  PROBLÈME: Le fichier .env contient des caractères non-UTF-8\n";
    } else {
        echo "   ✅ Encodage UTF-8 OK\n";
    }
    
    // Afficher quelques variables (sans les mots de passe)
    echo "   📊 Variables détectées:\n";
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            // Ne pas afficher les mots de passe
            if (strpos(strtoupper($key), 'PASSWORD') !== false || 
                strpos(strtoupper($key), 'SECRET') !== false ||
                strpos(strtoupper($key), 'KEY') !== false) {
                echo "      $key = [MASQUÉ]\n";
            } elseif (in_array($key, ['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'])) {
                echo "      $key = " . trim($value, '"\'') . "\n";
            }
        }
    }
} else {
    echo "   ❌ Fichier .env non trouvé!\n";
    echo "   📁 Contenu du répertoire:\n";
    $files = scandir($basePath);
    foreach ($files as $file) {
        if ($file[0] !== '.' || $file === '.env' || $file === '.htaccess') {
            $type = is_dir($basePath . '/' . $file) ? '[DIR]' : '[FILE]';
            echo "      $type $file\n";
        }
    }
}

echo "\n";

// 2. Vider les caches Laravel
echo "2. Vidage des caches Laravel...\n";

$cachePaths = [
    $basePath . '/bootstrap/cache/config.php',
    $basePath . '/bootstrap/cache/routes-v7.php',
    $basePath . '/bootstrap/cache/services.php',
    $basePath . '/bootstrap/cache/packages.php',
    $basePath . '/bootstrap/cache/events.php',
];

$deletedCount = 0;
foreach ($cachePaths as $cachePath) {
    if (file_exists($cachePath)) {
        if (@unlink($cachePath)) {
            echo "   ✅ Supprimé: " . basename($cachePath) . "\n";
            $deletedCount++;
        } else {
            echo "   ❌ Impossible de supprimer: " . basename($cachePath) . "\n";
        }
    }
}

if ($deletedCount === 0) {
    echo "   ℹ️  Aucun cache bootstrap à supprimer\n";
}

// Vider le cache des vues compilées
$viewsCachePath = $basePath . '/storage/framework/views';
if (is_dir($viewsCachePath)) {
    $files = glob($viewsCachePath . '/*.php');
    $count = 0;
    foreach ($files as $file) {
        if (@unlink($file)) {
            $count++;
        }
    }
    echo "   ✅ Supprimé: $count fichiers de cache de vues\n";
} else {
    echo "   ⚠️  Dossier views cache non trouvé: $viewsCachePath\n";
}

// Vider le cache d'application
$appCachePath = $basePath . '/storage/framework/cache/data';
if (is_dir($appCachePath)) {
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appCachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getFilename() !== '.gitignore') {
            if (@unlink($item->getPathname())) {
                $count++;
            }
        }
    }
    echo "   ✅ Supprimé: $count fichiers de cache d'application\n";
}

echo "\n";

// 3. Tester la connexion à la base de données
echo "3. Test de la base de données...\n";

// Parser le fichier .env pour les variables DB
$dbVars = [];
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            if (strpos($key, 'DB_') === 0) {
                $dbVars[$key] = $value;
            }
        }
    }
}

$host = $dbVars['DB_HOST'] ?? '127.0.0.1';
$port = $dbVars['DB_PORT'] ?? '3306';
$database = $dbVars['DB_DATABASE'] ?? '';
$username = $dbVars['DB_USERNAME'] ?? '';
$password = $dbVars['DB_PASSWORD'] ?? '';

echo "   📊 Configuration DB:\n";
echo "      Host: $host:$port\n";
echo "      Database: $database\n";
echo "      Username: $username\n";
echo "      Password: " . (empty($password) ? '(vide!)' : str_repeat('*', min(strlen($password), 8))) . "\n";

if (empty($database) || empty($username)) {
    echo "   ❌ Configuration DB incomplète dans .env!\n";
} else {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]);
        
        echo "   ✅ Connexion à la base de données OK\n";
        
        // Vérifier l'encodage
        $stmt = $pdo->query("SHOW VARIABLES LIKE 'character_set%'");
        $charsets = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        echo "   📊 Encodages serveur:\n";
        foreach ($charsets as $var => $value) {
            $status = (strpos($value, 'utf8') !== false) ? '✅' : '⚠️';
            echo "      $status $var: $value\n";
        }
        
        // Tester les catégories
        echo "\n   📊 Test des données (catégories):\n";
        $stmt = $pdo->query("SELECT id, name FROM categories LIMIT 5");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($categories)) {
            echo "      ℹ️  Aucune catégorie trouvée\n";
        } else {
            $hasIssues = false;
            foreach ($categories as $cat) {
                $name = $cat['name'];
                $isValidUtf8 = mb_check_encoding($name, 'UTF-8');
                $status = $isValidUtf8 ? '✅' : '❌';
                if (!$isValidUtf8) $hasIssues = true;
                
                $jsonTest = json_encode(['name' => $name]);
                $jsonStatus = ($jsonTest !== false) ? '✅' : '❌';
                if ($jsonTest === false) $hasIssues = true;
                
                echo "      ID {$cat['id']}: $status UTF8 | $jsonStatus JSON | \"$name\"\n";
            }
            
            if (!$hasIssues) {
                echo "   ✅ Toutes les catégories testées sont en UTF-8 valide\n";
            }
        }
        
    } catch (PDOException $e) {
        echo "   ❌ Erreur de connexion: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 4. Vérifier les fichiers PHP pour BOM
echo "4. Vérification des fichiers pour BOM UTF-8...\n";

$filesToCheck = [
    $basePath . '/app/Traits/ApiResponses.php',
    $basePath . '/app/Http/Controllers/CategoryController.php',
    $basePath . '/app/Http/Controllers/BrandController.php',
    $basePath . '/routes/api.php',
    $basePath . '/bootstrap/app.php',
];

foreach ($filesToCheck as $file) {
    $shortName = str_replace($basePath . '/', '', $file);
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $hasBom = (substr($content, 0, 3) === "\xEF\xBB\xBF");
        
        if ($hasBom) {
            echo "   ⚠️  BOM détecté: $shortName\n";
            $content = substr($content, 3);
            if (file_put_contents($file, $content)) {
                echo "   🔧 BOM supprimé de: $shortName\n";
            }
        } else {
            echo "   ✅ OK: $shortName\n";
        }
    } else {
        echo "   ⚠️  Non trouvé: $shortName\n";
    }
}

echo "\n";

// 5. Créer un endpoint de test API
echo "5. Création d'un endpoint de test API...\n";

$testApiContent = '<?php
// Test API endpoint - À SUPPRIMER APRÈS USAGE
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$basePath = __DIR__;
$envPath = $basePath . "/.env";

try {
    if (!file_exists($envPath)) {
        throw new Exception(".env file not found at: $envPath");
    }
    
    $envContent = file_get_contents($envPath);
    $dbVars = [];
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, "#") === 0) continue;
        if (strpos($line, "=") !== false) {
            list($key, $value) = explode("=", $line, 2);
            $dbVars[trim($key)] = trim($value, "\"\'");
        }
    }
    
    $dsn = "mysql:host=" . ($dbVars["DB_HOST"] ?? "127.0.0.1") . 
           ";port=" . ($dbVars["DB_PORT"] ?? "3306") .
           ";dbname=" . ($dbVars["DB_DATABASE"] ?? "") . 
           ";charset=utf8mb4";
    
    $pdo = new PDO($dsn, $dbVars["DB_USERNAME"] ?? "", $dbVars["DB_PASSWORD"] ?? "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
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
        "message" => $e->getMessage(),
        "env_path" => $envPath ?? "unknown",
        "base_path" => $basePath ?? "unknown"
    ], JSON_PRETTY_PRINT);
}
';

$testApiPath = $basePath . '/test-api-direct.php';
if (file_put_contents($testApiPath, $testApiContent)) {
    echo "   ✅ Endpoint de test créé: /test-api-direct.php\n";
} else {
    echo "   ❌ Impossible de créer l'endpoint de test\n";
}

echo "\n";
echo "=== Diagnostic terminé ===\n\n";

echo "📋 PROCHAINES ÉTAPES:\n";
echo "1. Si .env a été corrigé, testez: curl https://vitapp.mykenyastudentprocess.com/test-api-direct.php\n";
echo "2. Si ça fonctionne, testez l'API Laravel: curl https://vitapp.mykenyastudentprocess.com/api/v1/categories\n";
echo "3. SUPPRIMEZ ces fichiers: rm fix-utf8.php test-api-direct.php\n";
