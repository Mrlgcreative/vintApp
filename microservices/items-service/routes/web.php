<?php

/*
|--------------------------------------------------------------------------
| Sonde de vivacité
|--------------------------------------------------------------------------
|
| Route stateless : elle ne passe pas par le groupe `web`, donc aucune
| session n'est ouverte. Un service d'API n'a pas de session à maintenir.
|
*/
Route::get('/', fn () => response()->json([
    'success' => true,
    'message' => 'VintApp items-service',
    'data' => [
        'service' => 'items',
        'version' => 'v1',
    ],
]));
