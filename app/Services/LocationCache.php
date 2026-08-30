<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Vidage ciblé du cache lié à la géolocalisation.
 * Remplace l'ancien Cache::flush() qui purgeait tout le cache applicatif après
 * chaque modification des villes/régions autorisées.
 */
class LocationCache
{
    /** Préfixes de clés affectées par une modification des villes/régions autorisées. */
    protected const PREFIXES = ['ip_geo_v1:', 'city_search_', 'geocode_'];

    public static function clear(): void
    {
        $store = config('cache.default');

        if ($store === 'database') {
            $table = (string) config('cache.stores.database.table', 'cache');
            DB::table($table)
                ->where(function ($q) {
                    foreach (self::PREFIXES as $i => $prefix) {
                        $q->orWhere('key', 'like', '%' . $prefix . '%');
                    }
                })
                ->delete();

            return;
        }

        if ($store === 'file') {
            $path = (string) config('cache.stores.file.path');
            if (! is_dir($path)) {
                return;
            }

            foreach (glob($path . '/*') as $file) {
                $content = @file_get_contents($file);
                if (is_string($content)) {
                    foreach (self::PREFIXES as $prefix) {
                        if (str_contains($content, $prefix)) {
                            @unlink($file);
                            break;
                        }
                    }
                }
            }

            return;
        }

        // Drivers sans énumération de clés (redis, memcached…) : purge globale par prudence.
        Cache::flush();
    }
}