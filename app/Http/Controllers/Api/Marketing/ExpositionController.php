<?php

namespace App\Http\Controllers\Api\Marketing;

use App\Http\Controllers\Api\ApiController;
use App\Models\Exposition;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExpositionController extends ApiController
{
    /**
     * API publique : liste des expositions actives (en cours et à venir).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $expositions = Exposition::publiclyVisible()
                ->with(['user', 'items' => function ($q) {
                    $q->withAvg('reviews', 'rating')
                        ->withCount(['reviews', 'favoritedBy'])
                        ->where('status', 'active');
                }])
                ->orderByRaw('(starts_at IS NOT NULL AND starts_at > UTC_TIMESTAMP()) ASC')
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->paginate($request->get('per_page', 15));

            return $this->paginatedResponse($expositions, 'Expositions récupérées avec succès');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API publique : détail d'une exposition en cours.
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $exposition = Exposition::with(['user'])
                ->findOrFail($id);

            if ($exposition->status !== 'active') {
                return $this->notFoundResponse('Exposition non trouvée');
            }

            $exposition->increment('views');

            $exposition->setRelation('items', $exposition->items()
                ->where('status', 'active')
                ->withAvg('reviews', 'rating')
                ->withCount(['reviews', 'favoritedBy'])
                ->orderByDesc('exposition_item.id')
                ->paginate($request->get('per_page', 20)));

            return $this->successResponse($exposition, 'Exposition récupérée avec succès');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exposition non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API publique : incrémente le compteur de vues.
     */
    public function incrementViews($id): JsonResponse
    {
        try {
            $exposition = Exposition::findOrFail($id);
            $exposition->increment('views');

            return $this->successResponse(['views' => $exposition->fresh()->views], 'Compteur de vues mis à jour');
        } catch (\Exception $e) {
            return $this->notFoundResponse('Exposition non trouvée');
        }
    }

    /**
     * API vendeur : liste de ses expositions.
     */
    public function myExpositions(Request $request): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $expositions = Exposition::where('user_id', $request->user()->id)
                ->with('items')
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->paginate(20);

            return $this->paginatedResponse($expositions, 'Expositions récupérées avec succès');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur : créer une exposition.
     */
    public function store(Request $request): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:2000'],
                'starts_at' => ['nullable', 'date'],
                'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
                'items' => ['required', 'array', 'min:1'],
                'items.*' => ['integer', 'exists:items,id'],
            ]);

            $exposition = Exposition::create([...$data, 'user_id' => $request->user()->id]);

            $this->syncItems($exposition, $request->user()->id, $data['items']);

            $exposition->load(['items', 'user']);

            return $this->createdResponse($exposition, 'Exposition créée avec succès');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur : modifier une exposition.
     */
    public function update(Request $request, $id): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $exposition = Exposition::findOrFail($id);

            if (! $this->isOwner($request->user(), $exposition)) {
                return $this->forbiddenResponse('Vous ne pouvez pas gérer cette exposition');
            }

            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:2000'],
                'starts_at' => ['nullable', 'date'],
                'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
                'items' => ['required', 'array', 'min:1'],
                'items.*' => ['integer', 'exists:items,id'],
            ]);

            $exposition->update($data);

            $itemIds = $request->input('items', []);
            if (! empty($itemIds)) {
                $this->syncItems($exposition, $request->user()->id, $itemIds);
            }

            $exposition->load(['items', 'user']);

            return $this->updatedResponse($exposition, 'Exposition mise à jour avec succès');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exposition non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur : pause / reprise / fin.
     */
    public function toggleStatus(Request $request, $id): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $exposition = Exposition::findOrFail($id);

            if (! $this->isOwner($request->user(), $exposition)) {
                return $this->forbiddenResponse('Vous ne pouvez pas gérer cette exposition');
            }

            $exposition->update([
                'status' => match ($exposition->status) {
                    'active' => 'paused',
                    'paused' => 'active',
                    'ended' => 'active',
                },
            ]);

            return $this->updatedResponse(['id' => $exposition->id, 'status' => $exposition->fresh()->status], 'Statut de l\'exposition mis à jour');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exposition non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    /**
     * API vendeur : supprimer une exposition.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->forbiddenResponse();
        }

        try {
            $exposition = Exposition::findOrFail($id);

            if (! $this->isOwner($request->user(), $exposition)) {
                return $this->forbiddenResponse('Vous ne pouvez pas gérer cette exposition');
            }

            $exposition->delete();

            return $this->deletedResponse('Exposition supprimée avec succès');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Exposition non trouvée');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    protected function canManage(): bool
    {
        return auth()->check() && (auth()->user()->isSeller() || auth()->user()->isAdmin());
    }

    protected function isOwner($user, Exposition $exposition): bool
    {
        return $user->isAdmin() || $exposition->user_id === $user->id;
    }

    protected function syncItems(Exposition $exposition, int $userId, array $itemIds): void
    {
        $itemIds = Item::whereIn('id', $itemIds)
            ->where('user_id', $userId)
            ->pluck('id')
            ->all();

        $exposition->items()->sync($itemIds);
    }
}
