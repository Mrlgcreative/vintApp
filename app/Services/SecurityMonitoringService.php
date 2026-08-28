<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Events\NewNotification;
use App\Mail\MonitoringAlertMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Détection d'anomalies & alertes de sécurité.
 *
 * Analyse les données existantes (wallet, retraits, commandes, sessions,
 * performances) pour identifier les mouvements suspects et erreurs,
 * puis alerte automatiquement tous les administrateurs en temps réel
 * (notification in-app) et par email.
 */
class SecurityMonitoringService
{
    /**
     * Clé mémoire des alertes actives en cache.
     */
    protected const ACTIVE_KEY = 'monitoring:active_alerts';

    /**
     * Clé mémoire des dernières horodatages d'envoi (cooldown email).
     */
    protected const SENT_KEY = 'monitoring:sent_alerts';

    /**
     * Clé mémoire du dernier instant de détection (anti-spam / perf).
     */
    protected const LAST_RUN_KEY = 'monitoring:last_detect';

    /**
     * Fenêtre minimale entre deux détections (secondes). Évite de lancer
     * les agrégats SQL à chaque polling de 5s.
     */
    protected const DETECT_INTERVAL = 20;

    /**
     * Lance la détection complète des anomalies et renvoie la liste
     * des anomalies actives. Déclenche les alertes admin si besoin.
     */
    public function detectAndAlert(array $health = []): array
    {
        $lastRun = Cache::get(self::LAST_RUN_KEY, 0);

        // Retourne l'état actif sans relancer de requêtes si la détection
        // vient d'être exécutée il y a moins de DETECT_INTERVAL secondes.
        if (now()->timestamp - $lastRun < self::DETECT_INTERVAL) {
            return $this->getActive();
        }

        Cache::put(self::LAST_RUN_KEY, now()->timestamp, now()->addMinutes(60));

        $anomalies = $this->detectAnomalies($health);

        $this->persistActive($anomalies);
        $this->sendAdminAlerts($anomalies);

        return $this->getActive();
    }

    /**
     * Réinitialise les alertes actives.
     */
    public function resetAlerts(): void
    {
        Cache::forget(self::ACTIVE_KEY);
        Cache::forget(self::SENT_KEY);
        Cache::forget(self::LAST_RUN_KEY);
    }

    /**
     * Exécute tous les détecteurs et renvoie les anomalies actives.
     * Chaque détecteur est isolé : un échec ne bloque pas les autres.
     */
    public function detectAnomalies(array $health = []): array
    {
        $anomalies = [];

        foreach ([
            'wallet'   => fn() => $this->detectWalletAnomalies(),
            'orders'   => fn() => $this->detectOrderAnomalies(),
            'accounts' => fn() => $this->detectAccountAnomalies(),
            'errors'   => fn() => $this->detectErrorAnomalies(),
            'health'   => fn() => $this->detectHealthAnomalies($health),
        ] as $name => $detector) {
            try {
                $anomalies = array_merge($anomalies, $detector());
            } catch (\Throwable $e) {
                Log::warning('Monitoring: détecteur "'.$name.'" en échec: '.$e->getMessage());
            }
        }

        // Trie par sévérité (critical > warning > info)
        $order = ['critical' => 0, 'warning' => 1, 'info' => 2];
        usort($anomalies, fn($a, $b) => ($order[$a['severity']] ?? 3) <=> ($order[$b['severity']] ?? 3));

        return $anomalies;
    }

    /**
     * Alertes actuellement actives en cache.
     */
    public function getActive(): array
    {
        return Cache::get(self::ACTIVE_KEY, []);
    }

    /**
     * Nombre total d'alertes actives.
     */
    public function getActiveCount(): int
    {
        return count($this->getActive());
    }

    /* ------------------------------------------------------------------
     | Détecteurs
     |------------------------------------------------------------------ */

    /**
     * Retraits / transactions wallet suspects.
     */
    protected function detectWalletAnomalies(): array
    {
        $anomalies = [];
        $cfg = config('monitoring.wallet');

        // Retraits de montant élevé récents (24h)
        $largeWithdrawals = DB::table('withdrawal_requests')
            ->where('amount', '>=', $cfg['large_withdrawal_threshold'])
            ->where('created_at', '>=', now()->subHours(24))
            ->select('id', 'amount', 'currency', 'phone_number', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        if ($largeWithdrawals->isNotEmpty()) {
            $total = $largeWithdrawals->sum('amount');
            $anomalies[] = $this->make(
                'wallet_large_withdrawal',
                'large withdrawals',
                'warning',
                "Retraits élevés détectés (24h) : {$largeWithdrawals->count()} retrait(s) pour un total de {$total} " . ($largeWithdrawals->first()->currency ?? 'CDF'),
                [
                    'count' => $largeWithdrawals->count(),
                    'total' => $total,
                    'items' => $largeWithdrawals->map(fn($w) => [
                        'amount' => $w->amount,
                        'currency' => $w->currency,
                        'phone' => $w->phone_number,
                        'status' => $w->status,
                    ]),
                ]
            );
        }

        // Cascade de retraits échoués dans la fenêtre
        $window = now()->subMinutes(60);
        $failedWithdrawals = DB::table('withdrawal_requests')
            ->where('status', 'failed')
            ->where('created_at', '>=', $window)
            ->count();

        if ($failedWithdrawals >= $cfg['max_withdrawal_failures']) {
            $anomalies[] = $this->make(
                'wallet_withdrawal_failures',
                'withdrawal failures',
                'warning',
                "Cascade de retraits échoués ({$failedWithdrawals} en 1h) : vérifiez les paiements mobile money.",
                ['count' => $failedWithdrawals]
            );
        }

        return $anomalies;
    }

    /**
     * Commandes / paiements échoués ou annulés en cascade.
     */
    protected function detectOrderAnomalies(): array
    {
        $anomalies = [];
        $cfg = config('monitoring.orders');
        $window = now()->subMinutes($cfg['window_minutes']);

        $cancelled = DB::table('orders')
            ->whereIn('status', ['cancelled', 'refunded'])
            ->where('created_at', '>=', $window)
            ->count();

        if ($cancelled >= $cfg['max_cancelled']) {
            $anomalies[] = $this->make(
                'orders_cancellation_cascade',
                'order cancellations',
                'warning',
                "{$cancelled} commandes annulées ou remboursées en {$cfg['window_minutes']} min : pic anormal d'annulations.",
                ['count' => $cancelled, 'window_minutes' => $cfg['window_minutes']]
            );
        }

        return $anomalies;
    }

    /**
     * Comptes multiples depuis une même IP / appareil partagé suspect.
     */
    protected function detectAccountAnomalies(): array
    {
        $anomalies = [];
        $cfg = config('monitoring.login');
        $window = now()->subMinutes($cfg['window_minutes']);

        // Comptes distincts, actifs récemment, groupés par IP
        $groups = DB::table('user_sessions')
            ->join('users', 'users.id', '=', 'user_sessions.user_id')
            ->where('user_sessions.created_at', '>=', $window)
            ->whereNull('users.google_id')
            ->whereNull('users.apple_id')
            ->select(
                'user_sessions.ip_address',
                DB::raw('COUNT(DISTINCT user_sessions.user_id) as accounts'),
                DB::raw('GROUP_CONCAT(DISTINCT user_sessions.user_id) as user_ids')
            )
            ->groupBy('user_sessions.ip_address')
            ->havingRaw('COUNT(DISTINCT user_sessions.user_id) >= ?', [$cfg['max_accounts_per_ip']])
            ->orderByDesc('accounts')
            ->get();

        foreach ($groups as $group) {
            $anomalies[] = $this->make(
                'accounts_same_ip',
                'multiple accounts / IP',
                'warning',
                sprintf(
                    "%d comptes actifs depuis la même adresse IP (%s) en %d min : création de comptes groupée suspecte.",
                    $group->accounts,
                    $group->ip_address,
                    $cfg['window_minutes']
                ),
                ['ip' => $group->ip_address, 'accounts' => $group->accounts, 'user_ids' => $group->user_ids]
            );
        }

        return $anomalies;
    }

    /**
     * Erreurs applicatives récentes (via les métriques MonitoringService).
     */
    protected function detectErrorAnomalies(): array
    {
        $anomalies = [];
        $cfg = config('monitoring.errors');

        $monitoring = app(MonitoringService::class);
        $errorStats = $monitoring->getErrorStatsForAlerts();

        $recent = $errorStats['total_errors'] ?? 0;
        if ($recent >= $cfg['max_recent_errors']) {
            $anomalies[] = $this->make(
                'errors_application',
                'application errors',
                'critical',
                "{$recent} erreurs applicatives récentes détectées. Consultez le dashboard et le log des erreurs.",
                [
                    'count' => $recent,
                    'last_error' => $errorStats['last_error'] ?? null,
                ]
            );
        }

        return $anomalies;
    }

    /**
     * Santé dégradée (DB, cache, disque, charge).
     */
    protected function detectHealthAnomalies(array $health): array
    {
        $anomalies = [];
        if (empty($health) || !isset($health['status'])) {
            return $anomalies;
        }

        if ($health['status'] === 'unhealthy') {
            $failed = $this->failedChecks($health);
            $anomalies[] = $this->make(
                'health_unhealthy',
                'system unhealthy',
                'critical',
                "Le système est indisponible : " . implode(', ', $failed) . ".",
                ['checks' => $failed]
            );
        } elseif ($health['status'] === 'degraded') {
            $degraded = $this->failedChecks($health);
            $anomalies[] = $this->make(
                'health_degraded',
                'system degraded',
                'warning',
                "Le système est dégradé : " . implode(', ', $degraded) . ".",
                ['checks' => $degraded]
            );
        }

        return $anomalies;
    }

    /**
     * Liste des contrôles de santé non "ok".
     */
    protected function failedChecks(array $health): array
    {
        $failed = [];
        foreach ($health['checks'] ?? [] as $name => $check) {
            if (($check['status'] ?? 'ok') !== 'ok') {
                $failed[] = $name . ($check['error'] ?? '');
            }
            // Disque > 90% considéré comme problème
            if (($check['usage_percent'] ?? 0) >= 90) {
                $failed[] = $name . " ({$check['usage_percent']}%)";
            }
        }
        return $failed ?: ['contrôles santé non spécifiés'];
    }

    /* ------------------------------------------------------------------
     | Construction d'une anomalie
     |------------------------------------------------------------------ */

    protected function make(string $type, string $label, string $severity, string $message, array $context = []): array
    {
        return [
            'type' => $type,
            'label' => $label,
            'severity' => $severity,
            'message' => $message,
            'context' => $context,
            'first_seen' => now()->toIso8601String(),
            'last_seen' => now()->toIso8601String(),
        ];
    }

    /**
     * Met à jour l'état des alertes actives en cache en fusionnant
     * avec celles déjà présentes (conserve la première apparition).
     */
    protected function persistActive(array $anomalies): void
    {
        $ttl = config('monitoring.alert.active_ttl_minutes');
        $existing = $this->getActive();
        $now = now();

        if (empty($anomalies)) {
            Cache::forget(self::ACTIVE_KEY);
            return;
        }

        // Index des alertes actives existantes par type
        $byType = collect($existing)->keyBy('type');

        $active = collect($anomalies)->map(function ($anomaly) use ($byType, $now) {
            $prior = $byType->get($anomaly['type']);
            if ($prior) {
                $anomaly['first_seen'] = $prior['first_seen'] ?? $now->toIso8601String();
            }
            $anomaly['last_seen'] = $now->toIso8601String();
            return $anomaly;
        })->values()->all();

        Cache::put(self::ACTIVE_KEY, $active, now()->addMinutes($ttl));
    }

    /* ------------------------------------------------------------------
     | Alertes admin (in-app + email)
     |------------------------------------------------------------------ */

    /**
     * Envoie une notification + email aux administrateurs pour chaque
     * nouvelle anomalie (cooldown par type pour éviter le spam).
     */
    protected function sendAdminAlerts(array $anomalies): void
    {
        if (empty($anomalies)) {
            return;
        }

        $cooldown = config('monitoring.alert.email_cooldown_minutes');
        $sent = Cache::get(self::SENT_KEY, []);

        foreach ($anomalies as $anomaly) {
            $type = $anomaly['type'];
            $lastSent = $sent[$type] ?? 0;

            if ($lastSent && now()->timestamp - $lastSent < $cooldown * 60) {
                continue; // déjà alerté récemment pour ce type
            }

            $this->notifyAsMany($anomaly);
            $sent[$type] = now()->timestamp;
        }

        Cache::put(self::SENT_KEY, $sent, now()->addHours(24));
    }

    /**
     * Notifie tous les administrateurs : notification in-app (table
     * notifications + broadcast temps réel) et email d'alerte.
     */
    protected function notifyAsMany(array $anomaly): void
    {
        try {
            $admins = User::query()
                ->select('users.*')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->where('roles.slug', 'admin')
                ->get();

            if ($admins->isEmpty()) {
                Log::warning('Monitoring: aucun admin à alerter', ['type' => $anomaly['type']]);
                return;
            }

            $title = $this->buildTitle($anomaly);

            foreach ($admins as $admin) {
                // 1. Notification in-app (+ broadcast temps réel)
                $notification = Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'monitoring_alert',
                    'title' => $title,
                    'message' => $anomaly['message'],
                    'data' => [
                        'severity' => $anomaly['severity'],
                        'alert_type' => $anomaly['type'],
                        'url' => route('admin.monitoring.index'),
                    ],
                ]);

                try {
                    NewNotification::dispatch($notification);
                } catch (\Throwable $e) {
                    Log::error('Monitoring: échec broadcast notification: ' . $e->getMessage());
                }

                // 2. Email d'alerte
                if ($admin->email) {
                    try {
                        Mail::to($admin->email)->send(new MonitoringAlertMail($anomaly));
                    } catch (\Throwable $e) {
                        Log::error('Monitoring: échec envoi email alerte: ' . $e->getMessage());
                    }
                }
            }

            Log::info('Monitoring: alerte envoyée aux administrateurs', [
                'type' => $anomaly['type'],
                'severity' => $anomaly['severity'],
                'admins' => $admins->count(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Monitoring: échec envoi alerte admin: ' . $e->getMessage());
        }
    }

    /**
     * Titre de l'alerte selon la sévérité.
     */
    protected function buildTitle(array $anomaly): string
    {
        $severityLabel = match ($anomaly['severity']) {
            'critical' => 'Critique',
            'warning'  => 'Avertissement',
            default    => 'Information',
        };

        return "[{$severityLabel}] Alerte monitoring";
    }
}
