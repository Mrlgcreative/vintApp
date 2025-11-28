<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function __construct(
        protected MonitoringService $monitoring
    ) {}

    /**
     * Dashboard de monitoring principal
     */
    public function index()
    {
        $stats = $this->monitoring->getRealTimeStats();
        $health = $this->monitoring->healthCheck();

        return view('admin.monitoring.dashboard', [
            'stats' => $stats,
            'health' => $health,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * API pour récupérer les stats en temps réel (AJAX)
     */
    public function stats()
    {
        return response()->json([
            'stats' => $this->monitoring->getRealTimeStats(),
            'health' => $this->monitoring->healthCheck(),
            'timestamp' => now()->toIso8601String(),
        ]);
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
