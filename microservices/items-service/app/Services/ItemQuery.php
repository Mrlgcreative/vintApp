<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Construction des listes d'articles.
 *
 * Le tri est résolu via une table blanche : `orderBy($request->sort)` serait
 * une injection SQL déguisée. La pagination est plafonnée pour la même raison.
 */
class ItemQuery
{
    private const SORTS = [
        'recent' => ['created_at', 'desc'],
        'oldest' => ['created_at', 'asc'],
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'views' => ['views', 'desc'],
        'name' => ['name', 'asc'],
    ];

    /**
     * @param  array{seller_id?: int, category_id?: int, brand_id?: int}  $constraints
     */
    public function paginate(Request $request, array $constraints = []): LengthAwarePaginator
    {
        $query = Item::query()->with(['category', 'brand']);

        if (isset($constraints['seller_id'])) {
            // Vue vendeur : tous les statuts, y compris inactifs et vendus.
            $query->where('user_id', $constraints['seller_id']);
        } else {
            $query->active();
        }

        if (isset($constraints['category_id'])) {
            $query->where('category_id', $constraints['category_id']);
        }

        if (isset($constraints['brand_id'])) {
            $query->where('brand_id', $constraints['brand_id']);
        }

        $this->applyFilters($query, $request, $constraints);
        $this->applySort($query, $request);

        return $query->paginate($this->perPage($request));
    }

    /**
     * @param  array{seller_id?: int, category_id?: int, brand_id?: int}  $constraints
     */
    private function applyFilters(Builder $query, Request $request, array $constraints): void
    {
        if (! isset($constraints['category_id']) && $request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if (! isset($constraints['brand_id']) && $request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn (Builder $sub) => $sub->where('slug', $request->string('category')->toString()));
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn (Builder $sub) => $sub->where('slug', $request->string('brand')->toString()));
        }

        if ($request->filled('condition') && in_array($request->input('condition'), Item::CONDITIONS, true)) {
            $query->where('condition', $request->input('condition'));
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->string('currency')->toString());
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->float('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->float('max_price'));
        }

        if ($request->filled('search')) {
            $query->search($request->string('search')->toString());
        }
    }

    private function applySort(Builder $query, Request $request): void
    {
        [$column, $direction] = self::SORTS[$request->string('sort')->toString()] ?? self::SORTS['recent'];

        $query->orderBy($column, $direction);
    }

    private function perPage(Request $request): int
    {
        $default = (int) config('items.pagination.per_page', 15);
        $max = (int) config('items.pagination.max_per_page', 100);

        return max(1, min((int) $request->integer('per_page', $default), $max));
    }
}
