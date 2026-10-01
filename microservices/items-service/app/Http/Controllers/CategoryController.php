<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ItemResource;
use App\Models\Category;
use App\Services\ItemQuery;
use App\Support\Identity;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    public function __construct(private readonly ItemQuery $query) {}

    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->active()
            ->withCount(['items' => fn ($query) => $query->active()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            ['categories' => CategoryResource::collection($categories)->resolve()],
            'Catégories récupérées.',
        );
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['parent', 'children'])
            ->loadCount(['items' => fn ($query) => $query->active()]);

        return $this->successResponse(['category' => (new CategoryResource($category))->resolve()], 'Catégorie récupérée.');
    }

    public function items(Request $request, Category $category): JsonResponse
    {
        $paginator = $this->query->paginate($request, ['category_id' => $category->id]);

        return $this->successResponse(
            ['items' => ItemResource::collection($paginator->items())->resolve()],
            'Articles de la catégorie.',
            $this->paginationMeta($paginator),
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validated();
        $data['slug'] = Slug::unique(Category::class, $data['slug'] ?? $data['name']);

        $category = Category::create($data);

        return $this->successResponse(
            ['category' => (new CategoryResource($category))->resolve()],
            'Catégorie créée.',
            [],
            201,
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validated();

        if (array_key_exists('name', $data) || array_key_exists('slug', $data)) {
            $data['slug'] = Slug::unique(
                Category::class,
                $data['slug'] ?? $data['name'] ?? $category->name,
                $category->id,
            );
        }

        $category->update($data);

        return $this->successResponse(
            ['category' => (new CategoryResource($category->fresh()))->resolve()],
            'Catégorie mise à jour.',
        );
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $this->authorizeAdmin($request);

        $itemsCount = $category->items()->count();
        if ($itemsCount > 0) {
            return $this->errorResponse("Impossible de supprimer : {$itemsCount} article(s) rattaché(s).", 409);
        }

        if ($category->children()->exists()) {
            return $this->errorResponse('Impossible de supprimer : des sous-catégories existent.', 409);
        }

        $category->delete();

        return $this->successResponse(null, 'Catégorie supprimée.');
    }

    private function authorizeAdmin(Request $request): void
    {
        if (! Identity::isAdmin($request)) {
            abort(403, 'Action réservée aux administrateurs.');
        }
    }
}
