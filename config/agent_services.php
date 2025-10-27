<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des Agents Mobile Money
    |--------------------------------------------------------------------------
    |
    | Configuration pour le décaissement via les agents mobile money.
    | Chaque opérateur a sa propre configuration d'API pour les agents.
    |
    */

    'orange_money_agent' => [
        'enabled' => env('ORANGE_MONEY_AGENT_ENABLED', false),
        'api_url' => env('ORANGE_MONEY_AGENT_API_URL', 'https://api.orange.com/orange-money-agents/cd/v1'),
        'agent_key' => env('ORANGE_MONEY_AGENT_AGENT_KEY'),
        'api_key' => env('ORANGE_MONEY_AGENT_API_KEY'),
        'webhook_secret' => env('ORANGE_AGENT_WEBHOOK_SECRET'),
    ],

    'airtel_money_agent' => [
        'enabled' => env('AIRTEL_MONEY_AGENT_ENABLED', false),
        'api_url' => env('AIRTEL_MONEY_AGENT_API_URL', 'https://openapiuat.airtel.africa/merchant/v1/agents'),
        'client_id' => env('AIRTEL_MONEY_AGENT_CLIENT_ID'),
        'client_secret' => env('AIRTEL_MONEY_AGENT_CLIENT_SECRET'),
        'webhook_token' => env('AIRTEL_AGENT_WEBHOOK_TOKEN'),
    ],

    'mpesa_agent' => [
        'enabled' => env('MPESA_AGENT_ENABLED', false),
        'api_url' => env('MPESA_AGENT_API_URL', 'https://api.vodacom.cd/mpesa/agent/v1'),
        'api_key' => env('MPESA_AGENT_API_KEY'),
        'agent_code' => env('MPESA_AGENT_AGENT_CODE'),
        'service_code' => env('MPESA_AGENT_SERVICE_CODE'),
        'webhook_secret' => env('MPESA_AGENT_WEBHOOK_SECRET'),
    ],

    'africell_agent' => [
        'enabled' => env('AFRICELL_AGENT_ENABLED', false),
        'api_url' => env('AFRICELL_AGENT_API_URL', 'https://api.africell.cd/agent/v1'),
        'agent_id' => env('AFRICELL_AGENT_AGENT_ID'),
        'api_secret' => env('AFRICELL_AGENT_API_SECRET'),
        'webhook_secret' => env('AFRICELL_AGENT_WEBHOOK_SECRET'),
    ],

    'illicocash_agent' => [
        'enabled' => env('ILLICOCASH_AGENT_ENABLED', false),
        'api_url' => env('ILLICOCASH_AGENT_API_URL', 'https://api.illicocash.com/agent/v1'),
        'agent_code' => env('ILLICOCASH_AGENT_AGENT_CODE'),
        'api_token' => env('ILLICOCASH_AGENT_API_TOKEN'),
        'webhook_secret' => env('ILLICOCASH_AGENT_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Générale des Agents
    |--------------------------------------------------------------------------
    */
    'general' => [
        'webhook_base_url' => env('AGENT_WEBHOOK_BASE_URL', env('APP_URL') . '/wallet/withdrawals/webhook'),
        'timeout' => env('AGENT_API_TIMEOUT', 30), // secondes
        'retry_attempts' => env('AGENT_API_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('AGENT_API_RETRY_DELAY', 2), // secondes
    ],

    /*
    |--------------------------------------------------------------------------
    | Mapping des Préfixes de Numéros vers Opérateurs
    |--------------------------------------------------------------------------
    */
    'phone_prefixes' => [
        '84' => 'orange_money',
        '85' => 'orange_money',
        '89' => 'orange_money',
        '81' => 'mpesa',
        '82' => 'mpesa', 
        '83' => 'mpesa',
        '97' => 'airtel_money',
        '98' => 'airtel_money',
        '99' => 'airtel_money',
        '90' => 'africell',
        '91' => 'africell',
        '92' => 'africell',
        '93' => 'africell',
    ],
];