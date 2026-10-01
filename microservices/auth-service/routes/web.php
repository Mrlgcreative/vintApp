<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'success' => true,
    'message' => 'VintApp auth-service',
    'data' => [
        'service' => 'auth',
        'version' => 'v1',
    ],
]));
