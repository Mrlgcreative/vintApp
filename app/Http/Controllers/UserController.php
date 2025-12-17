<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponses;
use App\Services\StorageSyncService;

class UserController extends Controller
{
    use ApiResponses;
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['items', 'receivedReviews', 'givenReviews']);
        
        // Calculer les statistiques
        $stats = [
            'total_items' => $user->items()->count(),
            'active_items' => $user->items()->where('status', 'active')->count(),
            'total_sales' => $user->ordersAsSeller()->where('status', 'delivered')->count(),
            'total_purchases' => $user->ordersAsBuyer()->where('status', 'delivered')->count(),
            'average_rating' => round($user->receivedReviews()->avg('rating'), 1),
            'total_reviews' => $user->receivedReviews()->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'email', 'phone', 'city', 'bio', 'location']));

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }

    /**
     * Upload user avatar
     */

public function uploadAvatar(Request $request)
{
    $validator = Validator::make($request->all(), [
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $validator->errors()
        ], 422);
    }

    $user = $request->user();

    // Supprimer l'ancien avatar s'il existe
    if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
        Storage::disk('public')->delete($user->profile_image);
    }

    // Upload du nouvel avatar
    $path = $request->file('avatar')->store('avatars', 'public');
    if (!$path) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'upload du fichier.'
        ], 500);
    }

    // Copier vers public/storage pour Hostinger
    StorageSyncService::syncFile($path);

    $user->update(['profile_image' => $path]);

    return response()->json([
        'success' => true,
        'message' => 'Avatar mis à jour avec succès',
        'avatar_url' => Storage::url($path)
    ]);
}
    /**
     * Get user profile for API
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load(['items', 'receivedReviews']);
        
        // Calculer la note moyenne
        $averageRating = $user->receivedReviews->avg('rating');
        $totalReviews = $user->receivedReviews->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $totalReviews,
                'total_items' => $user->items->count(),
            ]
        ]);
    }

    /**
     * Update user profile for API
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'data' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'phone', 'city', 'bio', 'location']));

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'data' => $user
        ]);
    }

    /**
     * Get user statistics
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'total_items' => $user->items()->count(),
            'active_items' => $user->items()->where('status', 'active')->count(),
            'sold_items' => $user->items()->where('status', 'sold')->count(),
            'total_sales' => $user->ordersAsSeller()->where('status', 'delivered')->count(),
            'total_purchases' => $user->ordersAsBuyer()->where('status', 'delivered')->count(),
            'total_revenue' => $user->ordersAsSeller()->where('status', 'delivered')->sum('total_price'),
            'total_spent' => $user->ordersAsBuyer()->where('status', 'delivered')->sum('total_price'),
            'average_rating' => round($user->receivedReviews()->avg('rating'), 1),
            'total_reviews' => $user->receivedReviews()->count(),
            'favorites_count' => $user->favorites()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get user's items
     */
    public function getItems(Request $request)
    {
        $user = $request->user();
        
        $items = $user->items()
                     ->with(['category', 'brand', 'primaryImage'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Get user's orders
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();
        
        $orders = $user->ordersAsBuyer()
                      ->with(['item', 'seller'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get user's sales
     */
    public function getSales(Request $request)
    {
        $user = $request->user();
        
        $sales = $user->ordersAsSeller()
                     ->with(['item', 'buyer'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $sales
        ]);
    }

    /**
     * Get user's reviews
     */
    public function getReviews(Request $request)
    {
        $user = $request->user();
        
        $reviews = $user->receivedReviews()
                       ->with(['fromUser', 'order'])
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Supprimer l'avatar s'il existe
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Supprimer l'utilisateur
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compte supprimé avec succès'
        ]);
    }

    /**
     * Sauvegarder une adresse de livraison
     */
    public function saveDeliveryAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Si c'est la première adresse ou si is_default est true
        $isDefault = $request->is_default || $user->deliveryAddresses()->count() === 0;

        $deliveryAddress = $user->deliveryAddresses()->create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'city' => $request->city,
            'commune' => $request->commune,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => $request->notes,
            'is_default' => $isDefault,
        ]);

        // Si c'est défini comme adresse par défaut, retirer le statut des autres
        if ($isDefault) {
            $deliveryAddress->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Adresse de livraison enregistrée avec succès',
            'data' => $deliveryAddress
        ]);
    }

    /**
     * Obtenir toutes les adresses de livraison de l'utilisateur
     */
    public function getDeliveryAddresses(Request $request)
    {
        $user = $request->user();
        $addresses = $user->deliveryAddresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    /**
     * Obtenir l'adresse de livraison par défaut
     */
    public function getDefaultDeliveryAddress(Request $request)
    {
        $user = $request->user();
        $address = $user->deliveryAddresses()->where('is_default', true)->first();

        if (!$address) {
            $address = $user->deliveryAddresses()->latest()->first();
        }

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    /**
     * Mettre à jour une adresse de livraison
     */
    public function updateDeliveryAddress(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $deliveryAddress = $user->deliveryAddresses()->findOrFail($id);

        $deliveryAddress->update([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'city' => $request->city,
            'commune' => $request->commune,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => $request->notes,
            'is_default' => $request->is_default ?? $deliveryAddress->is_default,
        ]);

        // Si c'est défini comme adresse par défaut
        if ($request->is_default) {
            $deliveryAddress->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Adresse de livraison mise à jour avec succès',
            'data' => $deliveryAddress
        ]);
    }

    /**
     * Définir une adresse comme adresse par défaut
     */
    public function setDefaultDeliveryAddress(Request $request, $id)
    {
        $user = $request->user();
        $deliveryAddress = $user->deliveryAddresses()->findOrFail($id);

        $deliveryAddress->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Adresse définie comme adresse par défaut',
            'data' => $deliveryAddress
        ]);
    }

    /**
     * Supprimer une adresse de livraison
     */
    public function deleteDeliveryAddress(Request $request, $id)
    {
        $user = $request->user();
        $deliveryAddress = $user->deliveryAddresses()->findOrFail($id);

        // Si c'est l'adresse par défaut, définir une autre comme par défaut
        if ($deliveryAddress->is_default) {
            $newDefault = $user->deliveryAddresses()->where('id', '!=', $id)->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $deliveryAddress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Adresse de livraison supprimée avec succès'
        ]);
    }

    // ==================== API Methods ====================

    /**
     * Get user profile with API response format
     */
    public function apiProfile(Request $request)
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
     * Update user profile with API response format
     */
    public function apiUpdateProfile(Request $request)
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
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $user->update($request->only(['name', 'email', 'phone', 'city', 'bio', 'location']));

            return $this->successResponse($user, 'Profil mis à jour avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour du profil', 500);
        }
    }

    /**
     * Update password with API response format
     */
    public function apiUpdatePassword(Request $request)
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
     * Upload avatar with API response format
     */
    public function apiUploadAvatar(Request $request)
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
     * Get user statistics with API response format
     */
    public function apiGetStats(Request $request)
    {
        try {
            $user = $request->user();
            
            $stats = [
                'total_items' => $user->items()->count(),
                'active_items' => $user->items()->where('status', 'active')->count(),
                'sold_items' => $user->items()->where('status', 'sold')->count(),
                'total_sales' => $user->ordersAsSeller()->where('status', 'delivered')->count(),
                'total_purchases' => $user->ordersAsBuyer()->where('status', 'delivered')->count(),
                'total_revenue' => $user->ordersAsSeller()->where('status', 'delivered')->sum('total_price'),
                'total_spent' => $user->ordersAsBuyer()->where('status', 'delivered')->sum('total_price'),
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
     * Get user's items with API response format
     */
    public function apiGetItems(Request $request)
    {
        try {
            $user = $request->user();
            
            $items = $user->items()
                         ->with(['category', 'brand'])
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->per_page ?? 12);

            return $this->paginatedResponse($items, 'Articles récupérés avec succès');
        } catch (\Exception $e) {
            \Log::error('apiGetItems error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des articles: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get user's orders with API response format
     */
    public function apiGetOrders(Request $request)
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
     * Get user's sales with API response format
     */
    public function apiGetSales(Request $request)
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
     * Get user's reviews with API response format
     */
    public function apiGetReviews(Request $request)
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
     * Delete user account with API response format
     */
    public function apiDestroy(Request $request)
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
