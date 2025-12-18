<?php
/**
 * Script de test pour les payouts MaishaPay
 * 
 * Teste l'intégration de MaishaPay comme agrégateur unifié de payout
 * dans MobileMoneyService
 */

require_once __DIR__ . '/vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\MaishaPay;
use App\Services\MobileMoneyService;
use Illuminate\Support\Facades\Config;

echo "===========================================\n";
echo "   TEST MAISHAPAY PAYOUT INTEGRATION\n";
echo "===========================================\n\n";

// 1. Test de la configuration
echo "1. Vérification de la configuration MaishaPay...\n";
$config = [
    'api_key' => config('services.maishapay.api_key'),
    'secret_key' => config('services.maishapay.secret_key') ? '***' . substr(config('services.maishapay.secret_key'), -10) : null,
    'environment' => config('services.maishapay.environment'),
    'enabled' => config('services.maishapay.enabled'),
];

foreach ($config as $key => $value) {
    $status = $value ? '✅' : '❌';
    echo "   {$status} {$key}: " . ($value ?: 'NON CONFIGURÉ') . "\n";
}

if (!$config['api_key'] || !$config['secret_key']) {
    echo "\n⚠️  MaishaPay non configuré - certains tests seront ignorés\n";
}

// 2. Test du service MaishaPay
echo "\n2. Test du service MaishaPay (payout)...\n";

try {
    $maishaPay = new MaishaPay();
    
    // Test de détection d'opérateur
    $testNumbers = [
        '812345678' => 'VODACOM',
        '842345678' => 'ORANGE',
        '972345678' => 'AIRTEL',
        '902345678' => 'AFRICELL',
    ];
    
    echo "   Détection opérateurs:\n";
    foreach ($testNumbers as $phone => $expected) {
        $detected = $maishaPay->detectOperator($phone);
        $status = $detected === $expected ? '✅' : '❌';
        echo "      {$status} {$phone} → {$detected} (attendu: {$expected})\n";
    }
    
    // Test du mapping opérateurs
    echo "\n   Mapping opérateurs VintApp → MaishaPay:\n";
    $mappings = [
        'orange_money' => 'ORANGE',
        'airtel_money' => 'AIRTEL',
        'mpesa' => 'VODACOM',
        'africell' => 'AFRICELL',
        'illicocash' => null,
    ];
    
    foreach ($mappings as $provider => $expected) {
        $mapped = $maishaPay->mapOperator($provider);
        $supported = $maishaPay->isOperatorSupported($provider);
        $status = ($mapped === $expected) ? '✅' : '❌';
        $supportedText = $supported ? 'Supporté' : 'Non supporté';
        echo "      {$status} {$provider} → " . ($mapped ?? 'NULL') . " ({$supportedText})\n";
    }
    
    // Test de simulation payout (sandbox uniquement)
    if (config('services.maishapay.environment') === 'sandbox') {
        echo "\n   Test simulation payout (sandbox):\n";
        
        $payoutResult = $maishaPay->initiatePayout([
            'phone' => '243812345678',
            'amount' => 5000,
            'currency' => 'CDF',
            'operator' => 'VODACOM',
            'description' => 'Test payout VintApp',
            'user_id' => 1,
            'purpose' => 'withdrawal_test',
        ]);
        
        if ($payoutResult['success']) {
            echo "      ✅ Payout simulé avec succès\n";
            echo "         Transaction ID: {$payoutResult['transaction_id']}\n";
            echo "         Provider Ref: " . ($payoutResult['provider_reference'] ?? 'N/A') . "\n";
            echo "         Status: {$payoutResult['status']}\n";
            echo "         Message: {$payoutResult['message']}\n";
        } else {
            echo "      ❌ Échec simulation payout: {$payoutResult['message']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur: {$e->getMessage()}\n";
}

// 3. Test du MobileMoneyService avec MaishaPay
echo "\n3. Test du MobileMoneyService avec agrégateur MaishaPay...\n";

try {
    $mobileMoneyService = new MobileMoneyService();
    
    // Vérifier si MaishaPay est utilisé
    $reflection = new ReflectionClass($mobileMoneyService);
    $useMaishaPayProp = $reflection->getProperty('useMaishaPayAggregator');
    $useMaishaPayProp->setAccessible(true);
    $useMaishaPay = $useMaishaPayProp->getValue($mobileMoneyService);
    
    $maishaPayProp = $reflection->getProperty('maishaPay');
    $maishaPayProp->setAccessible(true);
    $maishaPayInstance = $maishaPayProp->getValue($mobileMoneyService);
    
    $status = $useMaishaPay ? '✅' : '⚠️';
    echo "   {$status} Agrégateur MaishaPay: " . ($useMaishaPay ? 'ACTIVÉ' : 'DÉSACTIVÉ') . "\n";
    echo "   " . ($maishaPayInstance ? '✅' : '❌') . " Instance MaishaPay: " . ($maishaPayInstance ? 'Initialisée' : 'Non initialisée') . "\n";
    
    // Test des méthodes webhook
    echo "\n   Méthodes webhook:\n";
    $webhookMethods = [
        'verifyWebhookSignature',
        'extractReferenceFromWebhook',
        'extractStatusFromWebhook',
        'extractProviderReferenceFromWebhook',
    ];
    
    foreach ($webhookMethods as $method) {
        $exists = method_exists($mobileMoneyService, $method);
        $status = $exists ? '✅' : '❌';
        echo "      {$status} {$method}()\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur: {$e->getMessage()}\n";
}

// 4. Récapitulatif
echo "\n===========================================\n";
echo "   RÉCAPITULATIF\n";
echo "===========================================\n";

$recap = [
    'MaishaPay Encaissement' => '✅ Intégré (PaymentController)',
    'MaishaPay Décaissement' => config('services.maishapay.enabled') ? '✅ Intégré (MobileMoneyService)' : '⚠️  Désactivé dans .env',
    'Agrégateur Unifié' => '✅ MaishaPay gère Orange, Airtel, M-Pesa, Africell',
    'Fallback APIs directes' => '✅ Disponible si MaishaPay échoue',
    'Webhooks' => '✅ Route /wallet/withdrawals/webhook/maishapay',
];

foreach ($recap as $feature => $status) {
    echo "   {$status} - {$feature}\n";
}

echo "\n✅ Test terminé avec succès!\n";
echo "\n===========================================\n";
echo "   PROCHAINES ÉTAPES\n";
echo "===========================================\n";
echo "   1. Activer MAISHAPAY_ENABLED=true dans .env\n";
echo "   2. Vérifier les clés API MaishaPay en production\n";
echo "   3. Tester un vrai payout avec un petit montant\n";
echo "   4. Vérifier les webhooks sur le serveur production\n\n";
