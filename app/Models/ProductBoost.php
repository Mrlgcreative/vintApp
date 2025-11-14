<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBoost extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'boost_type_id',
        'duration',
        'total_price',
        'activated_at',
        'expires_at',
        'cancelled_at',
        'refund_amount',
        'status',
        'views_generated',
        'clicks_generated',
        'metadata'
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    // Relations
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boostType()
    {
        return $this->belongsTo(BoostType::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('activated_at', '<=', now())
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere(function($q) {
                        $q->where('status', 'active')
                          ->where('expires_at', '<=', now());
                    });
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Méthodes d'assistance
    public function isActive()
    {
        return $this->status === 'active' 
               && $this->activated_at <= now() 
               && $this->expires_at > now();
    }

    public function isExpired()
    {
        return $this->status === 'expired' 
               || ($this->status === 'active' && $this->expires_at <= now());
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function getRemainingTimeAttribute()
    {
        if (!$this->isActive()) {
            return 0;
        }
        
        return $this->expires_at->diffInSeconds(now());
    }

    public function getRemainingTimeForHumans()
    {
        if (!$this->isActive()) {
            return 'Expiré';
        }
        
        return $this->expires_at->diffForHumans();
    }

    public function getProgressPercentage()
    {
        if (!$this->activated_at || !$this->expires_at) {
            return 0;
        }
        
        $total = $this->activated_at->diffInSeconds($this->expires_at);
        $elapsed = $this->activated_at->diffInSeconds(now());
        
        if ($total <= 0) {
            return 100;
        }
        
        $progress = ($elapsed / $total) * 100;
        return min(100, max(0, $progress));
    }

    public function calculateRefundAmount()
    {
        if (!$this->isActive()) {
            return 0;
        }
        
        $now = now();
        $activatedAt = $this->activated_at;
        $expiresAt = $this->expires_at;
        
        // Temps écoulé depuis l'activation (en heures)
        $elapsedHours = $activatedAt->diffInHours($now);
        
        // Durée totale du boost (en heures)
        $totalHours = $activatedAt->diffInHours($expiresAt);
        
        // Si annulation dans les 24h: remboursement complet
        if ($elapsedHours <= 24) {
            return $this->total_price;
        }
        
        // Calculer le pourcentage de temps écoulé
        $timeElapsedRatio = $elapsedHours / $totalHours;
        
        // Si plus de 50% du temps est écoulé: pas de remboursement
        if ($timeElapsedRatio >= 0.5) {
            return 0;
        }
        
        // Remboursement partiel: 50% du temps restant
        $remainingTimeRatio = 1 - $timeElapsedRatio;
        return round($this->total_price * $remainingTimeRatio * 0.5, 2);
    }

    // Auto-expire les boosts
    public function checkAndExpire()
    {
        if ($this->status === 'active' && $this->expires_at <= now()) {
            $this->update(['status' => 'expired']);
            return true;
        }
        
        return false;
    }

    // Activate boost
    public function activate()
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
            'expires_at' => now()->addDays($this->duration)
        ]);
    }

    // Cancel boost
    public function cancel()
    {
        $refundAmount = $this->calculateRefundAmount();
        
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'refund_amount' => $refundAmount
        ]);
        
        return $refundAmount;
    }

    // Increment views
    public function incrementViews($count = 1)
    {
        $this->increment('views_generated', $count);
    }

    // Increment clicks
    public function incrementClicks($count = 1)
    {
        $this->increment('clicks_generated', $count);
    }
}
    