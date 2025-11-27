<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $userId = auth()->id();
        
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone' => ['nullable', 'string', 'regex:/^[+]?[0-9]{8,15}$/', Rule::unique('users')->ignore($userId)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'], // 2MB max
            'theme_preference' => ['sometimes', 'in:light,dark,system'],
            'newsletter_subscribed' => ['boolean'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'name.min' => 'Le nom doit contenir au moins 2 caractères',
            'email.unique' => 'Cet email est déjà utilisé',
            'phone.regex' => 'Format de téléphone invalide',
            'phone.unique' => 'Ce numéro est déjà utilisé',
            'avatar.max' => 'Taille maximale de l\'avatar : 2MB',
            'bio.max' => 'La bio ne peut dépasser 1000 caractères',
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
        if ($this->has('bio')) {
            $this->merge(['bio' => strip_tags($this->bio)]);
        }
        if ($this->has('phone')) {
            $cleaned = preg_replace('/[^0-9+]/', '', $this->phone);
            $this->merge(['phone' => $cleaned]);
        }
    }
}
