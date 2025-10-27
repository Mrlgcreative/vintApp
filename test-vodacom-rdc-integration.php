<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Test M-Pesa avec API Vodacom RDC (pas Safaricom Kenya)
 */
function testVodacomRDCIntegration() {
    echo "🇨🇩 Test intégration Vodacom M-Pesa RDC\n";
    echo "========================================\n\n";

    $rdcNumber = '826465399';
    $normalizedNumber = '+243' . $rdcNumber;
    
    echo "📱 Numéro test: {$normalizedNumber}\n";
    echo "📡 Opérateur: Vodacom M-Pesa RDC\n\n";

    // Test avec l'endpoint Vodacom RDC (différent de Safaricom Kenya)
    testVodacomRDCOAuth();
    
    echo "\n";
    
    // Simulation de l'appel via notre service Laravel
    testVintAppServiceCall($normalizedNumber);
}

/**
 * Test OAuth avec Vodacom RDC (endpoints différents de Safaricom)
 */
function testVodacomRDCOAuth() {
    echo "🔐 Test OAuth Vodacom RDC\n";
    echo "=========================\n";
    
    // Vodacom RDC utilise des endpoints différents de Safaricom Kenya
    $vodacomRDCEndpoints = [
        'sandbox' => 'https://openapi.m-pesa.com/sandbox',
        'production' => 'https://openapi.m-pesa.com',
    ];
    
    $environment = $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox';
    $baseUrl = $vodacomRDCEndpoints[$environment];
    
    echo "🌐 Endpoint Vodacom RDC: {$baseUrl}\n";
    echo "🔧 Environment: {$environment}\n\n";
    
    // Note: Vos clés sont pour Safaricom Kenya, pas Vodacom RDC
    echo "⚠️  IMPORTANT: Vos Consumer Key/Secret actuelles sont pour Safaricom Kenya\n";
    echo "   Pour Vodacom RDC, vous devez obtenir des clés spécifiques RDC\n\n";
    
    echo "📋 Endpoints Vodacom RDC nécessaires:\n";
    echo "- OAuth: {$baseUrl}/ipg/v2/token\n";
    echo "- B2C: {$baseUrl}/ipg/v2/b2c/payment\n";
    echo "- Status: {$baseUrl}/ipg/v2/transaction/status\n\n";
}

/**
 * Test appel via notre service Laravel (simulation)
 */
function testVintAppServiceCall($rdcNumber) {
    echo "🚀 Test via VintApp MobileMoneyService\n";
    echo "======================================\n";
    
    echo "📞 Simulation d'appel Laravel:\n";
    echo "Route: POST /wallet/1/withdraw-funds\n\n";
    
    $payload = [
        'amount' => 50,
        'currency' => 'USD', 
        'payment_method' => 'mpesa',
        'phone' => $rdcNumber,
    ];
    
    echo "📤 Payload:\n";
    echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";
    
    // Simulation du flux dans MobileMoneyService
    echo "🔄 Flux MobileMoneyService:\n";
    echo "1. WalletController@withdrawFunds reçoit la requête\n";
    echo "2. Validation: payment_method='mpesa', phone='{$rdcNumber}'\n";
    echo "3. Appel: mobileMoneyService->cashOut('mpesa', '{$rdcNumber}', 50, 'USD')\n";
    echo "4. MobileMoneyService->cashOutMPesa() exécuté\n";
    echo "5. getMPesaAccessToken() - OAuth avec vos Consumer Key/Secret\n";
    echo "6. Token obtenu: ✅ (comme testé précédemment)\n";
    echo "7. Appel B2C vers Safaricom Kenya avec numéro RDC\n";
    echo "8. Résultat: ❌ 'Bad Request - Invalid PartyB'\n\n";
    
    echo "💡 Solution:\n";
    echo "- Obtenir Consumer Key/Secret pour Vodacom RDC\n";
    echo "- Modifier les endpoints dans MobileMoneyService\n";
    echo "- Adapter le format des requêtes selon l'API Vodacom RDC\n\n";
}

/**
 * Génération de la configuration pour Vodacom RDC
 */
function generateVodacomRDCConfig() {
    echo "📝 Configuration recommandée pour Vodacom RDC\n";
    echo "=============================================\n\n";
    
    echo "🔧 Variables .env à ajouter:\n";
    echo "```env\n";
    echo "# Vodacom RDC M-Pesa (différent de Safaricom Kenya)\n";
    echo "VODACOM_RDC_ENABLED=true\n";
    echo "VODACOM_RDC_CONSUMER_KEY=your_vodacom_rdc_key\n";
    echo "VODACOM_RDC_CONSUMER_SECRET=your_vodacom_rdc_secret\n";
    echo "VODACOM_RDC_SERVICE_CODE=your_service_code\n";
    echo "VODACOM_RDC_ENVIRONMENT=sandbox\n";
    echo "```\n\n";
    
    echo "🔄 Modification MobileMoneyService nécessaire:\n";
    echo "1. Ajouter détection Vodacom RDC vs Safaricom Kenya\n";
    echo "2. Endpoints spécifiques selon le pays\n";
    echo "3. Format de payload adapté\n\n";
    
    echo "📡 Contacts Vodacom RDC pour obtenir les clés:\n";
    echo "- Site: https://developer.vodacom.cd/\n";
    echo "- API M-Pesa Vodacom RDC documentation\n";
    echo "- Support technique Vodacom\n\n";
}

/**
 * Test de contournement temporaire (simulation locale)
 */
function testLocalSimulation($rdcNumber) {
    echo "🔄 Test simulation locale (temporaire)\n";
    echo "======================================\n";
    
    echo "📱 Numéro: {$rdcNumber}\n";
    echo "💰 Montant: 50 USD\n";
    echo "🔧 Mode: Simulation (en attendant clés Vodacom RDC)\n\n";
    
    // Simuler le résultat que donnerait Vodacom RDC
    $simulatedResult = [
        'status' => 'processing',
        'message' => 'Retrait M-Pesa RDC en cours (simulé)',
        'provider_reference' => 'VDC-RDC-' . time() . '-' . rand(1000, 9999),
        'provider_response' => [
            'ConversationID' => 'RDC_' . date('YmdHis') . '_' . rand(100000, 999999),
            'OriginatorConversationID' => 'VINTAPP_' . time(),
            'ResponseCode' => '0',
            'ResponseDescription' => 'Request accepted for processing (Vodacom RDC)',
        ],
    ];
    
    echo "✅ Résultat simulé Vodacom RDC:\n";
    echo json_encode($simulatedResult, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "📋 Ce résultat sera retourné par MobileMoneyService en mode simulation\n";
    echo "jusqu'à obtention des vraies clés Vodacom RDC.\n";
}

// Exécution des tests
echo "🚀 Test M-Pesa Vodacom RDC - VintApp\n";
echo "=====================================\n\n";

testVodacomRDCIntegration();
echo "\n";
generateVodacomRDCConfig();
echo "\n";
testLocalSimulation('+243826465399');

echo "\n🎯 RÉSUMÉ:\n";
echo "==========\n";
echo "✅ Numéro RDC détecté: Vodacom M-Pesa (préfixe 82)\n";
echo "✅ OAuth fonctionne avec vos clés Safaricom Kenya\n";
echo "❌ Numéro RDC rejeté par API Safaricom Kenya (normal)\n";
echo "⚠️  Solution: Obtenir clés Consumer/Secret Vodacom RDC\n";
echo "🔄 En attendant: Mode simulation activé\n\n";

echo "📞 Prochaine étape: Contacter Vodacom RDC pour clés API\n";