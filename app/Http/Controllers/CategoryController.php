<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponses;

class CategoryController extends Controller
{
    use ApiResponses;

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

    // ==================== API METHODS ====================

    /**
     * API: Liste des catégories
     */
    public function apiIndex()
    {
        $categories = Category::with('parent')
            ->withCount(['items' => function($q) {
                $q->where('status', 'approved');
            }])
            ->orderBy('name')
            ->get();

        return $this->successResponse($categories, 'Catégories récupérées avec succès');
    }

    /**
     * API: Créer une catégorie
     */
    public function apiStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:categories,name',
                'description' => 'nullable|string|max:500',
                'icon' => 'nullable|string|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_id' => 'nullable|exists:categories,id',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
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

            // Définir is_active et sort_order par défaut
            $validated['is_active'] = $validated['is_active'] ?? true;
            $validated['sort_order'] = $validated['sort_order'] ?? 0;

            $category = Category::create($validated);
            
            return $this->successResponse($category, 'Catégorie créée avec succès', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Erreur de validation', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * API: Afficher une catégorie
     */
    public function apiShow($id)
    {
        try {
            $category = Category::with(['parent', 'children'])
                ->withCount(['items' => function($q) {
                    $q->where('status', 'approved');
                }])
                ->findOrFail($id);

            return $this->successResponse($category, 'Catégorie récupérée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Catégorie non trouvée', 404);
        }
    }

    /**
     * API: Mettre à jour une catégorie
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
                'description' => 'nullable|string|max:500',
                'icon' => 'nullable|string|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_id' => 'nullable|exists:categories,id',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            // Vérifier que la catégorie ne devient pas son propre parent
            if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
                return $this->errorResponse('Une catégorie ne peut pas être son propre parent', 400);
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

            $category->update($validated);
            
            return $this->successResponse($category->fresh(), 'Catégorie mise à jour avec succès');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Erreur de validation', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * API: Supprimer une catégorie
     */
    public function apiDestroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Vérifier s'il y a des articles dans cette catégorie
            $itemsCount = Item::where('category_id', $category->id)->count();
            if ($itemsCount > 0) {
                return $this->errorResponse(
                    'Impossible de supprimer cette catégorie car elle contient ' . $itemsCount . ' article(s)',
                    400
                );
            }

            // Vérifier s'il y a des sous-catégories
            $subcategoriesCount = Category::where('parent_id', $category->id)->count();
            if ($subcategoriesCount > 0) {
                return $this->errorResponse(
                    'Impossible de supprimer cette catégorie car elle contient ' . $subcategoriesCount . ' sous-catégorie(s)',
                    400
                );
            }

            $category->delete();
            
            return $this->successResponse(null, 'Catégorie supprimée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Catégorie non trouvée', 404);
        }
    }

    /**
     * API: Articles d'une catégorie
     */
    public function apiItems(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $query = Item::with(['category', 'brand', 'user'])
                ->where('category_id', $category->id)
                ->where('status', 'approved');

            // Filtres optionnels
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
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

            return $this->paginatedResponse($items, 'Articles de la catégorie récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Catégorie non trouvée', 404);
        }
    }
}
