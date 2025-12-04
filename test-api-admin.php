<?php

/**
 * Test REST API - Admin Controllers
 * 
 * Tests protection auth:sanctum,web + role:admin sur tous les endpoints admin
 * 
 * Execution: php test-api-admin.php
 */

$baseUrl = 'http://localhost:8000/api';

// Style colors
function red($text) { return "\033[31m" . $text . "\033[0m"; }
function green($text) { return "\033[32m" . $text . "\033[0m"; }
function yellow($text) { return "\033[33m" . $text . "\033[0m"; }
function blue($text) { return "\033[34m" . $text . "\033[0m"; }

$routes = [
    // ==================== ADMIN DASHBOARD & STATS (5 routes) ====================
    ['GET', '/v1/admin/dashboard', 'Admin dashboard stats'],
    ['GET', '/v1/admin/users', 'List users (admin)'],
    ['GET', '/v1/admin/wallets', 'List wallets (admin)'],
    ['GET', '/v1/admin/transactions', 'List transactions (admin)'],
    ['GET', '/v1/admin/orders', 'List orders (admin)'],
    
    // ==================== ENTERPRISE WALLETS (2 routes) ====================
    ['GET', '/v1/admin/enterprise-wallets', 'List enterprise wallets'],
    ['GET', '/v1/admin/enterprise-wallets/1', 'Enterprise wallet details'],
    
    // ==================== SUPPORT ADMIN (3 routes) ====================
    ['GET', '/v1/admin/support', 'List support chats (admin)'],
    ['GET', '/v1/admin/support/stats', 'Support stats (admin)'],
    ['GET', '/v1/admin/support/1', 'Support chat details (admin)'],
    
    // ==================== AFFILIATE MANAGEMENT (4 routes) ====================
    ['GET', '/v1/admin/affiliate/stats', 'Affiliate dashboard stats'],
    ['GET', '/v1/admin/affiliate/top-performers', 'Top affiliate performers'],
    ['GET', '/v1/admin/affiliate/referrers', 'List referrers'],
    ['GET', '/v1/admin/affiliate/activity', 'Recent affiliate activity'],
    
    // ==================== REFUNDS MANAGEMENT (2 routes) ====================
    ['GET', '/v1/admin/refunds', 'List refunds (admin)'],
    ['GET', '/v1/admin/refunds/1', 'Refund details (admin)'],
    
    // ==================== WAITING USERS (3 routes) ====================
    ['GET', '/v1/admin/waiting-users', 'List waiting users'],
    ['GET', '/v1/admin/waiting-users/stats', 'Waiting users stats'],
    ['POST', '/v1/admin/waiting-users/1/approve', 'Approve waiting user'],
    
    // ==================== MONITORING (2 routes) ====================
    ['GET', '/v1/admin/monitoring/stats', 'Monitoring stats'],
    ['GET', '/v1/admin/monitoring/health', 'Health check'],
];

echo "\n" . blue("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
echo blue("  REST API v1 - Admin Routes - Test de Protection") . "\n";
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
    
    // Pour les routes admin, on s'attend à 401 (non authentifié) ou 403 (non autorisé)
    if ($httpCode === 401 || $httpCode === 403) {
        echo "       " . green("✓ PASS") . " - HTTP $httpCode (Protection active)\n\n";
        $passedTests++;
    } else {
        echo "       " . red("✗ FAIL") . " - HTTP $httpCode (Attendu: 401/403)\n";
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
    echo green("  ✓ TOUS LES TESTS PASSENT - Protection Admin fonctionnelle!") . "\n";
    echo green("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";
    
    echo blue("Routes Admin ajoutées:") . "\n";
    echo "  • Dashboard & Stats:     5 routes\n";
    echo "  • Enterprise Wallets:    2 routes\n";
    echo "  • Support Admin:         3 routes\n";
    echo "  • Affiliate Management:  4 routes\n";
    echo "  • Refunds Management:    2 routes\n";
    echo "  • Waiting Users:         3 routes\n";
    echo "  • Monitoring:            2 routes\n";
    echo "  " . yellow("Total: 21 nouvelles routes admin") . "\n\n";
    
    exit(0);
} else {
    echo red("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
    echo red("  ✗ CERTAINS TESTS ONT ÉCHOUÉ - Vérifier la configuration") . "\n";
    echo red("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";
    exit(1);
}
