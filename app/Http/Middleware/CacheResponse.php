<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Routes qui ne doivent JAMAIS être cachées
     */
    protected $excludedRoutes = [
        'api/user',
        'api/user/*',
        'api/notifications',
        'api/messages',
        'api/orders',
        'api/wallet',
        'api/dashboard',
        'payment-callbacks/*',
    ];

    /**
     * Durées de cache par route (en secondes)
     */
    protected $cacheDurations = [
        'api/items' => 300,           // 5 minutes
        'api/items/*' => 180,         // 3 minutes pour un item spécifique
        'api/categories' => 3600,     // 1 heure
        'api/brands' => 3600,         // 1 heure
        'api/health' => 60,           // 1 minute
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?int $ttl = null): Response
    {
        // Ne pas cacher les requêtes non-GET
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Ne pas cacher si l'utilisateur est authentifié (pour les données personnalisées)
        if ($request->user()) {
            return $next($request);
        }

        // Vérifier si la route est exclue
        if ($this->shouldExclude($request)) {
            return $next($request);
        }

        // Générer une clé de cache unique basée sur l'URL complète avec query params
        $cacheKey = $this->getCacheKey($request);

        // Déterminer la durée de cache
        $duration = $ttl ?? $this->getCacheDuration($request);

        // Essayer de récupérer depuis le cache
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse !== null) {
            return response($cachedResponse['content'], $cachedResponse['status'])
                ->withHeaders(array_merge($cachedResponse['headers'], [
                    'X-Cache' => 'HIT',
                    'X-Cache-Key' => $cacheKey,
                ]));
        }

        // Exécuter la requête
        $response = $next($request);

        // Cacher seulement les réponses réussies (et non compressées!)
        if ($response->isSuccessful()) {
            // IMPORTANT: Ne pas cacher les réponses compressées - cela cause des corruptions
            $contentEncoding = $response->headers->get('Content-Encoding');
            if (empty($contentEncoding)) {
                $content = $response->getContent();
                
                // Validation JSON avant mise en cache
                if ($this->isJsonResponse($response)) {
                    $decoded = json_decode($content);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // JSON invalide - ne pas cacher
                        \Log::warning('CacheResponse: Invalid JSON detected, skipping cache', [
                            'path' => $request->path(),
                            'error' => json_last_error_msg()
                        ]);
                        return $response->withHeaders([
                            'X-Cache' => 'SKIP-INVALID-JSON',
                        ]);
                    }
                }
                
                Cache::put($cacheKey, [
                    'content' => $content,
                    'status' => $response->getStatusCode(),
                    'headers' => $this->getCacheableHeaders($response),
                ], $duration);
            }
        }

        return $response->withHeaders([
            'X-Cache' => 'MISS',
            'X-Cache-Duration' => $duration,
        ]);
    }

    /**
     * Vérifier si la requête doit être exclue du cache
     */
    protected function shouldExclude(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->excludedRoutes as $pattern) {
            if (fnmatch($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Générer une clé de cache unique
     */
    protected function getCacheKey(Request $request): string
    {
        // Inclure le chemin, les paramètres de requête, et les headers importants
        $queryString = $request->getQueryString();
        $accept = $request->header('Accept', 'application/json');
        
        return 'http_cache:' . md5(
            $request->path() . 
            ($queryString ? '?' . $queryString : '') .
            '|accept:' . $accept
        );
    }

    /**
     * Déterminer la durée de cache pour la requête
     */
    protected function getCacheDuration(Request $request): int
    {
        $path = $request->path();

        foreach ($this->cacheDurations as $pattern => $duration) {
            if (fnmatch($pattern, $path)) {
                return $duration;
            }
        }

        // Durée par défaut: 2 minutes
        return 120;
    }

    /**
     * Extraire les headers cachables de la réponse
     */
    protected function getCacheableHeaders(Response $response): array
    {
        $headers = [];
        $cacheableHeaders = [
            'Content-Type',
            'Content-Language',
            'X-RateLimit-Limit',
            'X-RateLimit-Remaining',
        ];

        foreach ($cacheableHeaders as $header) {
            if ($response->headers->has($header)) {
                $headers[$header] = $response->headers->get($header);
            }
        }

        return $headers;
    }

    /**
     * Vérifier si la réponse est du JSON
     */
    protected function isJsonResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'application/json');
    }
}
