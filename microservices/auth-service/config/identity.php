<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contrat de service
    |--------------------------------------------------------------------------
    |
    | Réglages propres à auth-service : durée de vie des tokens, secret
    | partagé servant à authentifier les appels des autres microservices.
    | Le secret est volontairement hors de config/auth.php, qui est réservé
    | au framework.
    |
    */

    'token_ttl_days' => (int) env('AUTH_TOKEN_TTL_DAYS', 60),

    'two_factor_pending_ttl_minutes' => (int) env('AUTH_2FA_PENDING_TTL_MINUTES', 5),

    'service_shared_secret' => env('SERVICE_SHARED_SECRET'),
];
