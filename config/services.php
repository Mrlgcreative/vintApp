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

];
