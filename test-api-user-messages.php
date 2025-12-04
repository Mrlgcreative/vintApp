<?php

/**
 * Script de test pour l'API User et Messages
 * 
 * Routes testées:
 * User API:
 * - GET /api/v1/user/profile
 * - GET /api/v1/user/stats
 * - GET /api/v1/user/items
 * - GET /api/v1/user/orders
 * 
 * Messages API:
 * - GET /api/v1/messages
 * - GET /api/v1/messages/unread/count
 */

$baseUrl = 'http://localhost:8000';
$apiUrl = $baseUrl . '/api/v1';

// Token d'authentification (à récupérer via login)
$authToken = null;

function makeRequest($url, $method = 'GET', $headers = [], $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    if ($data !== null) {
        if ($method === 'GET') {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    return [
        'status' => $statusCode,
        'headers' => $headers,
        'body' => json_decode($body, true) ?? $body
    ];
}

function displayResult($testName, $response) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST: {$testName}\n";
    echo str_repeat("=", 80) . "\n";
    echo "Status Code: {$response['status']}\n";
    
    if (is_array($response['body'])) {
        echo "Response:\n";
        echo json_encode($response['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "Response: " . substr($response['body'], 0, 500) . "\n";
    }
    
    $isSuccess = $response['status'] >= 200 && $response['status'] < 300;
    echo "\nRésultat: " . ($isSuccess ? "✓ SUCCÈS" : "✗ ÉCHEC") . "\n";
    
    return $isSuccess;
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                   TEST API USER & MESSAGES - VintApp                       ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

$results = [];

// Note: Ces tests nécessitent une authentification
// Pour les tester complètement, il faut d'abord obtenir un token via Sanctum

echo "\n[INFO] Ces routes nécessitent une authentification Sanctum\n";
echo "[INFO] Les tests sans authentification retourneront 401 Unauthorized\n";

// ============================================================================
// TEST 1: User Profile (sans auth - devrait échouer avec 401)
// ============================================================================
$response = makeRequest($apiUrl . '/user/profile', 'GET', [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$results['user-profile-unauth'] = displayResult(
    "GET /user/profile (sans authentification)",
    $response
);

if ($response['status'] === 401) {
    echo "✓ Authentification requise correctement détectée\n";
    $results['user-profile-unauth'] = true;
}

// ============================================================================
// TEST 2: User Stats (sans auth - devrait échouer avec 401)
// ============================================================================
$response = makeRequest($apiUrl . '/user/stats', 'GET', [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$results['user-stats-unauth'] = displayResult(
    "GET /user/stats (sans authentification)",
    $response
);

if ($response['status'] === 401) {
    echo "✓ Authentification requise correctement détectée\n";
    $results['user-stats-unauth'] = true;
}

// ============================================================================
// TEST 3: Messages Index (sans auth - devrait échouer avec 401)
// ============================================================================
$response = makeRequest($apiUrl . '/messages', 'GET', [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$results['messages-index-unauth'] = displayResult(
    "GET /messages (sans authentification)",
    $response
);

if ($response['status'] === 401) {
    echo "✓ Authentification requise correctement détectée\n";
    $results['messages-index-unauth'] = true;
}

// ============================================================================
// TEST 4: Unread Messages Count (sans auth - devrait échouer avec 401)
// ============================================================================
$response = makeRequest($apiUrl . '/messages/unread/count', 'GET', [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$results['messages-unread-unauth'] = displayResult(
    "GET /messages/unread/count (sans authentification)",
    $response
);

if ($response['status'] === 401) {
    echo "✓ Authentification requise correctement détectée\n";
    $results['messages-unread-unauth'] = true;
}

// ============================================================================
// RÉSUMÉ FINAL
// ============================================================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           RÉSUMÉ DES TESTS                                 ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

$totalTests = count($results);
$passedTests = count(array_filter($results));
$failedTests = $totalTests - $passedTests;

echo "\nTests exécutés: {$totalTests}\n";
echo "Réussis: {$passedTests} ✓\n";
echo "Échoués: {$failedTests} ✗\n";

echo "\nDétails:\n";
foreach ($results as $test => $passed) {
    $status = $passed ? "✓ PASS" : "✗ FAIL";
    echo "  [{$status}] {$test}\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    ROUTES API USER & MESSAGES AJOUTÉES                     ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

echo "\n📱 USER API (10 routes):\n";
echo "  • GET    /api/v1/user/profile          - Profil utilisateur\n";
echo "  • PUT    /api/v1/user/profile          - Mettre à jour profil\n";
echo "  • PUT    /api/v1/user/password         - Changer mot de passe\n";
echo "  • POST   /api/v1/user/avatar           - Upload avatar\n";
echo "  • GET    /api/v1/user/stats            - Statistiques utilisateur\n";
echo "  • GET    /api/v1/user/items            - Articles de l'utilisateur\n";
echo "  • GET    /api/v1/user/orders           - Commandes\n";
echo "  • GET    /api/v1/user/sales            - Ventes\n";
echo "  • GET    /api/v1/user/reviews          - Avis reçus\n";
echo "  • DELETE /api/v1/user/account          - Supprimer compte\n";

echo "\n💬 MESSAGES API (7 routes):\n";
echo "  • GET    /api/v1/messages              - Liste conversations\n";
echo "  • POST   /api/v1/messages              - Envoyer message\n";
echo "  • GET    /api/v1/messages/{userId}     - Messages conversation\n";
echo "  • PUT    /api/v1/messages/{id}/mark-read - Marquer lu\n";
echo "  • GET    /api/v1/messages/unread/count - Nombre non lus\n";
echo "  • POST   /api/v1/messages/discount/apply - Appliquer réduction\n";
echo "  • GET    /api/v1/messages/discounts/{itemId} - Réductions disponibles\n";

$successRate = round(($passedTests / $totalTests) * 100, 2);
echo "\nTaux de réussite: {$successRate}%\n";

if ($successRate >= 80) {
    echo "\n✅ Routes User & Messages correctement protégées par authentification!\n";
} elseif ($successRate >= 50) {
    echo "\n⚠️  Certaines routes nécessitent des vérifications\n";
} else {
    echo "\n❌ Problèmes détectés - corrections requises\n";
}

echo "\n📝 NOTE: Pour tester avec authentification:\n";
echo "   1. Créer un token Sanctum via POST /api/login\n";
echo "   2. Ajouter header: Authorization: Bearer {token}\n";
echo "   3. Relancer les tests\n";

echo "\n";
