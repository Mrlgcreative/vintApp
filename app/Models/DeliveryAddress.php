<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAddress extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'email',
        'city',
        'commune',
        'address',
        'latitude',
        'longitude',
        'notes',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Définir cette adresse comme adresse par défaut
     */
    public function setAsDefault(): void
    {
        // Retirer le statut par défaut des autres adresses de l'utilisateur
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Définir cette adresse comme par défaut
        $this->update(['is_default' => true]);
    }

    /**
     * Obtenir l'adresse complète formatée
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->commune}, {$this->city}";
    }

    /**
     * Vérifier si l'adresse a des coordonnées GPS
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Obtenir les coordonnées par défaut pour les villes du Congo
     */
    public function getDefaultCoordinatesAttribute(): array
    {
        $cityCoordinates = [
            'Kinshasa' => ['lat' => -4.325, 'lng' => 15.308],
            'Lubumbashi' => ['lat' => -11.655, 'lng' => 27.479],
            'Kolwezi' => ['lat' => -10.715556, 'lng' => 25.471389],
            'Kisangani' => ['lat' => 0.516, 'lng' => 25.191],
            'Bukavu' => ['lat' => -2.507, 'lng' => 28.842],
            'Goma' => ['lat' => -1.674, 'lng' => 29.227],
            'Kananga' => ['lat' => -5.896, 'lng' => 22.452],
            'Mbuji-Mayi' => ['lat' => -6.136, 'lng' => 23.590],
            'Likasi' => ['lat' => -10.982, 'lng' => 26.737],
            'Matadi' => ['lat' => -5.838, 'lng' => 13.463],
        ];

        $city = ucfirst(strtolower(trim($this->city)));
        return $cityCoordinates[$city] ?? ['lat' => -4.325, 'lng' => 15.308]; // Kinshasa par défaut
    }

    /**
     * Obtenir la latitude effective (coordonnées GPS ou par défaut selon la ville)
     */
    public function getEffectiveLatitudeAttribute(): float
    {
        return $this->latitude ?? $this->default_coordinates['lat'];
    }

    /**
     * Obtenir la longitude effective (coordonnées GPS ou par défaut selon la ville)
     */
    public function getEffectiveLongitudeAttribute(): float
    {
        return $this->longitude ?? $this->default_coordinates['lng'];
    }
}
