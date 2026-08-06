<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    /**
     * Changer la préférence de thème de l'utilisateur
     */
    public function toggle(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non connecté'], 401);
        }

        $currentTheme = $user->theme_preference ?? 'auto';
        
        // Cycle: auto -> light -> dark -> auto
        $newTheme = match($currentTheme) {
            'auto' => 'light',
            'light' => 'dark',
            'dark' => 'auto',
            default => 'auto'
        };

        $user->update(['theme_preference' => $newTheme]);

        return response()->json([
            'success' => true,
            'theme' => $newTheme,
            'message' => "Thème changé vers: $newTheme"
        ]);
    }

    /**
     * Définir un thème spécifique
     */
    public function set(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => 'required|in:light,dark,auto'
        ]);

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non connecté'], 401);
        }

        $theme = $request->input('theme');
        $user->update(['theme_preference' => $theme]);

        return response()->json([
            'success' => true,
            'theme' => $theme,
            'message' => "Thème défini sur: $theme"
        ]);
    }
}
