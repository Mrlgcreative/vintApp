<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;
use App\Services\SettingService;

class ShareAppSettings
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
        // Partager les paramètres d'application avec toutes les vues
        View::share([
            'appName' => $this->settingService->getAppName(),
            'appLogo' => $this->settingService->getAppLogo(),
            'appFavicon' => $this->settingService->getAppFavicon(),
            'appDescription' => $this->settingService->getAppDescription(),
            'isMaintenanceMode' => $this->settingService->isMaintenanceMode(),
        ]);

        return $next($request);
    }
}
