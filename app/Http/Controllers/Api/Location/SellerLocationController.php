<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Api\ApiController;
use App\Models\AllowedCity;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SellerLocationController extends ApiController
{
    /**
     * GET /api/v1/seller-location
     * Récupère la localisation du vendeur connecté.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return $this->successResponse([
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'city' => $user->city,
                'commune' => $user->commune,
                'address' => $user->address,
                'location' => $user->location,
                'location_updated_at' => $user->location_updated_at?->toISOString(),
            ], 'Localisation récupérée');
        } catch (\Exception $e) {
            Log::error('API SellerLocation index error: ' . $e->getMessage());
            return $this->serverErrorResponse('Erreur lors de la récupération de la localisation', $e);
        }
    }

    /**
     * PUT /api/v1/seller-location
     * Met à jour la localisation du vendeur connecté.
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city'      => 'nullable|string|max:100',
            'commune'   => 'nullable|string|max:100',
            'address'   => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $user = $request->user();

            $data = [
                'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'location_updated_at' => now(),
            ];

            if ($request->filled('city')) {
                $data['city'] = $request->city;
            }
            if ($request->filled('commune')) {
                $data['commune'] = $request->commune;
            }
            if ($request->filled('address')) {
                $data['address'] = $request->address;
            }

            $user->update($data);

            return $this->successResponse([
                'latitude'            => $user->latitude,
            'longitude'           => $user->longitude,
            'city'                => $user->city,
            'commune'             => $user->commune,
            'address'             => $user->address,
            'location_updated_at' => $user->location_updated_at->toISOString(),
            ], 'Localisation mise à jour');
        } catch (\Exception $e) {
            Log::error('API SellerLocation update error: ' . $e->getMessage());
            return $this->serverErrorResponse('Erreur lors de la mise à jour de la localisation', $e);
        }
    }

    /**
     * GET /api/v1/seller-location/{userId}
     * Récupère la localisation d'un vendeur spécifique (public pour les acheteurs).
     */
    public function show($userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);

            return $this->successResponse([
                'user_id'  => $user->id,
                'name'     => $user->name,
                'avatar'   => $user->avatar,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'city'     => $user->city,
                'commune'  => $user->commune,
                'location' => $user->location,
            ], 'Localisation du vendeur récupérée');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Vendeur introuvable');
        } catch (\Exception $e) {
            Log::error('API SellerLocation show error: ' . $e->getMessage());
            return $this->serverErrorResponse('Erreur lors de la récupération de la localisation', $e);
        }
    }

    /**
     * GET /api/v1/sellers/nearby
     * Recherche les vendeurs à proximité.
     *
     * Query params :
     *   - latitude  (required)
     *   - longitude (required)
     *   - radius    (optional, default 50 km)
     *   - category_id (optional, filtre par catégorie)
     *   - per_page  (optional, default 20)
     */
    public function nearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius'       => 'nullable|numeric|min:1|max:500',
            'category_id'  => 'nullable|integer|exists:categories,id',
            'per_page'     => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $lat      = $request->latitude;
            $lng      = $request->longitude;
            $radius   = $request->input('radius', 50);
            $perPage  = $request->input('per_page', 20);

            // Haversine formula — distance en km
            $haversine = "(6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            ))";

            $query = User::select([
                'id', 'name', 'avatar', 'latitude', 'longitude',
                'city', 'commune', 'location',
                DB::raw("{$haversine} AS distance_km"),
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance_km', '<=', $radius)
            ->orderBy('distance_km');

            if ($request->filled('category_id')) {
                $query->whereHas('items', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id)
                      ->where('is_active', true);
                });
            }

            $sellers = $query->setBindings([$lat, $lng, $lat])
                ->paginate($perPage);

            return $this->paginatedResponse($sellers, 'Vendeurs à proximité récupérés');
        } catch (\Exception $e) {
            Log::error('API SellerLocation nearby error: ' . $e->getMessage());
            return $this->serverErrorResponse('Erreur lors de la recherche de vendeurs', $e);
        }
    }

    /**
     * DELETE /api/v1/seller-location
     * Supprime la localisation du vendeur connecté.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $request->user()->update([
                'latitude'            => null,
                'longitude'           => null,
                'city'                => null,
                'commune'             => null,
                'location_updated_at' => null,
            ]);

            return $this->deletedResponse('Localisation supprimée');
        } catch (\Exception $e) {
            Log::error('API SellerLocation destroy error: ' . $e->getMessage());
            return $this->serverErrorResponse('Erreur lors de la suppression de la localisation', $e);
        }
    }
}
