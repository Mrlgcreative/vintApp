<?php

namespace App\Http\Controllers\Api\Reviews;

use App\Http\Controllers\Api\ApiController;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends ApiController
{
    /**
     * API: Tous les avis
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $reviews = Review::with(['reviewer', 'seller', 'item', 'order'])
                ->latest()
                ->paginate($request->per_page ?? 15);

            return $this->paginatedResponse($reviews, 'Avis récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des avis', 500);
        }
    }

    /**
     * API: Avis d'un article
     */
    public function itemReviews(Request $request, $itemId): JsonResponse
    {
        try {
            $reviews = Review::where('item_id', $itemId)
                ->with(['reviewer', 'seller'])
                ->latest()
                ->paginate($request->per_page ?? 10);

            $averageRating = Review::where('item_id', $itemId)->avg('rating');

            return $this->successResponse([
                'reviews' => $reviews->items(),
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $reviews->total(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ]
            ], 'Avis de l\'article récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des avis', 500);
        }
    }

    /**
     * API: Avis d'un vendeur
     */
    public function sellerReviews(Request $request, $sellerId): JsonResponse
    {
        try {
            $reviews = Review::where('seller_id', $sellerId)
                ->with(['reviewer', 'item'])
                ->latest()
                ->paginate($request->per_page ?? 10);

            $averageRating = Review::where('seller_id', $sellerId)->avg('rating');

            return $this->successResponse([
                'reviews' => $reviews->items(),
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $reviews->total(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ]
            ], 'Avis du vendeur récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des avis', 500);
        }
    }

    /**
     * API: Créer un avis
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $order = Order::findOrFail($request->order_id);

            if ($order->buyer_id !== $request->user()->id) {
                return $this->errorResponse('Non autorisé', 403);
            }

            if (Review::where('order_id', $order->id)->where('reviewer_id', $request->user()->id)->exists()) {
                return $this->errorResponse('Vous avez déjà noté cette commande', 422);
            }

            $review = Review::create([
                'reviewer_id' => $request->user()->id,
                'seller_id' => $order->seller_id,
                'item_id' => $order->item_id,
                'order_id' => $order->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'approved'
            ]);

            return $this->successResponse(
                $review->load(['reviewer', 'seller', 'item']),
                'Merci pour votre avis !',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la création de l\'avis', 500);
        }
    }

    /**
     * API: Mettre à jour un avis
     */
    public function update(Request $request, $reviewId): JsonResponse
    {
        try {
            $review = Review::findOrFail($reviewId);

            if ($review->reviewer_id !== $request->user()->id) {
                return $this->errorResponse('Non autorisé', 403);
            }

            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $review->update($request->only(['rating', 'comment']));

            return $this->successResponse(
                $review->load(['reviewer', 'seller', 'item']),
                'Avis mis à jour avec succès'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour de l\'avis', 500);
        }
    }

    /**
     * API: Supprimer un avis
     */
    public function destroy(Request $request, $reviewId): JsonResponse
    {
        try {
            $review = Review::findOrFail($reviewId);

            if ($review->reviewer_id !== $request->user()->id) {
                return $this->errorResponse('Non autorisé', 403);
            }

            $review->delete();

            return $this->successResponse(null, 'Avis supprimé avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la suppression de l\'avis', 500);
        }
    }
}
