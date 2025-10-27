<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Test OAuth M-Pesa avec vos Consumer Key/Secret
 */
function testMPesaOAuth() {
    $consumerKey = $_ENV['MPESA_API_KEY'];
    $consumerSecret = $_ENV['MPESA_API_SECRET'];
    $environment = $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox';

    echo "🔧 Configuration OAuth M-Pesa\n";
    echo "Consumer Key: " . substr($consumerKey, 0, 10) . "...\n";
    echo "Environment: {$environment}\n\n";

    // URL OAuth selon l'environnement
    $oauthUrl = $environment === 'production'
        ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    // Encoder les credentials en base64
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
    
    echo "📞 Appel OAuth: {$oauthUrl}\n";
    echo "Authorization: Basic " . substr($credentials, 0, 20) . "...\n\n";

    try {
        // Simulation d'une requête HTTP (sans Laravel Http facade)
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
        $error = curl_error($curl);
        
        curl_close($curl);

        if ($error) {
            throw new Exception("CURL Error: {$error}");
        }

        echo "🔄 Statut HTTP: {$httpCode}\n";
        echo "📥 Réponse brute:\n{$response}\n\n";

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            
            if (isset($data['access_token'])) {
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 'unknown';
                
                echo "✅ Token OAuth obtenu avec succès !\n";
                echo "Token: " . substr($accessToken, 0, 20) . "...\n";
                echo "Expire dans: {$expiresIn} secondes\n";
                
                return $accessToken;
            } else {
                echo "❌ Token non trouvé dans la réponse\n";
                echo "Réponse JSON: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "❌ Échec OAuth - Code {$httpCode}\n";
            
            if ($response) {
                $errorData = json_decode($response, true);
                if (isset($errorData['errorMessage'])) {
                    echo "Erreur: {$errorData['errorMessage']}\n";
                }
            }
        }

    } catch (Exception $e) {
        echo "💥 Exception: " . $e->getMessage() . "\n";
    }

    return null;
}

/**
 * Test complet avec simulation de B2C
 */
function testB2CSimulation($accessToken) {
    if (!$accessToken) {
        echo "⚠️ Pas de token, simulation annulée\n";
        return;
    }

    echo "\n🏦 Test simulation B2C Payment\n";
    echo "=================================\n";

    $shortcode = $_ENV['MPESA_SHORTCODE'];
    $passkey = $_ENV['MPESA_PASSKEY'];
    $environment = $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox';
    
    $b2cUrl = $environment === 'production'
        ? 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest'
        : 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';

    // Timestamp et password
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);

    $payload = [
        'InitiatorName' => 'VintApp',
        'SecurityCredential' => $password,
        'CommandID' => 'BusinessPayment',
        'Amount' => 1, // Test avec 1 USD
        'PartyA' => $shortcode,
        'PartyB' => '254708374149', // Numéro test Safaricom
        'Remarks' => 'Test VintApp OAuth',
        'QueueTimeOutURL' => 'https://example.com/timeout',
        'ResultURL' => 'https://example.com/result',
        'Occasion' => 'Test',
    ];

    echo "📞 Appel B2C: {$b2cUrl}\n";
    echo "Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

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
    curl_close($curl);

    echo "🔄 Statut HTTP: {$httpCode}\n";
    echo "📥 Réponse: {$response}\n";

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['ConversationID'])) {
            echo "✅ Requête B2C acceptée - ConversationID: {$data['ConversationID']}\n";
        }
    }
}

// Exécution des tests
echo "🚀 Test OAuth M-Pesa - VintApp\n";
echo "===============================\n\n";

$token = testMPesaOAuth();
testB2CSimulation($token);

echo "\n✨ Tests terminés !\n";