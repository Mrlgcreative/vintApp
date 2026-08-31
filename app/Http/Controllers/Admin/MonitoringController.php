<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use App\Services\SecurityMonitoringService;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;

class MonitoringController extends Controller
{
    use ApiResponses;
    public function __construct(
        protected MonitoringService $monitoring,
        protected SecurityMonitoringService $security
    ) {}

    /**
     * Dashboard de monitoring principal
     */
    public function index()
    {
        // Détection d'anomalies + alertes automatiques (avant capture pour
        // que l'événement temps réel embarque aussi les alertes actives).
        $metrics = $this->monitoring->capture(false);
        $alerts = $this->security->detectAndAlert($metrics['health']);

        $this->monitoring->captureWithPusher($metrics, $alerts);

        return view('admin.monitoring.dashboard', [
            'stats' => $metrics['stats'],
            'health' => $metrics['health'],
            'timestamp' => $metrics['timestamp'],
            'alerts' => $alerts,
            'securityAttempts' => \App\Models\SecurityLoginAttempt::recent(50),
            'suspiciousIps' => \App\Models\SecurityLoginAttempt::suspiciousIps(
                (int) config('monitoring.brute_force.max_failures_per_ip'),
                (int) config('monitoring.brute_force.window_minutes')
            ),
            'pusher' => [
                'enabled' => config('broadcasting.default') === 'pusher',
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster', 'us2'),
            ],
        ]);
    }

    /**
     * API pour récupérer les stats en temps réel (AJAX).
     * Capture un point + diffuse l'événement Pusher pour les autres onglets.
     */
    public function stats()
    {
        $metrics = $this->monitoring->capture(false);
        $alerts = $this->security->detectAndAlert($metrics['health']);

        $this->monitoring->captureWithPusher($metrics, $alerts);

        return response()->json(array_merge($metrics, [
            'alerts' => $alerts,
            'security_attempts' => \App\Models\SecurityLoginAttempt::recent(50),
            'suspicious_ips' => \App\Models\SecurityLoginAttempt::suspiciousIps(
                (int) config('monitoring.brute_force.max_failures_per_ip'),
                (int) config('monitoring.brute_force.window_minutes')
            ),
        ]));
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        $health = $this->monitoring->healthCheck();
        $statusCode = $health['status'] === 'healthy' ? 200 : 503;

        return response()->json($health, $statusCode);
    }

    /**
     * Réinitialiser les métriques
     */
    public function reset()
    {
        $this->monitoring->resetMetrics();
        $this->security->resetAlerts();

        return redirect()->back()->with('success', 'Métriques réinitialisées avec succès');
    }
}
