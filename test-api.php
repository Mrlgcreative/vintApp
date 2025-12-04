<?php
/**
 * Script de test de l'API VintApp
 * Usage: php test-api.php
 */

$baseUrl = 'http://127.0.0.1:8000';

echo "=== Tests API VintApp ===\n\n";

// Test 1: Health check
echo "1. Test Health Check\n";
$response = file_get_contents("$baseUrl/api/health");
$data = json_decode($response, true);
echo "   ✓ Status: {$data['status']}\n";
echo "   ✓ Message: {$data['message']}\n\n";

// Test 2: Categories
echo "2. Test GET /api/v1/categories\n";
$response = file_get_contents("$baseUrl/api/v1/categories");
$data = json_decode($response, true);
$count = count($data['data']);
echo "   ✓ Catégories trouvées: $count\n";
if ($count > 0) {
    echo "   ✓ Première catégorie: {$data['data'][0]['name']}\n";
}
echo "\n";

// Test 3: Brands
echo "3. Test GET /api/v1/brands\n";
$response = file_get_contents("$baseUrl/api/v1/brands");
$data = json_decode($response, true);
$count = count($data['data']);
echo "   ✓ Marques trouvées: $count\n";
if ($count > 0) {
    echo "   ✓ Première marque: {$data['data'][0]['name']}\n";
}
echo "\n";

// Test 4: Items
echo "4. Test GET /api/v1/items\n";
$response = file_get_contents("$baseUrl/api/v1/items");
$data = json_decode($response, true);
$count = isset($data['data']) ? count($data['data']) : 0;
$total = isset($data['meta']['total']) ? $data['meta']['total'] : 0;
echo "   ✓ Articles trouvés: $count sur $total\n";
if ($count > 0) {
    echo "   ✓ Premier article: {$data['data'][0]['title']}\n";
}
echo "\n";

// Test 5: Items avec filtres
echo "5. Test GET /api/v1/items?per_page=3&sort=price_asc\n";
$response = file_get_contents("$baseUrl/api/v1/items?per_page=3&sort=price_asc");
$data = json_decode($response, true);
$count = isset($data['data']) ? count($data['data']) : 0;
echo "   ✓ Articles trouvés: $count\n";
echo "   ✓ Per page: {$data['meta']['per_page']}\n\n";

// Test 6: Single item (si existe)
if (isset($data['data'][0]['id'])) {
    $itemId = $data['data'][0]['id'];
    echo "6. Test GET /api/v1/items/$itemId\n";
    $response = file_get_contents("$baseUrl/api/v1/items/$itemId");
    $item = json_decode($response, true);
    if (isset($item['data']['title'])) {
        echo "   ✓ Article trouvé: {$item['data']['title']}\n";
        echo "   ✓ Prix: {$item['data']['price']} USD\n";
    }
    echo "\n";
}

echo "=== Tests terminés avec succès ! ===\n";
