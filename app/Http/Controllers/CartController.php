<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Discount;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected function getSessionId(Request $request)
    {
        return $request->session()->getId();
    }

    protected function getCartArray(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        $userId = Auth::id();

        $cartRows = Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->get();

        $cart = [];
        foreach ($cartRows as $row) {
            $item = [
                'id' => $row->item_id,
                'name' => $row->item_name,
                'price' => (float) $row->price,
                'currency' => $row->currency,
                'quantity' => $row->quantity,
                'image' => $row->image,
            ];
            if ($row->has_discount) {
                $item['original_price'] = (float) $row->original_price;
                $item['discount_id'] = $row->discount_id;
                $item['discount_percentage'] = (float) $row->discount_percentage;
                $item['has_discount'] = true;
            }
            $cart[$row->item_id] = $item;
        }

        return $cart;
    }

    protected function syncSessionToDb(Request $request)
    {
        $sessionCart = $request->session()->get('cart', []);
        if (empty($sessionCart)) {
            return;
        }

        $sessionId = $this->getSessionId($request);
        $userId = Auth::id();

        foreach ($sessionCart as $itemId => $item) {
            Cart::updateOrCreate(
                ['session_id' => $sessionId, 'item_id' => $itemId],
                [
                    'user_id' => $userId,
                    'item_name' => $item['name'],
                    'price' => $item['price'],
                    'currency' => $item['currency'] ?? 'CDF',
                    'quantity' => $item['quantity'],
                    'image' => $item['image'] ?? null,
                    'original_price' => $item['original_price'] ?? null,
                    'discount_id' => $item['discount_id'] ?? null,
                    'discount_percentage' => $item['discount_percentage'] ?? null,
                    'has_discount' => $item['has_discount'] ?? false,
                ]
            );
        }

        $request->session()->forget('cart');
    }

    public function index(Request $request)
    {
        $this->syncSessionToDb($request);
        $cart = $this->getCartArray($request);
        return view('cart', ['cart' => $cart]);
    }

    public function add(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);
        $quantity = max(1, (int) $request->input('quantity', 1));

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

        $sessionId = $this->getSessionId($request);
        $userId = Auth::id();

        $cartRow = Cart::where('session_id', $sessionId)
            ->where('item_id', $itemId)
            ->first();

        if ($cartRow) {
            $cartRow->increment('quantity', $quantity);
            if ($activeDiscount) {
                $cartRow->update([
                    'price' => $finalPrice,
                    'original_price' => $item->price,
                    'discount_id' => $activeDiscount->id,
                    'discount_percentage' => $activeDiscount->discount_percentage,
                    'has_discount' => true,
                ]);
            }
        } else {
            $data = [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'item_id' => $item->id,
                'item_name' => $item->name,
                'price' => $finalPrice,
                'currency' => $item->currency,
                'quantity' => $quantity,
                'image' => $item->images[0] ?? null,
            ];

            if ($activeDiscount) {
                $data['original_price'] = $item->price;
                $data['discount_id'] = $activeDiscount->id;
                $data['discount_percentage'] = $activeDiscount->discount_percentage;
                $data['has_discount'] = true;
            }

            Cart::create($data);
        }

        $message = $activeDiscount
            ? 'Article ajouté au panier avec réduction de ' . $activeDiscount->discount_percentage . '% !'
            : 'Article ajouté au panier.';

        if ($request->ajax() || $request->wantsJson()) {
            $cartCount = Cart::where(function ($q) use ($sessionId, $userId) {
                $q->where('session_id', $sessionId);
                if ($userId) {
                    $q->orWhere('user_id', $userId);
                }
            })->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $cartCount,
            ]);
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    public function update(Request $request, $itemId)
    {
        $sessionId = $this->getSessionId($request);
        $userId = Auth::id();

        $cartRow = Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->where('item_id', $itemId)->first();

        if ($cartRow) {
            $cartRow->update([
                'quantity' => max(1, (int) $request->input('quantity', 1)),
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function remove(Request $request, $itemId)
    {
        $sessionId = $this->getSessionId($request);
        $userId = Auth::id();

        Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->where('item_id', $itemId)->delete();

        return redirect()->route('cart.index');
    }

    public function clear(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        $userId = Auth::id();

        Cart::where(function ($q) use ($sessionId, $userId) {
            $q->where('session_id', $sessionId);
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->delete();

        return redirect()->route('cart.index');
    }

    public function checkout(Request $request)
    {
        $this->syncSessionToDb($request);
        $cart = $this->getCartArray($request);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $transportFeePercentage = \DB::table('settings')
            ->where('key', 'transport_fee_percentage')
            ->value('value') ?? 5;

        $transportFee = ($subtotal * $transportFeePercentage) / 100;
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

    public function pay(Request $request)
    {
        $this->syncSessionToDb($request);
        $cart = $this->getCartArray($request);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $transportFeePercentage = \DB::table('settings')
            ->where('key', 'transport_fee_percentage')
            ->value('value') ?? 5;

        $transportFee = ($subtotal * $transportFeePercentage) / 100;
        $total = $subtotal + $transportFee;

        return view('payments', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'transportFee' => $transportFee,
            'transportFeePercentage' => $transportFeePercentage,
            'total' => $total,
        ]);
    }
}
