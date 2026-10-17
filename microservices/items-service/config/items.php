<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité inter-services
    |--------------------------------------------------------------------------
    |
    | items-service ne possède pas la table users : toute identité vient d'un
    | appel à auth-service (`/v1/token/introspect`). Aucun user_id n'est
    | accepté du client.
    |
    */

    'auth_service' => [
        'url' => env('ITEMS_AUTH_SERVICE_URL', env('AUTH_SERVICE_URL', '')),
        'shared_secret' => env('ITEMS_AUTH_SERVICE_SHARED_SECRET', env('AUTH_SERVICE_SHARED_SECRET')),
        'timeout' => (int) env('ITEMS_AUTH_SERVICE_TIMEOUT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | `max_per_page` borne une valeur fournie par le client : sans plafond, un
    | `?per_page=100000` transforme une route publique en vecteur de charge.
    |
    */

    'pagination' => [
        'per_page' => (int) env('ITEMS_PER_PAGE', 15),
        'max_per_page' => (int) env('ITEMS_MAX_PER_PAGE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Catalogue
    |--------------------------------------------------------------------------
    */

    'currency' => [
        'default' => env('ITEMS_DEFAULT_CURRENCY', 'USD'),
        'supported' => ['USD', 'CDF'],
    ],

    'storage' => [
        'disk' => env('ITEMS_STORAGE_DISK', 'public'),
    ],
];
