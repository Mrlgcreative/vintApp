<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\Setting;
use App\Models\AllowedCity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use App\Services\CacheService;
use App\Services\MonitoringService;
use App\Services\ItemService;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;

class ItemController extends Controller
{
    protected $cacheService;
    protected $itemService;

    public function __construct(CacheService $cacheService, ItemService $itemService)
    {
        $this->cacheService = $cacheService;
        $this->itemService = $itemService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Utiliser le cache pour les catégories et marques
        $categories = $this->cacheService->getCategories(true);
        $brands = $this->cacheService->getBrands(true);

        // Relations à charger de manière optimisée
        $eagerLoad = [
            'category:id,name,slug',
            'brand:id,name,country',
            'user:id,name,avatar,avatar_url',
            'activeBoosts.boostType:id,name,icon,visual_config'
        ];

        // Ville de l'utilisateur (session ou requête)
        $userCity = $request->has('city') ? $request->city : session('user_city');

        // Récupérer les articles avec boost prioritaires
        $boostedItemsQuery = Item::with($eagerLoad)
            ->whereHas('activeBoosts')
            ->where('status', 'active')
            ->visible()
            ->select('id', 'user_id', 'name', 'description', 'price', 'currency', 'category_id', 'brand_id', 'images', 'condition', 'status', 'views', 'created_at');

        // Récupérer les articles réguliers (non-boostés)
        $regularItemsQuery = Item::with(array_diff($eagerLoad, ['activeBoosts.boostType:id,name,icon,visual_config']))
            ->whereDoesntHave('activeBoosts')
            ->where('status', 'active')
            ->visible()
            ->select('id', 'user_id', 'name', 'description', 'price', 'currency', 'category_id', 'brand_id', 'images', 'condition', 'status', 'views', 'created_at');

        // Appliquer les filtres à toutes les requêtes
        $queries = [$boostedItemsQuery, $regularItemsQuery];
        
        foreach ($queries as $query) {
            // Filtre par ville
            if ($userCity) {
                $query->whereHas('user', function($q) use ($userCity) {
                    $q->where('location', 'like', "%{$userCity}%");
                });
            }

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
        }

        // Tri et ordre des boosts d'abord
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        
        $boostedItems = $boostedItemsQuery->orderBy($sort, $order)->get();
        $regularItems = $regularItemsQuery->orderBy($sort, $order)->get();
        
        // Combiner les collections en priorisant les boostés
        $allItems = $boostedItems->concat($regularItems);
        
        // Paginer manuellement
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $total = $allItems->count();
        $items = $allItems->forPage($currentPage, $perPage);
        
        // Créer un paginator personnalisé
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->values(), 
            $total, 
            $perPage, 
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        $items->appends($request->query());

        $cities = AllowedCity::active()->orderBy('name')->pluck('name');

        return view('items.index', compact('items', 'categories', 'brands', 'cities', 'userCity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->cacheService->getCategories(true);
        $brands = $this->cacheService->getBrands(true);
        
        // Vérifier si les restrictions géographiques sont activées
        $locationRestrictionsEnabled = Setting::get('enable_location_restrictions', true);
        
        // Récupérer les villes autorisées si les restrictions sont activées
        $allowedCities = $locationRestrictionsEnabled 
            ? AllowedCity::active()->orderBy('country')->orderBy('name')->get()
            : collect(); // Collection vide si désactivé
        
        return view('items.create', compact('categories', 'brands', 'locationRestrictionsEnabled', 'allowedCities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        $startTime = microtime(true);
        $monitoring = app(MonitoringService::class);
        
        try {
            // La validation est déjà faite par StoreItemRequest (minimum 3 images)
            
            Log::info('Création d\'article - Début', [
                'user_id' => Auth::id(),
                'name' => $request->name,
                'has_images' => $request->hasFile('images'),
                'image_count' => $request->hasFile('images') ? count($request->file('images')) : 0
            ]);

            $item = $this->itemService->createItem($request);

            Log::info('Article créé avec succès', [
                'item_id' => $item->id,
                'verification_status' => $item->verification_status,
                'score' => $item->verification_score
            ]);

            // Enregistrer la métrique business
            $monitoring->recordBusinessMetric('item_created', $item->price, [
                'item_id' => $item->id,
                'user_id' => $item->user_id,
                'category_id' => $item->category_id,
                'currency' => $item->currency,
            ]);

            // Enregistrer la performance
            $duration = microtime(true) - $startTime;
            $monitoring->recordPerformance('item.store', $duration, [
                'user_id' => $item->user_id,
                'images_count' => count($item->images ?? []),
            ]);

            // Message personnalisé selon le statut de vérification
            $message = 'Article créé avec succès et envoyé pour vérification !';
            
            if ($item->verification_score >= 75) {
                $message = "Article créé avec succès ! Score IA: {$item->verification_score}/100. En attente de validation admin.";
            } elseif ($item->verification_score >= 50) {
                $message = "Article créé ! Score IA: {$item->verification_score}/100. Notre équipe vérifiera votre article sous peu.";
            } else {
                $message = "Article créé. Score IA: {$item->verification_score}/100. Vérification manuelle requise par notre équipe.";
            }

            return redirect()->route('items.show', $item)
                ->with('success', $message);
        } catch (\Exception $e) {
            // Enregistrer l'erreur
            Log::error('Erreur création article', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $monitoring->recordError($e, [
                'action' => 'item.store',
                'user_id' => Auth::id(),
            ]);
            
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Incrémenter les vues de manière asynchrone pour ne pas ralentir l'affichage
        $item->incrementQuietly('views');
        
        // Invalider le cache de l'item après incrément
        $this->cacheService->forgetItem($item->id);
        
        // Charger les relations de manière optimisée
        $item->load([
            'category:id,name,slug',
            'brand:id,name,country',
            'user:id,name,avatar,avatar_url,email',
            'activeBoosts.boostType:id,name,icon,visual_config',
            'authenticityCheck'
        ]);
        
        // Récupérer les reviews approuvées pour cet item avec eager loading
        $reviews = \App\Models\Review::where('item_id', $item->id)
            ->where('status', 'approved')
            ->with(['reviewer:id,name,avatar,avatar_url'])
            ->select('id', 'item_id', 'reviewer_id', 'rating', 'comment', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();
        
        // Calculer la moyenne des ratings et le total
        $reviewsStats = \App\Models\Review::where('item_id', $item->id)
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total_reviews, AVG(rating) as average_rating')
            ->first();
        
        $averageRating = $reviewsStats->average_rating ? round($reviewsStats->average_rating, 1) : 0;
        $totalReviews = $reviewsStats->total_reviews ?? 0;
        
        // Articles similaires avec cache
        $cacheKey = "similar_items.{$item->category_id}.exclude.{$item->id}";
        $similarItems = $this->cacheService->remember($cacheKey, 300, function () use ($item) {
            return Item::where('category_id', $item->category_id)
                ->where('id', '!=', $item->id)
                ->where('status', 'active')
                ->visible()
                ->with([
                    'category:id,name,slug',
                    'brand:id,name',
                    'user:id,name'
                ])
                ->select('id', 'user_id', 'name', 'price', 'currency', 'category_id', 'brand_id', 'images', 'condition')
                ->limit(4)
                ->get();
        });

        // Vérifier si l'utilisateur connecté a cet article en favori
        $isFavorited = Auth::check() ? Auth::user()->favorites()->where('item_id', $item->id)->exists() : false;

        return view('items.show', compact('item', 'similarItems', 'reviews', 'averageRating', 'totalReviews', 'isFavorited'));
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

        $categories = $this->cacheService->getCategories(true);
        $brands = $this->cacheService->getBrands(true);

        return view('items.edit', compact('item', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        // Validation et autorisation déjà effectuées par UpdateItemRequest

        $item = $this->itemService->updateItem($item, $request);

        return redirect()->route('items.show', $item)
            ->with('success', "Article mis à jour avec succès ! Score de vérification: {$item->verification_score}/100");
    }

    /**
     * Liste les articles en attente de vérification (admin)
     * L'admin peut approuver ou rejeter les articles soumis
     */
    public function pendingVerificationList(Request $request)
    {
        $query = Item::where(function($q) {
                $q->where('verification_status', 'pending')
                  ->orWhereNull('verification_status');
            })
            ->where('status', '!=', 'sold')
            ->with(['user', 'category', 'brand']);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', $search);
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(20);

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.items.pending_verification', compact('items', 'categories'));
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

            // Supprimer les images puis l'article
            $this->itemService->deleteItem($item);

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
        $city = $request->has('city') ? $request->city : session('user_city');

        // Prioriser les articles boostés dans les résultats de recherche
        $boostedItems = Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereHas('activeBoosts')
            ->where('status', 'active')
            ->visible();

        $regularItems = Item::with(['category', 'brand', 'user'])
            ->whereDoesntHave('activeBoosts')
            ->where('status', 'active')
            ->visible();

        $queries = [$boostedItems, $regularItems];
        
        foreach ($queries as $items) {
            if ($city) {
                $items->whereHas('user', function($q) use ($city) {
                    $q->where('location', 'like', "%{$city}%");
                });
            }

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
        }

        $boostedResults = $boostedItems->orderBy('created_at', 'desc')->get();
        $regularResults = $regularItems->orderBy('created_at', 'desc')->get();
        
        $allResults = $boostedResults->concat($regularResults);
        
        // Paginer manuellement
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $total = $allResults->count();
        $items = $allResults->forPage($currentPage, $perPage);
        
        // Créer un paginator personnalisé
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->values(), 
            $total, 
            $perPage, 
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        $items->appends($request->query());
        
        $categories = Category::where('is_active', true)->get();
        $cities = AllowedCity::active()->orderBy('name')->pluck('name');

        return view('items.search', compact('items', 'categories', 'query', 'cities'));
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
     * Show user's favorite items
     */
    public function favorites()
    {
        $items = Auth::user()->favorites()
            ->with(['category', 'brand', 'user:id,name,avatar,avatar_url'])
            ->orderBy('favorites.created_at', 'desc')
            ->paginate(12);

        return view('items.favorites', compact('items'));
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
    public function buy(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $sessionId = $request->session()->getId();
        $userId = Auth::id();

        $cartRow = \App\Models\Cart::firstOrNew([
            'session_id' => $sessionId,
            'item_id' => $item->id,
        ]);

        if ($cartRow->exists) {
            $cartRow->increment('quantity', 1);
        } else {
            $cartRow->fill([
                'user_id' => $userId,
                'item_name' => $item->name,
                'price' => $item->price,
                'currency' => $item->currency,
                'quantity' => 1,
                'image' => $item->images[0] ?? null,
            ])->save();
        }

        return redirect()->route('cart.checkout');
    }

    // ==================== API METHODS ====================

    /**
     * API: Liste des articles (avec filtres et pagination)
     */
    public function apiIndex(Request $request)
    {
        $query = Item::with(['category', 'brand', 'user'])
            ->where('status', 'active')
            ->visible();

        // Filtres
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ]
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * API: Détails d'un article
     */
    public function apiShow($id)
    {
        $item = Item::with(['category', 'brand', 'user', 'reviews'])
            ->findOrFail($id);

        $isOwner = Auth::check() && $item->user_id === Auth::id();

        if (!$isOwner && ($item->status !== 'active' || !$item->isAvailable())) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * API: Créer un article
     */
    public function apiStore(StoreItemRequest $request)
    {
        try {
            $item = $this->itemService->createItem($request);

            return response()->json([
                'success' => true,
                'message' => 'Article créé avec succès',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Mettre à jour un article
     */
    public function apiUpdate(UpdateItemRequest $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            if ($item->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $item = $this->itemService->updateItem($item, $request);

            return response()->json([
                'success' => true,
                'message' => 'Article mis à jour avec succès',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Supprimer un article
     */
    public function apiDestroy($id)
    {
        try {
            $item = Item::findOrFail($id);

            if ($item->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $this->itemService->deleteItem($item);

            return response()->json([
                'success' => true,
                'message' => 'Article supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
