<?php
/**
 * Test API /user/items - SUPPRIMER APRÈS UTILISATION
 */

// Désactiver output buffering
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=UTF-8');

$basePath = __DIR__;

// Charger .env
$envFile = $basePath . '/.env';
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
    
    // Récupérer un utilisateur avec ses items
    $userId = $_GET['user_id'] ?? 1;
    
    $sql = "
        SELECT 
            i.id,
            i.name,
            i.description,
            i.price,
            i.currency,
            i.status,
            i.images,
            i.created_at,
            c.name as category_name,
            b.name as brand_name
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.id
        LEFT JOIN brands b ON i.brand_id = b.id
        WHERE i.user_id = ?
        ORDER BY i.created_at DESC
        LIMIT 12
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parser les images JSON
    foreach ($items as &$item) {
        if ($item['images']) {
            $item['images'] = json_decode($item['images'], true);
        }
    }
    
    $response = [
        'success' => true,
        'message' => 'Test user items - bypass Laravel',
        'user_id' => $userId,
        'count' => count($items),
        'data' => $items
    ];
    
    $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    header('Content-Length: ' . strlen($json));
    echo $json;
    
} catch (Exception $e) {
    $error = json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    header('Content-Length: ' . strlen($error));
    echo $error;
}
