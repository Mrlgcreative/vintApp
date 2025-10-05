<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'label',
        'description',
        'is_public',
        'is_encrypted'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
    ];

    /**
     * Accessor pour la valeur avec décryptage automatique
     */
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                // Si le décryptage échoue, retourner la valeur brute
            }
        }
        
        return $this->castValue($value);
    }

    /**
     * Mutator pour la valeur avec cryptage automatique
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            $value = Crypt::encryptString($value);
        } else {
            $value = $this->prepareValueForStorage($value);
        }
        
        $this->attributes['value'] = $value;
    }

    /**
     * Cast la valeur selon son type
     */
    private function castValue($value)
    {
        if ($value === null) {
            return null;
        }

        return match ($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Prépare la valeur pour le stockage
     */
    private function prepareValueForStorage($value)
    {
        return match ($this->type) {
            'boolean' => $value ? '1' : '0',
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Récupère une valeur de setting avec cache
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Définit une valeur de setting
     */
    public static function set(string $key, $value, array $attributes = [])
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            array_merge($attributes, ['value' => $value])
        );

        // Vider le cache
        Cache::forget("setting.{$key}");
        
        return $setting;
    }

    /**
     * Récupère tous les settings publics
     */
    public static function getPublicSettings()
    {
        return Cache::remember('settings.public', 3600, function () {
            return static::where('is_public', true)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Récupère les settings par catégorie
     */
    public static function getByCategory(string $category)
    {
        return Cache::remember("settings.category.{$category}", 3600, function () use ($category) {
            return static::where('category', $category)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting->value];
                });
        });
    }

    /**
     * Vide tout le cache des settings
     */
    public static function clearCache()
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
        Cache::forget('settings.public');
        
        $categories = static::distinct('category')->pluck('category');
        foreach ($categories as $category) {
            Cache::forget("settings.category.{$category}");
        }
    }

    /**
     * Boot method pour vider le cache automatiquement
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
