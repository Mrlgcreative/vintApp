<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use App\Services\StatsService;

class DashboardController extends Controller
{
    protected CacheService $cacheService;
    protected StatsService $statsService;

    public function __construct(CacheService $cacheService, StatsService $statsService)
    {
        $this->cacheService = $cacheService;
        $this->statsService = $statsService;
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

        $stats = $this->statsService->getUserStats($user->id);
        $recentItems = $this->getRecentItems($user);
        $recentOrders = $this->getRecentOrders($user);
        $recentMessages = $this->getRecentMessages($user);
        $notifications = $this->getUnreadNotifications($user);
        $salesChart = $this->statsService->getSalesChart($user->id);
        $popularItems = $this->getPopularItems($user);
        
        // Ajouter les statistiques de support si les modèles existent
        if (class_exists(\App\Models\SupportChat::class)) {
            $supportStats = $this->statsService->getUserSupportStats($user->id);
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
     * Get analytics data
     */
    public function analytics()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $analytics = $this->statsService->getUserAnalytics($user->id);

        return view('dashboard.analytics', compact('analytics'));
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

        $stats = $this->statsService->getUserStats($user->id);
        $salesChart = $this->statsService->getSalesChart($user->id);
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
