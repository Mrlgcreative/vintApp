<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Setting;
use App\Models\HeroSlide;
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

        // Récupérer les slides du carrousel actives
        $heroSlides = HeroSlide::active()->ordered()->get();

        // Paramètres du Hero (fallback si pas de slides)
        $heroSettings = [
            'title' => Setting::get('hero_title', 'Découvrez des articles uniques'),
            'subtitle' => Setting::get('hero_subtitle', 'La marketplace moderne pour acheter et vendre en toute sécurité.'),
            'image' => Setting::get('hero_image', 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80'),
            'button_primary' => Setting::get('hero_button_primary_text', 'Vendre'),
            'button_secondary' => Setting::get('hero_button_secondary_text', 'Parcourir'),
        ];

        return view('home', compact('categories', 'latestItems', 'stats', 'heroSlides', 'heroSettings'));
    }
} 