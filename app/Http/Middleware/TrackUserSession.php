<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class TrackUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Tracker la session uniquement pour les utilisateurs authentifiés
        if (Auth::check()) {
            UserSession::trackSession(
                Auth::id(),
                $request->session()->getId(),
                $request
            );
        }

        return $next($request);
    }
}
