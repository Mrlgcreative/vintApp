<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageSyncService;

class BrandController extends Controller
{
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
            'logo' => 'nullable|image|max:2048',
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
            'logo' => 'nullable|image|max:2048',
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
}
