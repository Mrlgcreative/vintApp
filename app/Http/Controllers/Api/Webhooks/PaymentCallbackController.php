<?php

namespace App\Http\Controllers\Api\Webhooks;

/**
 * Webhooks de paiement (callbacks opérateurs, publics).
 * Hérite de PaymentCallbackController pour réutiliser toute la logique
 * (vérification de signature, parsing par provider, traitement).
 */
class PaymentCallbackController extends \App\Http\Controllers\PaymentCallbackController
{
}
