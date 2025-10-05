<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DynamicConfigMiddleware
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Charger les paramètres publics pour les vues
            $publicSettings = $this->settingService->getPublicSettings();
            View::share('appSettings', $publicSettings);

            // Appliquer certains paramètres dynamiques à la configuration Laravel
            $this->applyDynamicConfig();

            // Vérifier le mode maintenance
            if ($this->settingService->isMaintenanceMode() && !$request->is('admin/*')) {
                return response()->view('maintenance', [], 503);
            }

        } catch (\Exception $e) {
            // En cas d'erreur, continuer sans les paramètres dynamiques
            Log::warning('Erreur lors du chargement des paramètres dynamiques: ' . $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Applique les paramètres dynamiques à la configuration Laravel
     */
    private function applyDynamicConfig()
    {
        // Nom de l'application
        if ($appName = $this->settingService->get('app_name')) {
            Config::set('app.name', $appName);
        }

        // Email de contact
        if ($contactEmail = $this->settingService->get('contact_email')) {
            Config::set('mail.from.address', $contactEmail);
        }

        // Limite de l'API
        if ($apiLimit = $this->settingService->get('api_rate_limit')) {
            Config::set('throttle.api', $apiLimit . ',1');
        }

        // Paramètres de session
        if ($sessionLifetime = $this->settingService->get('session_lifetime')) {
            Config::set('session.lifetime', $sessionLifetime);
        }
    }
}
