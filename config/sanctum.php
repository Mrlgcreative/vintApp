<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sanctum Configuration de Sécurité
    |--------------------------------------------------------------------------
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
        'localhost,localhost:3000,localhost:5173,127.0.0.1,127.0.0.1:8000,::1'
    )),

    'guard' => ['web'],

    'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 60 * 24 * 7), // 7 jours par défaut

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
