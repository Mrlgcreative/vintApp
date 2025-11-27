<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'buyer_id',
        'seller_id',
        'order_id',
        'amount',
        'currency',
        'designation',
        'status',
        'payment_method',
        'method',
        'cpm_trans_id',
        'cpm_result',
        'cpm_trans_status',
        'payment_token',
        'cpm_amount',
        'metadata',
        'error_message',
        'paid_at',
        'ip_address',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cpm_amount' => 'decimal:2',
        'payment_details' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // CinetPay Helper Methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed' && $this->cpm_result === '00';
    }

    public function markAsCompleted(array $data = []): void
    {
        $this->update([
            'status' => 'completed',
            'cpm_result' => $data['cpm_result'] ?? '00',
            'cpm_trans_status' => $data['cpm_trans_status'] ?? 'ACCEPTED',
            'payment_method' => $data['payment_method'] ?? null,
            'cpm_amount' => $data['cpm_amount'] ?? $this->amount,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
