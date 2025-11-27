<?php

echo "=== Test de Connectivité CinetPay ===\n\n";

// Test 1: Vérifier si cURL est activé
echo "1. Vérification de cURL...\n";
if (function_exists('curl_version')) {
    $version = curl_version();
    echo "   ✓ cURL est activé\n";
    echo "   Version: " . $version['version'] . "\n";
    echo "   SSL Version: " . $version['ssl_version'] . "\n\n";
} else {
    echo "   ✗ cURL n'est PAS activé\n\n";
    exit(1);
}

// Test 2: Tester la connexion à l'API sandbox
echo "2. Test de connexion à api.sandbox.cinetpay.com...\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.sandbox.cinetpay.com',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_VERBOSE => true,
    CURLOPT_STDERR => fopen('php://temp', 'w+'),
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);

if ($error) {
    echo "   ✗ Échec de connexion\n";
    echo "   Erreur: $error\n";
    echo "   Code HTTP: " . $info['http_code'] . "\n";
} else {
    echo "   ✓ Connexion réussie\n";
    echo "   Code HTTP: " . $info['http_code'] . "\n";
    echo "   Temps de connexion: " . $info['connect_time'] . "s\n";
}
curl_close($ch);

echo "\n3. Test de connexion à l'API production...\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.cinetpay.com',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_FOLLOWLOCATION => true,
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);

if ($error) {
    echo "   ✗ Échec de connexion\n";
    echo "   Erreur: $error\n";
    echo "   Code HTTP: " . $info['http_code'] . "\n";
} else {
    echo "   ✓ Connexion réussie\n";
    echo "   Code HTTP: " . $info['http_code'] . "\n";
    echo "   Temps de connexion: " . $info['connect_time'] . "s\n";
}
curl_close($ch);

// Test 3: Vérifier allow_url_fopen
echo "\n4. Vérification de allow_url_fopen...\n";
if (ini_get('allow_url_fopen')) {
    echo "   ✓ allow_url_fopen est activé\n";
} else {
    echo "   ✗ allow_url_fopen est désactivé\n";
}

// Test 4: Vérifier les extensions SSL
echo "\n5. Vérification des extensions SSL/TLS...\n";
if (extension_loaded('openssl')) {
    echo "   ✓ OpenSSL est chargé\n";
    echo "   Version: " . OPENSSL_VERSION_TEXT . "\n";
} else {
    echo "   ✗ OpenSSL n'est PAS chargé\n";
}

// Test 5: Proxy detection
echo "\n6. Détection de proxy...\n";
$proxy = getenv('HTTP_PROXY') ?: getenv('HTTPS_PROXY') ?: 'Aucun proxy détecté';
echo "   Proxy: $proxy\n";

echo "\n=== Fin du test ===\n";
