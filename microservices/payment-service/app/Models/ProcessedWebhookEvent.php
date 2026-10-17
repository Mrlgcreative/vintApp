<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedWebhookEvent extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'dedupe_key',
        'provider',
        'payment_callback_id',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
}
