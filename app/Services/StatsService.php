<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductAuthenticityCheck;
use App\Models\Review;
use App\Models\SupportChat;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsService
{
    /**
     * Statistiques d'un utilisateur (dashboard user web + API).
     */
    public function getUserStats(int $userId): array
    {
        return Cache::remember("dashboard.stats.{$userId}", 300, function () use ($userId) {
            $stats = [
                'total_items' => Item::where('user_id', $userId)->count(),
                'active_items' => Item::where('user_id', $userId)
                    ->where('status', 'active')
                    ->count(),
                'total_sales' => Order::where('seller_id', $userId)
                    ->where('status', 'completed')
                    ->count(),
                'total_revenue' => Payment::where('seller_id', $userId)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'unread_messages' => Message::where('receiver_id', $userId)
                    ->where('read_at', null)
                    ->count(),
                'unread_notifications' => Notification::where('user_id', $userId)
                    ->where('read_at', null)
                    ->count(),
                'average_rating' => Review::where('seller_id', $userId)
                    ->avg('rating') ?? 0,
                'total_reviews' => Review::where('seller_id', $userId)->count(),
            ];

            $currentMonth = Carbon::now()->month;
            $lastMonth = Carbon::now()->subMonth()->month;

            $currentMonthSales = Order::where('seller_id', $userId)
                ->where('status', 'completed')
                ->whereMonth('created_at', $currentMonth)
                ->count();

            $lastMonthSales = Order::where('seller_id', $userId)
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
     * Données du graphique de ventes des derniers mois (dashboard user web + API).
     */
    public function getSalesChart(int $userId, int $months = 6): array
    {
        return Cache::remember("dashboard.chart.{$userId}", 900, function () use ($userId, $months) {
            $labels = [];
            $sales = [];

            for ($i = $months - 1; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M Y');

                $monthSales = Order::where('seller_id', $userId)
                    ->where('status', 'completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $sales[] = $monthSales;
            }

            return [
                'labels' => $labels,
                'data' => $sales,
            ];
        });
    }

    /**
     * Données analytics d'un utilisateur (page analytics web).
     */
    public function getUserAnalytics(int $userId): array
    {
        return [
            'top_selling_items' => $this->getTopSellingItems($userId),
            'revenue_by_category' => $this->getRevenueByCategory($userId),
            'customer_demographics' => $this->getCustomerDemographics($userId),
            'conversion_rates' => $this->getConversionRates($userId),
        ];
    }

    /**
     * Statistiques de support d'un utilisateur.
     */
    public function getUserSupportStats(int $userId): array
    {
        $supportStats = [
            'total_support_chats' => 0,
            'open_support_chats' => 0,
            'pending_support_chats' => 0,
            'unassigned_support_chats' => 0,
        ];

        if (class_exists(SupportChat::class)) {
            $supportStats['total_support_chats'] = SupportChat::where('user_id', $userId)->count();
            $supportStats['open_support_chats'] = SupportChat::where('user_id', $userId)
                ->where('status', 'open')
                ->count();
            $supportStats['pending_support_chats'] = SupportChat::where('user_id', $userId)
                ->whereIn('status', ['in_progress', 'waiting_user'])
                ->count();
        }

        return $supportStats;
    }

    /**
     * Statistiques quotidiennes des derniers jours (dashboard admin web).
     */
    public function getDailyStats(int $days = 30)
    {
        $daysCollection = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $daysCollection->push([
                'date' => $date->format('Y-m-d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'transactions' => Transaction::whereDate('created_at', $date)->count(),
                'revenue' => Transaction::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'orders' => Order::whereDate('created_at', $date)->count(),
            ]);
        }

        return $daysCollection;
    }

    /**
     * Statistiques globales du dashboard admin (page web).
     */
    public function getAdminDashboardStats(): array
    {
        $support = app(SupportService::class)->getGlobalStats();

        return [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'active_users' => User::where('last_seen', '>=', Carbon::now()->subDays(7))->count(),

            'total_transactions' => Transaction::count(),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
            'total_transaction_amount' => Transaction::where('status', 'completed')->sum('amount'),

            // Revenus plateforme = sous-wallets entreprise
            'total_revenue_usd' => Wallet::getEnterpriseSubWallets('USD')->sum('balance'),
            'total_revenue_cdf' => Wallet::getEnterpriseSubWallets('CDF')->sum('balance'),

            // Wallets en attente de confirmation (type='pending')
            'pending_wallets' => Wallet::where('type', 'pending')->count(),
            'pending_wallets_usd' => Wallet::where('type', 'pending')
                ->where('currency', 'USD')
                ->sum('balance'),
            'pending_wallets_cdf' => Wallet::where('type', 'pending')
                ->where('currency', 'CDF')
                ->sum('balance'),
            'total_wallet_balance' => Wallet::where('is_active', true)->sum('balance'),

            // Sous-wallets entreprise
            'enterprise_commission_usd' => Wallet::getEnterpriseSubWallet('commission', 'USD')->balance ?? 0,
            'enterprise_transport_usd' => Wallet::getEnterpriseSubWallet('transport', 'USD')->balance ?? 0,
            'enterprise_boost_usd' => Wallet::getEnterpriseSubWallet('boost', 'USD')->balance ?? 0,

            // Statistiques de vérification d'authenticité
            'total_verifications' => ProductAuthenticityCheck::count(),
            'pending_verifications' => ProductAuthenticityCheck::whereIn('status', ['pending', 'expert_review'])->count(),
            'completed_verifications' => ProductAuthenticityCheck::where('payment_completed', true)->count(),
            'verification_revenue_usd' => ProductAuthenticityCheck::where('payment_completed', true)->sum('verification_fee') ?? 0,

            'total_orders' => Order::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),

            'total_items' => Item::count(),
            'active_items' => Item::where('status', 'active')->count(),

            // Statistiques de support
            'total_support_chats' => $support['total'],
            'open_support_chats' => $support['open'],
            'pending_support_chats' => $support['open'] + $support['in_progress'],
            'unassigned_support_chats' => $support['unassigned'],
        ];
    }

    /**
     * Statistiques du dashboard admin (API).
     */
    public function getAdminApiStats(): array
    {
        $support = app(SupportService::class)->getGlobalStats();

        return [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'active_users' => User::where('last_seen', '>=', Carbon::now()->subDays(7))->count(),

            'total_transactions' => Transaction::count(),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
            'total_revenue_usd' => Transaction::where('status', 'completed')
                ->where('currency', 'USD')
                ->sum('amount'),
            'total_revenue_cdf' => Transaction::where('status', 'completed')
                ->where('currency', 'CDF')
                ->sum('amount'),

            'pending_wallets' => Wallet::where('type', 'pending')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_items' => Item::count(),
            'active_items' => Item::where('status', 'active')->count(),

            'total_support_chats' => $support['total'],
            'open_support_chats' => $support['open'],
        ];
    }

    /**
     * Résumé statistique admin complet (API).
     */
    public function getStatsSummary(): array
    {
        $support = app(SupportService::class)->getGlobalStats();

        return [
            'users' => [
                'total' => User::count(),
                'today' => User::whereDate('created_at', today())->count(),
                'active_7d' => User::where('last_seen', '>=', Carbon::now()->subDays(7))->count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
            ],
            'transactions' => [
                'total' => Transaction::count(),
                'today' => Transaction::whereDate('created_at', today())->count(),
                'completed' => Transaction::where('status', 'completed')->count(),
                'total_amount_usd' => Transaction::where('status', 'completed')
                    ->where('currency', 'USD')
                    ->sum('amount'),
                'total_amount_cdf' => Transaction::where('status', 'completed')
                    ->where('currency', 'CDF')
                    ->sum('amount'),
            ],
            'orders' => [
                'total' => Order::count(),
                'today' => Order::whereDate('created_at', today())->count(),
                'pending' => Order::where('status', 'pending')->count(),
                'completed' => Order::where('status', 'completed')->count(),
            ],
            'items' => [
                'total' => Item::count(),
                'active' => Item::where('status', 'active')->count(),
                'pending' => Item::where('status', 'pending')->count(),
                'sold' => Item::where('status', 'sold')->count(),
            ],
            'wallets' => [
                'pending' => Wallet::where('type', 'pending')->count(),
                'total_balance_usd' => Wallet::where('is_active', true)
                    ->where('currency', 'USD')
                    ->sum('balance'),
                'total_balance_cdf' => Wallet::where('is_active', true)
                    ->where('currency', 'CDF')
                    ->sum('balance'),
            ],
            'support' => [
                'total' => $support['total'],
                'open' => $support['open'],
                'pending' => $support['open'] + $support['in_progress'],
                'unassigned' => $support['unassigned'],
            ],
            'verifications' => [
                'total' => ProductAuthenticityCheck::count(),
                'pending' => ProductAuthenticityCheck::whereIn('status', ['pending', 'expert_review'])->count(),
                'completed' => ProductAuthenticityCheck::where('payment_completed', true)->count(),
            ],
        ];
    }

    /**
     * Top 10 articles les plus vendus d'un utilisateur.
     */
    private function getTopSellingItems(int $userId)
    {
        return Item::where('user_id', $userId)
            ->with(['category', 'brand'])
            ->withCount(['orders as sales_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('sales_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Revenus par catégorie d'un utilisateur.
     */
    private function getRevenueByCategory(int $userId)
    {
        return DB::table('items')
            ->join('orders', 'items.id', '=', 'orders.item_id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->where('items.user_id', $userId)
            ->where('orders.status', 'completed')
            ->select('categories.name', DB::raw('SUM(orders.total_amount) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    /**
     * Démographie clients d'un utilisateur.
     */
    private function getCustomerDemographics(int $userId)
    {
        return DB::table('orders')
            ->join('users', 'orders.buyer_id', '=', 'users.id')
            ->where('orders.seller_id', $userId)
            ->where('orders.status', 'completed')
            ->select(
                DB::raw('COUNT(DISTINCT orders.buyer_id) as unique_customers'),
                DB::raw('AVG(orders.total_amount) as average_order_value'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->first();
    }

    /**
     * Taux de conversion d'un utilisateur.
     */
    private function getConversionRates(int $userId): array
    {
        $totalViews = Item::where('user_id', $userId)->sum('views');
        $totalOrders = Order::where('seller_id', $userId)
            ->where('status', 'completed')
            ->count();

        $conversionRate = $totalViews > 0 ? ($totalOrders / $totalViews) * 100 : 0;

        return [
            'total_views' => $totalViews,
            'total_orders' => $totalOrders,
            'conversion_rate' => round($conversionRate, 2),
        ];
    }
}
