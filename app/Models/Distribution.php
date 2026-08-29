<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    /**
     * Parts distribuées d'une vente confirmée, pour l'audit.
     *
     * - beneficiary_type 'seller'  : beneficiary_id = ID d'utilisateur (le vendeur).
     * - beneficiary_type 'platform_commission' : beneficiary_id = ID d'un
     *   wallet entreprise (sous-wallet 'commission'), pas un ID d'utilisateur.
     *
     * Ne pas confondre avec un FK utilisateur : si le type n'est pas 'seller',
     * beneficiary_id référence un wallet.
     *
     * @var array<string>
     */
    protected $fillable = [
        'transaction_id',
        'beneficiary_id',
        'beneficiary_type',
        'amount',
        'percentage',
    ];
}
