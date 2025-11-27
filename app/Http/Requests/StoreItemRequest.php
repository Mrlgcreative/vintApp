<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'currency' => ['required', 'string', 'in:USD,EUR,XAF,XOF'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'condition' => ['required', 'in:new,like_new,good,fair,poor'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'material' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5MB max
            'authenticity_guaranteed' => ['boolean'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du produit est obligatoire',
            'name.min' => 'Le nom doit contenir au moins 3 caractères',
            'description.required' => 'La description est obligatoire',
            'description.min' => 'La description doit contenir au moins 10 caractères',
            'price.required' => 'Le prix est obligatoire',
            'price.min' => 'Le prix doit être positif',
            'currency.in' => 'Devise non supportée',
            'category_id.exists' => 'Cette catégorie n\'existe pas',
            'condition.in' => 'État du produit invalide',
            'images.required' => 'Au moins une image est requise',
            'images.max' => 'Maximum 10 images autorisées',
            'images.*.image' => 'Le fichier doit être une image',
            'images.*.max' => 'Taille maximale par image : 5MB',
        ];
    }

    /**
     * Sanitize input data
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags($this->name),
            'description' => strip_tags($this->description, '<p><br><strong><em><ul><ol><li>'),
            'price' => floatval($this->price),
            'quantity' => intval($this->quantity ?? 1),
        ]);
    }
}
