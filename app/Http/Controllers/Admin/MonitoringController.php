<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;

class MonitoringController extends Controller
{
    use ApiResponses;
    public function __construct(
        protected MonitoringService $monitoring
    ) {}

    /**
     * Dashboard de monitoring principal
     */
    public function index()
    {
        // Capture un point + diffuse l'événement pour rafraîchir les autres fenêtres
        $metrics = $this->monitoring->capture(true);

        return view('admin.monitoring.dashboard', [
            'stats' => $metrics['stats'],
            'health' => $metrics['health'],
            'timestamp' => $metrics['timestamp'],
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
        $metrics = $this->monitoring->capture(true);

        return response()->json($metrics);
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

        return redirect()->back()->with('success', 'Métriques réinitialisées avec succès');
    }
}
