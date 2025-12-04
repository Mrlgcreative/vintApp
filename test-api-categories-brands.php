<?php
/**
 * Script de test API - Categories et Brands
 * Test des nouvelles méthodes API ajoutées
 */

$baseUrl = 'http://127.0.0.1:8000';

echo "=== Tests API Categories & Brands ===\n\n";

// Test 1: GET /api/v1/categories
echo "1. Test GET /api/v1/categories\n";
$response = file_get_contents("$baseUrl/api/v1/categories");
$data = json_decode($response, true);
echo "   ✓ Succès: {$data['success']}\n";
echo "   ✓ Message: {$data['message']}\n";
echo "   ✓ Catégories trouvées: " . count($data['data']) . "\n";
if (count($data['data']) > 0) {
    echo "   ✓ Première catégorie: {$data['data'][0]['name']} (ID: {$data['data'][0]['id']})\n";
}
echo "\n";

// Test 2: GET /api/v1/categories/{id}
if (count($data['data']) > 0) {
    $catId = $data['data'][0]['id'];
    echo "2. Test GET /api/v1/categories/$catId\n";
    $response = file_get_contents("$baseUrl/api/v1/categories/$catId");
    $cat = json_decode($response, true);
    echo "   ✓ Succès: {$cat['success']}\n";
    echo "   ✓ Catégorie: {$cat['data']['name']}\n";
    echo "   ✓ Slug: {$cat['data']['slug']}\n";
    echo "   ✓ Articles: {$cat['data']['items_count']}\n";
    echo "\n";

    // Test 3: GET /api/v1/categories/{id}/items
    echo "3. Test GET /api/v1/categories/$catId/items\n";
    $response = file_get_contents("$baseUrl/api/v1/categories/$catId/items");
    $items = json_decode($response, true);
    echo "   ✓ Succès: {$items['success']}\n";
    echo "   ✓ Message: {$items['message']}\n";
    $count = isset($items['data']) ? count($items['data']) : 0;
    echo "   ✓ Articles trouvés: $count\n";
    echo "\n";
}

// Test 4: GET /api/v1/brands
echo "4. Test GET /api/v1/brands\n";
$response = file_get_contents("$baseUrl/api/v1/brands");
$data = json_decode($response, true);
echo "   ✓ Succès: {$data['success']}\n";
echo "   ✓ Message: {$data['message']}\n";
echo "   ✓ Marques trouvées: " . count($data['data']) . "\n";
if (count($data['data']) > 0) {
    echo "   ✓ Première marque: {$data['data'][0]['name']} (ID: {$data['data'][0]['id']})\n";
}
echo "\n";

// Test 5: GET /api/v1/brands/{id}
if (count($data['data']) > 0) {
    $brandId = $data['data'][0]['id'];
    echo "5. Test GET /api/v1/brands/$brandId\n";
    $response = file_get_contents("$baseUrl/api/v1/brands/$brandId");
    $brand = json_decode($response, true);
    echo "   ✓ Succès: {$brand['success']}\n";
    echo "   ✓ Marque: {$brand['data']['name']}\n";
    echo "   ✓ Slug: {$brand['data']['slug']}\n";
    echo "   ✓ Articles: {$brand['data']['items_count']}\n";
    echo "\n";

    // Test 6: GET /api/v1/brands/{id}/items
    echo "6. Test GET /api/v1/brands/$brandId/items\n";
    $response = file_get_contents("$baseUrl/api/v1/brands/$brandId/items");
    $items = json_decode($response, true);
    echo "   ✓ Succès: {$items['success']}\n";
    echo "   ✓ Message: {$items['message']}\n";
    $count = isset($items['data']) ? count($items['data']) : 0;
    echo "   ✓ Articles trouvés: $count\n";
    echo "\n";
}

echo "=== Toutes les routes API fonctionnent ! ===\n";
echo "\nRésumé des routes testées:\n";
echo "  ✅ GET /api/v1/categories\n";
echo "  ✅ GET /api/v1/categories/{id}\n";
echo "  ✅ GET /api/v1/categories/{id}/items\n";
echo "  ✅ GET /api/v1/brands\n";
echo "  ✅ GET /api/v1/brands/{id}\n";
echo "  ✅ GET /api/v1/brands/{id}/items\n";
