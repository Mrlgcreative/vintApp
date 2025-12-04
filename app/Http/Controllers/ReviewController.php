<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Traits\ApiResponses;

class ReviewController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::with(['reviewer', 'seller', 'item', 'order'])->latest()->paginate(15);
        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        $orders = Order::orderBy('id', 'desc')->get();
        return view('reviews.create', compact('users', 'items', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'seller_id' => 'required|exists:users,id|different:reviewer_id',
            'item_id' => 'nullable|exists:items,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'status' => 'nullable|string|max:20',
        ]);
        // Logique métier : un utilisateur ne peut laisser qu'un avis par commande
        if ($validated['order_id'] && Review::where('order_id', $validated['order_id'])->where('reviewer_id', $validated['reviewer_id'])->exists()) {
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà laissé un avis pour cette commande.'
                ], 422);
            }
            
            return redirect()->back()->withInput()->withErrors(['order_id' => 'Vous avez déjà laissé un avis pour cette commande.']);
        }
        
        $review = Review::create($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Merci pour votre avis !',
                'review' => $review->load(['reviewer', 'seller', 'item'])
            ]);
        }
        
        return redirect()->route('reviews.index')->with('success', 'Avis ajouté !');
    }

    /**
     * Store review via AJAX for post-payment rating
     */
    public function storePostPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        
        // Vérifier que l'utilisateur connecté est bien l'acheteur
        if ($order->buyer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        // Vérifier si une note existe déjà
        if (Review::where('order_id', $order->id)->where('reviewer_id', Auth::id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà noté cette commande'
            ], 422);
        }

        $review = Review::create([
            'reviewer_id' => Auth::id(),
            'seller_id' => $order->seller_id,
            'item_id' => $order->item_id,
            'order_id' => $order->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'approved'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre avis !',
            'review' => $review
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load(['reviewer', 'seller', 'item', 'order']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $users = User::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        $orders = Order::orderBy('id', 'desc')->get();
        return view('reviews.edit', compact('review', 'users', 'items', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'seller_id' => 'required|exists:users,id|different:reviewer_id',
            'item_id' => 'nullable|exists:items,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'status' => 'nullable|string|max:20',
        ]);
        $review->update($validated);
        return redirect()->route('reviews.index')->with('success', 'Avis modifié !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Avis supprimé !');
    }

    // ==================== API Methods ====================

    /**
     * Get all reviews for API
     */
    public function apiIndex(Request $request)
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
     * Get reviews for a specific item
     */
    public function apiItemReviews(Request $request, $itemId)
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
     * Get reviews for a specific seller
     */
    public function apiSellerReviews(Request $request, $sellerId)
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
     * Store review via API
     */
    public function apiStore(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
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
     * Update review via API
     */
    public function apiUpdate(Request $request, $reviewId)
    {
        try {
            $review = Review::findOrFail($reviewId);

            if ($review->reviewer_id !== $request->user()->id) {
                return $this->errorResponse('Non autorisé', 403);
            }

            $validator = \Validator::make($request->all(), [
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
     * Delete review via API
     */
    public function apiDestroy(Request $request, $reviewId)
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
