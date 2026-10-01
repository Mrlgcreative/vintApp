<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Opérateurs
    |--------------------------------------------------------------------------
    |
    | Chaque opérateur est décrit par la manière dont il authentifie ses
    | webhooks. `credential` est le nom de la variable d'env contenant le
    | secret attendu. Un secret vide ou un placeholder DEMO_* est traité
    | comme absent : le webhook est alors refusé.
    |
    */

    'providers' => [

        'mpesa' => [
            'label' => 'M-Pesa',
            'credential' => 'MPESA_CALLBACK_SECRET',
            'strategy' => 'hmac_body',
            'header' => 'X-Signature',
            'enabled' => (bool) env('MPESA_ENABLED', true),
        ],

        'orange_money' => [
            'label' => 'Orange Money',
            'credential' => 'ORANGE_CALLBACK_KEY',
            'strategy' => 'api_key_header',
            'header' => 'X-Api-Key',
            'enabled' => (bool) env('ORANGE_MONEY_ENABLED', true),
        ],

        'airtel_money' => [
            'label' => 'Airtel Money',
            'credential' => 'AIRTEL_CALLBACK_TOKEN',
            'strategy' => 'bearer',
            'header' => 'Authorization',
            'enabled' => (bool) env('AIRTEL_MONEY_ENABLED', true),
        ],

        'africell' => [
            'label' => 'Africell',
            'credential' => 'AFRICELL_CALLBACK_SECRET',
            'strategy' => 'payload_field',
            'field' => 'secret',
            'enabled' => (bool) env('AFRICELL_ENABLED', true),
        ],

        'cinetpay' => [
            'label' => 'CinetPay',
            'credential' => 'CINETPAY_SHOP_KEY',
            'strategy' => 'notify_receipt',
            'enabled' => (bool) env('CINETPAY_ENABLED', true),
        ],

        'maishapay' => [
            'label' => 'MaishaPay',
            'credential' => 'MAISHAPAY_API_KEY',
            'strategy' => 'bearer',
            'enabled' => (bool) env('MAISHAPAY_ENABLED', true),
        ],

        'pawapay' => [
            'label' => 'PawaPay',
            'credential' => 'PAWAPAY_SECRET',
            'strategy' => 'hmac_body',
            'header' => 'X-Signature',
            'enabled' => (bool) env('PAWAPAY_ENABLED', true),
        ],

        'afribapay' => [
            'label' => 'AfribaPay',
            'credential' => 'AFRIBAPAY_TOKEN',
            'strategy' => 'payload_field',
            'field' => 'token',
            'enabled' => (bool) env('AFRIBAPAY_ENABLED', true),
        ],

        'kpay' => [
            'label' => 'KPay',
            'credential' => 'KPAY_SECRET',
            'strategy' => 'hmac_body',
            'header' => 'X-KPay-Signature',
            'enabled' => (bool) env('KPAY_ENABLED', true),
        ],
    ],

    /*
    | Placeholders refusés même s'ils sont présents dans l'env : les
    | conserver en production ferait accepter n'importe quelle signature.
    */
    'placeholder_secrets' => [
        'DEMO_SECRET',
        'DEMO_KEY',
        'DEMO_TOKEN',
        'DEMO_CLIENT',
        'DEMO_MERCHANT',
        'DEMO_CODE',
        'DEMO',
        'CHANGE_ME',
        'CHANGEME',
    ],

    'replay_window_minutes' => (int) env('WEBHOOK_REPLAY_WINDOW_MINUTES', 10),

    'idempotency_window_minutes' => (int) env('WEBHOOK_IDEMPOTENCY_WINDOW_MINUTES', 1440),

    'auth_service' => [
        'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:8101'),
        'shared_secret' => env('AUTH_SERVICE_SHARED_SECRET'),
        'timeout' => (int) env('AUTH_SERVICE_TIMEOUT', 5),
    ],
];
