<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCallback extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'payment_id',
        'provider',
        'status',
        'amount',
        'currency',
        'phone_number',
        'callback_type',
        'external_transaction_id',
        'reference',
        'raw_payload',
        'parsed_data',
        'signature',
        'ip_address',
        'is_verified',
        'is_processed',
        'processed_at',
        'processing_error',
        'retry_count',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'parsed_data' => 'array',
        'is_verified' => 'boolean',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'amount' => 'integer',
        'retry_count' => 'integer',
    ];

    public function markAsVerified(): void
    {
        $this->forceFill(['is_verified' => true])->save();
    }

    public function markAsProcessed(): void
    {
        $this->forceFill(['is_processed' => true, 'processed_at' => now()])->save();
    }

    public function recordError(string $message): void
    {
        $this->forceFill(['processing_error' => $message])->save();
    }
}
