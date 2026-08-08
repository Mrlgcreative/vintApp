<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use App\Events\OrderNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MonitoringService;
use App\Services\OrderService;
use App\Http\Requests\CreateOrderRequest;
use App\Traits\ApiResponses;

class OrderController extends Controller
{
    use ApiResponses;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
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
    public function store(CreateOrderRequest $request)
    {
        $startTime = microtime(true);
        $monitoring = app(MonitoringService::class);

        // Validation déjà effectuée par CreateOrderRequest
        $validated = $request->validated();

        try {
            $order = $this->orderService->create($request->all(), Auth::user());

            // Enregistrer la métrique business
            $monitoring->recordBusinessMetric('order_created', $order->total_amount, [
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'item_id' => $order->item_id,
                'currency' => $order->currency,
            ]);

            // Enregistrer la performance
            $duration = microtime(true) - $startTime;
            $monitoring->recordPerformance('order.store', $duration, [
                'buyer_id' => $order->buyer_id,
                'total_amount' => $order->total_amount,
            ]);

            // 🔔 Envoyer notification au vendeur (nouvelle commande)
            broadcast(new OrderNotification(
                $order,
                'new_order',
                "🛒 Nouvelle commande de {$order->buyer->name}",
                $order->seller_id // ID du vendeur
            ))->toOthers();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande créée avec succès !');

        } catch (\DomainException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            // Enregistrer l'erreur
            $monitoring->recordError($e, [
                'action' => 'order.store',
                'user_id' => Auth::id(),
                'item_id' => $request->item_id,
            ]);
            
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

        try {
            $this->orderService->cancel($order);

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Commande annulée avec succès !']);
            }

            return redirect()->route('orders.index')
                ->with('success', 'Commande annulée avec succès !');

        } catch (\DomainException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
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

        try {
            $this->orderService->confirmPayment($order);

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

        } catch (\DomainException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
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

        try {
            $this->orderService->markShipped($order);

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

        } catch (\DomainException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
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

        try {
            $this->orderService->markDelivered($order);

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

        } catch (\DomainException $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
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

        try {
            $this->orderService->confirmDelivery($order, $request->input('note'));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Réception confirmée avec succès ! Merci pour votre retour.'
                ]);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', '✅ Réception confirmée avec succès ! Merci pour votre retour.');

        } catch (\DomainException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'error' => $e->getMessage()
                ], 400);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
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
            
            // Confirmer la réception et distribuer les fonds
            $this->orderService->confirmDelivery($order, $request->input('note', 'Confirmé via scan QR code'));
            
            return redirect()->route('orders.scan', $token)
                ->with('success', 'Merci ! Votre réception a été confirmée avec succès. Les fonds ont été distribués.');
                
        } catch (\DomainException $e) {
            return redirect()->route('orders.scan', $token)
                ->with('error', $e->getMessage());
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

    // ==================== API METHODS ====================

    /**
     * API: Liste des commandes de l'utilisateur
     */
    public function apiIndex(Request $request)
    {
        $orders = Order::with(['item', 'buyer', 'deliveryAddress'])
            ->where('buyer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($orders, 'Commandes récupérées avec succès');
    }

    /**
     * API: Créer une commande
     */
    public function apiStore(CreateOrderRequest $request)
    {
        $startTime = microtime(true);
        $monitoring = app(MonitoringService::class);
        
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->errorResponse('Utilisateur non authentifié', 401);
            }
            
            $order = $this->orderService->create($request->all(), $user);

            // Charger les relations pour la réponse
            $order->load(['item', 'seller', 'buyer', 'deliveryAddress']);

            // Enregistrer la métrique business
            $monitoring->recordBusinessMetric('order_created', $order->total_amount, [
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'item_id' => $order->item_id,
                'currency' => $order->currency,
            ]);

            // Enregistrer la performance
            $duration = microtime(true) - $startTime;
            $monitoring->recordPerformance('order.store', $duration, [
                'buyer_id' => $order->buyer_id,
                'total_amount' => $order->total_amount,
            ]);

            // 🔔 Envoyer notification au vendeur (nouvelle commande)
            try {
                broadcast(new OrderNotification(
                    $order,
                    'new_order',
                    "🛒 Nouvelle commande de {$order->buyer->name}",
                    $order->seller_id
                ))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Notification broadcast failed: ' . $e->getMessage());
            }

            Log::info('Commande API créée avec succès', [
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'item_id' => $order->item_id,
            ]);

            return $this->successResponse($order, 'Commande créée avec succès', 201);

        } catch (\DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la commande API: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'item_id' => $request->item_id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->errorResponse('Une erreur est survenue lors de la création de la commande: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Détails d'une commande
     */
    public function apiShow($id)
    {
        $order = Order::with(['item', 'buyer', 'deliveryAddress'])->findOrFail($id);
        
        if ($order->buyer_id !== Auth::id() && $order->item->user_id !== Auth::id()) {
            return $this->errorResponse('Non autorisé', 403);
        }

        return $this->successResponse($order, 'Commande récupérée avec succès');
    }

    /**
     * API: Mes ventes
     */
    public function apiMySales(Request $request)
    {
        $orders = Order::with(['item', 'buyer', 'deliveryAddress'])
            ->whereHas('item', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($orders, 'Ventes récupérées avec succès');
    }

    /**
     * API: Confirmer le paiement
     */
    public function apiConfirmPayment($id)
    {
        try {
            $order = Order::findOrFail($id);
            $response = $this->confirmPayment($order);
            if ($response->getStatusCode() >= 400) {
                return $response;
            }
            return $this->successResponse($order->fresh(), 'Paiement confirmé avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * API: Marquer comme expédié
     */
    public function apiMarkAsShipped($id)
    {
        try {
            $order = Order::findOrFail($id);
            $response = $this->markAsShipped($order);
            if ($response->getStatusCode() >= 400) {
                return $response;
            }
            return $this->successResponse($order->fresh(), 'Commande marquée comme expédiée');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * API: Marquer comme livré
     */
    public function apiMarkAsDelivered($id)
    {
        try {
            $order = Order::findOrFail($id);
            $response = $this->markAsDelivered($order);
            if ($response->getStatusCode() >= 400) {
                return $response;
            }
            return $this->successResponse($order->fresh(), 'Commande marquée comme livrée');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * API: Confirmer la livraison (acheteur)
     */
    public function apiConfirmDelivery(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $response = $this->confirmDelivery($request, $order);
            if ($response->getStatusCode() >= 400) {
                return $response;
            }
            return $this->successResponse($order->fresh(), 'Livraison confirmée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
