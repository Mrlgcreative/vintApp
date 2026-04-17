<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\CacheService;

class DashboardController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display the main dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur est admin et le rediriger vers le dashboard admin
        $isAdmin = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
            
        if ($isAdmin) {
            return redirect()->route('admin.dashboard');
        }

        $stats = $this->getUserStats($user);
        $recentItems = $this->getRecentItems($user);
        $recentOrders = $this->getRecentOrders($user);
        $recentMessages = $this->getRecentMessages($user);
        $notifications = $this->getUnreadNotifications($user);
        $salesChart = $this->getSalesChart($user);
        $popularItems = $this->getPopularItems($user);
        
        // Ajouter les statistiques de support si les modèles existent
        if (class_exists(\App\Models\SupportChat::class)) {
            $supportStats = $this->getSupportStats($user);
            $stats = array_merge($stats, $supportStats);
        } else {
            // Valeurs par défaut si les modèles de support n'existent pas
            $stats = array_merge($stats, [
                'total_support_chats' => 0,
                'open_support_chats' => 0,
                'pending_support_chats' => 0,
                'unassigned_support_chats' => 0
            ]);
        }

        return view('dashboard.index', compact(
            'stats',
            'recentItems',
            'recentOrders',
            'recentMessages',
            'notifications',
            'salesChart',
            'popularItems'
        ));
    }

    /**
     * Get user statistics
     */
    private function getUserStats($user)
    {
        return Cache::remember("dashboard.stats.{$user->id}", 300, function () use ($user) {
            $stats = [
                'total_items' => Item::where('user_id', $user->id)->count(),
                'active_items' => Item::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->count(),
                'total_sales' => Order::where('seller_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'total_revenue' => Payment::where('seller_id', $user->id)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'unread_messages' => Message::where('receiver_id', $user->id)
                    ->where('read_at', null)
                    ->count(),
                'unread_notifications' => Notification::where('user_id', $user->id)
                    ->where('read_at', null)
                    ->count(),
                'average_rating' => Review::where('seller_id', $user->id)
                    ->avg('rating') ?? 0,
                'total_reviews' => Review::where('seller_id', $user->id)->count(),
            ];

            // Calculate monthly growth
            $currentMonth = Carbon::now()->month;
            $lastMonth = Carbon::now()->subMonth()->month;

            $currentMonthSales = Order::where('seller_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $lastMonthSales = Order::where('seller_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', $lastMonth)
                ->count();

            $stats['sales_growth'] = $lastMonthSales > 0 
                ? (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100 
                : 0;

            return $stats;
        });
    }

    /**
     * Get recent items for the user
     */
    private function getRecentItems($user)
    {
        return Item::where('user_id', $user->id)
            ->with(['category', 'brand'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent orders for the user
     */
    private function getRecentOrders($user)
    {
        return Order::where('seller_id', $user->id)
            ->orWhere('buyer_id', $user->id)
            ->with(['item', 'buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent messages for the user
     */
    private function getRecentMessages($user)
    {
        return Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get unread notifications
     */
    private function getUnreadNotifications($user)
    {
        return Notification::where('user_id', $user->id)
            ->where('read_at', null)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get sales chart data for the last 6 months
     */
    private function getSalesChart($user)
    {
        return Cache::remember("dashboard.chart.{$user->id}", 900, function () use ($user) {
            $months = [];
            $sales = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $months[] = $month->format('M Y');
                
                $monthSales = Order::where('seller_id', $user->id)
                    ->where('status', 'completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
                
                $sales[] = $monthSales;
            }

            return [
                'labels' => $months,
                'data' => $sales
            ];
        });
    }

    /**
     * Get popular items for the user
     */
    private function getPopularItems($user)
    {
        return Item::where('user_id', $user->id)
            ->with(['category', 'brand'])
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get support statistics for the user
     */
    private function getSupportStats($user)
    {
        $supportStats = [
            'total_support_chats' => 0,
            'open_support_chats' => 0,
            'pending_support_chats' => 0,
            'unassigned_support_chats' => 0
        ];

        if (class_exists(\App\Models\SupportChat::class)) {
            $supportModel = \App\Models\SupportChat::class;
            
            $supportStats['total_support_chats'] = $supportModel::where('user_id', $user->id)->count();
            $supportStats['open_support_chats'] = $supportModel::where('user_id', $user->id)
                ->where('status', 'open')->count();
            $supportStats['pending_support_chats'] = $supportModel::where('user_id', $user->id)
                ->whereIn('status', ['in_progress', 'waiting_user'])->count();
            
            // Pour un utilisateur normal, "unassigned" n'a pas de sens, on met 0
            $supportStats['unassigned_support_chats'] = 0;
        }

        return $supportStats;
    }

    /**
     * Get analytics data
     */
    public function analytics()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $analytics = [
            'top_selling_items' => $this->getTopSellingItems($user),
            'revenue_by_category' => $this->getRevenueByCategory($user),
            'customer_demographics' => $this->getCustomerDemographics($user),
            'conversion_rates' => $this->getConversionRates($user),
        ];

        return view('dashboard.analytics', compact('analytics'));
    }

    /**
     * Get top selling items
     */
    private function getTopSellingItems($user)
    {
        return Item::where('user_id', $user->id)
            ->with(['category', 'brand'])
            ->withCount(['orders as sales_count' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('sales_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get revenue by category
     */
    private function getRevenueByCategory($user)
    {
        return DB::table('items')
            ->join('orders', 'items.id', '=', 'orders.item_id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->where('items.user_id', $user->id)
            ->where('orders.status', 'completed')
            ->select('categories.name', DB::raw('SUM(orders.total_amount) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    /**
     * Get customer demographics
     */
    private function getCustomerDemographics($user)
    {
        return DB::table('orders')
            ->join('users', 'orders.buyer_id', '=', 'users.id')
            ->where('orders.seller_id', $user->id)
            ->where('orders.status', 'completed')
            ->select(
                DB::raw('COUNT(DISTINCT orders.buyer_id) as unique_customers'),
                DB::raw('AVG(orders.total_amount) as average_order_value'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->first();
    }

    /**
     * Get conversion rates
     */
    private function getConversionRates($user)
    {
        $totalViews = Item::where('user_id', $user->id)->sum('views');
        $totalOrders = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $conversionRate = $totalViews > 0 ? ($totalOrders / $totalViews) * 100 : 0;

        return [
            'total_views' => $totalViews,
            'total_orders' => $totalOrders,
            'conversion_rate' => round($conversionRate, 2)
        ];
    }

    /**
     * Get notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Notification::where('user_id', $user->id)
            ->where('read_at', null)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get API data for dashboard
     */
    public function apiData()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = $this->getUserStats($user);
        $salesChart = $this->getSalesChart($user);
        $recentItems = $this->getRecentItems($user);
        $recentOrders = $this->getRecentOrders($user);

        return response()->json([
            'stats' => $stats,
            'sales_chart' => $salesChart,
            'recent_items' => $recentItems,
            'recent_orders' => $recentOrders
        ]);
    }
}
