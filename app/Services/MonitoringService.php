<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service de Monitoring et Métriques
 * 
 * Collecte et stocke les métriques de performance et business
 */
class MonitoringService
{
    /**
     * Enregistrer une métrique de performance
     */
    public function recordPerformance(string $operation, float $duration, array $context = []): void
    {
        $metric = [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toIso8601String(),
            'context' => $context,
        ];

        // Logger si la performance est mauvaise (> 1 seconde)
        if ($duration > 1) {
            Log::channel('performance')->warning('Slow operation detected', $metric);
        }

        // Stocker en cache pour le dashboard
        $this->storeMetric('performance', $metric);
    }

    /**
     * Enregistrer une métrique business
     */
    public function recordBusinessMetric(string $event, $value, array $context = []): void
    {
        $metric = [
            'event' => $event,
            'value' => $value,
            'timestamp' => now()->toIso8601String(),
            'context' => $context,
        ];

        Log::channel('business')->info('Business event', $metric);
        $this->storeMetric('business', $metric);
    }

    /**
     * Enregistrer une erreur
     */
    public function recordError(\Throwable $exception, array $context = []): void
    {
        $error = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
            'context' => $context,
        ];

        Log::channel('errors')->error('Application error', $error);
        $this->storeMetric('errors', $error);
    }

    /**
     * Obtenir les statistiques en temps réel
     */
    public function getRealTimeStats(): array
    {
        return [
            'database' => $this->getDatabaseStats(),
            'cache' => $this->getCacheStats(),
            'performance' => $this->getPerformanceStats(),
            'business' => $this->getBusinessStats(),
            'errors' => $this->getErrorStats(),
        ];
    }

    /**
     * Statistiques de la base de données
     */
    protected function getDatabaseStats(): array
    {
        return Cache::remember('monitoring:db_stats', 60, function () {
            // Récupérer les revenus par devise
            $revenuesByCurrency = DB::table('orders')
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->select('currency', DB::raw('SUM(total_amount) as total'))
                ->groupBy('currency')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->currency => $item->total];
                })
                ->toArray();

            return [
                'total_users' => DB::table('users')->count(),
                'active_items' => DB::table('items')->where('status', 'approved')->count(),
                'pending_orders' => DB::table('orders')->where('status', 'pending')->count(),
                'total_orders_today' => DB::table('orders')
                    ->whereDate('created_at', today())
                    ->count(),
                'revenue_today' => $revenuesByCurrency,
            ];
        });
    }

    /**
     * Statistiques du cache
     */
    protected function getCacheStats(): array
    {
        $hits = Cache::get('monitoring:cache_hits', 0);
        $misses = Cache::get('monitoring:cache_misses', 0);
        $total = $hits + $misses;

        return [
            'hits' => $hits,
            'misses' => $misses,
            'hit_rate' => $total > 0 ? round(($hits / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Statistiques de performance
     */
    protected function getPerformanceStats(): array
    {
        $metrics = $this->getMetrics('performance', 100);
        
        if (empty($metrics)) {
            return [
                'avg_response_time' => 0,
                'slow_operations' => 0,
                'total_operations' => 0,
            ];
        }

        $durations = array_column($metrics, 'duration_ms');
        
        return [
            'avg_response_time' => round(array_sum($durations) / count($durations), 2),
            'max_response_time' => max($durations),
            'min_response_time' => min($durations),
            'slow_operations' => count(array_filter($durations, fn($d) => $d > 1000)),
            'total_operations' => count($metrics),
        ];
    }

    /**
     * Statistiques business
     */
    protected function getBusinessStats(): array
    {
        $metrics = $this->getMetrics('business', 100);
        
        $events = [];
        foreach ($metrics as $metric) {
            $event = $metric['event'] ?? 'unknown';
            $events[$event] = ($events[$event] ?? 0) + 1;
        }

        return [
            'total_events' => count($metrics),
            'events_by_type' => $events,
            'last_event' => $metrics[0] ?? null,
        ];
    }

    /**
     * Statistiques des erreurs
     */
    protected function getErrorStats(): array
    {
        $metrics = $this->getMetrics('errors', 50);
        
        return [
            'total_errors' => count($metrics),
            'last_error' => $metrics[0] ?? null,
            'error_types' => array_count_values(array_column($metrics, 'message')),
        ];
    }

    /**
     * Stocker une métrique en cache
     */
    protected function storeMetric(string $type, array $metric): void
    {
        $key = "monitoring:{$type}";
        $metrics = Cache::get($key, []);
        
        // Ajouter au début du tableau
        array_unshift($metrics, $metric);
        
        // Garder seulement les 100 dernières
        $metrics = array_slice($metrics, 0, 100);
        
        Cache::put($key, $metrics, now()->addHours(24));
    }

    /**
     * Récupérer les métriques stockées
     */
    protected function getMetrics(string $type, int $limit = 100): array
    {
        $key = "monitoring:{$type}";
        $metrics = Cache::get($key, []);
        
        return array_slice($metrics, 0, $limit);
    }

    /**
     * Incrémenter un compteur de cache
     */
    public function incrementCacheHits(): void
    {
        Cache::increment('monitoring:cache_hits');
    }

    /**
     * Incrémenter les cache misses
     */
    public function incrementCacheMisses(): void
    {
        Cache::increment('monitoring:cache_misses');
    }

    /**
     * Réinitialiser toutes les métriques
     */
    public function resetMetrics(): void
    {
        Cache::forget('monitoring:performance');
        Cache::forget('monitoring:business');
        Cache::forget('monitoring:errors');
        Cache::forget('monitoring:cache_hits');
        Cache::forget('monitoring:cache_misses');
        Cache::forget('monitoring:db_stats');
    }

    /**
     * Vérifier la santé du système
     */
    public function healthCheck(): array
    {
        $health = [
            'status' => 'healthy',
            'checks' => [],
        ];

        // Vérifier la base de données
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = ['status' => 'ok'];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['database'] = [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }

        // Vérifier le cache
        try {
            Cache::put('health_check', true, 1);
            $health['checks']['cache'] = ['status' => 'ok'];
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['cache'] = [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }

        // Vérifier l'espace disque
        $freeSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        $health['checks']['disk'] = [
            'status' => $usagePercent < 90 ? 'ok' : 'warning',
            'usage_percent' => round($usagePercent, 2),
            'free_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
        ];

        if ($usagePercent >= 95) {
            $health['status'] = 'unhealthy';
        } elseif ($usagePercent >= 90) {
            $health['status'] = 'degraded';
        }

        return $health;
    }
}
