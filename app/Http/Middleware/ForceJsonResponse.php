<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force accept JSON
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        try {
            $response = $next($request);
            
            // Ensure response is JSON
            if (!$response->headers->has('Content-Type') || 
                strpos($response->headers->get('Content-Type'), 'application/json') === false) {
                $response->headers->set('Content-Type', 'application/json');
            }
            
            // Si la réponse contient du HTML au lieu de JSON, convertir en JSON
            $content = $response->getContent();
            if (strpos($content, '<!DOCTYPE html>') !== false || strpos($content, '<html') !== false) {
                $response->setContent(json_encode([
                    'success' => false,
                    'message' => 'Une erreur inattendue s\'est produite. Veuillez réessayer.',
                    'error' => config('app.debug') ? 'HTML response detected' : null
                ]));
                $response->setStatusCode(500);
                $response->headers->set('Content-Type', 'application/json');
            }
            
            return $response;
            
        } catch (\Throwable $e) {
            // Intercepter toutes les exceptions et retourner JSON
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors du traitement de votre requête.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'exception' => config('app.debug') ? get_class($e) : null,
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}
