<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'transaction_id',
        'beneficiary_id',
        'beneficiary_type',
        'amount',
        'percentage',
    ];
}
