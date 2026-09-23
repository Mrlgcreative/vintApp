<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Liste des codes promo (admin).
     */
    public function index(Request $request): View
    {
        $query = Coupon::with('creator');

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $coupons = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Créer un code promo.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:coupons,code'],
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required_if:type,fixed', 'string', 'in:USD,CDF,XAF,XOF,EUR'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'max_redemptions' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validated['type'] === 'percent' && $validated['value'] > 100) {
            return back()
                ->withInput()
                ->withErrors(['value' => 'Un pourcentage ne peut pas dépasser 100.']);
        }

        Coupon::create([
            ...$validated,
            'status' => 'active',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Code promo créé avec succès.');
    }

    /**
     * Activer / désactiver un code promo.
     */
    public function toggleStatus(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->status = $coupon->status === 'active' ? 'inactive' : 'active';
        $coupon->save();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Statut du code promo mis à jour.');
    }

    /**
     * Supprimer un code promo.
     */
    public function destroy(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Code promo supprimé.');
    }
}