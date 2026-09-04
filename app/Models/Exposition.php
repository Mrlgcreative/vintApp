<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Exposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'status',
        'starts_at',
        'ends_at',
        'is_featured',
        'views',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Vendeur qui expose ses articles.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Produits exposés.
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'exposition_item');
    }

    /**
     * Ajoute un slug unique avant la création.
     */
    protected static function booted(): void
    {
        static::creating(function (Exposition $exposition) {
            if (!$exposition->slug) {
                $base = Str::slug($exposition->title ?: 'exposition');
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i;
                    $i++;
                }
                $exposition->slug = $slug;
            }
        });
    }

    /**
     * Expositions actives manuellement (non en pause ni terminées).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Expositions actuellement ouvertes (statut + fenêtre de dates).
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->active()
            ->where(function (Builder $q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * L'exposition est-elle actuellement visible ?
     */
    public function isRunning(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }
        return true;
    }

    /**
     * Vrai si les dates ont été définies.
     */
    public function hasSchedule(): bool
    {
        return $this->starts_at !== null || $this->ends_at !== null;
    }

    /**
     * Libellé d'état lisible.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'paused' => 'En pause',
            'ended' => 'Terminée',
            default => 'Active',
        };
    }
}