<?php

namespace App\Http\Middleware;

use App\Models\AllowedCity;
use App\Models\Setting;
use App\Services\IpGeoLocationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
        'location/unauthorized',
        'preregistration',
        'preregistration/*',
        'test-geo',
        'auth/firebase/*',
        'firebase/*',
        'storage/*',
    ];

    public function __construct(
        protected IpGeoLocationService $ipGeo,
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locationRestrictionsEnabled = Setting::get('enable_location_restrictions', true);

        if (!$locationRestrictionsEnabled) {
            return $next($request);
        }

        if (app()->environment('local') && !env('ENABLE_GEO_TESTING', false)) {
            Log::info("🔓 Bypass localhost activé - Restrictions GPS désactivées en local");

            return $next($request);
        }

        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // L'email doit être vérifié avant la zone : ce middleware tourne avant EnsureEmailIsVerified.
        // Sans ce passage, on redirige vers la localisation et on ne atteint jamais verify-code.
        if ($request->user() && is_null($request->user()->email_verified_at)) {
            return $next($request);
        }

        foreach ($this->excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        $sessionKey = 'gps_location_validated';
        $userCity = session('user_city');

        if (session($sessionKey) && session('geo_access_via_ip') === true) {
            $cc = session('ip_geo_country');
            if (is_string($cc) && AllowedCity::countryCodeIsAllowed($cc)) {
                return $next($request);
            }

            Log::warning('⚠️ Geo IP: session pays invalide', ['country' => $cc]);
            session()->forget([
                'gps_location_validated',
                'user_city',
                'validated_at',
                'geo_access_via_ip',
                'ip_geo_country',
                'geo_ip_source',
                'gps_coords',
            ]);
        }

        if (session($sessionKey) && $userCity) {
            $cityStillValid = AllowedCity::active()
                ->where('name', $userCity)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->exists();

            if ($cityStillValid) {
                return $next($request);
            }

            Log::warning('⚠️ GPS: Ville de session invalide', [
                'ville' => $userCity,
                'raison' => 'Ville supprimée ou désactivée',
            ]);

            session()->forget([
                'gps_location_validated',
                'user_city',
                'validated_at',
                'geo_access_via_ip',
                'ip_geo_country',
                'geo_ip_source',
                'gps_coords',
            ]);
        }

        $loc = $this->ipGeo->resolve($request);
        if ($loc !== null) {
            if ($this->ipGeo->shouldDenyByCountry($loc)) {
                Log::info('🔒 Geo IP: pays non desservi', ['country' => $loc['country_code'] ?? null]);
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'VintApp n’est pas disponible dans votre région.',
                        'error' => 'region_not_served',
                        'redirect' => route('location.unauthorized', ['reason' => 'geo']),
                    ], 403);
                }

                return redirect()->route('location.unauthorized', ['reason' => 'geo']);
            }

            if ($this->ipGeo->tryEstablishSessionFromIp($loc)) {
                return $next($request);
            }
        }

        $userAgent = $request->header('User-Agent', 'unknown');
        $isIos = preg_match('/iPhone|iPad|iPod/i', $userAgent) === 1;

        Log::info('🔒 GPS: confirmation manuelle requise (géoloc IP non concluante)', [
            'ip' => $request->ip(),
            'uri' => $request->getRequestUri(),
            'user_agent' => $userAgent,
            'is_ios' => $isIos,
            'expects_json' => $request->expectsJson(),
        ]);

        $hint = null;
        if ($isIos) {
            $hint = 'iOS : si la confirmation automatique échoue, vérifiez les paramètres de localisation pour Safari ou la PWA.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Nous n’avons pas pu confirmer automatiquement votre zone. Affinez votre localisation depuis la page dédiée.',
                'error' => 'location_required',
                'redirect' => route('location.validate'),
                'user_agent' => $userAgent,
                'hint' => $hint,
            ], 403);
        }

        if ($hint) {
            session()->flash('geo_hint', $hint);
        }

        return redirect()->route('location.validate');
    }
}
