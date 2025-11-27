<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'payment_method' => ['required', 'string', 'in:cinetpay,mobile_money,card,wallet,cash_on_delivery'],
            'delivery_address' => ['required', 'string', 'min:10', 'max:500'],
            'delivery_city' => ['required', 'string', 'max:100'],
            'delivery_phone' => ['required', 'string', 'regex:/^[+]?[0-9]{8,15}$/'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
            'coupon_code' => ['nullable', 'string', 'max:50', 'exists:coupons,code'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'item_id.exists' => 'Ce produit n\'existe pas',
            'quantity.min' => 'Quantité minimale : 1',
            'quantity.max' => 'Quantité maximale : 100',
            'payment_method.in' => 'Méthode de paiement non supportée',
            'delivery_address.required' => 'L\'adresse de livraison est obligatoire',
            'delivery_phone.regex' => 'Format de téléphone invalide',
        ];
    }

    /**
     * Sanitize input data
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'delivery_address' => strip_tags($this->delivery_address),
            'delivery_city' => strip_tags($this->delivery_city),
            'delivery_phone' => preg_replace('/[^0-9+]/', '', $this->delivery_phone),
            'quantity' => intval($this->quantity ?? 1),
        ]);
    }
}
