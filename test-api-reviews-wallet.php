<?php
/**
 * Test API Reviews & Wallet Routes
 * 
 * Tests all API endpoints for ReviewController and WalletController
 * Expects 401 Unauthorized since we're not authenticated
 */

$baseUrl = 'http://localhost:8000/api/v1';

// ANSI colors
$red = "\033[31m";
$green = "\033[32m";
$yellow = "\033[33m";
$blue = "\033[34m";
$reset = "\033[0m";

echo $blue . "╔══════════════════════════════════════════════╗\n";
echo "║   TEST API REVIEWS & WALLET ROUTES v1.0     ║\n";
echo "╚══════════════════════════════════════════════╝" . $reset . "\n\n";

$tests = [
    // ==================== REVIEWS API ====================
    [
        'name' => 'Reviews - List All',
        'url' => '/reviews',
        'method' => 'GET'
    ],
    [
        'name' => 'Reviews - List by Item',
        'url' => '/reviews/item/1',
        'method' => 'GET'
    ],
    [
        'name' => 'Reviews - List by Seller',
        'url' => '/reviews/seller/1',
        'method' => 'GET'
    ],
    [
        'name' => 'Reviews - Create',
        'url' => '/reviews',
        'method' => 'POST',
        'data' => [
            'order_id' => 1,
            'rating' => 5,
            'comment' => 'Excellent produit !'
        ]
    ],
    [
        'name' => 'Reviews - Update',
        'url' => '/reviews/1',
        'method' => 'PUT',
        'data' => [
            'rating' => 4,
            'comment' => 'Très bon produit'
        ]
    ],
    [
        'name' => 'Reviews - Delete',
        'url' => '/reviews/1',
        'method' => 'DELETE'
    ],

    // ==================== WALLET API ====================
    [
        'name' => 'Wallet - Get User Wallets',
        'url' => '/wallet',
        'method' => 'GET'
    ],
    [
        'name' => 'Wallet - Get Transactions',
        'url' => '/wallet/transactions',
        'method' => 'GET'
    ],
    [
        'name' => 'Wallet - Add Funds',
        'url' => '/wallet/add-funds',
        'method' => 'POST',
        'data' => [
            'wallet_id' => 1,
            'amount' => 50,
            'payment_method' => 'orange_money'
        ]
    ],
    [
        'name' => 'Wallet - Withdraw',
        'url' => '/wallet/withdraw',
        'method' => 'POST',
        'data' => [
            'wallet_id' => 1,
            'amount' => 25,
            'phone_number' => '0812345678',
            'payment_method' => 'orange_money'
        ]
    ],
    [
        'name' => 'Wallet - Convert Currency',
        'url' => '/wallet/convert',
        'method' => 'POST',
        'data' => [
            'from_wallet_id' => 1,
            'to_wallet_id' => 2,
            'amount' => 10
        ]
    ],
];

$results = [
    'passed' => 0,
    'failed' => 0,
    'total' => count($tests)
];

echo $yellow . "Running " . $results['total'] . " tests...\n" . $reset . "\n";

foreach ($tests as $index => $test) {
    $testNumber = $index + 1;
    echo "[Test {$testNumber}/{$results['total']}] {$test['name']}... ";

    $url = $baseUrl . $test['url'];
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    
    // IMPORTANT: Add Accept header for JSON response
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);

    switch ($test['method']) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if (isset($test['data'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test['data']));
            }
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if (isset($test['data'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test['data']));
            }
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Extract headers and body
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);

    // We expect 401 Unauthorized for protected routes
    if ($httpCode === 401) {
        echo $green . "✓ PASS" . $reset . " (401 Unauthorized)\n";
        $results['passed']++;
    } else {
        echo $red . "✗ FAIL" . $reset . " (Expected 401, got {$httpCode})\n";
        $results['failed']++;
        
        // Show response for debugging
        if ($httpCode !== 401) {
            $bodyPreview = substr($body, 0, 200);
            echo $yellow . "   Response: " . $bodyPreview . "\n" . $reset;
        }
    }
}

echo "\n" . $blue . "╔══════════════════════════════════════════════╗\n";
echo "║              TEST SUMMARY                   ║\n";
echo "╚══════════════════════════════════════════════╝" . $reset . "\n";
echo "Total Tests:  {$results['total']}\n";
echo $green . "Passed:       {$results['passed']}\n" . $reset;

if ($results['failed'] > 0) {
    echo $red . "Failed:       {$results['failed']}\n" . $reset;
} else {
    echo "Failed:       {$results['failed']}\n";
}

$successRate = ($results['passed'] / $results['total']) * 100;
echo "Success Rate: " . number_format($successRate, 1) . "%\n\n";

if ($results['failed'] === 0) {
    echo $green . "🎉 All routes are properly protected with authentication!" . $reset . "\n\n";
} else {
    echo $red . "⚠️  Some routes may have authentication issues." . $reset . "\n\n";
}

exit($results['failed'] > 0 ? 1 : 0);
