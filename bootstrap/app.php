<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclure les routes Firebase de la vérification CSRF (protégées par idToken Firebase)
        $middleware->validateCsrfTokens(except: [
            'firebase/*',
            'auth/firebase/*',
            'payments/callback',
            'wallet/withdrawals/webhook/*',
        ]);

        // Global Middleware
        $middleware->use([
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\SecurityHeaders::class, // ✅ Headers de sécurité sur toutes les requêtes
            \App\Http\Middleware\AddCameraPermissions::class, // ✅ Permissions pour la caméra
        ]);

        // Web Middleware Group
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\DynamicConfigMiddleware::class,
            \App\Http\Middleware\ShareAppSettings::class,
            \App\Http\Middleware\EnsureColorSystem::class, // 🎨 Système de couleurs automatique
            \App\Http\Middleware\MaintenanceMode::class,

            \App\Http\Middleware\CheckGPSCityAccess::class, // 📍 Vérification GPS précise
            \App\Http\Middleware\TrackUserSession::class, // 🆕 Tracker les sessions utilisateurs
            \App\Http\Middleware\ReferralCodeMiddleware::class, // 🆕 Gérer les codes de parrainage
            \App\Http\Middleware\TwoFactorMiddleware::class, // 🔐 Vérification 2FA globale
            \App\Http\Middleware\EnsureEmailIsVerified::class, // 📧 Forcer la vérification d'email
            \App\Http\Middleware\CaptureRequests::class, // 🔍 Middleware temporaire pour debug 404
        ]);

        // API Middleware Group
        $middleware->api(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Named Middleware Aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'cache.response' => \App\Http\Middleware\CacheResponse::class, // 🚀 Cache HTTP
            'compress.response' => \App\Http\Middleware\CompressResponse::class, // 🚀 Compression GZIP
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Auth\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'throttle.login' => \App\Http\Middleware\ThrottleLogin::class, // 🔐 Rate limit login
            'security.log' => \App\Http\Middleware\SecurityLogging::class, // 🔐 Security logging
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class, // ✅ Notre middleware personnalisé
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'expert' => \App\Http\Middleware\IsExpert::class, // 🆕 Middleware expert
            'dynamic.config' => \App\Http\Middleware\DynamicConfigMiddleware::class,
            'maintenance' => \App\Http\Middleware\MaintenanceMode::class,
            'preregistration' => \App\Http\Middleware\CheckPreregistrationMode::class,
            'mobile.optimize' => \App\Http\Middleware\MobileOptimization::class, // 📱 Optimisation mobile
            'referral' => \App\Http\Middleware\ReferralCodeMiddleware::class, // 🆕 Codes de parrainage
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class, // 🔐 Authentification à deux facteurs
            'force.json' => \App\Http\Middleware\ForceJsonResponse::class, // 🔥 Force JSON response pour Firebase
            'redirect.role' => \App\Http\Middleware\RedirectAdminToDashboard::class, // 🔄 Redirige admin/agent vers leur dashboard
            'seller' => \App\Http\Middleware\SellerMiddleware::class, // 🛒 Espace vendeur
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expirée. Veuillez rafraîchir la page.'], 419);
            }

            return redirect()->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Votre session a expiré. Veuillez réessayer.');
        });
    })->create();
