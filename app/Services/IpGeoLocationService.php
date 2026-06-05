<?php

namespace App\Services;

use App\Models\AllowedCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Résolution pays / coordonnées approximatives à partir de l'IP (sans interaction utilisateur).
 * Cloudflare (CF-IPCountry) en priorité, puis ip-api.com pour enrichir lat/lon (cache 24 h).
 */
class IpGeoLocationService
{
    public const CACHE_TTL_SECONDS = 86400;

    /** Rayon plus large que le GPS navigateur : la géoloc IP est imprécise. */
    public const IP_MATCH_RADIUS_KM = 450.0;

    /**
     * @return array{country_code: string, city: ?string, latitude: ?float, longitude: ?float, source: string}|null
     */
    public function resolve(Request $request): ?array
    {
        $ip = $request->ip();
        if (!$this->isPublicIp($ip)) {
            return null;
        }

        return Cache::remember($this->cacheKey($ip), self::CACHE_TTL_SECONDS, function () use ($request, $ip) {
            $fromApi = $this->fetchIpApi($ip);

            $cf = $request->header('CF-IPCountry');
            $cf = $cf && strtoupper(trim($cf)) !== 'XX' && strlen(trim($cf)) === 2
                ? strtoupper(trim($cf))
                : null;

            if ($cf !== null) {
                return [
                    'country_code' => $cf,
                    'city' => $fromApi['city'] ?? null,
                    'latitude' => $fromApi['latitude'] ?? null,
                    'longitude' => $fromApi['longitude'] ?? null,
                    'source' => $fromApi !== null ? 'cloudflare+ip-api' : 'cloudflare',
                ];
            }

            return $fromApi;
        });
    }

    public function isPublicIp(?string $ip): bool
    {
        if ($ip === null || $ip === '') {
            return false;
        }

        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private function cacheKey(string $ip): string
    {
        return 'ip_geo_v1:' . hash('sha256', $ip);
    }

    /**
     * @return array{country_code: string, city: ?string, latitude: ?float, longitude: ?float, source: string}|null
     */
    private function fetchIpApi(string $ip): ?array
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            return null;
        }

        try {
            $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,message,countryCode,city,lat,lon';
            $response = Http::timeout(2)->acceptJson()->get($url);
            if (!$response->successful()) {
                return null;
            }
            $j = $response->json();
            if (($j['status'] ?? '') !== 'success') {
                return null;
            }
            $cc = $j['countryCode'] ?? null;
            if (!is_string($cc) || $cc === '') {
                return null;
            }

            return [
                'country_code' => strtoupper($cc),
                'city' => isset($j['city']) && is_string($j['city']) ? $j['city'] : null,
                'latitude' => isset($j['lat']) && is_numeric($j['lat']) ? (float) $j['lat'] : null,
                'longitude' => isset($j['lon']) && is_numeric($j['lon']) ? (float) $j['lon'] : null,
                'source' => 'ip-api',
            ];
        } catch (\Throwable $e) {
            Log::debug('IpGeoLocationService: lookup failed', ['ip' => $ip, 'message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Tente d'ouvrir la session d'accès à partir du résultat IP (pays + optionnellement ville la plus proche).
     * Ne pas appeler si {@see shouldDenyByCountry()} est vrai.
     */
    public function tryEstablishSessionFromIp(array $loc): bool
    {
        $hasCountryRules = AllowedCity::hasCountryRestrictionConfigured();
        $alpha2 = AllowedCity::toAlpha2CountryCode($loc['country_code']);

        if ($hasCountryRules && !AllowedCity::countryCodeIsAllowed($alpha2)) {
            return false;
        }

        $lat = $loc['latitude'] ?? null;
        $lng = $loc['longitude'] ?? null;

        if ($lat !== null && $lng !== null) {
            $nearest = AllowedCity::nearestActiveWithinRadius(
                (float) $lat,
                (float) $lng,
                self::IP_MATCH_RADIUS_KM
            );
            if ($nearest !== null) {
                session([
                    'gps_location_validated' => true,
                    'user_city' => $nearest->name,
                    'validated_at' => now()->toDateTimeString(),
                    'geo_access_via_ip' => false,
                    'ip_geo_country' => null,
                    'geo_ip_source' => $loc['source'] ?? 'unknown',
                ]);
                session()->forget(['gps_coords']);
                Log::info('🔓 Geo IP: accès automatique (ville proche)', [
                    'ville' => $nearest->name,
                    'source' => $loc['source'] ?? null,
                ]);

                return true;
            }
        }

        if ($hasCountryRules && AllowedCity::countryCodeIsAllowed($alpha2)) {
            session([
                'gps_location_validated' => true,
                'geo_access_via_ip' => true,
                'ip_geo_country' => $alpha2,
                'user_city' => null,
                'validated_at' => now()->toDateTimeString(),
                'geo_ip_source' => $loc['source'] ?? 'unknown',
            ]);
            session()->forget(['gps_coords']);
            Log::info('🔓 Geo IP: accès automatique (pays autorisé)', [
                'country' => $alpha2,
                'source' => $loc['source'] ?? null,
            ]);

            return true;
        }

        return false;
    }

    public function shouldDenyByCountry(array $loc): bool
    {
        if (!AllowedCity::hasCountryRestrictionConfigured()) {
            return false;
        }

        return !AllowedCity::countryCodeIsAllowed($loc['country_code']);
    }
}
