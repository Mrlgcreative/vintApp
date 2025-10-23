<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'delivery_address_id',
        'seller_id',
        'item_id',
        'quantity',
        'unit_price',
        'total_amount',
        'currency',
        'status',
        'shipping_address',
        'shipping_city',
        'shipping_phone',
        'notes',
        'tracking_number',
        'scan_token',
        'scanned_at',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'confirmed_by_buyer_at',
        'buyer_confirmation_note',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'confirmed_by_buyer_at' => 'datetime',
        'scanned_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Y') . '-' . strtoupper(Str::random(8));
            }
            // Générer un token unique pour le scan QR code
            if (empty($order->scan_token)) {
                $order->scan_token = Str::random(32);
            }
        });
    }
    
    /**
     * Obtenir l'URL de scan du QR code
     */
    public function getScanUrlAttribute()
    {
        return route('orders.scan', ['token' => $this->scan_token]);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the delivery address for this order
     */
    public function deliveryAddress()
    {
        return $this->belongsTo(DeliveryAddress::class, 'delivery_address_id');
    }

    /**
     * Get the tracking information for this order
     */
    public function trackings()
    {
        return $this->hasMany(OrderTracking::class)->orderBy('tracked_at', 'desc');
    }

    /**
     * Get the latest tracking information
     */
    public function latestTracking()
    {
        return $this->hasOne(OrderTracking::class)->latestOfMany('tracked_at');
    }

    /**
     * Get the formatted total amount with currency
     */
    public function getFormattedTotalAmountAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : 'FC';
        return $symbol . ' ' . number_format((float)$this->total_amount, 2);
    }

    /**
     * Get the formatted unit price with currency
     */
    public function getFormattedUnitPriceAttribute()
    {
        $symbol = $this->currency === 'USD' ? '$' : 'FC';
        return $symbol . ' ' . number_format((float)$this->unit_price, 2);
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'confirmed' => 'bg-info',
            'shipped' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the status text
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => 'Inconnu'
        };
    }
}
