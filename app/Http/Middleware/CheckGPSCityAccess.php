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
        'preregistration',
        'preregistration/*',
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
        // Log diagnostic pour aider à comprendre pourquoi un client (ex: iPhone)
        // ne parvient pas à valider sa position. On enregistre l'IP, l'user-agent
        // et si la requête attend du JSON. Ceci aidera l'analyse côté serveur.
        $userAgent = $request->header('User-Agent', 'unknown');
        $isIos = preg_match('/iPhone|iPad|iPod/i', $userAgent) === 1;

        Log::info('🔒 GPS: validation requise', [
            'ip' => $request->ip(),
            'uri' => $request->getRequestUri(),
            'user_agent' => $userAgent,
            'is_ios' => $isIos,
            'expects_json' => $request->expectsJson(),
        ]);

        $hint = null;
        if ($isIos) {
            $hint = 'iOS detected: verifyer les permissions de localisation pour Safari / l\'application PWA dans Réglages > Confidentialité > Service de localisation.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Veuillez autoriser la géolocalisation pour accéder à VintApp.",
                'error' => 'location_required',
                'redirect' => route('location.validate'),
                'user_agent' => $userAgent,
                'hint' => $hint,
            ], 403);
        }

        // En cas de redirection HTML, on ajoute un message flash pour aider
        // l'utilisateur et on redirige vers la page de validation.
        if ($hint) {
            session()->flash('geo_hint', $hint);
        }

        return redirect()->route('location.validate');
    }
}
