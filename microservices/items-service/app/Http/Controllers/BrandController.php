<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ItemResource;
use App\Models\Brand;
use App\Services\ItemQuery;
use App\Support\Identity;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends ApiController
{
    public function __construct(private readonly ItemQuery $query) {}

    public function index(): JsonResponse
    {
        $brands = Brand::query()
            ->active()
            ->withCount(['items' => fn ($query) => $query->active()])
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            ['brands' => BrandResource::collection($brands)->resolve()],
            'Marques récupérées.',
        );
    }

    public function show(Brand $brand): JsonResponse
    {
        $brand->loadCount(['items' => fn ($query) => $query->active()]);

        return $this->successResponse(['brand' => (new BrandResource($brand))->resolve()], 'Marque récupérée.');
    }

    public function items(Request $request, Brand $brand): JsonResponse
    {
        $paginator = $this->query->paginate($request, ['brand_id' => $brand->id]);

        return $this->successResponse(
            ['items' => ItemResource::collection($paginator->items())->resolve()],
            'Articles de la marque.',
            $this->paginationMeta($paginator),
        );
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validated();
        $data['slug'] = Slug::unique(Brand::class, $data['slug'] ?? $data['name']);

        $brand = Brand::create($data);

        return $this->successResponse(
            ['brand' => (new BrandResource($brand))->resolve()],
            'Marque créée.',
            [],
            201,
        );
    }

    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validated();

        if (array_key_exists('name', $data) || array_key_exists('slug', $data)) {
            $data['slug'] = Slug::unique(
                Brand::class,
                $data['slug'] ?? $data['name'] ?? $brand->name,
                $brand->id,
            );
        }

        $brand->update($data);

        return $this->successResponse(
            ['brand' => (new BrandResource($brand->fresh()))->resolve()],
            'Marque mise à jour.',
        );
    }

    public function destroy(Request $request, Brand $brand): JsonResponse
    {
        $this->authorizeAdmin($request);

        $itemsCount = $brand->items()->count();
        if ($itemsCount > 0) {
            return $this->errorResponse("Impossible de supprimer : {$itemsCount} article(s) utilisent cette marque.", 409);
        }

        $brand->delete();

        return $this->successResponse(null, 'Marque supprimée.');
    }

    private function authorizeAdmin(Request $request): void
    {
        if (! Identity::isAdmin($request)) {
            abort(403, 'Action réservée aux administrateurs.');
        }
    }
}
