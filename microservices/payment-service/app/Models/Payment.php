<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'user_id',
        'buyer_id',
        'seller_id',
        'order_id',
        'wallet_id',
        'amount',
        'currency',
        'method',
        'provider_key',
        'status',
        'designation',
        'reference',
        'external_reference',
        'phone_number',
        'metadata',
        'error_message',
        'ip_address',
        'initiated_at',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'integer',
        'initiated_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
}
