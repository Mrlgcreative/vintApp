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
        
        // Ajouter les en-têtes de permissions pour la caméra
        $response->headers->set('Permissions-Policy', 'camera=(self)');
        $response->headers->set('Feature-Policy', 'camera *');
        
        return $response;
    }
}
