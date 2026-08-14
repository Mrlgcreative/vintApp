<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\ApiController;
use App\Services\StorageSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    /**
     * API: Profil de l'utilisateur
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load(['items', 'receivedReviews']);

            $stats = [
                'total_items' => $user->items->count(),
                'active_items' => $user->items->where('status', 'active')->count(),
                'sold_items' => $user->items->where('status', 'sold')->count(),
                'average_rating' => round($user->receivedReviews->avg('rating'), 1),
                'total_reviews' => $user->receivedReviews->count(),
            ];

            return $this->successResponse([
                'user' => $user,
                'stats' => $stats
            ], 'Profil récupéré avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération du profil', 500);
        }
    }

    /**
     * API: Mettre à jour le profil
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
                'phone' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'location' => 'nullable|string|max:255',
                'fcm_token' => 'nullable|string|max:500',
                'device_type' => 'nullable|string|in:android,ios,web',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $user->update($request->only([
                'name', 'email', 'phone', 'city', 'bio', 'location',
            ]));

            // Token de notification push (expo-notifications) : affectation
            // directe, ces champs ne sont volontairement pas mass-assignables
            // sur le modèle User.
            if ($request->has('fcm_token')) {
                $user->fcm_token = $request->input('fcm_token') ?: null;
                $user->fcm_token_updated_at = $user->fcm_token ? now() : null;
            }
            if ($request->has('device_type')) {
                $user->device_type = $request->input('device_type');
            }
            if ($request->has('fcm_token') || $request->has('device_type')) {
                $user->save();
            }

            return $this->successResponse($user, 'Profil mis à jour avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour du profil', 500);
        }
    }

    /**
     * API: Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|current_password',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $user = $request->user();
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return $this->successResponse(null, 'Mot de passe mis à jour avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour du mot de passe', 500);
        }
    }

    /**
     * API: Upload d'avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $user = $request->user();

            // Supprimer l'ancien avatar s'il existe et si ce n'est pas une URL externe
            if ($user->profile_image && !filter_var($user->profile_image, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }
            }

            // Upload du nouvel avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            if (!$path) {
                return $this->errorResponse('Erreur lors de l\'upload du fichier', 500);
            }

            // Copier vers public/storage pour Hostinger
            StorageSyncService::syncFile($path);

            $user->update(['profile_image' => $path]);

            return $this->successResponse([
                'avatar_url' => Storage::url($path)
            ], 'Avatar mis à jour avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de l\'upload de l\'avatar', 500);
        }
    }

    /**
     * API: Statistiques de l'utilisateur
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $stats = [
                'total_items' => $user->items()->count(),
                'active_items' => $user->items()->where('status', 'active')->count(),
                'sold_items' => $user->items()->where('status', 'sold')->count(),
                'total_sales' => $user->ordersAsSeller()->where('status', 'delivered')->count(),
                'total_purchases' => $user->ordersAsBuyer()->where('status', 'delivered')->count(),
                'total_revenue' => $user->ordersAsSeller()->where('status', 'delivered')->sum('total_amount'),
                'total_spent' => $user->ordersAsBuyer()->where('status', 'delivered')->sum('total_amount'),
                'average_rating' => round($user->receivedReviews()->avg('rating'), 1),
                'total_reviews' => $user->receivedReviews()->count(),
                'favorites_count' => $user->favorites()->count(),
            ];

            return $this->successResponse($stats, 'Statistiques récupérées avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des statistiques', 500);
        }
    }

    /**
     * API: Articles de l'utilisateur
     */
    public function getItems(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $items = $user->items()
                         ->with(['category', 'brand'])
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->per_page ?? 12);

            return $this->paginatedResponse($items, 'Articles récupérés avec succès');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('apiGetItems error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des articles: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Commandes de l'utilisateur
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $orders = $user->ordersAsBuyer()
                          ->with(['item', 'seller'])
                          ->orderBy('created_at', 'desc')
                          ->paginate($request->per_page ?? 10);

            return $this->paginatedResponse($orders, 'Commandes récupérées avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des commandes', 500);
        }
    }

    /**
     * API: Ventes de l'utilisateur
     */
    public function getSales(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $sales = $user->ordersAsSeller()
                         ->with(['item', 'buyer'])
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->per_page ?? 10);

            return $this->paginatedResponse($sales, 'Ventes récupérées avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des ventes', 500);
        }
    }

    /**
     * API: Avis reçus par l'utilisateur
     */
    public function getReviews(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $reviews = $user->receivedReviews()
                           ->with(['fromUser', 'order'])
                           ->orderBy('created_at', 'desc')
                           ->paginate($request->per_page ?? 10);

            return $this->paginatedResponse($reviews, 'Avis récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des avis', 500);
        }
    }

    /**
     * API: Supprimer le compte
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Supprimer l'avatar s'il existe et si ce n'est pas une URL externe
            if ($user->profile_image && !filter_var($user->profile_image, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }
            }

            // Supprimer l'utilisateur
            $user->delete();

            return $this->successResponse(null, 'Compte supprimé avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la suppression du compte', 500);
        }
    }
}
