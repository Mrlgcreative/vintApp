<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityLogging
{
    /**
     * Liste des actions sensibles à logger
     */
    private array $sensitiveActions = [
        'login', 'logout', 'register', 'password.reset',
        'order.create', 'payment.process', 'wallet.withdraw',
        'admin.access', 'expert.verify', 'boost.purchase',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Logger les requêtes sensibles
        if ($this->isSensitiveRequest($request)) {
            $this->logSecurityEvent($request, $response, $duration);
        }

        // Logger les échecs d'authentification
        if ($response->getStatusCode() === 401) {
            $this->logAuthenticationFailure($request);
        }

        // Logger les tentatives d'accès non autorisés
        if ($response->getStatusCode() === 403) {
            $this->logAuthorizationFailure($request);
        }

        // Logger les erreurs de validation
        if ($response->getStatusCode() === 422) {
            $this->logValidationError($request, $response);
        }

        return $response;
    }

    /**
     * Détermine si la requête est sensible
     */
    private function isSensitiveRequest(Request $request): bool
    {
        $route = $request->route();
        if (!$route) {
            return false;
        }

        $routeName = $route->getName();
        foreach ($this->sensitiveActions as $action) {
            if (str_contains($routeName ?? '', $action)) {
                return true;
            }
        }

        // Logger aussi toutes les requêtes POST/PUT/DELETE sur /api/admin
        if ($request->is('api/admin/*') && in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            return true;
        }

        return false;
    }

    /**
     * Logger un événement de sécurité
     */
    private function logSecurityEvent(Request $request, Response $response, float $duration): void
    {
        Log::channel('security')->info('Security Event', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Logger un échec d'authentification
     */
    private function logAuthenticationFailure(Request $request): void
    {
        Log::channel('security')->warning('Authentication Failure', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'email' => $request->input('email'),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Logger un échec d'autorisation
     */
    private function logAuthorizationFailure(Request $request): void
    {
        Log::channel('security')->warning('Authorization Failure', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Logger une erreur de validation
     */
    private function logValidationError(Request $request, Response $response): void
    {
        Log::channel('security')->notice('Validation Error', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'errors' => $response->getContent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
