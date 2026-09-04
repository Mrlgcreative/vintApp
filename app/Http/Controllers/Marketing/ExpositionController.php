<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Exposition;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpositionController extends Controller
{
    /**
     * Liste des expositions du vendeur.
     */
    public function index(Request $request): View
    {
        $expositions = Exposition::where('user_id', $request->user()->id)
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('seller.expositions.index', compact('expositions'));
    }

    /**
     * Formulaire de création.
     */
    public function create(Request $request): View
    {
        $items = $this->allowedItems($request);
        return view('seller.expositions.create', compact('items'));
    }

    /**
     * Enregistrement.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateExposition($request);

        $exposition = Exposition::create(array_merge($data, [
            'user_id' => $request->user()->id,
            'slug' => null, // généré dans le booted()
        ]));

        $this->syncItems($exposition, $request, $request->user()->id);

        return redirect()
            ->route('seller.expositions.index')
            ->with('success', 'Exposition « ' . $exposition->title . ' » créée.');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Request $request, Exposition $exposition): View
    {
        $this->authorizeOwner($request->user(), $exposition);
        $items = $this->allowedItems($request);

        return view('seller.expositions.edit', compact('exposition', 'items'));
    }

    /**
     * Mise à jour.
     */
    public function update(Request $request, Exposition $exposition): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $exposition);

        $data = $this->validateExposition($request);

        $exposition->update($data);
        $this->syncItems($exposition, $request, $request->user()->id);

        return redirect()
            ->route('seller.expositions.edit', $exposition)
            ->with('success', 'Exposition mise à jour.');
    }

    /**
     * Pause / reprise / fin.
     */
    public function toggleStatus(Request $request, Exposition $exposition): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $exposition);

        $exposition->update([
            'status' => match ($exposition->status) {
                'active' => 'paused',
                'paused' => 'active',
                'ended' => 'active',
            },
        ]);

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Suppression.
     */
    public function destroy(Request $request, Exposition $exposition): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $exposition);
        $exposition->delete();

        return redirect()
            ->route('seller.expositions.index')
            ->with('success', 'Exposition supprimée.');
    }

    // ==================== HELPERS ====================

    protected function authorizeOwner($user, Exposition $exposition): void
    {
        if ($exposition->user_id !== $user->id) {
            abort(403, 'Vous ne pouvez pas gérer cette exposition.');
        }
    }

    protected function validateExposition(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['integer', 'exists:items,id'],
        ]);
    }

    /**
     * Lie les produits exposés en s'assurant qu'ils appartiennent bien au vendeur.
     */
    protected function syncItems(Exposition $exposition, Request $request, int $userId): void
    {
        $itemIds = $request->input('items', []);

        // Un vendeur n'expose que ses propres produits.
        $itemIds = Item::whereIn('id', $itemIds)
            ->where('user_id', $userId)
            ->pluck('id')
            ->all();

        $exposition->items()->sync($itemIds);
    }

    /**
     * Produits sélectionnables par le vendeur.
     */
    protected function allowedItems(Request $request)
    {
        return Item::where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get();
    }
}