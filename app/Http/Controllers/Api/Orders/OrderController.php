<?php

namespace App\Http\Controllers\Api\Orders;

use App\Events\OrderNotification;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\MonitoringService;
use App\Services\OrderService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends ApiController
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * API: Liste des commandes de l'utilisateur
     */
    public function index(Request $request): JsonResponse
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
    public function store(CreateOrderRequest $request): JsonResponse
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

        } catch (DomainException $e) {
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
    public function show($id): JsonResponse
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
    public function mySales(Request $request): JsonResponse
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
     * API: Confirmer le paiement (acheteur)
     */
    public function confirmPayment($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->buyer_id !== Auth::id()) {
                return $this->errorResponse('Vous n\'êtes pas autorisé à confirmer le paiement de cette commande.', 403);
            }

            $this->orderService->confirmPayment($order);

            // 🔔 Notification au vendeur (paiement confirmé)
            broadcast(new OrderNotification(
                $order,
                'payment_confirmed',
                "💰 Paiement confirmé pour la commande #{$order->order_number}",
                $order->item->user_id
            ))->toOthers();

            return $this->successResponse($order->fresh(), 'Paiement confirmé avec succès');
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la confirmation du paiement API: ' . $e->getMessage());
            return $this->errorResponse('Une erreur est survenue lors de la confirmation du paiement.', 500);
        }
    }

    /**
     * API: Marquer comme expédié (vendeur)
     */
    public function markAsShipped($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->item->user_id !== Auth::id()) {
                return $this->errorResponse('Vous n\'êtes pas autorisé à modifier cette commande.', 403);
            }

            $this->orderService->markShipped($order);

            // 🔔 Notification à l'acheteur (commande expédiée)
            broadcast(new OrderNotification(
                $order,
                'order_shipped',
                "📦 Votre commande #{$order->order_number} a été expédiée",
                $order->buyer_id
            ))->toOthers();

            return $this->successResponse($order->fresh(), 'Commande marquée comme expédiée');
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage expédié API: ' . $e->getMessage());
            return $this->errorResponse('Une erreur est survenue.', 500);
        }
    }

    /**
     * API: Marquer comme livré (vendeur)
     */
    public function markAsDelivered($id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->item->user_id !== Auth::id()) {
                return $this->errorResponse('Vous n\'êtes pas autorisé à modifier cette commande.', 403);
            }

            $this->orderService->markDelivered($order);

            // 🔔 Notification à l'acheteur (commande livrée)
            broadcast(new OrderNotification(
                $order,
                'order_delivered',
                "🚚 Votre commande #{$order->order_number} a été livrée",
                $order->buyer_id
            ))->toOthers();

            return $this->successResponse($order->fresh(), 'Commande marquée comme livrée');
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage livré API: ' . $e->getMessage());
            return $this->errorResponse('Une erreur est survenue.', 500);
        }
    }

    /**
     * API: Confirmer la livraison (acheteur)
     */
    public function confirmDelivery(Request $request, $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->buyer_id !== Auth::id()) {
                return $this->errorResponse('Vous n\'êtes pas autorisé à confirmer la réception de cette commande.', 403);
            }

            $this->orderService->confirmDelivery($order, $request->input('note'));

            return $this->successResponse($order->fresh(), 'Réception confirmée avec succès ! Merci pour votre retour.');
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la confirmation de livraison API: ' . $e->getMessage());
            return $this->errorResponse('Une erreur est survenue lors de la confirmation de livraison.', 500);
        }
    }

    /**
     * API: Annuler une commande (acheteur, uniquement en attente de paiement)
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            // En API Sanctum, Auth::id() (garde par défaut 'web') peut être
            // null : on privilégie $request->user()->id.
            $userId = $request->user()?->id ?? Auth::id();

            if ($order->buyer_id !== $userId) {
                return $this->errorResponse('Vous n\'êtes pas autorisé à annuler cette commande.', 403);
            }

            $this->orderService->cancel($order);

            return $this->successResponse(null, 'Commande annulée avec succès');
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation de commande API: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse('Une erreur est survenue lors de l\'annulation de la commande.', 500);
        }
    }
}
