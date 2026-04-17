<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'forex' => [
        'provider' => env('FOREX_API_PROVIDER', 'exchangerate-api'),
        'exchangerate_api_key' => env('EXCHANGERATE_API_KEY'),
        'base_currency' => env('FOREX_BASE_CURRENCY', 'USD'),
        'cache_duration' => env('FOREX_CACHE_DURATION', 3600),
        
        // URLs des différents providers
        'providers' => [
            'exchangerate-api' => [
                'url' => 'https://v6.exchangerate-api.com/v6',
                'free' => true,
            ],
            'fixer' => [
                'url' => 'https://api.fixer.io',
                'free' => false,
            ],
            'currencyapi' => [
                'url' => 'https://api.currencyapi.com/v3',
                'free' => true,
            ],
        ],
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
        'enabled' => env('GOOGLE_MAPS_ENABLED', true),
        'default_zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 6),
        'default_center' => [
            'lat' => env('GOOGLE_MAPS_DEFAULT_LAT', -4.0383),
            'lng' => env('GOOGLE_MAPS_DEFAULT_LNG', 21.7587),
        ],
        'apis' => [
            'maps' => true,        // Maps JavaScript API
            'places' => true,      // Places API (autocomplete)
            'geocoding' => true,   // Geocoding API
            'directions' => false, // Directions API (désactivé)
            'distance_matrix' => false, // Distance Matrix API (désactivé)
        ],
        'language' => 'fr',
        'region' => 'CD', // Code pays par défaut (RDC)
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI', env('APP_URL') . '/auth/apple/callback'),
    ],

    // Configuration M-Pesa OAuth
    'mpesa' => [
        'consumer_key' => env('MPESA_API_KEY'),
        'consumer_secret' => env('MPESA_API_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE', '174379'),
        'passkey' => env('MPESA_PASSKEY'),
        'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),
        'enabled' => env('MPESA_ENABLED', true),
        'api_url' => env('MPESA_ENVIRONMENT', 'sandbox') === 'production' 
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke',
    ],

    // Configuration autres opérateurs mobile money
    'orange_money' => [
        'api_key' => env('ORANGE_MONEY_API_KEY'),
        'api_secret' => env('ORANGE_MONEY_API_SECRET'),
        'enabled' => env('ORANGE_MONEY_ENABLED', true),
    ],

    'airtel_money' => [
        'client_id' => env('AIRTEL_MONEY_API_KEY'),
        'client_secret' => env('AIRTEL_MONEY_API_SECRET'),
        'enabled' => env('AIRTEL_MONEY_ENABLED', true),
    ],

    'africell' => [
        'merchant_id' => env('AFRICELL_API_KEY'),
        'api_secret' => env('AFRICELL_API_SECRET'),
        'enabled' => env('AFRICELL_ENABLED', true),
    ],

    'illicocash' => [
        'merchant_code' => env('ILLICOCASH_API_KEY'),
        'api_token' => env('ILLICOCASH_API_SECRET'),
        'enabled' => env('ILLICOCASH_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | CinetPay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | CinetPay is a payment gateway for African markets
    | Supporting multiple payment methods including Mobile Money and Cards
    |
    */
    'cinetpay' => [
        'site_id' => env('CINETPAY_SITE_ID'),
        'api_key' => env('CINETPAY_API_KEY'),
        'platform' => env('CINETPAY_PLATFORM', 'TEST'), // TEST or PROD
        'version' => env('CINETPAY_VERSION', 'V2'), // V1 or V2
    ],

    /*
    |--------------------------------------------------------------------------
    | AfribaPay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | AfribaPay is a payment gateway supporting Mobile Money across Africa
    | Supports: CD (CDF/USD), CI, BF, CM, GA, GN, ML, NE, SN, TG, BJ
    |
    */
    'afribapay' => [
        'token' => env('AFRIBAPAY_TOKEN'),
        'environment' => env('AFRIBAPAY_ENVIRONMENT', 'sandbox'), // sandbox or production
        'webhook_secret' => env('AFRIBAPAY_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour les notifications push via Firebase
    | Supporte: Web Push, Android, iOS
    |
    */
    'firebase' => [
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
        'credentials' => env('FIREBASE_CREDENTIALS', storage_path('firebase/serviceAccountKey.json')),
    ],

    /*
    |--------------------------------------------------------------------------
    | MaishaPay Configuration
    |--------------------------------------------------------------------------
    */
    'maishapay' => [
        'api_key' => env('MAISHAPAY_API_KEY'),
        'secret_key' => env('MAISHAPAY_SECRET_KEY'),
        'merchant_id' => env('MAISHAPAY_MERCHANT_ID'),
        'environment' => env('MAISHAPAY_ENVIRONMENT', 'sandbox'),
        'enabled' => env('MAISHAPAY_ENABLED', false),
    ],

];
