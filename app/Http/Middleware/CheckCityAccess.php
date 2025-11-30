<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AllowedCity;
use App\Models\AllowedRegion;
use App\Models\Setting;
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
        // 🆕 VÉRIFIER D'ABORD SI LES RESTRICTIONS SONT ACTIVÉES
        $locationRestrictionsEnabled = Setting::get('enable_location_restrictions', true);
        
        if (!$locationRestrictionsEnabled) {
            // Restrictions désactivées : laisser passer tout le monde
            Log::info("Restrictions géographiques désactivées - Accès autorisé pour IP: {$request->ip()}");
            return $next($request);
        }
        
        // Désactiver le middleware si la variable d'environnement est définie (pour les tests)
        if (config('app.disable_geo_restriction', false)) {
            return $next($request);
        }

        // Bypass en environnement local pour le développement (SAUF si on veut tester)
        $enableTestingMode = env('ENABLE_GEO_TESTING', false);
        
        // En mode test, ne jamais bypass
        if (!$enableTestingMode && app()->environment('local') && $request->ip() === '127.0.0.1') {
            Log::info("🔓 Bypass localhost activé - Accès autorisé pour IP: 127.0.0.1");
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
        
        // 🧪 MODE TEST : Permettre de simuler une IP via query parameter
        $testIp = $request->query('test_ip');
        $enableTestingMode = env('ENABLE_GEO_TESTING', false);
        
        if ($enableTestingMode && $testIp) {
            $ip = $testIp;
            Log::info("🧪 MODE TEST : Simulation IP = {$ip}");
        }

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
            // 🧪 MODE TEST : Simuler des villes spécifiques
            $enableTestingMode = env('ENABLE_GEO_TESTING', false);
            
            if ($enableTestingMode && request()->query('test_city')) {
                $testCity = request()->query('test_city');
                $isAllowed = AllowedCity::isCityAllowed($testCity);
                
                Log::info("🧪 MODE TEST : Ville simulée = {$testCity}, Autorisée = " . ($isAllowed ? 'OUI' : 'NON'));
                
                return $isAllowed;
            }
            
            // Obtenir la localisation à partir de l'IP
            $position = Location::get($ip);

            if (!$position) {
                Log::warning("Impossible de déterminer la localisation pour l'IP: {$ip}");
                
                // 🧪 En mode test, bloquer si on ne peut pas détecter
                if ($enableTestingMode) {
                    Log::warning("🧪 MODE TEST : Localisation non détectée - BLOQUÉ");
                    return false;
                }
                
                // Par défaut, autoriser si on ne peut pas détecter
                return true;
            }

            $cityName = $position->cityName;
            $regionName = $position->regionName;
            $countryName = $position->countryName;

            Log::info("🌍 Vérification d'accès géographique", [
                'ip' => $ip,
                'ville' => $cityName,
                'région' => $regionName,
                'pays' => $countryName,
                'code_pays' => $position->countryCode
            ]);

            // Vérifier si le pays est la RDC
            $isDRC = stripos($countryName, 'Congo') !== false || 
                     stripos($countryName, 'Democratic Republic') !== false ||
                     $position->countryCode === 'CD';

            if (!$isDRC) {
                // Pour l'instant, bloquer les pays hors RDC (configurable plus tard)
                Log::warning("❌ Pays non autorisé: {$countryName}");
                return false;
            }

            // Vérifier si la ville est autorisée
            if ($cityName) {
                $cityAllowed = AllowedCity::isCityAllowed($cityName, $countryName);
                
                Log::info("🔍 Vérification ville", [
                    'ville_détectée' => $cityName,
                    'autorisée' => $cityAllowed ? 'OUI' : 'NON'
                ]);
                
                if ($cityAllowed) {
                    Log::info("✅ Accès autorisé pour la ville: {$cityName}");
                    return true;
                }
            }

            // Vérifier si la région est autorisée
            if ($regionName && AllowedRegion::isRegionAllowed($regionName)) {
                Log::info("✅ Accès autorisé pour la région: {$regionName}");
                return true;
            }

            Log::warning("❌ Accès refusé - Ville '{$cityName}' non autorisée");
            return false;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la vérification de localisation: {$e->getMessage()}");
            
            // 🧪 En mode test, bloquer en cas d'erreur
            $enableTestingMode = env('ENABLE_GEO_TESTING', false);
            if ($enableTestingMode) {
                Log::error("🧪 MODE TEST : Erreur - BLOQUÉ");
                return false;
            }
            
            // En cas d'erreur, autoriser par sécurité
            return true;
        }
    }
}
