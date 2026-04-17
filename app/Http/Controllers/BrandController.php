<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageSyncService;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Cache;

class BrandController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::orderBy('name')->get();
        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = [
            'France', 'Italie', 'Espagne', 'Allemagne', 'États-Unis', 'Royaume-Uni', 'Chine', 'Japon', 'Maroc', 'Tunisie', 'Turquie', 'Autre'
        ];
        $types = [
            'Luxe', 'Grand public', 'Sport', 'Autre'
        ];
        return view('brands.create', compact('countries', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:brands,name',
            'description' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'country' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
        ]);
        // Générer un slug unique
        $slug = Str::slug($validated['name']);
        $count = Brand::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
            // Synchroniser le fichier vers le bon emplacement (Hostinger ou standard)
            StorageSyncService::syncFile($validated['logo']);
        }

        $validated['is_active'] = $request->has('is_active');

        try {
            $brand = Brand::create($validated);
            Cache::forget('api.brands.list');
            return redirect()->route('brands.index')->with('success', 'Marque ajoutée avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $countries = [
            'France', 'Italie', 'Espagne', 'Allemagne', 'États-Unis', 'Royaume-Uni', 'Chine', 'Japon', 'Maroc', 'Tunisie', 'Turquie', 'Autre'
        ];
        $types = [
            'Luxe', 'Grand public', 'Sport', 'Autre'
        ];
        return view('brands.edit', compact('brand', 'countries', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'country' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
        ]);
        // Générer un slug unique si le nom change
        if ($brand->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $count = Brand::where('slug', $slug)->where('id', '!=', $brand->id)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $validated['slug'] = $slug;
        } else {
            $validated['slug'] = $brand->slug;
        }
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si présent
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
            // Synchroniser le fichier vers le bon emplacement (Hostinger ou standard)
            StorageSyncService::syncFile($validated['logo']);
        }
        $validated['is_active'] = $request->has('is_active');
        $brand->update($validated);
        Cache::forget('api.brands.list');
        return redirect()->route('brands.index')->with('success', 'Marque modifiée avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Protection : empêcher la suppression si la marque a des items associés
        if ($brand->items()->count() > 0) {
            return redirect()->route('brands.index')->with('error', 'Impossible de supprimer cette marque car elle est utilisée par des articles.');
        }
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Marque supprimée avec succès !');
    }

    // ==================== API METHODS ====================

    /**
     * API: Liste des marques
     */
    public function apiIndex()
    {
        $brands = Cache::remember('api.brands.list', 3600, function () {
            return Brand::withCount(['items as items_count' => function($q) {
                    $q->where('status', 'approved');
                }])
                ->withCount(['items as total_items_count'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        return $this->successResponse($brands, 'Marques récupérées avec succès');
    }

    /**
     * API: Créer une marque
     */
    public function apiStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:brands,name',
                'description' => 'nullable|string|max:500',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'country' => 'nullable|string|max:100',
                'type' => 'nullable|string|max:50',
                'website' => 'nullable|url|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            // Gérer l'upload du logo
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('brands', 'public');
                $validated['logo'] = $logoPath;
                
                // Sync storage pour Hostinger
                StorageSyncService::syncFile($logoPath);
            }

            // Générer un slug unique
            $slug = Str::slug($validated['name']);
            $count = Brand::where('slug', $slug)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $validated['slug'] = $slug;

            // Définir is_active par défaut
            $validated['is_active'] = $validated['is_active'] ?? true;

            $brand = Brand::create($validated);
            
            return $this->successResponse($brand, 'Marque créée avec succès', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Erreur de validation', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * API: Afficher une marque
     */
    public function apiShow($id)
    {
        try {
            $brand = Brand::withCount(['items' => function($q) {
                    $q->where('status', 'approved');
                }])
                ->findOrFail($id);

            return $this->successResponse($brand, 'Marque récupérée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Marque non trouvée', 404);
        }
    }

    /**
     * API: Mettre à jour une marque
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:brands,name,' . $brand->id,
                'description' => 'nullable|string|max:500',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'country' => 'nullable|string|max:100',
                'type' => 'nullable|string|max:50',
                'website' => 'nullable|url|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            // Gérer l'upload du logo
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo si existe
                if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                    Storage::disk('public')->delete($brand->logo);
                }
                
                $logoPath = $request->file('logo')->store('brands', 'public');
                $validated['logo'] = $logoPath;
                
                // Sync storage pour Hostinger
                StorageSyncService::syncFile($logoPath);
            }

            // Générer un slug unique si le nom change
            if ($brand->name !== $validated['name']) {
                $slug = Str::slug($validated['name']);
                $count = Brand::where('slug', $slug)->where('id', '!=', $brand->id)->count();
                if ($count > 0) {
                    $slug .= '-' . ($count + 1);
                }
                $validated['slug'] = $slug;
            }

            $brand->update($validated);
            
            return $this->successResponse($brand->fresh(), 'Marque mise à jour avec succès');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Erreur de validation', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * API: Supprimer une marque
     */
    public function apiDestroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);

            // Vérifier s'il y a des articles avec cette marque
            $itemsCount = Item::where('brand_id', $brand->id)->count();
            if ($itemsCount > 0) {
                return $this->errorResponse(
                    'Impossible de supprimer cette marque car elle est utilisée par ' . $itemsCount . ' article(s)',
                    400
                );
            }

            // Supprimer le logo si existe
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }

            $brand->delete();
            
            return $this->successResponse(null, 'Marque supprimée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Marque non trouvée', 404);
        }
    }

    /**
     * API: Articles d'une marque
     */
    public function apiItems(Request $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            $query = Item::with(['category', 'brand', 'user'])
                ->where('brand_id', $brand->id)
                ->where('status', 'approved');

            // Filtres optionnels
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('condition')) {
                $query->where('condition', $request->condition);
            }

            if ($request->has('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

            // Tri
            $sort = $request->get('sort', 'date_desc');
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $items = $query->paginate($request->get('per_page', 15));

            return $this->paginatedResponse($items, 'Articles de la marque récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Marque non trouvée', 404);
        }
    }
}
