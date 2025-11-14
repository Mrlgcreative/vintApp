<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCameraPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
    // Ajouter l'en-tête Permissions-Policy pour la caméra (Permissions-Policy remplace Feature-Policy)
    // Ne plus définir l'ancien en-tête 'Feature-Policy' — les navigateurs modernes utilisent 'Permissions-Policy'.
    $response->headers->set('Permissions-Policy', 'camera=(self)');
        
        return $response;
    }
}
