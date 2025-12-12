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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Services\CacheService;
use App\Services\MonitoringService;
use App\Services\ItemVerificationService;
use App\Services\StorageSyncService;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Notifications\ItemApproved;
use App\Notifications\ItemRejected;

class ItemController extends Controller
{
    protected $cacheService;
    protected $verificationService;

    public function __construct(CacheService $cacheService, ItemVerificationService $verificationService)
    {
        $this->cacheService = $cacheService;
        $this->verificationService = $verificationService;
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

        // Récupérer les articles avec boost prioritaires
        $boostedItemsQuery = Item::with($eagerLoad)
            ->whereHas('activeBoosts')
            ->where('status', 'active')
            ->select('id', 'user_id', 'name', 'description', 'price', 'currency', 'category_id', 'brand_id', 'images', 'condition', 'status', 'views', 'created_at');

        // Récupérer les articles réguliers (non-boostés)
        $regularItemsQuery = Item::with(array_diff($eagerLoad, ['activeBoosts.boostType:id,name,icon,visual_config']))
            ->whereDoesntHave('activeBoosts')
            ->where('status', 'active')
            ->select('id', 'user_id', 'name', 'description', 'price', 'currency', 'category_id', 'brand_id', 'images', 'condition', 'status', 'views', 'created_at');

        // Appliquer les filtres à toutes les requêtes
        $queries = [$boostedItemsQuery, $regularItemsQuery];
        
        foreach ($queries as $query) {
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

        return view('items.index', compact('items', 'categories', 'brands'));
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
        $item->color = $request->color;
        $item->size = $request->size;
        $item->item_number = $request->item_number;
        // Tous les articles doivent passer par la vérification admin
        $item->status = 'pending_verification';

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
                // Synchroniser le fichier vers le bon emplacement (Hostinger ou standard)
                StorageSyncService::syncFile($path);
                $images[] = $path;
            }
            $item->images = $images;
        }

        // Avant d'enregistrer définitivement, vérifier les images automatiquement avec le nouveau service
        if ($item->images && is_array($item->images) && count($item->images) >= 3) {
            try {
                Log::info('Vérification des images - Début');
                
                // Récupérer les noms de catégorie et marque pour la vérification
                $category = Category::find($item->category_id);
                $brand = Brand::find($item->brand_id);
                
                $verification = $this->verificationService->verifyItem(
                    $item->images,
                    $item->name,
                    $item->description ?? '',
                    $brand->name ?? null,
                    $category->name ?? null
                );
                
                Log::info('Vérification des images - Résultat', [
                    'status' => $verification['status'],
                    'score' => $verification['score']
                ]);
                
                // Appliquer le résultat de la vérification
                $item->verification_status = $verification['status'];
                $item->verification_score = $verification['score'];
                $item->verification_details = $verification['details'];
                $item->verified_at = now();
                
                // Tous les articles restent en attente de vérification manuelle par l'admin ET expert
                // Mettre le verification_status à 'pending' pour que les experts puissent vérifier
                if ($item->verification_status !== 'rejected') {
                    $item->verification_status = 'pending';
                }
                $item->status = 'pending_verification';
            } catch (\Exception $e) {
                // Si la vérification échoue, on met en attente de vérification manuelle
                Log::error('Erreur vérification automatique', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $item->verification_status = 'pending';
                $item->status = 'pending_verification';
                $item->verification_details = [
                    'error' => 'Erreur lors de la vérification automatique',
                    'message' => $e->getMessage()
                ];
            }
        } else {
            // Moins de 3 images = mise en attente automatique
            $item->verification_status = 'pending';
            $item->status = 'pending_verification';
            $item->verification_details = [
                'reason' => 'Nombre d\'images insuffisant (minimum 3 requises)'
            ];
        }

            $item->save();

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

            // Déclencher l'événement pour envoyer les emails newsletter
            event(new \App\Events\ItemCreated($item));

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
        
        // Nettoyer et valider les données
        $item->specifications = is_array($item->specifications) ? $item->specifications : [];
        $item->images = is_array($item->images) ? $item->images : [];
        
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
                ->with([
                    'category:id,name,slug',
                    'brand:id,name',
                    'user:id,name'
                ])
                ->select('id', 'user_id', 'name', 'price', 'currency', 'category_id', 'brand_id', 'images', 'condition')
                ->limit(4)
                ->get();
        });

        return view('items.show', compact('item', 'similarItems', 'reviews', 'averageRating', 'totalReviews'));
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

        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->currency = $request->currency;
        $item->quantity = $request->quantity;
        $item->condition = $request->condition;
        $item->category_id = $request->category_id;
        $item->brand_id = $request->brand_id;
        $item->color = $request->color;
        $item->size = $request->size;
        $item->item_number = $request->item_number;

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
                // Synchroniser le fichier vers le bon emplacement (Hostinger ou standard)
                StorageSyncService::syncFile($path);
                $currentImages[] = $path;
            }
            $item->images = $currentImages;
        }

        // Après mise à jour des images, relancer la vérification automatique
        if ($item->images && is_array($item->images) && count($item->images) > 0) {
            // Récupérer les noms de catégorie et marque
            $category = Category::find($item->category_id);
            $brand = Brand::find($item->brand_id);
            
            $verification = $this->verificationService->verifyItem(
                $item->images,
                $item->name,
                $item->description ?? '',
                $brand->name ?? null,
                $category->name ?? null
            );
            
            // Appliquer le résultat de la vérification
            $item->verification_status = $verification['status'];
            $item->verification_score = $verification['score'];
            $item->verification_details = $verification['details'];
            $item->verified_at = now();
            
            // Ajuster le statut
            if ($verification['status'] === 'approved') {
                $item->status = 'active';
            } elseif ($verification['status'] === 'pending') {
                $item->status = 'pending_verification';
            } else {
                $item->status = 'inactive';
            }
        }

        $item->save();

        return redirect()->route('items.show', $item)
            ->with('success', "Article mis à jour avec succès ! Score de vérification: {$item->verification_score}/100");
    }

    /**
     * Méthode admin : approuver un item après vérification manuelle
     */
    public function approveItem(Item $item)
    {
        // La vérification admin est déjà faite par le middleware
        $user = Auth::user();

        $item->status = 'active';
        $item->verification_status = 'approved';
        $item->verified_at = now();
        $item->verified_by = $user->id;
        
        // Nettoyer les anciennes données de vérification
        if (is_array($item->specifications) && isset($item->specifications['image_verification'])) {
            unset($item->specifications['image_verification']);
        }
        
        $item->save();

        // Envoyer la notification au vendeur
        try {
            $item->user->notify(new ItemApproved($item, $user->name));
            
            Log::info('Notification d\'approbation envoyée', [
                'item_id' => $item->id,
                'seller_id' => $item->user_id,
                'admin_id' => $user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi notification approbation', [
                'error' => $e->getMessage(),
                'item_id' => $item->id
            ]);
        }

        return redirect()->back()->with('success', 'Article approuvé et vendeur notifié.');
    }

    /**
     * Méthode admin : rejeter un item après vérification manuelle
     */
    public function rejectItem(Item $item, Request $request)
    {
        // La vérification admin est déjà faite par le middleware
        $user = Auth::user();

        $reason = $request->input('reason', 'Rejeté par l\'équipe de modération');
        
        $item->status = 'inactive';
        $item->verification_status = 'rejected';
        $item->verified_at = now();
        $item->verified_by = $user->id;
        
        // Ajouter la raison du rejet aux détails
        $details = $item->verification_details ?? [];
        $details['admin_rejection'] = [
            'reason' => $reason,
            'rejected_by' => $user->name,
            'rejected_at' => now()->toDateTimeString(),
        ];
        $item->verification_details = $details;
        
        $item->save();

        // Envoyer la notification au vendeur
        try {
            $item->user->notify(new ItemRejected($item, $reason, $user->name));
            
            Log::info('Notification de rejet envoyée', [
                'item_id' => $item->id,
                'seller_id' => $item->user_id,
                'admin_id' => $user->id,
                'reason' => $reason
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi notification rejet', [
                'error' => $e->getMessage(),
                'item_id' => $item->id
            ]);
        }

        return redirect()->back()->with('success', 'Article rejeté et vendeur notifié.');
    }

    /**
     * Liste les items en attente de vérification (admin)
     */
    public function pendingVerificationList(Request $request)
    {
        // La vérification admin est déjà faite par le middleware
        // Pas besoin de re-vérifier ici
        
        // Filtrer par status 'pending_verification' pour inclure tous les articles
        // en attente de validation admin (peu importe le score IA)
        // Trier par score IA décroissant pour prioriser les meilleurs articles
        $items = Item::where('status', 'pending_verification')
            ->with(['user', 'category', 'brand'])
            ->orderBy('verification_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.items.pending_verification', compact('items'));
    }

    /**
     * Affiche les détails d'un item pour l'admin
     */
    public function adminShow(Item $item)
    {
        $item->load(['user', 'category', 'brand']);
        return view('admin.items.show', compact('item'));
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

        // Prioriser les articles boostés dans les résultats de recherche
        $boostedItems = Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereHas('activeBoosts')
            ->where('status', 'active');

        $regularItems = Item::with(['category', 'brand', 'user'])
            ->whereDoesntHave('activeBoosts')
            ->where('status', 'active');

        $queries = [$boostedItems, $regularItems];
        
        foreach ($queries as $items) {
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

    // ==================== API METHODS ====================

    /**
     * API: Liste des articles (avec filtres et pagination)
     */
    public function apiIndex(Request $request)
    {
        $query = Item::with(['category', 'brand', 'user'])
            ->where('status', 'approved');

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
        ]);
    }

    /**
     * API: Détails d'un article
     */
    public function apiShow($id)
    {
        $item = Item::with(['category', 'brand', 'user', 'reviews'])
            ->findOrFail($id);

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
            $item->color = $request->color;
            $item->size = $request->size;
            $item->status = 'pending_verification';

            // Upload d'images
            if ($request->hasFile('images')) {
                $images = [];
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('items', $filename, 'public');
                    StorageSyncService::syncFile($path);
                    $images[] = $path;
                }
                $item->images = $images;
            }

            $item->save();

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

            $item->update($request->validated());

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

            $item->delete();

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
