<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ItemService
{
    protected $verificationService;

    public function __construct(ItemVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Création d'un article (canal web + API).
     * Tous les articles passent par la vérification admin.
     */
    public function createItem(Request $request): Item
    {
        $item = new Item();
        $item->user_id = auth()->id() ?? $request->user()?->id;

        $this->applyItemData($item, $request);

        $item->status = 'pending_verification';

        if ($request->hasFile('images')) {
            $item->images = $this->uploadImages($request->file('images'));
        }

        $this->applyVerification($item);

        $item->save();

        event(new \App\Events\ItemCreated($item));

        return $item;
    }

    /**
     * Mise à jour d'un article (canal web + API).
     * La vérification IA n'est relancée que si de nouvelles images sont uploadées.
     */
    public function updateItem(Item $item, Request $request): Item
    {
        $this->applyItemData($item, $request);

        if ($request->hasFile('images')) {
            $currentImages = $item->images ?? [];
            $item->images = array_merge($currentImages, $this->uploadImages($request->file('images')));
        }

        if ($request->hasFile('images')) {
            $this->applyVerification($item);
        }

        $item->save();

        return $item;
    }

    /**
     * Suppression d'un article et de ses images (canal web + API).
     */
    public function deleteItem(Item $item): void
    {
        if ($item->images && is_array($item->images)) {
            foreach ($item->images as $image) {
                try {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                } catch (\Exception $e) {
                    Log::warning("Impossible de supprimer l'image: {$image}", ['error' => $e->getMessage()]);
                }
            }
        }

        $item->delete();
    }

    /**
     * Applique les champs de base + spécifications (uniquement les champs présents,
     * pour supporter les mises à jour partielles de l'API).
     */
    protected function applyItemData(Item $item, Request $request): void
    {
        $fields = [
            'name', 'description', 'price', 'currency', 'quantity', 'condition',
            'category_id', 'brand_id', 'color', 'size', 'item_number',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $item->{$field} = $request->{$field};
            }
        }

        $this->applySpecifications($item, $request);
    }

    /**
     * Normalise les spécifications {key: [], value: []} en tableau associatif.
     */
    protected function applySpecifications(Item $item, Request $request): void
    {
        if ($request->filled('specifications') && is_array($request->specifications)) {
            $specifications = [];
            $keys = $request->specifications['key'] ?? [];
            $values = $request->specifications['value'] ?? [];

            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index]) && !empty($values[$index])) {
                    $specifications[$key] = $values[$index];
                }
            }

            if (!empty($specifications)) {
                $item->specifications = $specifications;
            } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
                $item->specifications = null;
            }
        }
    }

    /**
     * Upload des images dans le dossier 'items' + synchronisation Hostinger.
     */
    protected function uploadImages(array $files): array
    {
        $images = [];

        if (!Storage::disk('public')->exists('items')) {
            Storage::disk('public')->makeDirectory('items');
        }

        foreach ($files as $image) {
            $filename = time() . '_' . Str::random(10) . '.' . $image->guessExtension();
            $path = $image->storeAs('items', $filename, 'public');

            if (!Storage::disk('public')->exists($path)) {
                throw new \Exception('Erreur lors de l\'upload de l\'image.');
            }

            StorageSyncService::syncFile($path);
            $images[] = $path;
        }

        return $images;
    }

    /**
     * Pipeline IA de pré-vérification (images + texte + cohérence).
     * En cas d'échec, l'article reste en attente de vérification manuelle.
     */
    protected function applyVerification(Item $item): void
    {
        if ($item->images && is_array($item->images) && count($item->images) >= 3) {
            try {
                $category = Category::find($item->category_id);
                $brand = Brand::find($item->brand_id);

                $verification = $this->verificationService->verifyItem(
                    $item->images,
                    $item->name,
                    $item->description ?? '',
                    $brand->name ?? null,
                    $category->name ?? null
                );

                $item->verification_status = $verification['status'];
                $item->verification_score = $verification['score'];
                $item->verification_details = $verification['details'];

                // Tous les articles restent en attente de vérification manuelle
                if ($item->verification_status !== 'rejected') {
                    $item->verification_status = 'pending';
                }
                $item->status = 'pending_verification';
            } catch (\Exception $e) {
                Log::error('Erreur vérification automatique', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $item->verification_status = 'pending';
                $item->status = 'pending_verification';
                $item->verification_details = [
                    'error' => 'Erreur lors de la vérification automatique',
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $item->verification_status = 'pending';
            $item->status = 'pending_verification';
            $item->verification_details = [
                'reason' => 'Nombre d\'images insuffisant (minimum 3 requises)'
            ];
        }
    }
}
