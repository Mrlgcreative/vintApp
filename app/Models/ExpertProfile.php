<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpertProfile extends Model
{
    use HasFactory;

    const LEVEL_BRONZE = 'bronze';
    const LEVEL_SILVER = 'silver';
    const LEVEL_GOLD = 'gold';

    protected $fillable = [
        'user_id',
        'specialties',
        'verification_count',
        'approval_rate',
        'certification_level',
        'is_active',
        'commission_rate',
        'bio',
        'credentials'
    ];

    protected $casts = [
        'specialties' => 'array',
        'is_active' => 'boolean',
        'approval_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2'
    ];

    /**
     * Relations
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ProductAuthenticityCheck::class, 'expert_id', 'user_id');
    }

    /**
     * Méthodes utilitaires
     */
    public function canHandleCategory(string $category): bool
    {
        return in_array($category, $this->specialties ?? []);
    }

    public function getLevelBadgeClass(): string
    {
        return match($this->certification_level) {
            self::LEVEL_BRONZE => 'bg-orange-100 text-orange-800',
            self::LEVEL_SILVER => 'bg-gray-100 text-gray-800',
            self::LEVEL_GOLD => 'bg-yellow-100 text-yellow-800',
            default => 'bg-blue-100 text-blue-800'
        };
    }

    public function getLevelIcon(): string
    {
        return match($this->certification_level) {
            self::LEVEL_BRONZE => '🥉',
            self::LEVEL_SILVER => '🥈',
            self::LEVEL_GOLD => '🥇',
            default => '👤'
        };
    }

    public function getSpecialtiesText(): string
    {
        $specialtiesMap = [
            'mode_luxe' => 'Mode Luxe',
            'electronique' => 'Électronique',
            'bijoux' => 'Bijoux',
            'montres' => 'Montres',
            'sacs_maroquinerie' => 'Sacs & Maroquinerie',
            'chaussures' => 'Chaussures',
            'art' => 'Art & Objets de Collection'
        ];

        $formatted = [];
        foreach ($this->specialties ?? [] as $specialty) {
            $formatted[] = $specialtiesMap[$specialty] ?? ucfirst(str_replace('_', ' ', $specialty));
        }

        return implode(', ', $formatted);
    }
}