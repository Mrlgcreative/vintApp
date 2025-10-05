<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class SettingService
{
    protected $cachePrefix = 'settings.';
    protected $cacheTtl = 3600; // 1 heure

    /**
     * Récupère une valeur de setting
     */
    public function get(string $key, $default = null)
    {
        return Cache::remember($this->cachePrefix . $key, $this->cacheTtl, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Définit une valeur de setting
     */
    public function set(string $key, $value, array $attributes = [])
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            array_merge($attributes, ['value' => $value])
        );

        // Vider le cache pour cette clé
        Cache::forget($this->cachePrefix . $key);
        
        return $setting;
    }

    /**
     * Récupère plusieurs settings
     */
    public function getMultiple(array $keys, $default = null): Collection
    {
        $result = collect();
        
        foreach ($keys as $key) {
            $result->put($key, $this->get($key, $default));
        }
        
        return $result;
    }

    /**
     * Définit plusieurs settings
     */
    public function setMultiple(array $settings): Collection
    {
        $result = collect();
        
        foreach ($settings as $key => $value) {
            $result->put($key, $this->set($key, $value));
        }
        
        return $result;
    }

    /**
     * Récupère tous les settings publics
     */
    public function getPublicSettings(): Collection
    {
        return Cache::remember($this->cachePrefix . 'public', $this->cacheTtl, function () {
            return Setting::where('is_public', true)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting->value];
                });
        });
    }

    /**
     * Récupère les settings par catégorie
     */
    public function getByCategory(string $category): Collection
    {
        return Cache::remember($this->cachePrefix . 'category.' . $category, $this->cacheTtl, function () use ($category) {
            return Setting::where('category', $category)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting->value];
                });
        });
    }

    /**
     * Récupère toutes les catégories disponibles
     */
    public function getCategories(): Collection
    {
        return Cache::remember($this->cachePrefix . 'categories', $this->cacheTtl, function () {
            return Setting::distinct('category')->pluck('category');
        });
    }

    /**
     * Récupère tous les settings pour l'admin
     */
    public function getAllForAdmin(): Collection
    {
        return Cache::remember($this->cachePrefix . 'admin.all', $this->cacheTtl, function () {
            return Setting::orderBy('category')->orderBy('key')->get();
        });
    }

    /**
     * Vérifie si un setting existe
     */
    public function exists(string $key): bool
    {
        return Cache::remember($this->cachePrefix . 'exists.' . $key, $this->cacheTtl, function () use ($key) {
            return Setting::where('key', $key)->exists();
        });
    }

    /**
     * Supprime un setting
     */
    public function delete(string $key): bool
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            $setting->delete();
            Cache::forget($this->cachePrefix . $key);
            Cache::forget($this->cachePrefix . 'exists.' . $key);
            $this->clearCategoryCache($setting->category);
            return true;
        }
        
        return false;
    }

    /**
     * Vide tout le cache des settings
     */
    public function clearCache(): void
    {
        $keys = Setting::pluck('key');
        
        foreach ($keys as $key) {
            Cache::forget($this->cachePrefix . $key);
            Cache::forget($this->cachePrefix . 'exists.' . $key);
        }
        
        Cache::forget($this->cachePrefix . 'public');
        Cache::forget($this->cachePrefix . 'admin.all');
        Cache::forget($this->cachePrefix . 'categories');
        
        $categories = Setting::distinct('category')->pluck('category');
        foreach ($categories as $category) {
            Cache::forget($this->cachePrefix . 'category.' . $category);
        }
    }

    /**
     * Vide le cache pour une catégorie spécifique
     */
    public function clearCategoryCache(string $category): void
    {
        Cache::forget($this->cachePrefix . 'category.' . $category);
        Cache::forget($this->cachePrefix . 'categories');
    }

    /**
     * Méthodes de convenance pour des types spécifiques
     */
    
    public function getBool(string $key, bool $default = false): bool
    {
        return (bool) $this->get($key, $default);
    }
    
    public function getInt(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }
    
    public function getFloat(string $key, float $default = 0.0): float
    {
        return (float) $this->get($key, $default);
    }
    
    public function getString(string $key, string $default = ''): string
    {
        return (string) $this->get($key, $default);
    }
    
    public function getArray(string $key, array $default = []): array
    {
        $value = $this->get($key, $default);
        return is_array($value) ? $value : $default;
    }

    /**
     * Méthodes pour des settings spécifiques de l'application
     */
    
    public function getAppName(): string
    {
        return $this->getString('app_name', 'VintApp');
    }
    
    public function getCommissionRate(): float
    {
        return $this->getFloat('commission_rate', 5.0);
    }
    
    public function getMinWithdrawalAmount(): int
    {
        return $this->getInt('min_withdrawal_amount', 10);
    }
    
    public function isMaintenanceMode(): bool
    {
        return $this->getBool('maintenance_mode', false);
    }
    
    public function isRegistrationEnabled(): bool
    {
        return $this->getBool('registration_enabled', true);
    }
    
    public function isPaymentEnabled(): bool
    {
        return $this->getBool('payment_enabled', true);
    }
    
    public function getMaxImagesPerItem(): int
    {
        return $this->getInt('max_images_per_item', 10);
    }
    
    public function getAppLogo(): string
    {
        return $this->getString('app_logo', '/images/logo.png');
    }
    
    public function getAppFavicon(): string
    {
        return $this->getString('app_favicon', '/favicon.ico');
    }
    
    public function getAppDescription(): string
    {
        return $this->getString('app_description', 'Plateforme de vente vintage');
    }
    
    public function getMaxFileSize(): int
    {
        return $this->getInt('max_file_size', 10);
    }
    
    public function getAllowedFileTypes(): array
    {
        $types = $this->getString('allowed_file_types', 'jpg,jpeg,png,gif,pdf');
        return array_map('trim', explode(',', $types));
    }
}