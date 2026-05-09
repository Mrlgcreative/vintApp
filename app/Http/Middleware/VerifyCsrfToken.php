<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'payments/callback',  // Webhook des opérateurs mobile money (Orange, Airtel, M-Pesa, Africell, Illicocash)
        'wallet/withdrawals/webhook/*',  // Webhooks de décaissement mobile money
        'firebase/*',  // Firebase Auth routes (protégées par vérification idToken côté serveur)
        'auth/firebase/*',  // Firebase Auth alias routes
        'preregistration',  // Route de préinscription publique
    ];
}