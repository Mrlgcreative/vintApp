<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Afficher la page de profil de l'utilisateur
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Récupérer les statistiques de l'utilisateur
        $stats = $this->getUserStats($user);
        
        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Supprimer le compte utilisateur
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Mettre à jour les préférences de thème
     */
    public function updateTheme(Request $request): RedirectResponse
    {
        $request->validate([
            'theme_preference' => ['required', Rule::in(['auto', 'light', 'dark'])],
        ]);

        $user = $request->user();
        $user->theme_preference = $request->theme_preference;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'theme-updated');
    }

    /**
     * Mettre à jour l'avatar de l'utilisateur
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();
        
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
            
            // Stocker l'image dans le dossier public/avatars
            $avatar->storeAs('avatars', $filename, 'public');
            
            // Mettre à jour le chemin de l'avatar dans la base de données
            $user->avatar = 'avatars/' . $filename;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Afficher les statistiques de l'utilisateur
     */
    public function stats(Request $request): View
    {
        $user = $request->user();
        $stats = $this->getUserStats($user);
        
        return view('profile.stats', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Afficher les paramètres de sécurité
     */
    public function security(Request $request): View
    {
        $user = $request->user();
        
        return view('profile.security', [
            'user' => $user,
        ]);
    }

    /**
     * Afficher les notifications
     */
    public function notifications(Request $request): View
    {
        $user = $request->user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('profile.notifications', [
            'user' => $user,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Obtenir les statistiques de l'utilisateur
     */
    private function getUserStats($user)
    {
        return [
            'total_items' => $user->items()->count(),
            'active_items' => $user->items()->where('status', 'active')->count(),
            'sold_items' => $user->items()->where('status', 'sold')->count(),
            'total_orders' => $user->ordersAsBuyer()->count(),
            'completed_orders' => $user->ordersAsSeller()->where('status', 'completed')->count(),
            'total_revenue' => $user->ordersAsSeller()->where('status', 'completed')->sum('total_amount'),
            'total_messages' => $user->sentMessages()->count() + $user->receivedMessages()->count(),
            'unread_messages' => $user->receivedMessages()->where('is_read', false)->count(),
            'favorites_count' => $user->favorites()->count(),
            'reviews_count' => $user->reviewsReceived()->count(),
            'average_rating' => $user->reviewsReceived()->avg('rating') ?? 0,
        ];
    }
}
