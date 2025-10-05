<?php

use App\Services\SettingService;

if (!function_exists('setting')) {
    /**
     * Récupère ou définit une valeur de setting
     * 
     * @param string|array|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        $settingService = app(SettingService::class);
        
        if (is_null($key)) {
            return $settingService;
        }
        
        if (is_array($key)) {
            return $settingService->getMultiple($key, $default);
        }
        
        return $settingService->get($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Récupère plusieurs settings ou tous les settings publics
     * 
     * @param array|null $keys
     * @param mixed $default
     * @return \Illuminate\Support\Collection
     */
    function settings(array $keys = null, $default = null)
    {
        $settingService = app(SettingService::class);
        
        if (is_null($keys)) {
            return $settingService->getPublicSettings();
        }
        
        return $settingService->getMultiple($keys, $default);
    }
}

if (!function_exists('setting_bool')) {
    /**
     * Récupère une valeur de setting comme boolean
     * 
     * @param string $key
     * @param bool $default
     * @return bool
     */
    function setting_bool(string $key, bool $default = false): bool
    {
        return app(SettingService::class)->getBool($key, $default);
    }
}

if (!function_exists('setting_int')) {
    /**
     * Récupère une valeur de setting comme integer
     * 
     * @param string $key
     * @param int $default
     * @return int
     */
    function setting_int(string $key, int $default = 0): int
    {
        return app(SettingService::class)->getInt($key, $default);
    }
}

if (!function_exists('setting_float')) {
    /**
     * Récupère une valeur de setting comme float
     * 
     * @param string $key
     * @param float $default
     * @return float
     */
    function setting_float(string $key, float $default = 0.0): float
    {
        return app(SettingService::class)->getFloat($key, $default);
    }
}

if (!function_exists('setting_string')) {
    /**
     * Récupère une valeur de setting comme string
     * 
     * @param string $key
     * @param string $default
     * @return string
     */
    function setting_string(string $key, string $default = ''): string
    {
        return app(SettingService::class)->getString($key, $default);
    }
}

if (!function_exists('setting_array')) {
    /**
     * Récupère une valeur de setting comme array
     * 
     * @param string $key
     * @param array $default
     * @return array
     */
    function setting_array(string $key, array $default = []): array
    {
        return app(SettingService::class)->getArray($key, $default);
    }
}

if (!function_exists('app_name')) {
    /**
     * Récupère le nom de l'application
     * 
     * @return string
     */
    function app_name(): string
    {
        return app(SettingService::class)->getAppName();
    }
}

if (!function_exists('commission_rate')) {
    /**
     * Récupère le taux de commission
     * 
     * @return float
     */
    function commission_rate(): float
    {
        return app(SettingService::class)->getCommissionRate();
    }
}

if (!function_exists('min_withdrawal_amount')) {
    /**
     * Récupère le montant minimum de retrait
     * 
     * @return int
     */
    function min_withdrawal_amount(): int
    {
        return app(SettingService::class)->getMinWithdrawalAmount();
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Vérifie si l'application est en mode maintenance
     * 
     * @return bool
     */
    function is_maintenance_mode(): bool
    {
        return app(SettingService::class)->isMaintenanceMode();
    }
}

if (!function_exists('is_registration_enabled')) {
    /**
     * Vérifie si l'inscription est activée
     * 
     * @return bool
     */
    function is_registration_enabled(): bool
    {
        return app(SettingService::class)->isRegistrationEnabled();
    }
}

if (!function_exists('is_payment_enabled')) {
    /**
     * Vérifie si les paiements sont activés
     * 
     * @return bool
     */
    function is_payment_enabled(): bool
    {
        return app(SettingService::class)->isPaymentEnabled();
    }
}

if (!function_exists('max_images_per_item')) {
    /**
     * Récupère le nombre maximum d'images par article
     * 
     * @return int
     */
    function max_images_per_item(): int
    {
        return app(SettingService::class)->getMaxImagesPerItem();
    }
}

if (!function_exists('settings_by_category')) {
    /**
     * Récupère les settings par catégorie
     * 
     * @param string $category
     * @return \Illuminate\Support\Collection
     */
    function settings_by_category(string $category)
    {
        return app(SettingService::class)->getByCategory($category);
    }
}

if (!function_exists('app_logo')) {
    /**
     * Récupère le chemin du logo de l'application
     * 
     * @return string
     */
    function app_logo(): string
    {
        return setting_string('app_logo', '/images/logo.png');
    }
}

if (!function_exists('app_favicon')) {
    /**
     * Récupère le chemin du favicon
     * 
     * @return string
     */
    function app_favicon(): string
    {
        return setting_string('app_favicon', '/favicon.ico');
    }
}

if (!function_exists('app_description')) {
    /**
     * Récupère la description de l'application
     * 
     * @return string
     */
    function app_description(): string
    {
        return setting_string('app_description', 'Plateforme de vente vintage');
    }
}

if (!function_exists('max_upload_size')) {
    /**
     * Récupère la taille maximale d'upload en MB
     * 
     * @return int
     */
    function max_upload_size(): int
    {
        return setting_int('max_file_size', 10);
    }
}

if (!function_exists('allowed_file_types')) {
    /**
     * Récupère les types de fichiers autorisés
     * 
     * @return array
     */
    function allowed_file_types(): array
    {
        $types = setting_string('allowed_file_types', 'jpg,jpeg,png,gif,pdf');
        return array_map('trim', explode(',', $types));
    }
}