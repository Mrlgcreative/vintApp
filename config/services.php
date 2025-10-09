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

];

