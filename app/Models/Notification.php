<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'action_url',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'new_message' => 'fa-comment',
            'new_order' => 'fa-shopping-cart',
            'order_shipped' => 'fa-truck',
            'order_delivered' => 'fa-check-circle',
            'discount_applied' => 'fa-tag',
            'discount_offered' => 'fa-percent',
            'item_favorited' => 'fa-heart',
            'item_sold' => 'fa-handshake',
            'item_approved' => 'fa-check-circle',
            'item_rejected' => 'fa-times-circle',
            'refund_approved' => 'fa-undo',
            'refund_rejected' => 'fa-ban',
            'refund_negotiation' => 'fa-handshake',
            'local_delivery_proposed' => 'fa-truck',
            'local_delivery_accepted' => 'fa-check',
            'local_delivery_in_transit' => 'fa-shipping-fast',
            'local_delivery_delivered' => 'fa-home',
            'local_delivery_cancelled' => 'fa-times',
            'wallet_credit' => 'fa-plus-circle',
            'wallet_debit' => 'fa-minus-circle',
            'boost_activated' => 'fa-rocket',
            'boost_expired' => 'fa-clock',
            'item_moderated' => 'fa-shield-alt',
            'review_received' => 'fa-star',
            'affiliate_commission' => 'fa-coins',
            'system' => 'fa-bell',
            default => 'fa-bell',
        };
    }

    public function getIconColorAttribute(): string
    {
        return match($this->type) {
            'new_message' => 'text-blue-500',
            'new_order', 'item_sold' => 'text-green-500',
            'discount_applied', 'discount_offered' => 'text-purple-500',
            'item_favorited' => 'text-red-500',
            'item_approved', 'order_delivered' => 'text-emerald-500',
            'item_rejected' => 'text-red-500',
            'refund_approved' => 'text-green-500',
            'refund_rejected' => 'text-red-500',
            'item_moderated' => 'text-orange-500',
            'wallet_credit' => 'text-emerald-500',
            'wallet_debit' => 'text-red-500',
            'boost_activated' => 'text-purple-500',
            'boost_expired' => 'text-yellow-500',
            'affiliate_commission' => 'text-yellow-500',
            default => 'text-blue-500',
        };
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getTitleAttribute(?string $value): ?string
    {
        return $value ?? $this->data['title'] ?? null;
    }

    public function getMessageAttribute(?string $value): ?string
    {
        return $value ?? $this->data['message'] ?? null;
    }

    public function getActionUrlAttribute(?string $value): ?string
    {
        return $value ?? $this->data['action_url'] ?? $this->data['url'] ?? null;
    }
}
