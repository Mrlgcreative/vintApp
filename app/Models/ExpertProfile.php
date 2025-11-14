<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpertProfile extends Model
{
    use HasFactory;

    const LEVEL_JUNIOR = 'junior';
    const LEVEL_SENIOR = 'senior';
    const LEVEL_MASTER = 'master';

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
            self::LEVEL_JUNIOR => 'bg-green-100 text-green-800',
            self::LEVEL_SENIOR => 'bg-blue-100 text-blue-800', 
            self::LEVEL_MASTER => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getLevelIcon(): string
    {
        return match($this->certification_level) {
            self::LEVEL_JUNIOR => '🔰',
            self::LEVEL_SENIOR => '⭐',
            self::LEVEL_MASTER => '👑',
            default => '👤'
        };
    }

    public function getSpecialtiesText(): string
    {
        $specialtiesMap = [
            'mode_luxe' => 'Mode & Luxe',
            'electronique' => 'Électronique',
            'bijoux' => 'Bijoux',
            'montres' => 'Montres',
            'sacs_maroquinerie' => 'Sacs & Maroquinerie',
            'vetements-femmes' => 'Vêtements Femmes',
            'vetements-hommes' => 'Vêtements Hommes',
            'vareuse' => 'Vareuse',
            'general' => 'Généraliste'
        ];

        $formatted = [];
        foreach ($this->specialties ?? [] as $specialty) {
            $formatted[] = $specialtiesMap[$specialty] ?? ucfirst(str_replace('_', ' ', $specialty));
        }

        return implode(', ', $formatted);
    }
}