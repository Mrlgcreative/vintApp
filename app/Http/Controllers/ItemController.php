<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with(['category', 'brand', 'user'])
            ->where('status', 'active');

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);

        $items = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('items.index', compact('items', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        
        return view('items.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,CDF',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
        ]);

        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->currency = $request->currency;
        $item->quantity = $request->quantity;
        $item->condition = $request->condition;
        $item->category_id = $request->category_id;
        $item->brand_id = $request->brand_id;
        $item->status = 'active';

        // Gestion des spécifications
        if ($request->filled('specifications') && is_array($request->specifications)) {
            $specifications = [];
            $keys = $request->specifications['key'] ?? [];
            $values = $request->specifications['value'] ?? [];
            
            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index]) && !empty($values[$index])) {
                    $specifications[$key] = $values[$index];
                }
            }
            
            if (!empty($specifications)) {
                $item->specifications = $specifications;
            }
        }

        // Gestion des images
        if ($request->hasFile('images')) {
            $images = [];
            // S'assurer que le dossier existe
            if (!Storage::disk('public')->exists('items')) {
                Storage::disk('public')->makeDirectory('items');
            }
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('items', $filename, 'public');
                // Vérifier que le fichier a bien été créé
                if (!Storage::disk('public')->exists($path)) {
                    throw new \Exception('Erreur lors de l\'upload de l\'image.');
                }
                $images[] = $path;
            }
            $item->images = $images;
        }

        $item->save();

        return redirect()->route('items.show', $item)
            ->with('success', 'Article créé avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Incrémenter les vues
        $item->increment('views');
        
        // Nettoyer et valider les données
        $item->specifications = is_array($item->specifications) ? $item->specifications : [];
        $item->images = is_array($item->images) ? $item->images : [];
        
        // Charger les relations
        $item->load(['category', 'brand', 'user']);
        
        // Articles similaires
        $similarItems = Item::where('category_id', $item->category_id)
                           ->where('id', '!=', $item->id)
            ->where('status', 'active')
            ->with(['category', 'brand'])
                           ->limit(4)
                           ->get();

        return view('items.show', compact('item', 'similarItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cet article.');
        }

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('items.edit', compact('item', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cet article.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,CDF',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
        ]);

        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->currency = $request->currency;
        $item->quantity = $request->quantity;
        $item->condition = $request->condition;
        $item->category_id = $request->category_id;
        $item->brand_id = $request->brand_id;

        // Gestion des spécifications
        if ($request->filled('specifications') && is_array($request->specifications)) {
            $specifications = [];
            $keys = $request->specifications['key'] ?? [];
            $values = $request->specifications['value'] ?? [];
            
            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index]) && !empty($values[$index])) {
                    $specifications[$key] = $values[$index];
                }
            }
            
            if (!empty($specifications)) {
                $item->specifications = $specifications;
            } else {
                $item->specifications = null;
            }
        }

        // Gestion des nouvelles images
        if ($request->hasFile('images')) {
            $currentImages = $item->images ?? [];
            // S'assurer que le dossier existe
            if (!Storage::disk('public')->exists('items')) {
                Storage::disk('public')->makeDirectory('items');
            }
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('items', $filename, 'public');
                if (!Storage::disk('public')->exists($path)) {
                    throw new \Exception('Erreur lors de l\'upload de l\'image.');
                }
                $currentImages[] = $path;
            }
            $item->images = $currentImages;
        }

        $item->save();

        return redirect()->route('items.show', $item)
            ->with('success', 'Article mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try {
            // Vérifier que l'utilisateur est le propriétaire
            if ($item->user_id !== Auth::id()) {
                if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                        'message' => 'Vous n\'êtes pas autorisé à supprimer cet article.'
            ], 403);
                }
                abort(403, 'Vous n\'êtes pas autorisé à supprimer cet article.');
        }

            // Supprimer les images
            if ($item->images && is_array($item->images)) {
        foreach ($item->images as $image) {
                    try {
                        if (Storage::disk('public')->exists($image)) {
                            Storage::disk('public')->delete($image);
                        }
                    } catch (\Exception $e) {
                        // Log l'erreur mais continue
                        Log::warning("Impossible de supprimer l'image: {$image}", ['error' => $e->getMessage()]);
                    }
                }
            }

            // Supprimer l'article
        $item->delete();

            if (request()->expectsJson()) {
        return response()->json([
            'success' => true,
                    'message' => 'Article supprimé avec succès !'
                ]);
            }

            return redirect()->route('items.my-items')
                ->with('success', 'Article supprimé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'article', [
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de l\'article.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de l\'article.');
        }
    }

    /**
     * Search items
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $category = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $condition = $request->get('condition');

        $items = Item::with(['category', 'brand', 'user'])
            ->where('status', 'active');

        if ($query) {
            $items->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if ($category) {
            $items->where('category_id', $category);
        }

        if ($minPrice) {
            $items->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $items->where('price', '<=', $maxPrice);
        }

        if ($condition) {
            $items->where('condition', $condition);
        }

        $items = $items->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('items.search', compact('items', 'categories', 'query'));
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Item $item)
    {
        $user = Auth::user();
        
        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->detach($item->id);
            $message = 'Article retiré des favoris';
        } else {
            $user->favorites()->attach($item->id);
            $message = 'Article ajouté aux favoris';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $user->favorites()->where('item_id', $item->id)->exists()
        ]);
    }

    /**
     * Get user's items
     */
    public function myItems()
    {
        $items = Auth::user()->items()
            ->with(['category', 'brand'])
                    ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('items.my-items', compact('items'));
    }

    /**
     * Update item status
     */
    public function updateStatus(Request $request, Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:active,inactive,sold'
        ]);

        $item->status = $request->status;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès'
        ]);
    }

    /**
     * Affiche la page de personnalisation des articles du vendeur.
     */
    public function personalization()
    {
        $userItems = Item::with(['category', 'brand'])
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('items.personalization', compact('userItems', 'categories', 'brands'));
    }

    /**
     * Met à jour les informations de personnalisation d'un article.
     */
    public function updatePersonalization(Request $request, Item $item)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cet article.');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'size' => 'nullable|string|max:50',
        ]);

        $item->update([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'size' => $request->size,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Article mis à jour avec succès !'
        ]);
    }

    /**
     * Achat direct d'un article (ajoute au panier et redirige vers le checkout)
     */
    public function buy($id)
    {
        $item = Item::findOrFail($id);
        $cart = session('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += 1;
        } else {
            $cart[$id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'currency' => $item->currency,
                'quantity' => 1,
                'image' => $item->images[0] ?? null,
            ];
        }
        session(['cart' => $cart]);
        return redirect()->route('cart.checkout');
    }
}
