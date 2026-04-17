<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Setting;
use App\Models\HeroSlide;
use App\Services\CacheService;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        $data = $this->cacheService->getHomepageData();

        $categories = $data['categories'];
        $spotlightItems = $data['spotlightItems'];
        $boostedItems = $data['boostedItems'];
        $latestItems = $data['latestItems'];
        $stats = $data['stats'];

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

        return view('home', compact('categories', 'latestItems', 'stats', 'heroSlides', 'heroSettings', 'boostedItems', 'spotlightItems'));
    }

    /**
     * API endpoint pour la page d'accueil
     */
    public function apiIndex()
    {
        $data = $this->cacheService->getHomepageData();

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

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $data['categories'],
                'spotlight_items' => $data['spotlightItems'],
                'boosted_items' => $data['boostedItems'],
                'latest_items' => $data['latestItems'],
                'stats' => $data['stats'],
                'hero_slides' => $heroSlides,
                'hero_settings' => $heroSettings,
            ]
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }
} 