<?php

namespace App\Http\Controllers\Api\DeliveryAddress;

use App\Http\Controllers\Api\ApiController;
use App\Models\DeliveryAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeliveryAddressController extends ApiController
{
    /**
     * API: Liste des adresses de livraison de l'utilisateur
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $addresses = Auth::user()->deliveryAddresses()
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($addresses, 'Adresses de livraison récupérées');
        } catch (\Exception $e) {
            Log::error('API DeliveryAddress index error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des adresses', 500);
        }
    }

    /**
     * API: Détail d'une adresse de livraison
     */
    public function show($id): JsonResponse
    {
        try {
            $address = Auth::user()->deliveryAddresses()->findOrFail($id);

            return $this->successResponse($address, 'Adresse de livraison récupérée');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Adresse de livraison introuvable', 404);
        } catch (\Exception $e) {
            Log::error('API DeliveryAddress show error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération de l\'adresse', 500);
        }
    }

    /**
     * API: Créer une adresse de livraison
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $user = $request->user();

            $isDefault = $request->boolean('is_default') || $user->deliveryAddresses()->count() === 0;

            $address = $user->deliveryAddresses()->create([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'city' => $request->city,
                'commune' => $request->commune,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'is_default' => $isDefault,
            ]);

            if ($isDefault) {
                $address->setAsDefault();
            }

            return $this->successResponse($address, 'Adresse de livraison enregistrée avec succès', 201);
        } catch (\Exception $e) {
            Log::error('API DeliveryAddress store error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'enregistrement de l\'adresse', 500);
        }
    }

    /**
     * API: Mettre à jour une adresse de livraison
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $user = $request->user();
            $address = $user->deliveryAddresses()->findOrFail($id);

            $address->update([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'city' => $request->city,
                'commune' => $request->commune,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'is_default' => $request->boolean('is_default') || $address->is_default,
            ]);

            if ($request->boolean('is_default')) {
                $address->setAsDefault();
            }

            return $this->successResponse($address, 'Adresse de livraison mise à jour avec succès');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Adresse de livraison introuvable', 404);
        } catch (\Exception $e) {
            Log::error('API DeliveryAddress update error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de l\'adresse', 500);
        }
    }

    /**
     * API: Définir une adresse comme adresse par défaut
     */
    public function setDefault($id): JsonResponse
    {
        try {
            $address = Auth::user()->deliveryAddresses()->findOrFail($id);

            $address->setAsDefault();

            return $this->successResponse($address, 'Adresse définie comme adresse par défaut');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Adresse de livraison introuvable', 404);
        } catch (\Exception $e) {
            Log::error('API DeliveryAddress setDefault error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de l\'adresse', 500);
        }
    }

    /**
     * API: Supprimer une adresse de livraison
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $address = $user->deliveryAddresses()->findOrFail($id);

            if ($address->is_default) {
                $newDefault = $user->deliveryAddresses()
                    ->where('id', '!=', $id)
                    ->first();

                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            $address->delete();

            return $this->successResponse(null, 'Adresse de livraison supprimée avec succès');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Adresse de livraison introuvable', 404);
        } catch (\Exception $e) {
            Log::error('API DeliveryAddress destroy error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de l\'adresse', 500);
        }
    }

    /**
     * Règles de validation communes.
     */
    protected function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'nullable|boolean',
        ];
    }
}
