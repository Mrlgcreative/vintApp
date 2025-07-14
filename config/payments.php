<?php

return [
    'providers' => [
        'illicocash' => [
            'enabled' => env('ILLICOCASH_ENABLED', true),
            'api_key' => env('ILLICOCASH_API_KEY'),
            'api_secret' => env('ILLICOCASH_API_SECRET'),
        ],
        'orange_money' => [
            'enabled' => env('ORANGE_MONEY_ENABLED', true),
            'api_key' => env('ORANGE_MONEY_API_KEY'),
            'api_secret' => env('ORANGE_MONEY_API_SECRET'),
        ],
        'airtel_money' => [
            'enabled' => env('AIRTEL_MONEY_ENABLED', true),
            'api_key' => env('AIRTEL_MONEY_API_KEY'),
            'api_secret' => env('AIRTEL_MONEY_API_SECRET'),
        ],
        'mpesa' => [
            'enabled' => env('MPESA_ENABLED', true),
            'api_key' => env('MPESA_API_KEY'),
            'api_secret' => env('MPESA_API_SECRET'),
        ],
        'africell' => [
            'enabled' => env('AFRICELL_ENABLED', true),
            'api_key' => env('AFRICELL_API_KEY'),
            'api_secret' => env('AFRICELL_API_SECRET'),
        ],
    ],
]; 