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

        // Récupérer les articles avec boost Spotlight spécifiquement
        $spotlightItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereHas('activeBoosts', function($query) {
                $query->whereHas('boostType', function($subQuery) {
                    $subQuery->where('name', 'spotlight');
                })
                ->where('status', 'active')
                ->where('expires_at', '>', now());
            })
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Récupérer les articles avec boost prioritaires (tous types)
        $boostedItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereHas('activeBoosts')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Récupérer les articles récents (non-boostés)
        $regularItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereDoesntHave('activeBoosts')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Combiner les articles en priorisant les boostés
        $latestItems = $boostedItems->concat($regularItems)->take(12);

        $stats = [
            'users' => \App\Models\User::count(),
            'items' => \App\Models\Item::where('status', 'active')->count(),
            'categories' => \App\Models\Category::where('is_active', true)->count(),
            'boosted_items' => \App\Models\Item::whereHas('activeBoosts')->where('status', 'active')->count(),
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

        return view('home', compact('categories', 'latestItems', 'stats', 'heroSlides', 'heroSettings', 'boostedItems', 'spotlightItems'));
    }

    /**
     * API endpoint pour la page d'accueil
     * Retourne toutes les données nécessaires pour l'application mobile/React
     */
    public function apiIndex()
    {
        $categories = \App\Models\Category::withCount(['items' => function($q) {
            $q->where('status', 'active');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        // Récupérer les articles avec boost Spotlight spécifiquement
        $spotlightItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereHas('activeBoosts', function($query) {
                $query->whereHas('boostType', function($subQuery) {
                    $subQuery->where('name', 'spotlight');
                })
                ->where('status', 'active')
                ->where('expires_at', '>', now());
            })
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Récupérer les articles avec boost prioritaires (tous types)
        $boostedItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereHas('activeBoosts')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Récupérer les articles récents (non-boostés)
        $regularItems = \App\Models\Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
            ->whereDoesntHave('activeBoosts')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Combiner les articles en priorisant les boostés
        $latestItems = $boostedItems->concat($regularItems)->take(12);

        $stats = [
            'users' => \App\Models\User::count(),
            'items' => \App\Models\Item::where('status', 'active')->count(),
            'categories' => \App\Models\Category::where('is_active', true)->count(),
            'boosted_items' => \App\Models\Item::whereHas('activeBoosts')->where('status', 'active')->count(),
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

        // Fonction pour nettoyer les caractères UTF-8 malformés
        $cleanUtf8 = function ($data) use (&$cleanUtf8) {
            if (is_array($data)) {
                return array_map($cleanUtf8, $data);
            }
            if (is_object($data)) {
                $array = $data instanceof \Illuminate\Database\Eloquent\Model ? $data->toArray() : (array) $data;
                return array_map($cleanUtf8, $array);
            }
            if (is_string($data)) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
                return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data);
            }
            return $data;
        };

        return response()->json([
            'success' => true,
            'data' => $cleanUtf8([
                'categories' => $categories,
                'spotlight_items' => $spotlightItems,
                'boosted_items' => $boostedItems,
                'latest_items' => $latestItems,
                'stats' => $stats,
                'hero_slides' => $heroSlides,
                'hero_settings' => $heroSettings,
            ])
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
} 