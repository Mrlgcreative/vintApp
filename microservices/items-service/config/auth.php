<?php

/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
|
| items-service ne gère pas de session ni de sanctum local : l'identité est
| déléguée à auth-service via le middleware `service.identity`. Aucun guard
| n'est donc déclaré ici.
|
*/

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => 'users',
    ],

    'guards' => [],

    'providers' => [],

    'passwords' => [],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
