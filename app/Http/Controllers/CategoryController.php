<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Affiche toutes les catégories.
     */
    public function index()
    {
        $categories = Category::withCount(['items' => function($q) {
            $q->where('status', 'active');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création d'une catégorie.
     */
    public function create()
    {
        // Récupérer les catégories parentes possibles
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Enregistre une nouvelle catégorie.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Générer un slug unique
        $slug = Str::slug($validated['name']);
        $count = Category::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        $validated['slug'] = $slug;

        // Gérer la checkbox is_active
        $validated['is_active'] = $request->has('is_active');
        
        // Définir sort_order par défaut
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        try {
            $category = Category::create($validated);
            return redirect()->route('categories.index')
                ->with('success', 'Catégorie créée avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Affiche une catégorie et ses articles.
     */
    public function show(Category $category)
    {
        // Charger les articles de cette catégorie
        $items = Item::with(['category', 'brand', 'user'])
            ->where('category_id', $category->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Charger les sous-catégories si applicable
        $subcategories = Category::withCount(['items' => function($q) {
            $q->where('status', 'active');
        }])
        ->where('parent_id', $category->id)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        // Charger le parent si applicable
        $category->load('parent');

        return view('categories.show', compact('category', 'items', 'subcategories'));
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie.
     */
    public function edit(Category $category)
    {
        // Récupérer les catégories parentes possibles (exclure la catégorie actuelle et ses enfants)
        $parentCategories = Category::where('is_active', true)
            ->where('id', '!=', $category->id)
            ->whereNull('parent_id')
            ->orWhere(function($query) use ($category) {
                $query->where('parent_id', '!=', $category->id);
            })
            ->orderBy('name')
            ->get();

        // Charger le nombre d'articles
        $category->loadCount(['items' => function($q) {
            $q->where('status', 'active');
        }]);

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Met à jour une catégorie.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Vérifier que la catégorie ne devient pas son propre parent
        if ($validated['parent_id'] == $category->id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une catégorie ne peut pas être son propre parent.');
        }

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Générer un slug unique si le nom change
        if ($category->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $count = Category::where('slug', $slug)->where('id', '!=', $category->id)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $validated['slug'] = $slug;
        }

        // Gérer la checkbox is_active
        $validated['is_active'] = $request->has('is_active');
        
        // Définir sort_order par défaut
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = $category->sort_order ?? 0;
        }

        try {
            $category->update($validated);
            return redirect()->route('categories.index')
                ->with('success', 'Catégorie mise à jour avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprime une catégorie.
     */
    public function destroy(Category $category)
    {
        // Vérifier s'il y a des articles dans cette catégorie
        $itemsCount = Item::where('category_id', $category->id)->count();
        if ($itemsCount > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient ' . $itemsCount . ' article(s).');
        }

        // Vérifier s'il y a des sous-catégories
        $subcategoriesCount = Category::where('parent_id', $category->id)->count();
        if ($subcategoriesCount > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient ' . $subcategoriesCount . ' sous-catégorie(s).');
        }

        try {
            $category->delete();
            return redirect()->route('categories.index')
                ->with('success', 'Catégorie supprimée avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
}
