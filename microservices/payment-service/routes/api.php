<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhooks opérateurs
|--------------------------------------------------------------------------
|
| Authentifiés par la signature de l'opérateur, pas par un token : c'est le
| seul moyen fiable, l'opérateur n'a pas de compte chez nous.
|
*/
Route::post('webhooks/{provider}', [WebhookController::class, 'handle'])
    ->whereIn('provider', array_keys(config('payments.providers')))
    ->name('webhooks.handle');

/*
|--------------------------------------------------------------------------
| API authentifiée
|--------------------------------------------------------------------------
|
| L'identité est résolue via auth-service. Aucun user_id n'est accepté
| depuis le client.
|
*/
Route::middleware('service.identity')->group(function () {
    Route::get('payments', [PaymentController::class, 'index']);
    Route::post('payments', [PaymentController::class, 'store']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund']);
});
