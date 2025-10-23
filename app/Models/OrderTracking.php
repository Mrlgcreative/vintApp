<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderTracking extends Model
{
    use HasFactory;

    protected $table = 'order_tracking';

    protected $fillable = [
        'order_id',
        'status',
        'latitude',
        'longitude',
        'address',
        'city',
        'country',
        'description',
        'tracking_code',
        'carrier',
        'customer_latitude',
        'customer_longitude',
        'customer_address',
        'customer_city',
        'customer_phone',
        'tracked_at',
        'estimated_delivery',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'customer_latitude' => 'decimal:8',
        'customer_longitude' => 'decimal:8',
        'tracked_at' => 'datetime',
        'estimated_delivery' => 'datetime',
    ];

    /**
     * Relation avec la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Obtenir la dernière position de tracking pour une commande
     */
    public static function getLatestForOrder($orderId)
    {
        return static::where('order_id', $orderId)
            ->orderBy('tracked_at', 'desc')
            ->first();
    }

    /**
     * Obtenir l'historique complet de tracking pour une commande
     */
    public static function getHistoryForOrder($orderId)
    {
        return static::where('order_id', $orderId)
            ->orderBy('tracked_at', 'asc')
            ->get();
    }

    /**
     * Calculer la distance entre deux points GPS (en km)
     * Utilise la formule de Haversine
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) {
            return null;
        }

        $earthRadius = 6371; // Rayon de la Terre en km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($earthRadius * $angle, 2);
    }

    /**
     * Obtenir la distance restante jusqu'au client
     */
    public function getDistanceToCustomerAttribute()
    {
        if (!$this->latitude || !$this->longitude || !$this->customer_latitude || !$this->customer_longitude) {
            return null;
        }

        return self::calculateDistance(
            $this->latitude,
            $this->longitude,
            $this->customer_latitude,
            $this->customer_longitude
        );
    }

    /**
     * Obtenir le texte du statut en français
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'picked_up' => 'Collecté',
            'in_transit' => 'En transit',
            'out_for_delivery' => 'En cours de livraison',
            'delivered' => 'Livré',
            'failed' => 'Échec de livraison',
            'returned' => 'Retourné',
            default => 'Statut inconnu'
        };
    }

    /**
     * Obtenir la classe de badge pour le statut
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-secondary',
            'picked_up' => 'bg-info',
            'in_transit' => 'bg-primary',
            'out_for_delivery' => 'bg-warning',
            'delivered' => 'bg-success',
            'failed' => 'bg-danger',
            'returned' => 'bg-dark',
            default => 'bg-secondary'
        };
    }

    /**
     * Obtenir l'icône pour le statut
     */
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'pending' => 'fa-clock',
            'picked_up' => 'fa-box',
            'in_transit' => 'fa-truck',
            'out_for_delivery' => 'fa-shipping-fast',
            'delivered' => 'fa-check-circle',
            'failed' => 'fa-times-circle',
            'returned' => 'fa-undo',
            default => 'fa-question-circle'
        };
    }

    /**
     * Formater la date de tracking
     */
    public function getFormattedTrackedAtAttribute()
    {
        return $this->tracked_at ? $this->tracked_at->format('d/m/Y H:i') : '-';
    }

    /**
     * Formater la date de livraison estimée
     */
    public function getFormattedEstimatedDeliveryAttribute()
    {
        return $this->estimated_delivery ? $this->estimated_delivery->format('d/m/Y H:i') : '-';
    }

    /**
     * Vérifier si la commande est en retard
     */
    public function getIsLateAttribute()
    {
        if (!$this->estimated_delivery || $this->status === 'delivered') {
            return false;
        }

        return now()->isAfter($this->estimated_delivery);
    }

    /**
     * Obtenir les coordonnées pour la carte
     */
    public function getMapCoordinatesAttribute()
    {
        return [
            'current' => [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
                'address' => $this->address,
            ],
            'destination' => [
                'lat' => (float) $this->customer_latitude,
                'lng' => (float) $this->customer_longitude,
                'address' => $this->customer_address,
            ]
        ];
    }
}
