<?php

namespace App\Http\Controllers\Api\Marketing;

use App\Http\Controllers\Api\ApiController;
use App\Models\Item;
use App\Models\Offer;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OfferController extends ApiController
{
    /**
     * API publique : liste des promotions en cours.
     */
    public function promotions(Request $request): JsonResponse
    {
        try {
            $offers = Offer::running()
                ->with('categories')
                ->with(['items' => function ($q) {
                    $q->where('status', 'active')->withAvg('reviews', 'rating');
                }])
                ->orderByDesc('is_featured')
                ->orderByDesc('is_flash_sale')
                ->orderByDesc('created_at')
                ->paginate($request->get('per_page', 15));

            return $this->paginatedResponse($offers, 'Promotions récupérées avec succès');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur/admin : détail d'une promo (les siennes).
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $offer = Offer::with(['categories', 'items', 'creator'])
                ->findOrFail($id);

            if (! $this->canManage()) {
                return $this->forbiddenResponse();
            }

            if (! $this->isOwner($request->user(), $offer)) {
                return $this->forbiddenResponse('Vous ne pouvez pas consulter cette offre');
            }

            return $this->successResponse($offer, 'Offre récupérée avec succès');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Offre non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur : liste de ses offres.
     */
    public function myOffers(Request $request): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $query = Offer::with('creator');

            if (! $request->user()->isAdmin()) {
                $query->where('created_by', $request->user()->id);
            }

            $offers = $query->orderByDesc('created_at')->paginate(20);

            return $this->paginatedResponse($offers, 'Offres récupérées avec succès');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur/admin : créer une offre.
     */
    public function store(Request $request): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $data = $this->validateOffer($request);

            if (! $request->user()->isAdmin()) {
                $data['scope'] = 'items';
                $requestedItems = $request->input('items', []);
                $itemIds = Item::whereIn('id', $requestedItems)
                    ->where('user_id', $request->user()->id)
                    ->pluck('id')
                    ->all();
                $categoryIds = [];
            } else {
                $itemIds = $request->input('items', []);
                $categoryIds = $request->input('categories', []);
            }

            unset($data['items'], $data['categories']);

            $offer = Offer::create([
                ...$data,
                'created_by' => $request->user()->id,
                'status' => $request->input('status', 'active'),
            ]);

            $this->syncTargets($offer, $itemIds, $categoryIds);

            Item::clearRunningOffersCache();

            if ($offer->status === 'active') {
                app(NotificationService::class)->notifyClientsOfNewOffer($offer);
            }

            $offer->load(['categories', 'items', 'creator']);

            return $this->createdResponse($offer, 'Offre créée avec succès');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur/admin : modifier une offre.
     */
    public function update(Request $request, $id): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $offer = Offer::findOrFail($id);

            if (! $this->isOwner($request->user(), $offer)) {
                return $this->forbiddenResponse('Vous ne pouvez pas gérer cette offre');
            }

            $data = $this->validateOffer($request);

            if (! $request->user()->isAdmin()) {
                $data['scope'] = 'items';
                $requestedItems = $request->input('items', []);
                $filtered = Item::whereIn('id', $requestedItems)
                    ->where('user_id', $request->user()->id)
                    ->pluck('id')
                    ->all();

                $itemIds = $filtered;
            } else {
                $itemIds = $request->input('items', []);
            }

            $categoryIds = $request->user()->isAdmin() ? $request->input('categories', []) : [];
            $this->syncTargets($offer, $itemIds, $categoryIds);

            unset($data['items'], $data['categories']);

            $offer->update($data);

            Item::clearRunningOffersCache();

            $offer->load(['categories', 'items', 'creator']);

            return $this->updatedResponse($offer, 'Offre mise à jour avec succès');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Offre non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur/admin : activation / pause.
     */
    public function toggleStatus(Request $request, $id): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $offer = Offer::findOrFail($id);

            if (! $this->isOwner($request->user(), $offer)) {
                return $this->forbiddenResponse('Vous ne pouvez pas gérer cette offre');
            }

            $offer->status = $offer->status === 'active' ? 'paused' : 'active';
            $offer->save();

            Item::clearRunningOffersCache();

            return $this->updatedResponse(['id' => $offer->id, 'status' => $offer->fresh()->status], 'Statut de l\'offre mis à jour');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Offre non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur/admin : supprimer une offre.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $offer = Offer::findOrFail($id);

            if (! $this->isOwner($request->user(), $offer)) {
                return $this->forbiddenResponse('Vous ne pouvez pas gérer cette offre');
            }

            $offer->delete();

            Item::clearRunningOffersCache();

            return $this->deletedResponse('Offre supprimée avec succès');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Offre non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    protected function canManage(): bool
    {
        return auth()->check() && (auth()->user()->isSeller() || auth()->user()->isAdmin());
    }

    protected function isOwner($user, Offer $offer): bool
    {
        return $user->isAdmin() || $offer->created_by === $user->id;
    }

    protected function validateOffer(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'scope' => ['required', 'in:global,categories,items'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required_if:type,fixed', 'string', 'in:USD,CDF'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['nullable', 'in:active,paused'],
            'is_flash_sale' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'max_redemptions' => ['nullable', 'integer', 'min:1'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'items' => ['nullable', 'array'],
            'items.*' => ['integer', 'exists:items,id'],
        ]);

        return array_merge($data, [
            'is_flash_sale' => $request->boolean('is_flash_sale'),
            'is_featured' => $request->boolean('is_featured'),
        ]);
    }

    protected function syncTargets(Offer $offer, array $itemIds, array $categoryIds): void
    {
        $offer->categories()->sync($categoryIds);
        $offer->items()->sync($itemIds);
    }
}
