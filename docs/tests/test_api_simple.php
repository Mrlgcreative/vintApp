<?php

// Script de test simple pour l'API VintApp
$baseUrl = 'http://localhost:8000/api';

echo "🧪 Test API VintApp\n";
echo "===================\n\n";

// Test 1: Endpoint de santé
echo "1. Test endpoint de santé...\n";
$healthResponse = file_get_contents($baseUrl . '/health');
echo "Réponse: " . $healthResponse . "\n\n";

// Test 2: Endpoint utilisateur (sans authentification)
echo "2. Test endpoint utilisateur (sans auth)...\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]
]);

try {
    $userResponse = file_get_contents($baseUrl . '/user/profile', false, $context);
    echo "Réponse: " . $userResponse . "\n\n";
} catch (Exception $e) {
    echo "Erreur attendue (pas d'authentification): " . $e->getMessage() . "\n\n";
}

// Test 3: Lister les routes disponibles
echo "3. Routes disponibles:\n";
echo "- GET /api/health\n";
echo "- GET /api/user/profile (nécessite auth)\n";
echo "- GET /api/user/stats (nécessite auth)\n";
echo "- PUT /api/user/profile (nécessite auth)\n";
echo "- POST /api/user/avatar (nécessite auth)\n";
echo "- GET /api/items (nécessite auth)\n";
echo "- GET /api/items/search (nécessite auth)\n";
echo "- GET /api/orders (nécessite auth)\n";
echo "- GET /api/messages (nécessite auth)\n";
echo "- GET /api/reviews (nécessite auth)\n";
echo "- GET /api/notifications (nécessite auth)\n";
echo "- GET /api/dashboard/analytics (nécessite auth)\n";
echo "- GET /api/categories (nécessite auth)\n";
echo "- GET /api/brands (nécessite auth)\n\n";

echo "✅ Tests terminés !\n";
echo "Pour tester avec authentification, vous devez :\n";
echo "1. Créer un utilisateur\n";
echo "2. Obtenir un token d'authentification\n";
echo "3. Utiliser le token dans les headers\n\n";

echo "📝 Instructions pour tester avec Postman ou curl:\n";
echo "1. Créer un utilisateur: POST /api/register\n";
echo "2. Se connecter: POST /api/login\n";
echo "3. Utiliser le token dans: Authorization: Bearer {token}\n"; 