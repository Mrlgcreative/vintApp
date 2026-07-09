<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Discount;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Afficher le panier
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        return view('cart', ['cart' => $cart]);
    }

    // Ajouter un article au panier
    public function add(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);
        $quantity = max(1, (int) $request->input('quantity', 1));
        
        // Vérifier s'il y a une réduction active pour cet utilisateur et cet article
        $activeDiscount = null;
        $finalPrice = $item->price;
        
        if (Auth::check()) {
            $activeDiscount = Discount::where('item_id', $itemId)
                ->where('user_id', Auth::id())
                ->where('status', 'approved')
                ->where('expires_at', '>', now())
                ->first();
            
            if ($activeDiscount) {
                $finalPrice = $activeDiscount->final_price;
            }
        }
        
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] += $quantity;
            if ($activeDiscount) {
                $cart[$itemId]['price'] = $finalPrice;
                $cart[$itemId]['original_price'] = $item->price;
                $cart[$itemId]['discount_id'] = $activeDiscount->id;
                $cart[$itemId]['discount_percentage'] = $activeDiscount->discount_percentage;
            }
        } else {
            $cartItem = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $finalPrice,
                'currency' => $item->currency,
                'quantity' => $quantity,
                'image' => $item->images[0] ?? null,
            ];
            
            if ($activeDiscount) {
                $cartItem['original_price'] = $item->price;
                $cartItem['discount_id'] = $activeDiscount->id;
                $cartItem['discount_percentage'] = $activeDiscount->discount_percentage;
                $cartItem['has_discount'] = true;
            }
            
            $cart[$itemId] = $cartItem;
        }
        
        $request->session()->put('cart', $cart);
        $request->session()->save();
        
        $message = $activeDiscount 
            ? 'Article ajouté au panier avec réduction de ' . $activeDiscount->discount_percentage . '% !'
            : 'Article ajouté au panier.';
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => count($cart),
            ]);
        }
            
        return redirect()->route('cart.index')->with('success', $message);
    }

    // Modifier la quantité d'un article
    public function update(Request $request, $itemId)
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] = max(1, (int) $request->input('quantity', 1));
            $request->session()->put('cart', $cart);
            $request->session()->save();
        }
        return redirect()->route('cart.index');
    }

    // Supprimer un article du panier
    public function remove(Request $request, $itemId)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$itemId]);
        $request->session()->put('cart', $cart);
        return redirect()->route('cart.index');
    }

    // Vider le panier
    public function clear(Request $request)
    {
        $request->session()->forget('cart');
        return redirect()->route('cart.index');
    }

    // Page de checkout (récapitulatif avant paiement)
    public function checkout(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
        
        // Calculer le sous-total
        $subtotal = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        
        // Récupérer le pourcentage des frais de transport depuis les settings
        $transportFeePercentage = \DB::table('settings')
            ->where('key', 'transport_fee_percentage')
            ->value('value') ?? 5;
        
        // Calculer les frais de transport
        $transportFee = ($subtotal * $transportFeePercentage) / 100;
        
        // Calculer le total
        $total = $subtotal + $transportFee;
        
        $currency = !empty($cart) ? reset($cart)['currency'] ?? 'CDF' : 'CDF';

        return view('checkout', [
            'cart' => $cart, 
            'subtotal' => $subtotal,
            'transportFee' => $transportFee,
            'transportFeePercentage' => $transportFeePercentage,
            'total' => $total,
            'currency' => $currency,
        ]);
    }

    // Page de paiement mobile avec pré-remplissage
    public function pay(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
        
        // Calculer le sous-total
        $subtotal = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        
        // Récupérer le pourcentage des frais de transport depuis les settings
        $transportFeePercentage = \DB::table('settings')
            ->where('key', 'transport_fee_percentage')
            ->value('value') ?? 5;
        
        // Calculer les frais de transport
        $transportFee = ($subtotal * $transportFeePercentage) / 100;
        
        // Calculer le total
        $total = $subtotal + $transportFee;
        
        return view('payments', [
            'cart' => $cart, 
            'subtotal' => $subtotal,
            'transportFee' => $transportFee,
            'transportFeePercentage' => $transportFeePercentage,
            'total' => $total
        ]);
    }
}
