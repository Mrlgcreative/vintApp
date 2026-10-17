<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:120', 'alpha_dash'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:50'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'color' => ['sometimes', 'nullable', 'string', 'max:7'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id', $this->notSelf()],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'show_in_menu' => ['sometimes', 'boolean'],
            'meta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_keywords' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Une catégorie ne peut pas être son propre parent.
     */
    private function notSelf(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $category = $this->route('category');

            if ($category instanceof Category && (int) $value === $category->getKey()) {
                $fail('Une catégorie ne peut pas être son propre parent.');
            }
        };
    }
}
