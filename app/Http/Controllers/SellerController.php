<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use App\Models\Wallet;
use App\Models\Transaction;

class SellerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $items = $user->items()->latest()->take(5)->get();
        $sales = Order::where('seller_id', $user->id)->latest()->take(5)->get();
        $reviews = Review::where('seller_id', $user->id)->latest()->take(5)->get();

        $stats = [
            'total_items' => $user->items()->count(),
            'active_items' => $user->items()->where('status', 'active')->count(),
            'total_sales' => Order::where('seller_id', $user->id)->count(),
            'completed_sales' => Order::where('seller_id', $user->id)->where('status', 'completed')->count(),
            'total_revenue' => Order::where('seller_id', $user->id)->where('status', 'completed')->sum('total_amount'),
            'average_rating' => Review::where('seller_id', $user->id)->avg('rating') ?? 0,
            'total_reviews' => Review::where('seller_id', $user->id)->count(),
        ];

        $usdWallet = $user->usdWallet();
        $cdfWallet = $user->cdfWallet();

        return view('seller.dashboard', compact('stats', 'items', 'sales', 'reviews', 'usdWallet', 'cdfWallet'));
    }

    public function items()
    {
        $items = auth()->user()->items()->with('category', 'brand')->latest()->paginate(12);
        return view('seller.items', compact('items'));
    }

    public function sales()
    {
        $sales = Order::where('seller_id', auth()->id())->with('buyer', 'item')->latest()->paginate(15);
        return view('seller.sales', compact('sales'));
    }

    public function wallet()
    {
        $user = auth()->user();
        $usdWallet = $user->usdWallet();
        $cdfWallet = $user->cdfWallet();

        if (!$usdWallet) {
            $usdWallet = $user->wallets()->create(['currency' => 'USD', 'balance' => 0]);
        }
        if (!$cdfWallet) {
            $cdfWallet = $user->wallets()->create(['currency' => 'CDF', 'balance' => 0]);
        }

        $recentTransactions = Transaction::whereIn('wallet_id', [$usdWallet->id, $cdfWallet->id])
            ->latest()->paginate(15);
        return view('seller.wallet', compact('usdWallet', 'cdfWallet', 'recentTransactions'));
    }

    public function categories()
    {
        $categories = Category::withCount('items')->get();
        return view('seller.categories', compact('categories'));
    }

    public function brands()
    {
        $brands = Brand::withCount('items')->get();
        return view('seller.brands', compact('brands'));
    }

    public function reviews()
    {
        $reviews = Review::where('seller_id', auth()->id())->with('reviewer', 'item')->latest()->paginate(15);
        $stats = [
            'average' => Review::where('seller_id', auth()->id())->avg('rating') ?? 0,
            'total' => Review::where('seller_id', auth()->id())->count(),
            'by_rating' => [
                5 => Review::where('seller_id', auth()->id())->where('rating', 5)->count(),
                4 => Review::where('seller_id', auth()->id())->where('rating', 4)->count(),
                3 => Review::where('seller_id', auth()->id())->where('rating', 3)->count(),
                2 => Review::where('seller_id', auth()->id())->where('rating', 2)->count(),
                1 => Review::where('seller_id', auth()->id())->where('rating', 1)->count(),
            ]
        ];
        return view('seller.reviews', compact('reviews', 'stats'));
    }
}
