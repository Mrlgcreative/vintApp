<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Api\ApiController;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends ApiController
{
    /**
     * Clé session stable pour l'API mobile (pas de session web côté natif).
     */
    protected function cartSessionKey(Request $request): string
    {
        return 'api-' . Auth::id();
    }

    /**
     * API: Contenu du panier de l'utilisateur
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cart = $this->getCartArray($request);

            return $this->successResponse($cart, 'Panier récupéré avec succès');
        } catch (\Exception $e) {
            Log::error('API Cart index error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération du panier', 500);
        }
    }

    /**
     * API: Résumé du panier (sous-total, frais transport, total)
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $cart = $this->getCartArray($request);

            $subtotal = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $transportFeePercentage = (float) (DB::table('settings')
                ->where('key', 'transport_fee_percentage')
                ->value('value') ?? 5);

            $transportFee = round(($subtotal * $transportFeePercentage) / 100, 2);
            $total = round($subtotal + $transportFee, 2);

            $currency = !empty($cart) ? reset($cart)['currency'] ?? 'CDF' : 'CDF';

            return $this->successResponse([
                'items_count' => count($cart),
                'items_quantity' => collect($cart)->sum('quantity'),
                'subtotal' => $subtotal,
                'transport_fee_percentage' => $transportFeePercentage,
                'transport_fee' => $transportFee,
                'total' => $total,
                'currency' => $currency,
            ], 'Résumé du panier');
        } catch (\Exception $e) {
            Log::error('API Cart summary error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du calcul du panier', 500);
        }
    }

    /**
     * API: Ajouter un article au panier
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $item = Item::findOrFail($request->item_id);
            $quantity = (int) $request->quantity;

            if ($item->user_id === Auth::id()) {
                return $this->errorResponse('Vous ne pouvez pas ajouter votre propre article au panier.', 400);
            }

            if ($item->status !== 'active') {
                return $this->errorResponse("Cet article n'est plus disponible.", 400);
            }

            $finalPrice = $item->price;
            $offer = null;
            $activeDiscount = null;

            if ($item->has_offer) {
                $offer = $item->offer;
                $finalPrice = $offer->discountPriceFor($item);
            }

            $activeDiscount = Discount::where('item_id', $item->id)
                ->where('user_id', Auth::id())
                ->where('status', 'approved')
                ->where('expires_at', '>', now())
                ->first();

            if ($activeDiscount) {
                // La réduction négociée (acheteur) prime sur l'offre de la boutique.
                $finalPrice = $activeDiscount->final_price;
            }

            $sessionId = $this->cartSessionKey($request);

            $cartRow = Cart::where('session_id', $sessionId)
                ->where('item_id', $item->id)
                ->first();

            $newQuantity = ($cartRow ? $cartRow->quantity : 0) + $quantity;
            if ($newQuantity > $item->quantity) {
                return $this->errorResponse('La quantité demandée dépasse le stock disponible.', 400);
            }

            if ($cartRow) {
                $cartRow->update(['quantity' => $newQuantity]);

                if ($activeDiscount) {
                    $cartRow->update([
                        'price' => $finalPrice,
                        'original_price' => $item->price,
                        'discount_id' => $activeDiscount->id,
                        'discount_percentage' => $activeDiscount->discount_percentage,
                        'has_discount' => true,
                    ]);
                } elseif ($offer) {
                    $cartRow->update([
                        'price' => $finalPrice,
                        'original_price' => $item->price,
                        'discount_id' => $offer->id,
                        'discount_percentage' => $offer->type === 'percent' ? $offer->value : null,
                        'has_discount' => true,
                    ]);
                }
            } else {
                $data = [
                    'session_id' => $sessionId,
                    'user_id' => Auth::id(),
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
                } elseif ($offer) {
                    $data['original_price'] = $item->price;
                    $data['discount_id'] = $offer->id;
                    $data['discount_percentage'] = $offer->type === 'percent' ? $offer->value : null;
                    $data['has_discount'] = true;
                }

                Cart::create($data);
            }

            return $this->successResponse($this->getCartArray($request), 'Article ajouté au panier avec succès', 201);
        } catch (\Exception $e) {
            Log::error('API Cart add error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'ajout au panier', 500);
        }
    }

    /**
     * API: Mettre à jour la quantité d'un article du panier
     */
    public function update(Request $request, $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $sessionId = $this->cartSessionKey($request);

            $cartRow = Cart::where('session_id', $sessionId)
                ->where('item_id', $itemId)
                ->first();

            if (!$cartRow) {
                return $this->errorResponse('Article introuvable dans le panier.', 404);
            }

            $item = $cartRow->item;
            if ($item && (int) $request->quantity > $item->quantity) {
                return $this->errorResponse('La quantité demandée dépasse le stock disponible.', 400);
            }

            $cartRow->update(['quantity' => (int) $request->quantity]);

            return $this->successResponse($this->getCartArray($request), 'Quantité mise à jour');
        } catch (\Exception $e) {
            Log::error('API Cart update error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour du panier', 500);
        }
    }

    /**
     * API: Retirer un article du panier
     */
    public function remove($itemId): JsonResponse
    {
        try {
            $sessionId = $this->cartSessionKey(request());

            Cart::where('session_id', $sessionId)
                ->where('item_id', $itemId)
                ->delete();

            return $this->successResponse($this->getCartArray(request()), 'Article retiré du panier');
        } catch (\Exception $e) {
            Log::error('API Cart remove error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du retrait du panier', 500);
        }
    }

    /**
     * API: Vider le panier
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $sessionId = $this->cartSessionKey($request);

            Cart::where('session_id', $sessionId)->delete();

            return $this->successResponse([], 'Panier vidé avec succès');
        } catch (\Exception $e) {
            Log::error('API Cart clear error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du vidage du panier', 500);
        }
    }

    /**
     * Récupère le panier de l'utilisateur sous forme de tableau.
     */
    protected function getCartArray(Request $request): array
    {
        $sessionId = $this->cartSessionKey($request);

        $cartRows = Cart::where('session_id', $sessionId)
            ->orWhere('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $cart = [];
        foreach ($cartRows as $row) {
            $item = [
                'item_id' => $row->item_id,
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
            $cart[] = $item;
        }

        return $cart;
    }
}
