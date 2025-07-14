<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::with(['reviewer', 'seller', 'item', 'order'])->latest()->paginate(15);
        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        $orders = Order::orderBy('id', 'desc')->get();
        return view('reviews.create', compact('users', 'items', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'seller_id' => 'required|exists:users,id|different:reviewer_id',
            'item_id' => 'nullable|exists:items,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'status' => 'nullable|string|max:20',
        ]);
        // Logique métier : un utilisateur ne peut laisser qu'un avis par commande
        if ($validated['order_id'] && Review::where('order_id', $validated['order_id'])->where('reviewer_id', $validated['reviewer_id'])->exists()) {
            return redirect()->back()->withInput()->withErrors(['order_id' => 'Vous avez déjà laissé un avis pour cette commande.']);
        }
        Review::create($validated);
        return redirect()->route('reviews.index')->with('success', 'Avis ajouté !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load(['reviewer', 'seller', 'item', 'order']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $users = User::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        $orders = Order::orderBy('id', 'desc')->get();
        return view('reviews.edit', compact('review', 'users', 'items', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'seller_id' => 'required|exists:users,id|different:reviewer_id',
            'item_id' => 'nullable|exists:items,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'status' => 'nullable|string|max:20',
        ]);
        $review->update($validated);
        return redirect()->route('reviews.index')->with('success', 'Avis modifié !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Avis supprimé !');
    }
}
