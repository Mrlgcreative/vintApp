<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::withCount(['items' => function($q) {
            $q->where('status', 'active');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        $latestItems = \App\Models\Item::with(['category', 'brand', 'user'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        $stats = [
            'users' => \App\Models\User::count(),
            'items' => \App\Models\Item::where('status', 'active')->count(),
            'categories' => \App\Models\Category::where('is_active', true)->count(),
        ];

        return view('home', compact('categories', 'latestItems', 'stats'));
    }
} 