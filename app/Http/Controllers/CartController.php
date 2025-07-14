<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class CartController extends Controller
{
    // Afficher le panier
    public function index(Request $request)
    {
        $cart = session('cart', []);
        return view('cart', ['cart' => $cart]);
    }

    // Ajouter un article au panier
    public function add(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);
        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart = session('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] += $quantity;
        } else {
            $cart[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'currency' => $item->currency,
                'quantity' => $quantity,
                'image' => $item->images[0] ?? null,
            ];
        }
        session(['cart' => $cart]);
        return redirect()->route('cart.index')->with('success', 'Article ajouté au panier.');
    }

    // Modifier la quantité d'un article
    public function update(Request $request, $itemId)
    {
        $cart = session('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] = max(1, (int) $request->input('quantity', 1));
            session(['cart' => $cart]);
        }
        return redirect()->route('cart.index');
    }

    // Supprimer un article du panier
    public function remove($itemId)
    {
        $cart = session('cart', []);
        unset($cart[$itemId]);
        session(['cart' => $cart]);
        return redirect()->route('cart.index');
    }

    // Vider le panier
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index');
    }

    // Page de checkout (récapitulatif avant paiement)
    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
        $total = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        return view('checkout', ['cart' => $cart, 'total' => $total]);
    }

    // Page de paiement mobile avec pré-remplissage
    public function pay()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
        $total = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        return view('payments', ['cart' => $cart, 'total' => $total]);
    }
}
