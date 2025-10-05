<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\models\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['item', 'buyer'])
            ->where('buyer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $item = null;
        if ($request->has('item')) {
            $item = Item::findOrFail($request->item);
            
            // Vérifier que l'utilisateur ne peut pas acheter son propre article
            if ($item->user_id === Auth::id()) {
                return redirect()->route('items.show', $item)
                    ->with('error', 'Vous ne pouvez pas acheter votre propre article.');
            }
        }

        return view('orders.create', compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Vérifications
        if ($item->user_id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas acheter votre propre article.']);
        }

        if ($item->status !== 'active') {
            return back()->withErrors(['error' => 'Cet article n\'est plus disponible.']);
        }

        if ($request->quantity > $item->quantity) {
            return back()->withErrors(['quantity' => 'La quantité demandée dépasse le stock disponible.']);
        }

        try {
            DB::beginTransaction();

            // Créer la commande
            $order = new Order();
            $order->buyer_id = Auth::id();
            $order->seller_id = $item->user_id;
            $order->item_id = $item->id;
            $order->quantity = $request->quantity;
            $order->unit_price = $item->price;
            $order->total_amount = $item->price * $request->quantity;
            $order->currency = $item->currency;
            $order->status = 'pending';
            $order->shipping_address = $request->shipping_address;
            $order->shipping_city = $request->shipping_city;
            $order->shipping_phone = $request->shipping_phone;
            $order->notes = $request->notes;
            $order->save();

            // Mettre à jour le stock
            $item->quantity -= $request->quantity;
            if ($item->quantity <= 0) {
                $item->status = 'sold';
            }
            $item->save();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la commande: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la création de la commande.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Vérifier que l'utilisateur peut voir cette commande
        if ($order->buyer_id !== Auth::id() && $order->item->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette commande.');
        }

        $order->load(['item', 'buyer']);

        return view('orders.show', compact('order'));
    }


    public function acheterProduit(Request $request, Wallet $wallet, $produitId)
{
    $user = Auth::user();

    // Vérifier que le wallet appartient bien à l'utilisateur
    if ($wallet->user_id !== $user->id) {
        abort(403, 'Accès non autorisé');
    }

    $produit = Item::findOrFail($produitId); // récupère le produit

    // Vérifier solde suffisant
    if ($wallet->balance < $produit->prix) {
        return redirect()->back()
            ->with('error', 'Solde insuffisant pour effectuer cet achat.');
    }

    try {
        DB::transaction(function () use ($wallet, $produit, $user) {

            // Débiter le wallet
            $wallet->decrement('balance', $produit->prix);

            // Ajouter transaction
            $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $produit->prix,
                'balance_after' => $wallet->fresh()->balance,
                'description' => 'Achat du produit: ' . $produit->nom,
                'reference' => 'BUY-' . time() . '-' . rand(1000, 9999),
            ]);

            // Créer la commande
            Order::create([
                'user_id' => $user->id,
                'produit_id' => $produit->id,
                'wallet_id' => $wallet->id,
                'prix' => $produit->prix,
                'status' => 'pending', // ou confirmé selon ton flow
            ]);
        });

        return redirect()->route('wallet.index')
            ->with('success', 'Achat effectué avec succès !');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Erreur lors de l\'achat : ' . $e->getMessage());
    }
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Seul le vendeur peut modifier le statut
        if ($order->item->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette commande.');
        }

        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Seul le vendeur peut modifier le statut
        if ($order->item->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette commande.');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->notes = $request->notes;

        // Mettre à jour les timestamps selon le statut
        if ($request->status === 'shipped' && $oldStatus !== 'shipped') {
            $order->shipped_at = now();
        }

        if ($request->status === 'delivered' && $oldStatus !== 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Statut de la commande mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Seul l'acheteur peut annuler sa commande si elle est en attente
        if ($order->buyer_id !== Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Vous n\'êtes pas autorisé à supprimer cette commande.'], 403);
            }
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette commande.');
        }

        if ($order->status !== 'pending') {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Vous ne pouvez annuler que les commandes en attente.'], 400);
            }
            return back()->withErrors(['error' => 'Vous ne pouvez annuler que les commandes en attente.']);
        }

        try {
            DB::beginTransaction();

            // Remettre le stock
            $item = $order->item;
            $item->quantity += $order->quantity;
            if ($item->status === 'sold') {
                $item->status = 'active';
            }
            $item->save();

            // Supprimer la commande
            $order->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Commande annulée avec succès !']);
            }

            return redirect()->route('orders.index')
                ->with('success', 'Commande annulée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'annulation de la commande: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Une erreur est survenue lors de l\'annulation.'], 500);
            }
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'annulation.']);
        }
    }

    /**
     * Afficher les commandes du vendeur
     */
    public function mySales()
    {
        $orders = Order::with(['item.category', 'item.brand', 'buyer'])
            ->whereHas('item', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.my-sales', compact('orders'));
    }

    /**
     * Confirmer le paiement
     */
    public function confirmPayment(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Vous n\'êtes pas autorisé à confirmer le paiement de cette commande.'], 403);
            }
            return back()->withErrors(['error' => 'Vous n\'êtes pas autorisé à confirmer le paiement de cette commande.']);
        }

        if ($order->status !== 'pending') {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Cette commande ne peut plus être confirmée.'], 400);
            }
            return back()->withErrors(['error' => 'Cette commande ne peut plus être confirmée.']);
        }

        try {
            $order->paid_at = now();
            $order->status = 'confirmed';
            $order->save();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Paiement confirmé avec succès !']);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Paiement confirmé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la confirmation du paiement: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Une erreur est survenue lors de la confirmation du paiement.'], 500);
            }
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la confirmation du paiement.']);
        }
    }
}
