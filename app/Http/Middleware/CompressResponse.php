<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Taille minimale pour activer la compression (en octets)
     */
    const MIN_SIZE = 1024; // 1KB

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Vérifier si le client accepte la compression
        $acceptEncoding = $request->header('Accept-Encoding', '');
        
        // Vérifier si la réponse est déjà compressée
        if ($response->headers->has('Content-Encoding')) {
            return $response;
        }

        // Ne compresser que les réponses JSON/text
        $contentType = $response->headers->get('Content-Type', '');
        if (!$this->shouldCompress($contentType)) {
            return $response;
        }

        $content = $response->getContent();
        $size = strlen($content);

        // Ne compresser que si la taille dépasse le minimum
        if ($size < self::MIN_SIZE) {
            return $response;
        }

        // Compression gzip
        if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
            $compressed = gzencode($content, 6); // Niveau 6 = bon compromis vitesse/ratio
            
            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', strlen($compressed));
                $response->headers->set('X-Original-Size', $size);
                $response->headers->set('X-Compressed-Size', strlen($compressed));
                
                return $response;
            }
        }

        // Compression deflate (fallback)
        if (str_contains($acceptEncoding, 'deflate') && function_exists('gzdeflate')) {
            $compressed = gzdeflate($content, 6);
            
            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'deflate');
                $response->headers->set('Content-Length', strlen($compressed));
                
                return $response;
            }
        }

        return $response;
    }

    /**
     * Déterminer si le contenu doit être compressé
     */
    protected function shouldCompress(string $contentType): bool
    {
        $compressibleTypes = [
            'application/json',
            'application/javascript',
            'text/html',
            'text/css',
            'text/plain',
            'text/xml',
            'application/xml',
        ];

        foreach ($compressibleTypes as $type) {
            if (str_contains($contentType, $type)) {
                return true;
            }
        }

        return false;
    }
}
