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
            // Capture any stray print/echo output (e.g. from third-party libraries)
            ob_start();
            $response = $next($request);
            $strayOutput = ob_get_clean();

            // Prepend stray output to the response content so the regex below catches it
            $content = $response->getContent();
            if (!empty($strayOutput)) {
                $content = $strayOutput . $content;
            }

            // Ensure response is JSON
            if (!$response->headers->has('Content-Type') || 
                strpos($response->headers->get('Content-Type'), 'application/json') === false) {
                $response->headers->set('Content-Type', 'application/json');
            }
            
            // Si la réponse contient du HTML au lieu de JSON, convertir en JSON
            if (preg_match('/<(!?DOCTYPE|html|style|script|form|body|head)/i', $content)) {
                $response->setContent(json_encode([
                    'success' => false,
                    'message' => 'Une erreur inattendue s\'est produite. Veuillez réessayer.',
                    'error' => config('app.debug') ? 'HTML response detected' : null
                ]));
                $response->setStatusCode(500);
                $response->headers->set('Content-Type', 'application/json');
            } elseif (!empty($strayOutput)) {
                // No HTML detected but there was stray output — strip it from the response
                $response->setContent($content);
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
