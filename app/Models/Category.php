<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'parent_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relation avec les articles de cette catégorie.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Relation avec la catégorie parente.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relation avec les sous-catégories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Récupère toutes les sous-catégories actives.
     */
    public function activeChildren()
    {
        return $this->children()->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Vérifie si la catégorie a des sous-catégories.
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Vérifie si la catégorie est une sous-catégorie.
     */
    public function isChild()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Scope pour récupérer seulement les catégories actives.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour récupérer seulement les catégories principales (sans parent).
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Accesseur pour obtenir l'URL de la catégorie.
     */
    public function getUrlAttribute()
    {
        return route('categories.show', $this->slug ?? $this->id);
    }

    /**
     * Accesseur pour obtenir le nom complet (avec parent si applicable).
     */
    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Accesseur pour obtenir l'URL complète de l'image.
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // En développement local
        if (config('app.env') === 'local') {
            return asset('storage/' . $this->image);
        }

        // En production (Hostinger)
        // Vérifier si le symlink public/storage existe
        if (file_exists(public_path('storage'))) {
            return asset('storage/' . $this->image);
        }

        // Fallback: accès direct à storage/app/public
        return asset('storage/app/public/' . $this->image);
    }
}
