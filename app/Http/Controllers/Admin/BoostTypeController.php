<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostType;
use App\Models\ProductBoost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BoostTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = BoostType::query()->withCount('productBoosts');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $sort = $request->sort ?? 'sort_order';
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        $query->orderBy($sort, $direction);

        $boostTypes = $query->paginate(15)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json(['boost_types' => $boostTypes]);
        }

        return view('admin.boost-types.index', compact('boostTypes'));
    }

    public function create()
    {
        return view('admin.boost-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:boost_types,name',
            'display_name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'base_price' => 'nullable|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0',
            'price_cdf' => 'nullable|numeric|min:0',
            'min_duration' => 'nullable|integer|min:1',
            'max_duration' => 'nullable|integer|min:1',
            'available_durations' => 'nullable|array',
            'available_durations.*' => 'integer|min:1',
            'benefits' => 'nullable|string',
            'visual_config' => 'nullable|array',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'max_concurrent' => 'nullable|integer|min:1',
            'admin_notes' => 'nullable|string',
        ]);

        $validated['available_durations'] = $validated['available_durations'] ?? [];
        $validated['benefits'] = $validated['benefits']
            ? array_filter(array_map('trim', explode("\n", $validated['benefits'])), fn($v) => $v !== '')
            : [];
        $validated['visual_config'] = $validated['visual_config'] ?? [];
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_premium'] = $request->boolean('is_premium', false);

        BoostType::create($validated);

        return redirect()->route('admin.boost-types.index')
            ->with('success', 'Type de boost créé avec succès.');
    }

    public function show(BoostType $boostType)
    {
        $boostType->loadCount('productBoosts');
        $recentBoosts = $boostType->productBoosts()
            ->with(['item', 'user'])
            ->latest()
            ->take(20)
            ->get();

        $stats = [
            'total_revenue' => ProductBoost::where('boost_type_id', $boostType->id)
                ->where('status', 'active')
                ->sum('total_price'),
            'active_count' => ProductBoost::where('boost_type_id', $boostType->id)
                ->where('status', 'active')
                ->count(),
            'total_views' => ProductBoost::where('boost_type_id', $boostType->id)
                ->sum('views_generated'),
            'total_clicks' => ProductBoost::where('boost_type_id', $boostType->id)
                ->sum('clicks_generated'),
        ];

        return view('admin.boost-types.show', compact('boostType', 'recentBoosts', 'stats'));
    }

    public function edit(BoostType $boostType)
    {
        return view('admin.boost-types.edit', compact('boostType'));
    }

    public function update(Request $request, BoostType $boostType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:boost_types,name,' . $boostType->id,
            'display_name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'base_price' => 'nullable|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0',
            'price_cdf' => 'nullable|numeric|min:0',
            'min_duration' => 'nullable|integer|min:1',
            'max_duration' => 'nullable|integer|min:1',
            'available_durations' => 'nullable|array',
            'available_durations.*' => 'integer|min:1',
            'benefits' => 'nullable|string',
            'visual_config' => 'nullable|array',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'max_concurrent' => 'nullable|integer|min:1',
            'admin_notes' => 'nullable|string',
        ]);

        $validated['available_durations'] = $validated['available_durations'] ?? [];
        $validated['benefits'] = $validated['benefits']
            ? array_filter(array_map('trim', explode("\n", $validated['benefits'])), fn($v) => $v !== '')
            : [];
        $validated['visual_config'] = $validated['visual_config'] ?? [];
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_premium'] = $request->boolean('is_premium', false);

        $boostType->update($validated);

        return redirect()->route('admin.boost-types.index')
            ->with('success', 'Type de boost mis à jour avec succès.');
    }

    public function destroy(BoostType $boostType)
    {
        $activeBoosts = ProductBoost::where('boost_type_id', $boostType->id)
            ->where('status', 'active')
            ->count();

        if ($activeBoosts > 0) {
            return redirect()->route('admin.boost-types.index')
                ->with('error', 'Impossible de supprimer ce type de boost : il y a ' . $activeBoosts . ' boost(s) actif(s) associé(s).');
        }

        $boostType->delete();

        return redirect()->route('admin.boost-types.index')
            ->with('success', 'Type de boost supprimé avec succès.');
    }

    public function updateStatus(Request $request, BoostType $boostType)
    {
        $request->validate(['is_active' => 'required|boolean']);

        $boostType->update(['is_active' => $request->is_active]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.boost-types.index')
            ->with('success', 'Statut mis à jour avec succès.');
    }
}
