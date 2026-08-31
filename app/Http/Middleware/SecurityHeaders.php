<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Ajoute les headers HTTP de sécurité à toutes les réponses
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Protection contre Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Permettre la communication avec les popups (Google Sign-In)
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        // Protection XSS (pour les navigateurs anciens)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Protection MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // HTTPS strict (seulement en production)
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content Security Policy (CSP) - VERSION OPTIMISÉE POUR DÉVELOPPEMENT
        // En développement : plus permissif pour ne pas bloquer les fonctionnalités
        // En production : plus strict
        
        if (app()->environment('local', 'development')) {
            // MODE DÉVELOPPEMENT : Permissif
            $csp = implode('; ', [
                "default-src 'self' 'unsafe-inline' 'unsafe-eval'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http: data:",
                "style-src 'self' 'unsafe-inline' https: http:",
                "img-src 'self' data: https: http: blob:",
                "font-src 'self' data: https: http:",
                "connect-src 'self' https: http: ws: wss:",
                "frame-src 'self' https: http:",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ]);
        } else {
            // MODE PRODUCTION : Strict mais fonctionnel
            $csp = implode('; ', [
                "default-src 'self'",
                // Scripts : CDNs nécessaires + inline pour admin
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: " .
                    "https://cdn.tailwindcss.com " .
                    "https://cdn.jsdelivr.net " .
                    "https://code.jquery.com " .
                    "https://unpkg.com " .
                    "https://js.stripe.com " .
                    "https://www.google.com " .
                    "https://www.gstatic.com " .
                    "https://apis.google.com " .
                    "https://maps.googleapis.com",
                
                // Styles : CDNs + inline
                "style-src 'self' 'unsafe-inline' " .
                    "https://cdn.tailwindcss.com " .
                    "https://cdn.jsdelivr.net " .
                    "https://fonts.googleapis.com " .
                    "https://fonts.bunny.net " .
                    "https://cdnjs.cloudflare.com " .
                    "https://unpkg.com",
                
                // Images : tous les domaines HTTPS
                "img-src 'self' data: https: blob:",
                
                // Fonts
                "font-src 'self' data: " .
                    "https://cdn.jsdelivr.net " .
                    "https://fonts.gstatic.com " .
                    "https://fonts.bunny.net " .
                    "https://cdnjs.cloudflare.com " .
                    "https://unpkg.com",
                
                // Connexions API
                "connect-src 'self' " .
                    "https://api.openai.com " .
                    "https://cdn.jsdelivr.net " .
                    "https://www.gstatic.com " .
                    "https://www.googleapis.com " .
                    "https://securetoken.googleapis.com " .
                    "https://identitytoolkit.googleapis.com " .
                    "https://firebaseinstallations.googleapis.com " .
                    "https://fcmregistrations.googleapis.com " .
                    "https://firestore.googleapis.com " .
                    "https://accounts.google.com " .
                    "https://uncomely-uneffusing-averie.ngrok-free.dev " .
                    "https://*.ngrok-free.dev " .
                    "https://*.loca.lt " .
                    "wss:",
                
                // Frames pour intégrations
                "frame-src 'self' " .
                    "https://js.stripe.com " .
                    "https://www.google.com " .
                    "https://apis.google.com " .
                    "https://accounts.google.com " .
                    "https://*.firebaseapp.com " .
                    "https://maps.googleapis.com",
                
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "upgrade-insecure-requests",
            ]);
        }
        
        $response->headers->set('Content-Security-Policy', $csp);

        // Permissions Policy - Le QR scanner de commande nécessite la caméra (top-level uniquement)
        $permissions = implode(', ', [
            'geolocation=(self)',
            'microphone=()',
            'camera=(self)',
            'payment=(self)',
            'usb=()',
        ]);
        $response->headers->set('Permissions-Policy', $permissions);

        return $response;
    }
}
