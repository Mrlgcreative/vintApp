<?php

/**
 * Script de test pour l'API Authenticity Verification
 * 
 * Routes testées:
 * - GET /api/v1/items/{item}/authenticity/can-verify
 * - GET /api/v1/items/{item}/authenticity/status
 * - GET /api/v1/authenticity/dashboard
 */

$baseUrl = 'http://localhost:8000';
$apiUrl = $baseUrl . '/api/v1';

// Fonction helper pour les requêtes
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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

// Fonction pour afficher les résultats
function displayResult($testName, $response) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST: {$testName}\n";
    echo str_repeat("=", 80) . "\n";
    echo "Status Code: {$response['status']}\n";
    
    if (is_array($response['body'])) {
        echo "Response:\n";
        echo json_encode($response['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "Response: {$response['body']}\n";
    }
    
    // Vérifier le succès
    $isSuccess = $response['status'] >= 200 && $response['status'] < 300;
    echo "\nRésultat: " . ($isSuccess ? "✓ SUCCÈS" : "✗ ÉCHEC") . "\n";
    
    return $isSuccess;
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║          TEST API AUTHENTICITY VERIFICATION - VintApp                      ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

$results = [];

// ============================================================================
// TEST 1: Obtenir la liste des items pour trouver un ID valide
// ============================================================================
echo "\n[PRÉPARATION] Récupération d'un item valide...\n";
$response = makeRequest($apiUrl . '/items?per_page=1');
$itemId = null;

if ($response['status'] === 200 && isset($response['body']['data'][0]['id'])) {
    $itemId = $response['body']['data'][0]['id'];
    echo "✓ Item ID trouvé: {$itemId}\n";
    echo "  Nom: {$response['body']['data'][0]['name']}\n";
    echo "  Prix: {$response['body']['data'][0]['price']} FCFA\n";
} else {
    echo "✗ Impossible de trouver un item\n";
    exit(1);
}

// ============================================================================
// TEST 2: Vérifier l'éligibilité pour la vérification d'authenticité
// ============================================================================
$response = makeRequest($apiUrl . "/items/{$itemId}/authenticity/can-verify");
$results['can-verify'] = displayResult(
    "GET /items/{$itemId}/authenticity/can-verify",
    $response
);

// ============================================================================
// TEST 3: Obtenir le statut de vérification (devrait être vide si aucune demande)
// ============================================================================
$response = makeRequest($apiUrl . "/items/{$itemId}/authenticity/status");
$results['status'] = displayResult(
    "GET /items/{$itemId}/authenticity/status",
    $response
);

// ============================================================================
// TEST 4: Dashboard des vérifications (nécessite authentification)
// ============================================================================
echo "\n[INFO] Test du dashboard (peut échouer sans authentification)...\n";
$response = makeRequest($apiUrl . "/authenticity/dashboard");
$results['dashboard'] = displayResult(
    "GET /authenticity/dashboard",
    $response
);

// ============================================================================
// TEST 5: Test avec un ID d'item invalide
// ============================================================================
$response = makeRequest($apiUrl . "/items/99999/authenticity/can-verify");
$results['invalid-item'] = displayResult(
    "GET /items/99999/authenticity/can-verify (ID invalide)",
    $response
);

// Devrait retourner 404
if ($response['status'] === 404) {
    echo "✓ Gestion correcte des IDs invalides\n";
    $results['invalid-item'] = true;
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
echo "║                    ROUTES API AUTHENTICITY DISPONIBLES                     ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\nRoutes GET (testées):\n";
echo "  • GET /api/v1/items/{item}/authenticity/can-verify\n";
echo "  • GET /api/v1/items/{item}/authenticity/status\n";
echo "  • GET /api/v1/authenticity/dashboard\n";
echo "\nRoutes POST/PUT (nécessitent authentification + données):\n";
echo "  • POST /api/v1/items/{item}/authenticity/submit\n";
echo "  • POST /api/v1/authenticity/{check}/confirm-payment\n";
echo "  • PUT /api/v1/authenticity/{check}/update-status\n";

$successRate = round(($passedTests / $totalTests) * 100, 2);
echo "\nTaux de réussite: {$successRate}%\n";

if ($successRate >= 80) {
    echo "\n🎉 API Authenticity Verification opérationnelle!\n";
} elseif ($successRate >= 50) {
    echo "\n⚠️  API partiellement fonctionnelle - vérifications nécessaires\n";
} else {
    echo "\n❌ Problèmes détectés - corrections requises\n";
}

echo "\n";
