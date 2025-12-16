<?php
/**
 * Test authentification API - SUPPRIMER APRÈS UTILISATION
 */

while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$basePath = __DIR__;

try {
    // Charger Laravel
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Créer une requête
    $request = Illuminate\Http\Request::capture();
    
    // Vérifier le header Authorization
    $authHeader = $request->header('Authorization');
    
    $result = [
        'timestamp' => date('Y-m-d H:i:s'),
        'auth_header_present' => !empty($authHeader),
        'auth_header_value' => $authHeader ? substr($authHeader, 0, 20) . '...' : null,
    ];
    
    if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
        $token = substr($authHeader, 7);
        
        // Charger .env pour la connexion DB
        $dotenv = Dotenv\Dotenv::createImmutable($basePath);
        $dotenv->load();
        
        $pdo = new PDO(
            "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4",
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Chercher le token dans personal_access_tokens
        $tokenHash = hash('sha256', $token);
        $stmt = $pdo->prepare("
            SELECT pat.*, u.id as user_id, u.name, u.email 
            FROM personal_access_tokens pat
            JOIN users u ON pat.tokenable_id = u.id
            WHERE pat.token = ?
        ");
        $stmt->execute([$tokenHash]);
        $tokenRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tokenRecord) {
            $result['token_valid'] = true;
            $result['user'] = [
                'id' => $tokenRecord['user_id'],
                'name' => $tokenRecord['name'],
                'email' => $tokenRecord['email'],
            ];
            $result['token_name'] = $tokenRecord['name'];
            $result['token_created'] = $tokenRecord['created_at'];
            $result['token_last_used'] = $tokenRecord['last_used_at'];
            
            // Tester la récupération des items de cet utilisateur
            $itemsStmt = $pdo->prepare("SELECT COUNT(*) as count FROM items WHERE user_id = ?");
            $itemsStmt->execute([$tokenRecord['user_id']]);
            $itemsCount = $itemsStmt->fetch(PDO::FETCH_ASSOC);
            $result['user_items_count'] = $itemsCount['count'];
            
        } else {
            $result['token_valid'] = false;
            $result['error'] = 'Token not found in database';
        }
    } else {
        $result['error'] = 'No Bearer token provided';
    }
    
    $json = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo $json;
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_UNESCAPED_UNICODE);
}
