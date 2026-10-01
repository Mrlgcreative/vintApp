<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => $this->input('currency') ?? config('items.currency.default', 'USD'),
            'condition' => $this->input('condition') ?? 'good',
            'quantity' => $this->input('quantity') ?? 1,
            'status' => $this->input('status') ?? 'active',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['required', Rule::in(config('items.currency.supported'))],
            'quantity' => ['required', 'integer', 'min:0'],
            'condition' => ['required', Rule::in(Item::CONDITIONS)],
            // La mise en ligne directe est permise ; `pending_verification` et
            // `sold` relèvent d'un flux (modération / commande) hors de cette route.
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'specifications' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
            'color' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
            'item_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}
