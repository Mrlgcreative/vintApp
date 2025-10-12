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
}
