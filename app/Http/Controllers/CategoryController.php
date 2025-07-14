<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Affiche une catégorie et ses articles.
     */
    public function show(Category $category)
    {
        $items = Item::with(['category', 'brand', 'user'])
            ->where('category_id', $category->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Category::where('is_active', true)->get();

        return view('categories.show', compact('category', 'items', 'categories'));
    }

    /**
     * Affiche toutes les catégories.
     */
    public function index()
    {
        $categories = Category::withCount(['items' => function($q) {
            $q->where('status', 'active');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return view('categories.index', compact('categories'));
    }
}
