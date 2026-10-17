<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'parent_id' => $this->parent_id,
            'parent' => new self($this->whenLoaded('parent')),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'image' => $this->image,
            'color' => $this->color,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'show_in_menu' => (bool) $this->show_in_menu,
            'sort_order' => (int) $this->sort_order,
            'items_count' => $this->whenCounted('items'),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
