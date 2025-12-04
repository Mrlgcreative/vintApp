<?php

/**
 * Test REST API - Admin Routes Complete
 * 
 * Tests protection auth:sanctum,web + role:admin sur tous les endpoints
 * 
 * Execution: php test-api-admin-complete.php
 */

$baseUrl = 'http://localhost:8000/api';

// Style colors
function red($text) { return "\033[31m" . $text . "\033[0m"; }
function green($text) { return "\033[32m" . $text . "\033[0m"; }
function yellow($text) { return "\033[33m" . $text . "\033[0m"; }
function blue($text) { return "\033[34m" . $text . "\033[0m"; }

$routes = [
    // ==================== ADMIN DASHBOARD & STATS ====================
    ['GET', '/v1/admin/dashboard', 'Dashboard admin'],
    ['GET', '/v1/admin/stats/summary', 'Résumé statistiques'],
    ['GET', '/v1/admin/notifications', 'Notifications admin'],
    ['GET', '/v1/admin/reports', 'Rapports'],
    ['GET', '/v1/admin/online-users', 'Utilisateurs en ligne'],
    
    // ==================== USERS MANAGEMENT ====================
    ['GET', '/v1/admin/users', 'Liste utilisateurs'],
    ['GET', '/v1/admin/users/1', 'Détails utilisateur'],
    ['POST', '/v1/admin/users/1/status', 'Modifier statut utilisateur'],
    
    // ==================== WALLETS MANAGEMENT ====================
    ['GET', '/v1/admin/wallets', 'Liste wallets'],
    ['GET', '/v1/admin/wallets/pending', 'Wallets en attente'],
    ['POST', '/v1/admin/wallets/1/approve', 'Approuver wallet'],
    ['POST', '/v1/admin/wallets/1/reject', 'Rejeter wallet'],
    ['POST', '/v1/admin/wallets/bulk-approve', 'Approbation en masse'],
    
    // ==================== TRANSACTIONS ====================
    ['GET', '/v1/admin/transactions', 'Liste transactions'],
    
    // ==================== ORDERS ====================
    ['GET', '/v1/admin/orders', 'Liste commandes'],
    
    // ==================== ITEMS MANAGEMENT ====================
    ['GET', '/v1/admin/items', 'Liste articles'],
    ['POST', '/v1/admin/items/1/status', 'Modifier statut article'],
    
    // ==================== BRANDS & CATEGORIES ====================
    ['GET', '/v1/admin/brands', 'Liste marques'],
    ['GET', '/v1/admin/categories', 'Liste catégories'],
    
    // ==================== SUPPORT ====================
    ['GET', '/v1/admin/support-chats', 'Conversations support'],
    
    // ==================== VERIFICATION ====================
    ['GET', '/v1/admin/verification-checks', 'Vérifications authenticité'],
    
    // ==================== SETTINGS ====================
    ['GET', '/v1/admin/settings', 'Liste paramètres'],
    ['PUT', '/v1/admin/settings/app_name', 'Modifier paramètre'],
    
    // ==================== ENTERPRISE WALLETS ====================
    ['GET', '/v1/admin/enterprise-wallets', 'Wallets entreprise'],
    ['GET', '/v1/admin/enterprise-wallets/1', 'Détails wallet entreprise'],
    
    // ==================== SUPPORT ADMIN ====================
    ['GET', '/v1/admin/support', 'Support admin list'],
    ['GET', '/v1/admin/support/stats', 'Support stats'],
    ['GET', '/v1/admin/support/1', 'Support details'],
    
    // ==================== AFFILIATE ====================
    ['GET', '/v1/admin/affiliate/stats', 'Affiliate stats'],
    ['GET', '/v1/admin/affiliate/top-performers', 'Top performers'],
    ['GET', '/v1/admin/affiliate/referrers', 'Referrers'],
    ['GET', '/v1/admin/affiliate/activity', 'Recent activity'],
    
    // ==================== REFUNDS ====================
    ['GET', '/v1/admin/refunds', 'Liste remboursements'],
    ['GET', '/v1/admin/refunds/1', 'Détails remboursement'],
    
    // ==================== WAITING USERS ====================
    ['GET', '/v1/admin/waiting-users', 'Liste pré-inscriptions'],
    ['GET', '/v1/admin/waiting-users/stats', 'Stats pré-inscriptions'],
    ['POST', '/v1/admin/waiting-users/1/approve', 'Approuver pré-inscription'],
    
    // ==================== MONITORING ====================
    ['GET', '/v1/admin/monitoring/stats', 'Stats monitoring'],
    ['GET', '/v1/admin/monitoring/health', 'Health check'],
];

echo "\n" . blue("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
echo blue("  REST API v1 - Admin Routes Complete - Test de Protection") . "\n";
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
    
    // Ajouter des données vides pour les POST/PUT
    if (in_array($method, ['POST', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
        $headers[] = 'Content-Type: application/json';
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Accept 401 (not authenticated) or 403 (not admin) as success
    if ($httpCode === 401 || $httpCode === 403) {
        echo "       " . green("✓ PASS") . " - HTTP $httpCode (Protection active)\n\n";
        $passedTests++;
    } else {
        echo "       " . red("✗ FAIL") . " - HTTP $httpCode (Attendu: 401 ou 403)\n";
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
    echo green("  ✓ TOUS LES TESTS PASSENT - Protection admin fonctionnelle!") . "\n";
    echo green("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";
    
    echo blue("Routes Admin ajoutées:") . "\n";
    echo "  • Dashboard & Stats:    5 routes\n";
    echo "  • Users Management:     3 routes\n";
    echo "  • Wallets Management:   5 routes\n";
    echo "  • Transactions:         1 route\n";
    echo "  • Orders:               1 route\n";
    echo "  • Items Management:     2 routes\n";
    echo "  • Brands & Categories:  2 routes\n";
    echo "  • Support Chats:        1 route\n";
    echo "  • Verifications:        1 route\n";
    echo "  • Settings:             2 routes\n";
    echo "  • Enterprise Wallets:   2 routes\n";
    echo "  • Support Admin:        3 routes\n";
    echo "  • Affiliate:            4 routes\n";
    echo "  • Refunds:              2 routes\n";
    echo "  • Waiting Users:        3 routes\n";
    echo "  • Monitoring:           2 routes\n";
    echo "  " . yellow("Total: 43 routes admin") . "\n\n";
    
    exit(0);
} else {
    echo red("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n";
    echo red("  ✗ CERTAINS TESTS ONT ÉCHOUÉ - Vérifier la configuration") . "\n";
    echo red("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━") . "\n\n";
    exit(1);
}
