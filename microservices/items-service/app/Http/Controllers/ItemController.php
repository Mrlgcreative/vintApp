<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Services\ItemQuery;
use App\Services\ItemService;
use App\Support\Identity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends ApiController
{
    public function __construct(
        private readonly ItemService $items,
        private readonly ItemQuery $query,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->query->paginate($request);

        return $this->successResponse(
            ['items' => ItemResource::collection($paginator->items())->resolve()],
            'Articles récupérés.',
            $this->paginationMeta($paginator),
        );
    }

    public function show(Item $item): JsonResponse
    {
        // Un article non actif n'est pas public : sa consultation par son
        // propriétaire passe par `/v1/me/items`.
        abort_unless($item->status === 'active', 404);

        $item->load(['category', 'brand']);

        return $this->successResponse(['item' => (new ItemResource($item))->resolve()], 'Article récupéré.');
    }

    public function mine(Request $request): JsonResponse
    {
        $sellerId = $this->requireIdentity($request);

        $paginator = $this->query->paginate($request, ['seller_id' => $sellerId]);

        return $this->successResponse(
            ['items' => ItemResource::collection($paginator->items())->resolve()],
            'Vos articles.',
            $this->paginationMeta($paginator),
        );
    }

    public function store(StoreItemRequest $request): JsonResponse
    {
        $sellerId = $this->requireIdentity($request);

        $item = $this->items->create($request->validated(), $sellerId)->load(['category', 'brand']);

        return $this->successResponse(
            ['item' => (new ItemResource($item))->resolve()],
            'Article créé.',
            [],
            201,
        );
    }

    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        $this->authorizeSeller($request, $item);

        $updated = $this->items->update($item, $request->validated())->load(['category', 'brand']);

        return $this->successResponse(['item' => (new ItemResource($updated))->resolve()], 'Article mis à jour.');
    }

    public function destroy(Request $request, Item $item): JsonResponse
    {
        $this->authorizeSeller($request, $item);

        $this->items->delete($item);

        return $this->successResponse(null, 'Article supprimé.');
    }

    private function requireIdentity(Request $request): int
    {
        $sellerId = Identity::id($request);

        abort_if($sellerId === null, 403, 'Identité sans user_id.');

        return $sellerId;
    }

    private function authorizeSeller(Request $request, Item $item): void
    {
        $actor = Identity::id($request);

        if ($actor === null || ($actor !== (int) $item->user_id && ! Identity::isAdmin($request))) {
            abort(403, "Action réservée au vendeur de l'article.");
        }
    }
}
