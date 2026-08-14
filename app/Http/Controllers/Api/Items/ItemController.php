<?php

namespace App\Http\Controllers\Api\Items;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends ApiController
{
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * API: Liste des articles (lecture publique)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Item::with(['category', 'brand', 'user'])
            ->where('status', 'active')
            ->visible();

        // Filtres
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ]
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * API: Détails d'un article (lecture publique)
     */
    public function show($id): JsonResponse
    {
        $item = Item::with(['category', 'brand', 'user', 'reviews'])
            ->findOrFail($id);

        $isOwner = Auth::check() && $item->user_id === Auth::id();

        // Un article non actif (bloqué/suspendu/rejeté/vendu) reste visible
        // uniquement pour son propriétaire (ex. depuis la notification de modération).
        if (!$isOwner && ($item->status !== 'active' || !$item->isAvailable())) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * API: Recherche (priorise les articles boostés)
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        $category = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $condition = $request->get('condition');

        $boostedItems = Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereHas('activeBoosts')
            ->where('status', 'active')
            ->visible();

        $regularItems = Item::with(['category', 'brand', 'user'])
            ->whereDoesntHave('activeBoosts')
            ->where('status', 'active')
            ->visible();

        foreach ([$boostedItems, $regularItems] as $items) {
            if ($query) {
                $items->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            }

            if ($category) {
                $items->where('category_id', $category);
            }

            if ($minPrice) {
                $items->where('price', '>=', $minPrice);
            }

            if ($maxPrice) {
                $items->where('price', '<=', $maxPrice);
            }

            if ($condition) {
                $items->where('condition', $condition);
            }
        }

        $boostedResults = $boostedItems->orderBy('created_at', 'desc')->get();
        $regularResults = $regularItems->orderBy('created_at', 'desc')->get();
        $allResults = $boostedResults->concat($regularResults);

        $perPage = 12;
        $currentPage = (int) $request->get('page', 1);
        $total = $allResults->count();
        $items = $allResults->forPage($currentPage, $perPage);

        return response()->json([
            'success' => true,
            'data' => $items->values(),
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => (int) ceil($total / $perPage),
            ]
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * API: Créer un article
     */
    public function store(StoreItemRequest $request): JsonResponse
    {
        try {
            $item = $this->itemService->createItem($request);

            return response()->json([
                'success' => true,
                'message' => 'Article créé avec succès',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Mettre à jour un article
     */
    public function update(UpdateItemRequest $request, $id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);

            if ($item->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $item = $this->itemService->updateItem($item, $request);

            return response()->json([
                'success' => true,
                'message' => 'Article mis à jour avec succès',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Supprimer un article
     */
    public function destroy($id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);

            if ($item->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $this->itemService->deleteItem($item);

            return response()->json([
                'success' => true,
                'message' => 'Article supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Toggle favorite
     */
    public function toggleFavorite(Item $item): JsonResponse
    {
        $user = Auth::user();

        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->detach($item->id);
            $message = 'Article retiré des favoris';
        } else {
            $user->favorites()->attach($item->id);
            $message = 'Article ajouté aux favoris';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $user->favorites()->where('item_id', $item->id)->exists()
        ]);
    }
}
