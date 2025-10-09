<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AllowedCity;
use App\Models\AllowedRegion;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckCityAccess
{
    /**
     * Routes exclues de la vérification géographique
     */
    protected $excludedRoutes = [
        'admin/*',
        'login',
        'logout',
        'register',
        'password/*',
        'city-restricted',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Désactiver le middleware si la variable d'environnement est définie (pour les tests)
        if (config('app.disable_geo_restriction', false)) {
            return $next($request);
        }

        // Bypass en environnement local pour le développement
        if (app()->environment('local') && $request->ip() === '127.0.0.1') {
            return $next($request);
        }

        // Bypass pour les administrateurs authentifiés
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // Bypass pour les routes exclues
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Récupérer l'IP de l'utilisateur
        $ip = $request->ip();

        // Cache la vérification pour 1 heure par IP
        $cacheKey = "location_access_{$ip}";
        
        $isAllowed = Cache::remember($cacheKey, 3600, function () use ($ip) {
            return $this->checkLocationAccess($ip);
        });

        if (!$isAllowed) {
            Log::warning("Accès bloqué pour l'IP: {$ip}");
            
            // Rediriger vers la page d'accès restreint
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "VintApp n'est pas encore disponible dans ta ville.",
                    'error' => 'city_restricted'
                ], 403);
            }

            return response()->view('errors.city_restricted', [], 403);
        }

        return $next($request);
    }

    /**
     * Vérifier si la localisation est autorisée
     */
    protected function checkLocationAccess($ip): bool
    {
        try {
            // Obtenir la localisation à partir de l'IP
            $position = Location::get($ip);

            if (!$position) {
                Log::warning("Impossible de déterminer la localisation pour l'IP: {$ip}");
                // Par défaut, autoriser si on ne peut pas détecter
                return true;
            }

            $cityName = $position->cityName;
            $regionName = $position->regionName;
            $countryName = $position->countryName;

            Log::info("Vérification d'accès - IP: {$ip}, Ville: {$cityName}, Région: {$regionName}, Pays: {$countryName}");

            // Vérifier si le pays est la RDC
            $isDRC = stripos($countryName, 'Congo') !== false || 
                     stripos($countryName, 'Democratic Republic') !== false ||
                     $position->countryCode === 'CD';

            if (!$isDRC) {
                // Pour l'instant, bloquer les pays hors RDC (configurable plus tard)
                Log::info("Pays non autorisé: {$countryName}");
                return false;
            }

            // Vérifier si la ville est autorisée
            if ($cityName && AllowedCity::isCityAllowed($cityName)) {
                return true;
            }

            // Vérifier si la région est autorisée
            if ($regionName && AllowedRegion::isRegionAllowed($regionName)) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la vérification de localisation: {$e->getMessage()}");
            // En cas d'erreur, autoriser par sécurité
            return true;
        }
    }
}
