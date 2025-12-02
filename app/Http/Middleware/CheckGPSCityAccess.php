<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AllowedCity;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class CheckGPSCityAccess
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
        'api/validate-location',
        'location/validate',
        'test-geo',
        'auth/firebase/*',
        'firebase/*',
        'storage/*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si les restrictions sont activées
        $locationRestrictionsEnabled = Setting::get('enable_location_restrictions', true);
        
        if (!$locationRestrictionsEnabled) {
            return $next($request);
        }

        // Bypass en environnement local (sauf mode test)
        if (app()->environment('local') && !env('ENABLE_GEO_TESTING', false)) {
            Log::info("🔓 Bypass localhost activé - Restrictions GPS désactivées en local");
            return $next($request);
        }

        // Bypass pour les administrateurs
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // Bypass pour les routes exclues
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Vérifier si l'utilisateur a déjà validé sa position GPS
        $sessionKey = 'gps_location_validated';
        $userCity = session('user_city');
        
        if (session($sessionKey) && $userCity) {
            // ✅ Vérifier que la ville de la session existe toujours et est active
            $cityStillValid = AllowedCity::active()
                ->where('name', $userCity)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->exists();
            
            if ($cityStillValid) {
                // Ville toujours valide, laisser passer
                return $next($request);
            }
            
            // ❌ Ville supprimée ou désactivée - invalider la session
            Log::warning('⚠️ GPS: Ville de session invalide', [
                'ville' => $userCity,
                'raison' => 'Ville supprimée ou désactivée'
            ]);
            
            session()->forget(['gps_location_validated', 'user_city', 'validated_at']);
        }

        // Rediriger vers la page de validation GPS
        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Veuillez autoriser la géolocalisation pour accéder à VintApp.",
                'error' => 'location_required',
                'redirect' => route('location.validate')
            ], 403);
        }

        return redirect()->route('location.validate');
    }
}
