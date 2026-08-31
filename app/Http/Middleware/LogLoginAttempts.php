<?php

namespace App\Http\Middleware;

use App\Models\SecurityLoginAttempt;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journalise chaque tentative de connexion (web ou API) en base de données,
 * afin de les afficher dans le monitoring et de détecter la force brute.
 *
 * À appliquer sur les routes POST de login/register/password :
 *  - succès (200 / 302 vers un espace protégé),
 *  - échec (401, 422),
 *  - rate-limit (429).
 */
class LogLoginAttempts
{
    /**
     * Routes considérées comme de la connexion (ne journaliser que celles-là).
     */
    protected const LOGIN_ROUTES = [
        'login',
        'auth.firebase.login',
        'firebase.login',
        'password.request',
        'password.email',
        'password.reset',
        'password.store',
        'verification.send',
        'verification.code.verify',
        'verification.code.resend',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Journaliser uniquement les tentatives de connexion/inscription/mot de passe.
        // La détection combine le nom de route ET le chemin : les routes de l'API
        // (ex: /api/login, /api/register) n'ont souvent pas de nom de route,
        // il faut donc aussi se baser sur le path.
        if ($request->isMethod('post') && $this->isLoginRequest($request)) {
            return $this->handleLoginRequest($request, $next);
        }

        return $next($request);
    }

    /**
     * Détermine si la requête est une tentative de connexion (par nom et/ou path).
     */
    protected function isLoginRequest(Request $request): bool
    {
        $route = $request->route();
        $routeName = (string) ($route?->getName() ?? '');

        // 1. Par nom de route
        if ($this->matchesRouteName($routeName)) {
            return true;
        }

        // 2. Par chemin (les routes sans nom, notamment l'API)
        $path = '/' . ltrim($request->path(), '/');

        foreach (self::LOGIN_PATHS as $suffix) {
            if ($path === $suffix || str_ends_with($path, $suffix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Suites de chemins identifiant une tentative de login/register/password.
     */
    protected const LOGIN_PATHS = [
        '/login',
        '/register',
        '/logout',
        '/forgot-password',
        '/reset-password',
        '/email/verification-notification',
        '/verify-code',
        '/verify-code/resend',
        '/password/email',
        '/password/forgot',
        '/password/reset',
        '/two-factor/verify',
        '/two-factor/enable',
        '/two-factor/confirm',
    ];

    /**
     * Vérifie le nom de route contre les routes de connexion connues.
     */
    protected function matchesRouteName(string $routeName): bool
    {
        if ($routeName === '') {
            return false;
        }

        return in_array($routeName, self::LOGIN_ROUTES, true)
            || str_contains($routeName, '.login')
            || str_contains($routeName, '.password')
            || str_contains($routeName, 'verification.');
    }

    /**
     * Traite et journalise une tentative de connexion.
     */
    protected function handleLoginRequest(Request $request, Closure $next): Response
    {
        // Déterminer la clé de throttle email|IP (alignée sur RouteServiceProvider)
        $email = (string) ($request->input('email') ?? '');
        $throttleKey = Str::lower($email) . '|' . $request->ip();

        // Garder la trace de l'authentification avant la requête
        $wasAuthenticated = Auth::check();

        $response = $next($request);

        // Succès si l'utilisateur est maintenant connecté (web) OU statut 2xx/3xx hors échec
        $status = $response->getStatusCode();
        $success = Auth::check();

        // Pour l'API (guard sanctum), Auth::check() sur le guard par défaut est insuffisant.
        // On considère un 2xx (hors 422 validation) comme un succès, un 401/429 comme un échec.
        if ($request->is('api/*') || $request->expectsJson()) {
            $success = $status >= 200 && $status < 400 && $status !== 302;
        }

        // Un redirect vers /login (web) = échec ; vers dashboard = succès
        if (!$request->is('api/*') && !$request->expectsJson()) {
            if ($status === 302 && $response->headers->has('Location')) {
                $location = (string) $response->headers->get('Location');
                $success = !Str::contains($location, ['/login', '/register', '?error']);
            }
            $success = $success && Auth::check();
        }

        SecurityLoginAttempt::record([
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => (string) ($request->route()?->getName() ?? ('/' . ltrim($request->path(), '/'))),
            'guard' => $request->is('api/*') ? 'sanctum' : 'web',
            'success' => $success,
            'status_code' => $status,
            'throttle_key' => $throttleKey,
        ]);

        return $response;
    }
}
