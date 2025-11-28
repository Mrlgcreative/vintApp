<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\CinetPay;

echo "=== Test du SDK CinetPay ===\n\n";

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$siteId = $_ENV['CINETPAY_SITE_ID'];
$apiKey = $_ENV['CINETPAY_API_KEY'];
$mode = $_ENV['CINETPAY_PLATFORM'];

echo "Configuration:\n";
echo "  Site ID: $siteId\n";
echo "  Mode: $mode\n\n";

try {
    // Initialiser CinetPay
    $cinetPay = new CinetPay($siteId, $apiKey, $mode, 'V2');
    
    echo "✓ SDK initialisé avec succès\n\n";
    
    // Configurer une transaction de test
    $transactionId = CinetPay::generateTransId();
    $cinetPay->setTransId($transactionId)
        ->setDesignation('Test de paiement VintApp')
        ->setAmount(1000) // 1000 XOF
        ->setCurrency('XOF')
        ->setNotifyUrl('https://votresite.com/payment/notify')
        ->setReturnUrl('https://votresite.com/payment/return');
    
    echo "✓ Transaction configurée\n";
    echo "  ID Transaction: $transactionId\n";
    echo "  Montant: 1000 XOF\n\n";
    
    // Test de récupération de la signature (appel API)
    echo "Test de connexion à l'API pour récupérer la signature...\n";
    
    // Utiliser la réflexion pour accéder à la méthode privée
    $reflection = new ReflectionClass($cinetPay);
    $method = $reflection->getMethod('getSignature');
    $method->setAccessible(true);
    
    $result = $method->invoke($cinetPay);
    
    echo "✓ Signature récupérée avec succès !\n";
    echo "  L'API CinetPay est accessible et fonctionne.\n\n";
    
    echo "=== Tout fonctionne correctement ! ===\n";
    
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    echo "  Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
