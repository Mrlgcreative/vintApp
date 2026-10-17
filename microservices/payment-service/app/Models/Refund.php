<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'payment_id',
        'amount',
        'currency',
        'reason',
        'status',
        'external_reference',
        'error_message',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'refunded_at' => 'datetime',
    ];
}
