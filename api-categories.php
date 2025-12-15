<?php
/**
 * API Categories - Bypass des middlewares Laravel
 * Remplace temporairement /api/v1/categories
 * SUPPRIMER APRÈS CORRECTION DU PROBLÈME
 */

// Désactiver output buffering et compression
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');
while (ob_get_level()) ob_end_clean();

// Headers
header('Content-Type: application/json; charset=UTF-8');
header('Content-Encoding: identity');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    // Récupérer toutes les catégories avec leurs parents
    $sql = "
        SELECT 
            c.id,
            c.parent_id,
            c.name,
            c.slug,
            c.description,
            c.icon,
            c.image,
            c.is_active,
            c.sort_order,
            c.created_at,
            c.updated_at,
            p.id as parent_id_rel,
            p.name as parent_name,
            p.slug as parent_slug,
            p.image as parent_image,
            p.is_active as parent_is_active,
            (SELECT COUNT(*) FROM items WHERE category_id = c.id AND status = 'active') as items_count
        FROM categories c
        LEFT JOIN categories p ON c.parent_id = p.id
        ORDER BY c.name
    ";
    
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $appUrl = getenv('APP_URL') ?: 'https://vitapp.mykenyastudentprocess.com';
    
    $categories = [];
    foreach ($rows as $row) {
        $category = [
            'id' => (int)$row['id'],
            'parent_id' => $row['parent_id'] ? (int)$row['parent_id'] : null,
            'name' => $row['name'],
            'slug' => $row['slug'],
            'description' => $row['description'],
            'icon' => $row['icon'],
            'image' => $row['image'],
            'is_active' => (bool)$row['is_active'],
            'sort_order' => (int)$row['sort_order'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'items_count' => (int)$row['items_count'],
            'image_url' => $row['image'] ? $appUrl . '/storage/' . $row['image'] : null,
            'parent' => null
        ];
        
        if ($row['parent_id_rel']) {
            $category['parent'] = [
                'id' => (int)$row['parent_id_rel'],
                'parent_id' => null,
                'name' => $row['parent_name'],
                'slug' => $row['parent_slug'],
                'description' => null,
                'icon' => null,
                'image' => $row['parent_image'],
                'is_active' => (bool)$row['parent_is_active'],
                'sort_order' => 0,
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'image_url' => $row['parent_image'] ? $appUrl . '/storage/' . $row['parent_image'] : null
            ];
        }
        
        $categories[] = $category;
    }
    
    $response = [
        'success' => true,
        'message' => 'Catégories récupérées avec succès',
        'data' => $categories
    ];
    
    $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    header('Content-Length: ' . strlen($json));
    echo $json;
    
} catch (Exception $e) {
    http_response_code(500);
    $error = json_encode([
        'success' => false,
        'message' => 'Erreur serveur',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    header('Content-Length: ' . strlen($error));
    echo $error;
}
exit;
