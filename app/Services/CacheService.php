<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Setting;

class CacheService
{
    /**
     * Durées de cache (en secondes)
     */
    const CACHE_ITEMS_LIST = 300; // 5 minutes
    const CACHE_ITEM_DETAIL = 600; // 10 minutes
    const CACHE_CATEGORIES = 3600; // 1 heure
    const CACHE_BRANDS = 3600; // 1 heure
    const CACHE_SETTINGS = 7200; // 2 heures
    const CACHE_USER_STATS = 300; // 5 minutes
    const CACHE_POPULAR_ITEMS = 900; // 15 minutes

    /**
     * Récupérer les catégories avec cache
     */
    public function getCategories(bool $activeOnly = true)
    {
        $key = 'categories.' . ($activeOnly ? 'active' : 'all');
        
        return Cache::remember($key, self::CACHE_CATEGORIES, function () use ($activeOnly) {
            $query = Category::query();
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            
            return $query->orderBy('name')->get();
        });
    }

    /**
     * Récupérer les marques avec cache
     */
    public function getBrands(bool $activeOnly = true)
    {
        $key = 'brands.' . ($activeOnly ? 'active' : 'all');
        
        return Cache::remember($key, self::CACHE_BRANDS, function () use ($activeOnly) {
            $query = Brand::query();
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            
            return $query->orderBy('name')->get();
        });
    }

    /**
     * Récupérer les paramètres avec cache
     */
    public function getSettings()
    {
        return Cache::remember('settings.all', self::CACHE_SETTINGS, function () {
            return Setting::all()->pluck('value', 'key');
        });
    }

    /**
     * Récupérer un paramètre spécifique
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->getSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Récupérer un item avec cache
     */
    public function getItem(int $itemId)
    {
        return Cache::remember("item.{$itemId}", self::CACHE_ITEM_DETAIL, function () use ($itemId) {
            return Item::with([
                'category',
                'brand',
                'user:id,name,avatar,avatar_url',
                'activeBoosts.boostType',
                'authenticityCheck'
            ])->find($itemId);
        });
    }

    /**
     * Récupérer les items populaires avec cache
     */
    public function getPopularItems(int $limit = 10)
    {
        return Cache::remember("items.popular.{$limit}", self::CACHE_POPULAR_ITEMS, function () use ($limit) {
            return Item::with(['category', 'brand', 'user:id,name,avatar,avatar_url'])
                ->where('status', 'active')
                ->orderByDesc('views')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Récupérer les items boostés avec cache
     */
    public function getBoostedItems(int $limit = 20)
    {
        return Cache::remember("items.boosted.{$limit}", self::CACHE_ITEMS_LIST, function () use ($limit) {
            return Item::with(['category', 'brand', 'user:id,name,avatar,avatar_url', 'activeBoosts.boostType'])
                ->whereHas('activeBoosts')
                ->where('status', 'active')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Récupérer les statistiques d'un utilisateur avec cache
     */
    public function getUserStats(int $userId)
    {
        return Cache::remember("user.{$userId}.stats", self::CACHE_USER_STATS, function () use ($userId) {
            return [
                'items_count' => Item::where('user_id', $userId)->count(),
                'sales_count' => DB::table('orders')
                    ->join('items', 'orders.item_id', '=', 'items.id')
                    ->where('items.user_id', $userId)
                    ->where('orders.status', 'completed')
                    ->count(),
                'purchases_count' => DB::table('orders')
                    ->where('buyer_id', $userId)
                    ->where('status', 'completed')
                    ->count(),
            ];
        });
    }

    /**
     * Invalider le cache des catégories
     */
    public function forgetCategories()
    {
        Cache::forget('categories.active');
        Cache::forget('categories.all');
    }

    /**
     * Invalider le cache des marques
     */
    public function forgetBrands()
    {
        Cache::forget('brands.active');
        Cache::forget('brands.all');
    }

    /**
     * Invalider le cache des paramètres
     */
    public function forgetSettings()
    {
        Cache::forget('settings.all');
    }

    /**
     * Invalider le cache d'un item
     */
    public function forgetItem(int $itemId)
    {
        Cache::forget("item.{$itemId}");
        // Invalider aussi les listes qui pourraient contenir cet item
        $this->forgetItemsLists();
    }

    /**
     * Invalider le cache des listes d'items
     */
    public function forgetItemsLists()
    {
        Cache::forget('items.popular.10');
        Cache::forget('items.popular.20');
        Cache::forget('items.boosted.10');
        Cache::forget('items.boosted.20');
    }

    /**
     * Invalider le cache des stats d'un utilisateur
     */
    public function forgetUserStats(int $userId)
    {
        Cache::forget("user.{$userId}.stats");
    }

    /**
     * Invalider tout le cache de l'application
     */
    public function forgetAll()
    {
        Cache::flush();
    }

    /**
     * Récupérer ou mettre en cache avec une clé personnalisée
     */
    public function remember(string $key, int $ttl, \Closure $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Mettre en cache avec tags (Redis uniquement)
     */
    public function rememberWithTags(array $tags, string $key, int $ttl, \Closure $callback)
    {
        if (config('cache.default') === 'redis') {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }
        
        // Fallback sans tags pour database cache
        return $this->remember($key, $ttl, $callback);
    }

    /**
     * Invalider par tags (Redis uniquement)
     */
    public function forgetByTags(array $tags)
    {
        if (config('cache.default') === 'redis') {
            Cache::tags($tags)->flush();
        }
    }

    /**
     * Récupérer les données de la page d'accueil avec cache
     */
    public function getHomepageData(): array
    {
        return Cache::remember('homepage.data', self::CACHE_ITEMS_LIST, function () {
            $categories = \App\Models\Category::withCount(['items' => function ($q) {
                $q->where('status', 'active');
            }])->where('is_active', true)->orderBy('sort_order')->get();

            $spotlightItems = Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
                ->whereHas('activeBoosts', function ($query) {
                    $query->whereHas('boostType', function ($subQuery) {
                        $subQuery->where('name', 'spotlight');
                    })->where('status', 'active')->where('expires_at', '>', now());
                })
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();

            $boostedItems = Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
                ->whereHas('activeBoosts')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get();

            $regularItems = Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
                ->whereDoesntHave('activeBoosts')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get();

            $latestItems = $boostedItems->concat($regularItems)->take(12);

            $stats = [
                'users' => \App\Models\User::count(),
                'items' => Item::where('status', 'active')->count(),
                'categories' => \App\Models\Category::where('is_active', true)->count(),
                'boosted_items' => Item::whereHas('activeBoosts')->where('status', 'active')->count(),
            ];

            return compact('categories', 'spotlightItems', 'boostedItems', 'regularItems', 'latestItems', 'stats');
        });
    }

    /**
     * Invalider le cache de la page d'accueil
     */
    public function forgetHomepage()
    {
        Cache::forget('homepage.data');
    }

    /**
     * Récupérer les stats du dashboard utilisateur avec cache
     */
    public function getDashboardStats(int $userId): array
    {
        return Cache::remember("dashboard.stats.{$userId}", self::CACHE_USER_STATS, function () use ($userId) {
            return [
                'total_items' => Item::where('user_id', $userId)->count(),
                'active_items' => Item::where('user_id', $userId)->where('status', 'active')->count(),
                'total_sales' => \App\Models\Order::where('seller_id', $userId)->where('status', 'completed')->count(),
                'total_revenue' => \App\Models\Payment::where('seller_id', $userId)->where('status', 'completed')->sum('amount'),
                'unread_messages' => \App\Models\Message::where('receiver_id', $userId)->whereNull('read_at')->count(),
                'unread_notifications' => \App\Models\Notification::where('user_id', $userId)->whereNull('read_at')->count(),
                'average_rating' => \App\Models\Review::where('seller_id', $userId)->avg('rating') ?? 0,
                'total_reviews' => \App\Models\Review::where('seller_id', $userId)->count(),
            ];
        });
    }

    /**
     * Invalider le cache du dashboard utilisateur
     */
    public function forgetDashboardStats(int $userId)
    {
        Cache::forget("dashboard.stats.{$userId}");
    }
}
