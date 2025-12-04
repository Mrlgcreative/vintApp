<?php

/**
 * Test REST API - Notification, Support & Payment Controllers
 * 
 * Tests protection auth:sanctum,web sur tous les endpoints
 * 
 * Execution: php test-api-notification-support-payment.php
 */

$baseUrl = 'http://localhost:8000/api';

// Style colors
function red($text) { return "\033[31m" . $text . "\033[0m"; }
function green($text) { return "\033[32m" . $text . "\033[0m"; }
function yellow($text) { return "\033[33m" . $text . "\033[0m"; }
function blue($text) { return "\033[34m" . $text . "\033[0m"; }

$routes = [
    // ==================== NOTIFICATION API (7 routes) ====================
    ['GET', '/v1/notifications', 'List notifications'],
    ['GET', '/v1/notifications/unread', 'List unread notifications'],
    ['GET', '/v1/notifications/unread/count', 'Unread count'],
    ['POST', '/v1/notifications/mark-all-read', 'Mark all as read'],
    ['POST', '/v1/notifications/1/mark-read', 'Mark one as read'],
    ['DELETE', '/v1/notifications/1', 'Delete notification'],
    ['DELETE', '/v1/notifications/read/all', 'Delete all read'],
    
    // ==================== SUPPORT API (6 routes) ====================
    ['GET', '/v1/support', 'List support chats'],
    ['POST', '/v1/support', 'Create support ticket'],
    ['GET', '/v1/support/stats', 'Support stats'],
    ['GET', '/v1/support/1', 'Get conversation'],
    ['POST', '/v1/support/1/reply', 'Reply to conversation'],
    ['POST', '/v1/support/1/close', 'Close conversation'],
    
    // ==================== PAYMENT API (6 routes) ====================
    ['GET', '/v1/payments', 'Payment history'],
    ['GET', '/v1/payments/stats', 'Payment stats'],
    ['GET', '/v1/payments/TXN123456', 'Payment details'],
    ['POST', '/v1/payments/initiate', 'Initiate payment'],
    ['POST', '/v1/payments/refund/1', 'Request refund'],
    ['GET', '/v1/payments/refund/1/status', 'Refund status'],
];

echo "\n" . blue("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
echo blue("  REST API v1 - Notification, Support & Payment - Test de Protection") . "\n";
echo blue("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";

$totalRoutes = count($routes);
$passedTests = 0;
$failedTests = 0;

foreach ($routes as $index => $route) {
    [$method, $endpoint, $description] = $route;
    $url = $baseUrl . $endpoint;
    
    echo sprintf("[%d/%d] ", $index + 1, $totalRoutes);
    echo yellow(str_pad($method, 6)) . " " . blue($endpoint) . "\n";
    echo "       " . $description . "\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    // Headers pour forcer réponse JSON
    $headers = ['Accept: application/json'];
    
    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }
    
    // Ajouter des données vides pour les POST
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
        $headers[] = 'Content-Type: application/json';
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 401) {
        echo "       " . green("✓ PASS") . " - 401 Unauthorized (Protection active)\n\n";
        $passedTests++;
    } else {
        echo "       " . red("✗ FAIL") . " - HTTP $httpCode (Attendu: 401)\n";
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['message'])) {
                echo "       Message: " . $data['message'] . "\n";
            }
        }
        echo "\n";
        $failedTests++;
    }
}

// ==================== SUMMARY ====================
echo blue("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
echo blue("  RÉSUMÉ DES TESTS") . "\n";
echo blue("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";

echo "Total routes testées: " . yellow($totalRoutes) . "\n";
echo "Tests réussis:        " . green($passedTests) . " ✓\n";
echo "Tests échoués:        " . ($failedTests > 0 ? red($failedTests) : green($failedTests)) . "\n";
echo "Taux de réussite:     " . yellow(round(($passedTests / $totalRoutes) * 100, 1)) . "%\n\n";

if ($failedTests === 0) {
    echo green("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
    echo green("  ✓ TOUS LES TESTS PASSENT - Protection auth:sanctum,web fonctionnelle!") . "\n";
    echo green("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";
    
    echo blue("Routes ajoutées:") . "\n";
    echo "  • Notification API: 7 routes\n";
    echo "  • Support API:      6 routes\n";
    echo "  • Payment API:      6 routes\n";
    echo "  " . yellow("Total: 19 nouvelles routes") . "\n\n";
    
    exit(0);
} else {
    echo red("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
    echo red("  ✗ CERTAINS TESTS ONT ÉCHOUÉ - Vérifier la configuration") . "\n";
    echo red("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";
    exit(1);
}
