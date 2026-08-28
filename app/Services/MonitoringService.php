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
            'realtime' => $this->getLiveMetrics(),
            'history' => $this->getHistoryStats(),
            'series' => $this->getSeriesData(),
        ];
    }

    /**
     * Métriques réellement en temps réel (non mises en cache).
     */
    protected function getLiveMetrics(): array
    {
        $now = now();

        return [
            'users_online' => $this->countUsersOnline(),
            'new_users_today' => DB::table('users')
                ->whereDate('created_at', today())
                ->count(),
            'orders_today' => DB::table('orders')
                ->whereDate('created_at', today())
                ->count(),
            'items_pending' => DB::table('items')
                ->where('status', 'pending')
                ->count(),
            'revenue_today_total' => $this->sumCurrency(
                DB::table('orders')
                    ->whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->get()
            ),
            'revenue_total' => $this->sumCurrency(
                DB::table('orders')
                    ->where('status', 'completed')
                    ->get()
            ),
            'load_avg' => $this->getSystemLoad(),
        ];
    }

    /**
     * Compte les utilisateurs actuellement connectés (sessions récentes).
     */
    protected function countUsersOnline(): int
    {
        try {
            return DB::table('user_sessions')
                ->where('last_activity', '>', now()->subMinutes(10))
                ->where('is_active', true)
                ->distinct('user_id')
                ->count('user_id');
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Charge système moyenne (réel + fallback 0 si indisponible).
     */
    protected function getSystemLoad(): ?array
    {
        if (function_exists('sys_getloadavg')) {
            $load = @sys_getloadavg();
            if (is_array($load) && count($load) >= 2) {
                return [
                    '1min' => round($load[0], 2),
                    '5min' => round($load[1], 2),
                ];
            }
        }

        return null;
    }

    /**
     * Calcule le total des revenus convertis en une devise de référence
     * (conservateur : retourne les montants bruts par devise si plusieurs).
     */
    protected function sumCurrency(iterable $orders): array
    {
        $totals = [];
        foreach ($orders as $order) {
            $currency = $order->currency ?? 'XAF';
            $amount = (float) ($order->total_amount ?? $order->total ?? 0);
            $totals[$currency] = ($totals[$currency] ?? 0) + $amount;
        }

        return $totals;
    }

    /**
     * Statistiques d'historique (agrégats sur plusieurs jours).
     */
    protected function getHistoryStats(): array
    {
        $days = 7;
        $start = now()->subDays($days - 1)->startOfDay();

        return [
            'users_last_7d' => DB::table('users')
                ->where('created_at', '>=', $start)
                ->count(),
            'orders_last_7d' => DB::table('orders')
                ->where('created_at', '>=', $start)
                ->count(),
            'revenue_last_7d' => $this->sumCurrency(
                DB::table('orders')
                    ->where('created_at', '>=', $start)
                    ->where('status', 'completed')
                    ->get()
            ),
        ];
    }

    /**
     * Série temporelle pour les graphiques (perf, revenus, erreurs).
     */
    protected function getSeriesData(): array
    {
        return [
            'performance' => $this->buildSeries('series:performance'),
            'revenue'     => $this->buildSeries('series:revenue'),
            'errors'      => $this->buildSeries('series:errors'),
            'users'       => $this->buildSeries('series:users'),
        ];
    }

    /**
     * Construit une série temporelle d'après les points stockés en cache.
     */
    protected function buildSeries(string $key): array
    {
        $points = Cache::get($key, []);

        // Garder un maximum de 60 points (1h à 1 point/minute)
        return array_slice($points, -60);
    }

    /**
     * Capture un instantané de métriques (enregistre un point
     * dans les séries temporelles et diffuse l'événement Pusher).
     */
    public function capture(bool $broadcast = true): array
    {
        $start = microtime(true);

        $stats = $this->getRealTimeStats();
        $health = $this->healthCheck();

        $this->pushSeriesPoint('series:performance', [
            'time' => now()->toIso8601String(),
            'value' => $stats['performance']['avg_response_time'],
        ]);

        $this->pushSeriesPoint('series:errors', [
            'time' => now()->toIso8601String(),
            'value' => $stats['errors']['total_errors'],
        ]);

        $this->pushSeriesPoint('series:users', [
            'time' => now()->toIso8601String(),
            'value' => $stats['realtime']['users_online'] ?? 0,
        ]);

        $this->pushSeriesPoint('series:revenue', [
            'time' => now()->toIso8601String(),
            'value' => $stats['realtime']['revenue_today_total'],
        ]);

        $metrics = [
            'stats' => $stats,
            'health' => $health,
            'timestamp' => now()->toIso8601String(),
            'capture_ms' => round((microtime(true) - $start) * 1000, 2),
        ];

        if ($broadcast) {
            $this->broadcast($metrics);
        }

        return $metrics;
    }

    /**
     * Pousse un point dans une série temporelle (10s d'intervalle max).
     */
    protected function pushSeriesPoint(string $key, array $point): void
    {
        $points = Cache::get($key, []);

        // Éviter de spammer : ne pas ajouter si le dernier point est < 8s
        $last = end($points);
        if ($last && isset($last['time'])
            && now()->diffInSeconds($last['time']) < 8
            && $key !== 'series:errors') {
            return;
        }

        $points[] = $point;
        $points = array_slice($points, -60);
        Cache::put($key, $points, now()->addMinutes(60));
    }

    /**
     * Diffuse l'événement de mise à jour du monitoring.
     */
    protected function broadcast(array $metrics): void
    {
        try {
            \App\Events\MonitoringUpdated::dispatch(
                $metrics['stats'],
                $metrics['health']
            );
        } catch (\Throwable $e) {
            // Ne pas faire échouer la capture si la diffusion échoue
        }
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
        Cache::forget('series:performance');
        Cache::forget('series:revenue');
        Cache::forget('series:errors');
        Cache::forget('series:users');
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
