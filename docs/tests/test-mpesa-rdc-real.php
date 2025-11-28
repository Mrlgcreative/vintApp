<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Test OAuth M-Pesa avec numéro RDC réel
 */
function testMPesaWithRealRDCNumber() {
    echo "🇨🇩 Test M-Pesa avec numéro RDC réel\n";
    echo "====================================\n\n";

    // Numéro RDC fourni par l'utilisateur
    $rdcNumber = '826465399';
    $normalizedNumber = normalizeCongoPhoneNumber($rdcNumber);
    
    echo "📱 Numéro original: {$rdcNumber}\n";
    echo "📱 Numéro normalisé: {$normalizedNumber}\n\n";

    // Étape 1: Obtenir le token OAuth
    $accessToken = getMPesaOAuthToken();
    
    if (!$accessToken) {
        echo "❌ Impossible d'obtenir le token OAuth\n";
        return;
    }

    echo "✅ Token OAuth obtenu: " . substr($accessToken, 0, 20) . "...\n\n";

    // Étape 2: Test B2C avec le numéro RDC
    testB2CWithRDCNumber($accessToken, $normalizedNumber);
}

/**
 * Normaliser un numéro de téléphone congolais
 */
function normalizeCongoPhoneNumber($phone) {
    // Retirer tous les espaces et caractères spéciaux sauf +
    $phone = preg_replace('/[^\d+]/', '', $phone);

    // Si commence par 0, remplacer par +243
    if (str_starts_with($phone, '0')) {
        return '+243' . substr($phone, 1);
    }

    // Si commence par 243, ajouter le +
    if (str_starts_with($phone, '243')) {
        return '+' . $phone;
    }

    // Si ne commence pas par +, c'est juste le numéro sans indicatif
    if (!str_starts_with($phone, '+')) {
        return '+243' . $phone;
    }

    return $phone;
}

/**
 * Obtenir token OAuth M-Pesa
 */
function getMPesaOAuthToken() {
    $consumerKey = $_ENV['MPESA_API_KEY'];
    $consumerSecret = $_ENV['MPESA_API_SECRET'];
    $environment = $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox';

    // URL OAuth selon l'environnement
    $oauthUrl = $environment === 'production'
        ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    // Encoder les credentials en base64
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    try {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $oauthUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $credentials,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $data['access_token'] ?? null;
        }

    } catch (Exception $e) {
        echo "❌ Erreur OAuth: " . $e->getMessage() . "\n";
    }

    return null;
}

/**
 * Test B2C avec le numéro RDC normalisé
 */
function testB2CWithRDCNumber($accessToken, $rdcNumber) {
    echo "🏦 Test B2C avec numéro RDC réel\n";
    echo "================================\n";

    $shortcode = $_ENV['MPESA_SHORTCODE'];
    $passkey = $_ENV['MPESA_PASSKEY'];
    $environment = $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox';
    
    $b2cUrl = $environment === 'production'
        ? 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest'
        : 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';

    // Timestamp et password
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);

    // Montant de test très petit
    $testAmount = 1;

    $payload = [
        'InitiatorName' => 'VintApp',
        'SecurityCredential' => $password,
        'CommandID' => 'BusinessPayment',
        'Amount' => $testAmount,
        'PartyA' => $shortcode,
        'PartyB' => $rdcNumber,  // Numéro RDC normalisé
        'Remarks' => "Test VintApp RDC - {$rdcNumber}",
        'QueueTimeOutURL' => 'https://vintapp-webhook.ngrok.io/webhook/mpesa/timeout',
        'ResultURL' => 'https://vintapp-webhook.ngrok.io/webhook/mpesa/result',
        'Occasion' => 'Test RDC',
    ];

    echo "📞 Endpoint: {$b2cUrl}\n";
    echo "💰 Montant test: {$testAmount} (minimal pour test)\n";
    echo "📱 Destinataire: {$rdcNumber}\n";
    echo "🔐 Shortcode: {$shortcode}\n\n";

    echo "📤 Payload envoyé:\n";
    echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

    try {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $b2cUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        echo "🔄 Statut HTTP: {$httpCode}\n";
        
        if ($error) {
            echo "❌ Erreur CURL: {$error}\n";
            return;
        }

        echo "📥 Réponse brute:\n{$response}\n\n";

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            
            if (isset($data['ConversationID'])) {
                echo "✅ SUCCESS! Requête B2C acceptée\n";
                echo "🆔 ConversationID: {$data['ConversationID']}\n";
                echo "📝 Description: {$data['ResponseDescription']}\n";
                
                echo "\n📋 Résumé de la transaction:\n";
                echo "- Numéro RDC: {$rdcNumber}\n";
                echo "- Montant: {$testAmount}\n";
                echo "- Référence: {$data['ConversationID']}\n";
                echo "- Statut: En attente de confirmation M-Pesa\n";
                
            } else {
                echo "⚠️ Réponse inattendue:\n";
                echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
            }
            
        } elseif ($httpCode === 400) {
            $errorData = json_decode($response, true);
            echo "❌ Erreur 400 - Données invalides:\n";
            
            if (isset($errorData['errorMessage'])) {
                echo "Message: {$errorData['errorMessage']}\n";
            }
            if (isset($errorData['errorCode'])) {
                echo "Code: {$errorData['errorCode']}\n";
            }
            
        } elseif ($httpCode === 401) {
            echo "❌ Erreur 401 - Token invalide ou expiré\n";
            
        } else {
            echo "❌ Erreur HTTP {$httpCode}:\n{$response}\n";
        }

    } catch (Exception $e) {
        echo "💥 Exception: " . $e->getMessage() . "\n";
    }
}

/**
 * Analyser le préfixe du numéro pour déterminer l'opérateur
 */
function analyzeRDCOperator($normalizedNumber) {
    echo "\n🔍 Analyse de l'opérateur RDC\n";
    echo "=============================\n";
    
    // Extraire le préfixe après +243
    if (str_starts_with($normalizedNumber, '+243')) {
        $prefix = substr($normalizedNumber, 4, 2); // Les 2 premiers chiffres après 243
        
        echo "📱 Numéro: {$normalizedNumber}\n";
        echo "🔢 Préfixe: {$prefix}\n";
        
        $operator = match($prefix) {
            '81', '82', '83', '84', '85' => 'Vodacom M-Pesa',
            '99', '98', '97' => 'Orange Money',
            '90', '91' => 'Airtel Money',
            '95', '96' => 'Africell Money',
            default => 'Opérateur inconnu'
        };
        
        echo "📡 Opérateur détecté: {$operator}\n";
        
        if ($operator === 'Vodacom M-Pesa') {
            echo "✅ Compatible M-Pesa - Test possible\n";
        } else {
            echo "⚠️ Pas M-Pesa - Test informatif seulement\n";
        }
    }
}

// Exécution du test
echo "🚀 Test M-Pesa RDC - VintApp\n";
echo "============================\n\n";

$rdcTestNumber = '826465399';
$normalized = normalizeCongoPhoneNumber($rdcTestNumber);

analyzeRDCOperator($normalized);
echo "\n";
testMPesaWithRealRDCNumber();

echo "\n✨ Test terminé !\n";
echo "\n💡 Note: En environnement sandbox, les vrais numéros RDC peuvent ne pas fonctionner.\n";
echo "Pour des tests réels, basculer vers MPESA_ENVIRONMENT=production\n";