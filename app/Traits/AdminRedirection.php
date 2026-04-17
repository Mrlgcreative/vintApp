<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait AdminRedirection
{
    /**
     * Redirige vers le dashboard approprié selon le rôle de l'utilisateur
     * 
     * @param string $defaultRoute Route par défaut si pas admin
     * @param string $successMessage Message de succès
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole($defaultRoute = '/dashboard', $successMessage = 'Connexion réussie')
    {
        $user = Auth::user();
        
        if ($user) {
            // Vérification si l'utilisateur est admin
            $isAdmin = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', $user->id)
                ->where('roles.slug', 'admin')
                ->exists();
                
            if ($isAdmin) {
                return redirect()->intended(route('admin.dashboard'))->with('success', '👑 ' . $successMessage . ' (Admin)');
            }

            // Vérification si l'utilisateur est agent support
            $isSupport = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', $user->id)
                ->where('roles.slug', 'support')
                ->exists();

            if ($isSupport) {
                return redirect()->route('agent.dashboard')->with('success', '🎧 ' . $successMessage . ' (Agent)');
            }
        }
        
        return redirect()->intended($defaultRoute)->with('success', '🎉 ' . $successMessage);
    }
    
    /**
     * Vérifie si l'utilisateur connecté est admin
     * 
     * @return bool
     */
    protected function isCurrentUserAdmin(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
    }
}