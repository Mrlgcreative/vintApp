<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalDelivery extends Model
{
    protected $fillable = [
        'order_id', 'seller_id', 'buyer_id',
        'delivery_type', 'status',
        'seller_latitude', 'seller_longitude', 'seller_address', 'seller_city', 'seller_commune',
        'buyer_latitude', 'buyer_longitude', 'buyer_address', 'buyer_city', 'buyer_commune',
        'meetup_latitude', 'meetup_longitude', 'meetup_address', 'meetup_landmark',
        'distance_km', 'delivery_fee', 'currency',
        'estimated_pickup_time', 'estimated_delivery_time',
        'actual_pickup_time', 'actual_delivery_time',
        'seller_phone', 'buyer_phone',
        'delivery_instructions', 'special_notes',
        'delivery_code', 'buyer_confirmed', 'seller_confirmed',
        'cancellation_reason'
    ];

    protected $casts = [
        'seller_latitude' => 'decimal:8',
        'seller_longitude' => 'decimal:8',
        'buyer_latitude' => 'decimal:8',
        'buyer_longitude' => 'decimal:8',
        'meetup_latitude' => 'decimal:8',
        'meetup_longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'buyer_confirmed' => 'boolean',
        'seller_confirmed' => 'boolean',
        'estimated_pickup_time' => 'datetime',
        'estimated_delivery_time' => 'datetime',
        'actual_pickup_time' => 'datetime',
        'actual_delivery_time' => 'datetime'
    ];

    /**
     * Relations
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Accessors
     */
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            'pending' => 'En attente',
            'accepted' => 'Accepté',
            'in_transit' => 'En transit',
            'delivered' => 'Livré',
            'cancelled' => 'Annulé'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getDeliveryTypeTextAttribute(): string
    {
        $types = [
            'hand_delivery' => 'Livraison à domicile',
            'pickup' => 'Récupération chez le vendeur',
            'meetup' => 'Point de rendez-vous'
        ];

        return $types[$this->delivery_type] ?? $this->delivery_type;
    }

    /**
     * Calculer la distance entre deux points GPS (formule Haversine)
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Rayon de la Terre en kilomètres

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Vérifier si la livraison est éligible (distance < 50km)
     */
    public function isEligibleForLocalDelivery(): bool
    {
        if (!$this->seller_latitude || !$this->seller_longitude || 
            !$this->buyer_latitude || !$this->buyer_longitude) {
            return false;
        }

        $distance = self::calculateDistance(
            $this->seller_latitude, 
            $this->seller_longitude,
            $this->buyer_latitude, 
            $this->buyer_longitude
        );

        return $distance <= 50; // Maximum 50km
    }

    /**
     * Générer un code de vérification pour la livraison
     */
    public function generateDeliveryCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Estimer les frais de livraison basés sur la distance
     */
    public function calculateDeliveryFee(): float
    {
        if (!$this->distance_km) {
            return 0;
        }

        // Tarification basique : 2$ pour les 5 premiers km, puis 0.5$ par km supplémentaire
        $baseFee = 2.00;
        $perKmFee = 0.50;
        $freeDistanceKm = 5;

        if ($this->distance_km <= $freeDistanceKm) {
            return $baseFee;
        }

        return $baseFee + (($this->distance_km - $freeDistanceKm) * $perKmFee);
    }

    /**
     * Obtenir la direction Google Maps
     */
    public function getGoogleMapsDirectionUrl(): string
    {
        if ($this->delivery_type === 'meetup' && $this->meetup_latitude && $this->meetup_longitude) {
            return "https://www.google.com/maps/dir/{$this->seller_latitude},{$this->seller_longitude}/{$this->meetup_latitude},{$this->meetup_longitude}";
        }

        return "https://www.google.com/maps/dir/{$this->seller_latitude},{$this->seller_longitude}/{$this->buyer_latitude},{$this->buyer_longitude}";
    }
}
