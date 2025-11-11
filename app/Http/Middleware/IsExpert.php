<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IsExpert
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur a le rôle expert
        $isExpert = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'expert')
            ->exists();

        if (!$isExpert) {
            abort(403, 'Accès réservé aux experts certifiés VintApp.');
        }

        // Vérifier si l'expert est actif
        $expertProfile = $user->expertProfile ?? null;
        if (!$expertProfile || !$expertProfile->is_active) {
            abort(403, 'Votre compte expert n\'est pas actif. Contactez l\'administration.');
        }

        return $next($request);
    }
}
