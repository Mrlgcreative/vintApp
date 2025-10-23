<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\models\Wallet;
use App\Models\User;
use App\Events\OrderNotification;
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
        $orders = Order::with(['item', 'buyer', 'deliveryAddress'])
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

            // 🔔 Envoyer notification au vendeur (nouvelle commande)
            broadcast(new OrderNotification(
                $order,
                'new_order',
                "🛒 Nouvelle commande de {$order->buyer->name}",
                $item->user_id // ID du vendeur
            ))->toOthers();

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

        $order->load(['item', 'buyer', 'deliveryAddress']);

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
    public function mySales(Request $request)
    {
        $query = Order::with(['item.category', 'item.brand', 'buyer'])
            ->whereHas('item', function($q) {
                $q->where('user_id', Auth::id());
            });

        // Filtrer par statut si spécifié
        if ($request->has('status') && $request->status) {
            // Support pour plusieurs statuts séparés par virgule (ex: delivered,completed)
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(15);

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

            // 🔔 Notification au vendeur (paiement confirmé)
            broadcast(new OrderNotification(
                $order,
                'payment_confirmed',
                "💰 Paiement confirmé pour la commande #{$order->order_number}",
                $order->item->user_id // ID du vendeur
            ))->toOthers();

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

    /**
     * Marquer la commande comme expédiée (vendeur)
     */
    public function markAsShipped(Order $order)
    {
        // Vérifier que c'est bien le vendeur qui expédie
        if ($order->item->user_id !== Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Vous n\'êtes pas autorisé à modifier cette commande.'
                ], 403);
            }
            return back()->withErrors(['error' => 'Vous n\'êtes pas autorisé à modifier cette commande.']);
        }

        // Vérifier que la commande est en statut confirmed
        if ($order->status !== 'confirmed') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Cette commande ne peut pas être expédiée dans son état actuel.'
                ], 400);
            }
            return back()->withErrors(['error' => 'Cette commande ne peut pas être expédiée dans son état actuel.']);
        }

        try {
            $order->status = 'shipped';
            $order->shipped_at = now();
            $order->save();

            // 🔔 Notification à l'acheteur (commande expédiée)
            broadcast(new OrderNotification(
                $order,
                'order_shipped',
                "📦 Votre commande #{$order->order_number} a été expédiée",
                $order->buyer_id // ID de l'acheteur
            ))->toOthers();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Commande marquée comme expédiée !']);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande marquée comme expédiée avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage expédié: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Une erreur est survenue.'], 500);
            }
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors du marquage de la commande.']);
        }
    }

    /**
     * Marquer la commande comme livrée (vendeur)
     */
    public function markAsDelivered(Order $order)
    {
        // Vérifier que c'est bien le vendeur qui livre
        if ($order->item->user_id !== Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Vous n\'êtes pas autorisé à modifier cette commande.'
                ], 403);
            }
            return back()->withErrors(['error' => 'Vous n\'êtes pas autorisé à modifier cette commande.']);
        }

        // Vérifier que la commande est en statut shipped
        if ($order->status !== 'shipped') {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Cette commande doit d\'abord être marquée comme expédiée.'
                ], 400);
            }
            return back()->withErrors(['error' => 'Cette commande doit d\'abord être marquée comme expédiée.']);
        }

        try {
            $order->status = 'delivered';
            $order->delivered_at = now();
            $order->save();

            // 🔔 Notification à l'acheteur (commande livrée)
            broadcast(new OrderNotification(
                $order,
                'order_delivered',
                "🚚 Votre commande #{$order->order_number} a été livrée",
                $order->buyer_id // ID de l'acheteur
            ))->toOthers();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Commande marquée comme livrée !']);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande marquée comme livrée avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage livré: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Une erreur est survenue.'], 500);
            }
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors du marquage de la commande.']);
        }
    }

    /**
     * Confirmer la réception de la livraison par le client
     */
    public function confirmDelivery(Request $request, Order $order)
    {
        // Vérifier que c'est bien l'acheteur qui confirme
        if ($order->buyer_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Vous n\'êtes pas autorisé à confirmer la réception de cette commande.'
                ], 403);
            }
            return back()->withErrors(['error' => 'Vous n\'êtes pas autorisé à confirmer la réception de cette commande.']);
        }

        // Vérifier que la commande est en statut shipped ou delivered
        if (!in_array($order->status, ['shipped', 'delivered'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Cette commande n\'est pas encore expédiée.'
                ], 400);
            }
            return back()->withErrors(['error' => 'Cette commande n\'est pas encore expédiée.']);
        }

        // Vérifier si la commande n'a pas déjà été confirmée
        if ($order->confirmed_by_buyer_at) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Vous avez déjà confirmé la réception de cette commande.'
                ], 400);
            }
            return back()->withErrors(['error' => 'Vous avez déjà confirmé la réception de cette commande.']);
        }

        try {
            DB::beginTransaction();

            // Enregistrer la confirmation de livraison
            $order->confirmed_by_buyer_at = now();
            $order->buyer_confirmation_note = $request->input('note');
            $order->status = 'completed';
            $order->save();

            // Récupérer les pourcentages de commission et transport depuis les settings
            $commissionPercent = (float) (DB::table('settings')
                ->where('key', 'platform_commission_percentage')
                ->value('value') ?? 10);
            
            $transportPercent = (float) (DB::table('settings')
                ->where('key', 'transport_fee_percentage')
                ->value('value') ?? 5);
            
            // Calculer les montants de distribution
            $totalAmount = (float) $order->total_amount;
            $commissionAmount = round($totalAmount * ($commissionPercent / 100), 2);
            $transportAmount = round($totalAmount * ($transportPercent / 100), 2);
            $sellerAmount = $totalAmount - $commissionAmount - $transportAmount;
            
            Log::info("Distribution calculée pour commande #{$order->id}", [
                'total' => $totalAmount,
                'commission_percent' => $commissionPercent,
                'commission_amount' => $commissionAmount,
                'transport_percent' => $transportPercent,
                'transport_amount' => $transportAmount,
                'seller_amount' => $sellerAmount,
                'currency' => $order->currency
            ]);

            // Transférer l'argent du wallet pending au wallet main du vendeur
            $seller = User::find($order->seller_id);
            if ($seller) {
                // Récupérer le wallet pending du vendeur
                $sellerPendingWallet = \App\Models\Wallet::where('user_id', $seller->id)
                    ->where('type', 'pending')
                    ->where('currency', $order->currency)
                    ->first();
                
                if ($sellerPendingWallet && $sellerPendingWallet->balance >= $order->total_amount) {
                    // Créer ou récupérer le wallet main du vendeur
                    $sellerMainWallet = \App\Models\Wallet::firstOrCreate(
                        [
                            'user_id' => $seller->id,
                            'type' => 'main',
                            'currency' => $order->currency
                        ],
                        [
                            'balance' => 0,
                            'status' => 'active',
                            'is_active' => true
                        ]
                    );
                    
                    // Créer ou récupérer le wallet enterprise pour la commission et transport
                    $enterpriseWallet = \App\Models\Wallet::firstOrCreate(
                        [
                            'user_id' => null, // Wallet de la plateforme
                            'type' => 'enterprise',
                            'currency' => $order->currency
                        ],
                        [
                            'balance' => 0,
                            'status' => 'active',
                            'is_active' => true
                        ]
                    );
                    
                    // Débiter le montant total du wallet pending
                    $sellerPendingWallet->decrement('balance', $totalAmount);
                    
                    // Créditer le montant du vendeur (après déductions) dans le wallet main
                    $sellerMainWallet->increment('balance', $sellerAmount);
                    
                    // Créditer la commission + transport dans le wallet enterprise
                    $platformAmount = $commissionAmount + $transportAmount;
                    $enterpriseWallet->increment('balance', $platformAmount);
                    
                    // Log pour traçabilité
                    Log::info("Distribution effectuée", [
                        'seller_id' => $seller->id,
                        'order_id' => $order->id,
                        'total_amount' => $totalAmount,
                        'seller_amount' => $sellerAmount,
                        'commission_amount' => $commissionAmount,
                        'transport_amount' => $transportAmount,
                        'platform_amount' => $platformAmount,
                        'currency' => $order->currency,
                        'pending_balance' => $sellerPendingWallet->balance,
                        'main_balance' => $sellerMainWallet->balance,
                        'enterprise_balance' => $enterpriseWallet->balance
                    ]);
                    
                    // Créer une transaction pour le vendeur
                    \App\Models\Transaction::create([
                        'transaction_id' => 'SELLER-' . strtoupper(\Illuminate\Support\Str::random(12)),
                        'user_id' => $seller->id,
                        'buyer_id' => $seller->id,
                        'wallet_id' => $sellerMainWallet->id,
                        'amount' => $sellerAmount,
                        'currency' => $order->currency,
                        'type' => 'deposit',
                        'status' => 'completed',
                        'payment_method' => 'wallet',
                        'purpose' => 'Vente confirmée - Commande #' . $order->id . ' (Montant net après commission ' . $commissionPercent . '% et transport ' . $transportPercent . '%)',
                        'provider' => 'Wallet Transfer',
                        'phone' => 'N/A',
                    ]);
                    
                    // Créer une transaction pour la commission plateforme
                    \App\Models\Transaction::create([
                        'transaction_id' => 'COMMISSION-' . strtoupper(\Illuminate\Support\Str::random(12)),
                        'user_id' => 1, // Admin/Plateforme (à adapter si vous avez un autre ID admin)
                        'buyer_id' => $order->buyer_id, // L'acheteur qui a payé
                        'wallet_id' => $enterpriseWallet->id,
                        'amount' => $commissionAmount,
                        'currency' => $order->currency,
                        'type' => 'deposit',
                        'status' => 'completed',
                        'payment_method' => 'wallet',
                        'purpose' => 'Commission plateforme (' . $commissionPercent . '%) - Commande #' . $order->id,
                        'provider' => 'Platform Commission',
                        'phone' => 'N/A',
                    ]);
                    
                    // Créer une transaction pour les frais de transport
                    \App\Models\Transaction::create([
                        'transaction_id' => 'TRANSPORT-' . strtoupper(\Illuminate\Support\Str::random(12)),
                        'user_id' => 1, // Admin/Plateforme (à adapter si vous avez un autre ID admin)
                        'buyer_id' => $order->buyer_id, // L'acheteur qui a payé
                        'wallet_id' => $enterpriseWallet->id,
                        'amount' => $transportAmount,
                        'currency' => $order->currency,
                        'type' => 'deposit',
                        'status' => 'completed',
                        'payment_method' => 'wallet',
                        'purpose' => 'Frais de transport (' . $transportPercent . '%) - Commande #' . $order->id,
                        'provider' => 'Transport Fee',
                        'phone' => 'N/A',
                    ]);
                } else {
                    Log::warning("Solde insuffisant dans le wallet pending pour la commande #{$order->id}");
                }
                
                // Créer une notification pour le vendeur avec détails de distribution
                $seller->notifications()->create([
                    'type' => 'order_delivered_confirmed',
                    'title' => 'Commande confirmée reçue - Paiement distribué',
                    'message' => Auth::user()->name . ' a confirmé avoir reçu la commande #' . $order->id . '. ' .
                                'Montant reçu: ' . number_format($sellerAmount, 2) . ' ' . $order->currency . ' ' .
                                '(Total: ' . number_format($totalAmount, 2) . ' - ' .
                                'Commission: ' . number_format($commissionAmount, 2) . ' - ' .
                                'Transport: ' . number_format($transportAmount, 2) . ')',
                    'action_url' => route('orders.show', $order->id),
                    'is_read' => false,
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Réception confirmée avec succès ! Merci pour votre retour.'
                ]);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', '✅ Réception confirmée avec succès ! Merci pour votre retour.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la confirmation de livraison: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Une erreur est survenue lors de la confirmation de livraison.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la confirmation de livraison.']);
        }
    }
    
    /**
     * Afficher la page de scan du QR code
     */
    public function scanOrder($token)
    {
        $order = Order::with(['item', 'buyer', 'seller', 'deliveryAddress'])
            ->where('scan_token', $token)
            ->firstOrFail();
        
        // Marquer comme scanné si c'est le premier scan
        if (!$order->scanned_at) {
            $order->update(['scanned_at' => now()]);
        }
        
        return view('orders.scan', compact('order'));
    }
    
    /**
     * Confirmer la réception de la commande via scan QR
     */
    public function confirmOrderDelivery(Request $request, $token)
    {
        try {
            $order = Order::where('scan_token', $token)->firstOrFail();
            
            // Vérifier que la commande peut être confirmée
            if ($order->confirmed_by_buyer_at) {
                return redirect()->route('orders.scan', $token)
                    ->with('info', 'Cette commande a déjà été confirmée le ' . $order->confirmed_by_buyer_at->format('d/m/Y à H:i'));
            }
            
            // Confirmer la réception
            $order->update([
                'confirmed_by_buyer_at' => now(),
                'buyer_confirmation_note' => $request->input('note', 'Confirmé via scan QR code'),
                'status' => 'completed'
            ]);
            
            // Distribuer les fonds (même logique que confirmDelivery)
            $this->distributeFunds($order);
            
            return redirect()->route('orders.scan', $token)
                ->with('success', 'Merci ! Votre réception a été confirmée avec succès. Les fonds ont été distribués.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la confirmation via QR scan', [
                'error' => $e->getMessage(),
                'token' => $token,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('orders.scan', $token)
                ->with('error', 'Une erreur est survenue lors de la confirmation: ' . $e->getMessage());
        }
    }
    
    /**
     * Distribuer les fonds après confirmation de réception
     */
    private function distributeFunds($order)
    {
        DB::beginTransaction();
        
        try {
            // Récupérer les portefeuilles
            $sellerWallet = \App\Models\Wallet::where('user_id', $order->seller_id)
                ->where('currency', $order->currency)
                ->first();
            
            if (!$sellerWallet) {
                Log::warning('Portefeuille vendeur non trouvé', [
                    'order_id' => $order->id,
                    'seller_id' => $order->seller_id,
                    'currency' => $order->currency,
                ]);
                DB::commit();
                return; // Pas de wallet, on continue quand même
            }
            
            // Calculer les montants (commission de 5%)
            $commission = $order->total_amount * 0.05;
            $sellerAmount = $order->total_amount - $commission;
            
            // Créditer le vendeur
            $sellerWallet->increment('balance', $sellerAmount);
            
            // Enregistrer la transaction pour le vendeur
            \App\Models\Transaction::create([
                'wallet_id' => $sellerWallet->id,
                'type' => 'vente',
                'amount' => $sellerAmount,
                'currency' => $order->currency,
                'status' => 'completed',
                'description' => 'Vente de ' . $order->item->name . ' (Commande #' . $order->order_number . ')',
                'order_id' => $order->id,
            ]);
            
            Log::info('Fonds distribués après confirmation', [
                'order_id' => $order->id,
                'seller_amount' => $sellerAmount,
                'commission' => $commission,
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la distribution des fonds', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
