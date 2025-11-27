<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware d'optimisation mobile
 * 
 * Détecte les appareils mobiles et applique des optimisations spécifiques
 */
class MobileOptimization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Détecter si l'utilisateur est sur mobile
        $isMobile = $this->isMobileDevice($request);
        
        if ($isMobile) {
            // Ajouter des headers spécifiques mobile
            $response->headers->set('X-Mobile-Detected', 'true');
            
            // Save-Data header support (économie de données)
            if ($request->header('Save-Data') === 'on') {
                $response->headers->set('X-Data-Saver', 'active');
            }
        }

        // Ajouter headers de performance
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        return $response;
    }

    /**
     * Détecte si la requête vient d'un appareil mobile
     */
    protected function isMobileDevice(Request $request): bool
    {
        $userAgent = $request->header('User-Agent', '');
        
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'Windows Phone', 'Opera Mini',
            'IEMobile', 'Mobile Safari'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
