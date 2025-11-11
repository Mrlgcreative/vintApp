<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'buyer_id',
        'seller_id',
        'transaction_id',
        'refund_transaction_id',
        'refund_amount',
        'original_amount',
        'counter_offer_amount',
        'currency',
        'reason',
        'refund_type',
        'status',
        'evidence_photos',
        'admin_notes',
        'requested_at',
        'approved_at',
        'rejected_at',
        'completed_at',
        'processed_by'
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'counter_offer_amount' => 'decimal:2',
        'evidence_photos' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Accessors
     */
    public function getFormattedRefundAmountAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : 'FC';
        return $symbol . ' ' . number_format((float)$this->refund_amount, 2);
    }

    public function getFormattedOriginalAmountAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : 'FC';
        return $symbol . ' ' . number_format((float)$this->original_amount, 2);
    }

    public function getFormattedCounterOfferAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : 'FC';
        return $symbol . ' ' . number_format((float)$this->counter_offer_amount, 2);
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'negotiation' => 'En négociation',
            'completed' => 'Terminé',
            default => 'Inconnu'
        };
    }

    public function getStatusDisplayAttribute()
    {
        return $this->getStatusTextAttribute();
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'negotiation' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}