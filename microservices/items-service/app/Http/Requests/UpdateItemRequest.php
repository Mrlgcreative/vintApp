<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['sometimes', 'required', Rule::in(config('items.currency.supported'))],
            'quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'condition' => ['sometimes', 'required', Rule::in(Item::CONDITIONS)],
            'status' => ['sometimes', 'required', Rule::in(Item::STATUSES)],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'brand_id' => ['sometimes', 'nullable', 'integer', 'exists:brands,id'],
            'specifications' => ['sometimes', 'nullable', 'array'],
            'images' => ['sometimes', 'nullable', 'array'],
            'images.*' => ['string'],
            'color' => ['sometimes', 'nullable', 'string', 'max:100'],
            'size' => ['sometimes', 'nullable', 'string', 'max:100'],
            'item_number' => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }
}
