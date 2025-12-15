<?php
/**
 * TEST DIRECT - Bypass complet de Laravel
 * SUPPRIMER APRÈS UTILISATION
 */

// Désactiver TOUT output buffering
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');
while (ob_get_level()) ob_end_clean();

// Headers stricts
header('Content-Type: application/json; charset=UTF-8');
header('Content-Encoding: identity'); // Force NO compression
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Charger .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (explode("\n", file_get_contents($envFile)) as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value, '"\''));
        }
    }
}

try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_DATABASE') . ";charset=utf8mb4",
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Récupérer catégories
    $stmt = $pdo->query("SELECT id, name, slug, description, image, is_active FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Construire réponse
    $response = [
        'success' => true,
        'message' => 'Test direct DB - bypass Laravel',
        'count' => count($categories),
        'data' => $categories
    ];
    
    // Encoder JSON de façon stricte
    $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    if ($json === false) {
        throw new Exception('JSON encode error: ' . json_last_error_msg());
    }
    
    // Vérifier la validité du JSON généré
    $decoded = json_decode($json);
    if ($decoded === null) {
        throw new Exception('JSON decode verification failed: ' . json_last_error_msg());
    }
    
    // Output avec longueur exacte
    header('Content-Length: ' . strlen($json));
    echo $json;
    
} catch (Exception $e) {
    $error = json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    header('Content-Length: ' . strlen($error));
    echo $error;
}
exit;
