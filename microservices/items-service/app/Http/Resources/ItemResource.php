<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrls = $this->imageUrls();

        return [
            'id' => $this->public_id,
            'seller_id' => (int) $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'quantity' => (int) $this->quantity,
            'condition' => $this->condition,
            'status' => $this->status,
            'color' => $this->color,
            'size' => $this->size,
            'item_number' => $this->item_number,
            'views' => (int) $this->views,
            'images' => $this->images ?? [],
            'image_urls' => $imageUrls,
            'first_image_url' => $imageUrls[0] ?? null,
            'specifications' => $this->specifications,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
