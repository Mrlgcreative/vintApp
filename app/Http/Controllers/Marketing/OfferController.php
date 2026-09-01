<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OfferController extends Controller
{
    /**
     * Page publique des promotions.
     */
    public function promotions(): View
    {
        $offers = Offer::running()
            ->with(['categories', 'items'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        return view('promotions', compact('offers'));
    }

    /**
     * Listing (admin = toutes, vendeur = les siennes).
     */
    public function index(Request $request): View
    {
        $query = Offer::with('creator');
        if (!$request->user()->isAdmin()) {
            $query->where('created_by', $request->user()->id);
        }

        $offers = $query->orderByDesc('created_at')->paginate(20);

        $view = $request->user()->isAdmin() ? 'admin.offers.index' : 'seller.offers.index';
        return view($view, compact('offers'));
    }

    /**
     * Formulaire de création.
     */
    public function create(Request $request): View
    {
        $this->authorizeManage();
        $categories = Category::orderBy('name')->get();
        $isSeller = !$request->user()->isAdmin();
        $items = $this->allowedItems($request);

        $view = $request->user()->isAdmin() ? 'admin.offers.create' : 'seller.offers.create';
        return view($view, compact('categories', 'isSeller', 'items'));
    }

    /**
     * Enregistrement.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $data = $this->validateOffer($request, $request->user()->isAdmin());

        // Un vendeur ne peut cibler que ses propres produits.
        if (!$request->user()->isAdmin()) {
            $data['scope'] = 'items';
            $requestedItems = $data['items'] ?? [];
            $data['items'] = Item::whereIn('id', $requestedItems)
                ->where('user_id', $request->user()->id)
                ->pluck('id')->all();
        }

        $offer = Offer::create(array_merge($data, [
            'created_by' => $request->user()->id,
            'status' => $request->input('status', 'active'),
        ]));

        $this->syncTargets($offer, $request);

        // Invalidation du cache de prix des items.
        Item::clearRunningOffersCache();

        // Notifie les utilisateurs d'une nouvelle promo active.
        if ($offer->status === 'active') {
            app(\App\Services\NotificationService::class)->notifyClientsOfNewOffer($offer);
            app(\App\Http\Controllers\NewsletterController::class)->notifyPromotion($offer);
        }

        return redirect()
            ->route($request->user()->isAdmin() ? 'admin.offers.index' : 'seller.offers.index')
            ->with('success', 'Offre « ' . $offer->title . ' » créée.');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Request $request, Offer $offer): View
    {
        $this->authorizeOwner($request->user(), $offer);
        $categories = Category::orderBy('name')->get();
        $isSeller = !$request->user()->isAdmin();
        $items = $this->allowedItems($request);

        $view = $request->user()->isAdmin() ? 'admin.offers.edit' : 'seller.offers.edit';
        return view($view, compact('offer', 'categories', 'isSeller', 'items'));
    }

    /**
     * Mise à jour.
     */
    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $offer);

        $data = $this->validateOffer($request, $request->user()->isAdmin());

        // Un vendeur ne peut pas changer le périmètre vers global/catégories.
        if (!$request->user()->isAdmin()) {
            $data['scope'] = 'items';
            $requestedItems = $data['items'] ?? [];
            $data['items'] = Item::whereIn('id', $requestedItems)
                ->where('user_id', $request->user()->id)
                ->pluck('id')->all();
        }

        $offer->update($data);
        $this->syncTargets($offer, $request);
        Item::clearRunningOffersCache();

        return redirect()
            ->route($request->user()->isAdmin() ? 'admin.offers.index' : 'seller.offers.index')
            ->with('success', 'Offre mise à jour.');
    }

    /**
     * Activation / pause.
     */
    public function toggleStatus(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $offer);
        $offer->status = $offer->status === 'active' ? 'paused' : 'active';
        $offer->save();
        Item::clearRunningOffersCache();

        return back()->with('success', 'Statut de l\'offre mis à jour.');
    }

    /**
     * Suppression.
     */
    public function destroy(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorizeOwner($request->user(), $offer);
        $offer->delete();
        Item::clearRunningOffersCache();

        return back()->with('success', 'Offre supprimée.');
    }

    // ==================== HELPERS ====================

    protected function authorizeManage(): void
    {
        if (!auth()->user() || (!auth()->user()->isAdmin() && !auth()->user()->isSeller())) {
            abort(403, 'Accès refusé.');
        }
    }

    protected function authorizeOwner($user, Offer $offer): void
    {
        if (!$user->isAdmin() && $offer->created_by !== $user->id) {
            abort(403, 'Vous ne pouvez pas gérer cette offre.');
        }
    }

    protected function validateOffer(Request $request, bool $isAdmin): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'scope' => ['required', 'in:global,categories,items'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required_if:type,fixed', 'string', 'in:USD,CDF'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => [$isAdmin ? 'required' : 'nullable', 'in:active,paused'],
            'is_flash_sale' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'max_redemptions' => ['nullable', 'integer', 'min:1'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'items' => ['nullable', 'array'],
            'items.*' => ['integer', 'exists:items,id'],
        ]);
    }

    protected function syncTargets(Offer $offer, Request $request): void
    {
        $offer->categories()->sync($request->input('categories', []));
        $offer->items()->sync($request->input('items', []));
    }

    /**
     * Produits modifiables selon le rôle (vendeur : les siens).
     */
    protected function allowedItems(Request $request)
    {
        if ($request->user()->isAdmin()) {
            return Item::where('status', 'active')->orderBy('name')->limit(500)->get();
        }
        return Item::where('user_id', $request->user()->id)->orderBy('name')->get();
    }
}