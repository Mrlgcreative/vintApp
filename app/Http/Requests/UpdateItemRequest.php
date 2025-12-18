<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $item = $this->route('item');
        return auth()->check() && auth()->id() === $item->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'currency' => ['sometimes', 'string', 'in:USD,EUR,XAF,XOF,CDF'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'condition' => ['sometimes', 'in:new,like_new,good,fair,poor'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'material' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:10000'],
            'status' => ['sometimes', 'in:active,sold,inactive'],
            'images' => ['sometimes', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    /**
     * Sanitize input data
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge(['name' => strip_tags($this->name)]);
        }
        if ($this->has('description')) {
            $this->merge(['description' => strip_tags($this->description, '<p><br><strong><em><ul><ol><li>')]);
        }
        if ($this->has('price')) {
            $this->merge(['price' => floatval($this->price)]);
        }
        if ($this->has('quantity')) {
            $this->merge(['quantity' => intval($this->quantity)]);
        }
    }
}
