<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasPublicId;

    public const CONDITIONS = ['new', 'like_new', 'good', 'fair', 'poor'];

    public const STATUSES = ['active', 'inactive', 'sold', 'pending', 'pending_verification'];

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'currency',
        'quantity',
        'condition',
        'category_id',
        'brand_id',
        'status',
        'specifications',
        'images',
        'views',
        'color',
        'size',
        'item_number',
    ];

    protected $casts = [
        'specifications' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'views' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Un article n'est public que lorsqu'il est actif. La modération et la
     * suspension sont hors périmètre v1 : `active` est le seul état visible.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->active();
    }

    public function scopeCondition(Builder $query, string $condition): Builder
    {
        return $query->where('condition', $condition);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * @return array<int, string>
     */
    public function imageUrls(): array
    {
        return array_values(array_map(
            static fn (string $path): string => asset('storage/'.$path),
            $this->images ?? [],
        ));
    }
}
