<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectAdminToDashboard
{
    /**
     * Gère une requête entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Si l'utilisateur est admin et essaie d'accéder au dashboard normal
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                // Si on est sur le dashboard normal, rediriger vers admin
                if ($request->routeIs('dashboard')) {
                    return redirect()->route('admin.dashboard');
                }
            }

            // Si l'utilisateur est agent support, rediriger vers l'espace agent
            if (method_exists($user, 'hasRole') && $user->hasRole('support') && !$user->hasRole('admin')) {
                if ($request->routeIs('dashboard')) {
                    return redirect()->route('agent.dashboard');
                }
            }
        }

        return $next($request);
    }
}