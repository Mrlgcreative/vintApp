<?php

return [
    /*
    |--------------------------------------------------------------------------
    | K-PAY — agrégateur Mobile Money
    |--------------------------------------------------------------------------
    |
    | Deux clés suffisent pour brancher l'agrégateur : une clé d'API et un
    | secret. La même URL de base sert le sandbox (clés kpay_test_*) et la
    | production (clés kpay_live_*).
    |
    | Authentification sortante : headers X-API-Key + X-Secret-Key.
    | La vérification des webhooks entrants est décrite séparément dans
    | config/payments.php (HMAC-SHA256 du corps brut).
    |
    | Doc : https://kpay.site/documentation
    |
    */

    'enabled' => (bool) env('KPAY_ENABLED', false),

    'environment' => env('KPAY_ENVIRONMENT', 'production'),

    'api_key' => env('KPAY_API_KEY'),

    'secret_key' => env('KPAY_SECRET_KEY'),

    'base_url' => env('KPAY_BASE_URL', 'https://admin.kpay.site'),

    // Opérateur utilisé par défaut en USSD quand aucun n'est précisé.
    'default_provider' => env('KPAY_DEFAULT_PROVIDER', 'VODACOM_MPESA_COD'),

    // URLs de retour par défaut pour le mode page hébergée (GATEWAY).
    'return_url' => env('KPAY_RETURN_URL'),

    'cancel_url' => env('KPAY_CANCEL_URL'),

    'timeout' => (int) env('KPAY_TIMEOUT', 30),
];
