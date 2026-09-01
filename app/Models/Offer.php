<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'scope',
        'type',
        'value',
        'currency',
        'starts_at',
        'ends_at',
        'status',
        'max_redemptions',
        'redemption_count',
        'is_flash_sale',
        'is_featured',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_flash_sale' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Catégories ciblées par l'offre (scope 'categories').
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'offer_category');
    }

    /**
     * Produits ciblés par l'offre (scope 'items').
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'offer_item');
    }

    /**
     * Créateur de l'offre (admin ou vendeur).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Offres actives manuellement (non mises en pause).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Offres actuellement valides (statut actif + fenêtre de dates courante).
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
     * L'offre est-elle actuellement valide ?
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
        if ($this->max_redemptions && $this->redemption_count >= $this->max_redemptions) {
            return false;
        }
        return true;
    }

    /**
     * L'offre s'applique-t-elle à un produit donné ?
     */
    public function appliesTo(Item $item): bool
    {
        if (!$this->isRunning()) {
            return false;
        }

        return match ($this->scope) {
            'global' => true,
            'items' => $this->items()->where('item_id', $item->id)->exists(),
            'categories' => $item->category_id && $this->categories()->where('category_id', $item->category_id)->exists(),
            default => false,
        };
    }

    /**
     * Montant de réduction en devise pour un produit.
     */
    public function discountAmountFor(Item $item): float
    {
        $price = (float) $item->price;

        return $this->type === 'percent'
            ? round($price * ((float) $this->value / 100), 2)
            : min(round((float) $this->value, 2), $price);
    }

    /**
     * Prix final après réduction pour un produit.
     */
    public function discountPriceFor(Item $item): float
    {
        return round((float) $item->price - $this->discountAmountFor($item), 2);
    }

    /**
     * Afficher la réduction sous forme lisible (−20 % ou −5 000 CDF).
     */
    public function discountLabel(): string
    {
        if ($this->type === 'percent') {
            return '-' . rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.') . ' %';
        }
        return '-' . number_format((float) $this->value, 0, ',', ' ') . ' ' . $this->currency;
    }
}