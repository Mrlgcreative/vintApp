<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Item;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    /**
     * Afficher les demandes de réduction pour un vendeur
     */
    public function index(Request $request)
    {
        $query = Discount::with(['item', 'user', 'message'])
            ->fromSeller(Auth::id())
            ->orderBy('created_at', 'desc');

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrer par période
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Rechercher par nom de produit
        if ($request->filled('search')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $discounts = $query->paginate(10);

        return view('discounts.index', compact('discounts'));
    }

    /**
     * Envoyer un message automatique avec demande de réduction
     */
    public function contactSeller(Request $request, Item $item)
    {
        $request->validate([
            'custom_message' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $seller = $item->user;

        // Vérifier que l'utilisateur n'est pas le vendeur
        if ($user->id === $seller->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous contacter vous-même.');
        }

        // Créer le message automatique
        $defaultMessage = "Bonjour,\n\nJe suis intéressé(e) par votre produit \"{$item->name}\" au prix de {$item->formatted_price}.\n\nSerait-il possible d'obtenir une réduction sur ce produit ?\n\nMerci pour votre réponse.";
        
        $customMessage = $request->input('custom_message');
        $finalMessage = $customMessage ? $customMessage : $defaultMessage;

        // Créer le message
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
            'item_id' => $item->id,
            'subject' => "Demande de réduction - {$item->name}",
            'content' => $finalMessage,
            'type' => 'item_inquiry',
            'attachment' => $item->images ? $item->images[0] : null // Première image du produit
        ]);

        // Créer une demande de réduction automatique
        $discount = Discount::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'seller_id' => $seller->id,
            'message_id' => $message->id,
            'original_price' => $item->price,
            'final_price' => $item->price, // Sera mis à jour par le vendeur
            'status' => 'pending',
            'expires_at' => now()->addDays(7), // Expire dans 7 jours
            'reason' => 'Demande automatique de réduction'
        ]);

        // Rediriger vers la conversation avec le vendeur
        return redirect()->route('messages.show', $seller->id)
            ->with('success', 'Votre demande de réduction a été envoyée avec succès !')
            ->with('item_id', $item->id);
    }

    /**
     * Proposer une réduction (pour les vendeurs)
     */
    public function proposeDiscount(Request $request, Discount $discount)
    {
        $request->validate([
            'discount_percentage' => 'required|numeric|min:1|max:99',
            'expires_in_days' => 'required|integer|min:1|max:30',
            'response_message' => 'nullable|string|max:1000'
        ]);

        // Vérifier que l'utilisateur est bien le vendeur
        if (Auth::id() !== $discount->seller_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette réduction.');
        }

        // Mettre à jour la réduction
        $discount->discount_percentage = $request->discount_percentage;
        $discount->calculateFinalPrice();
        $discount->status = 'approved';
        $discount->expires_at = now()->addDays($request->expires_in_days);
        $discount->save();

        // Envoyer un message de réponse
        if ($request->response_message) {
            Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $discount->user_id,
                'item_id' => $discount->item_id,
                'subject' => "Réduction approuvée - {$discount->item->name}",
                'content' => $request->response_message . "\n\nRéduction accordée : {$discount->discount_percentage}% (Prix final : " . number_format((float) $discount->final_price, 0, ',', ' ') . " FCFA)\nValable jusqu'au " . $discount->expires_at->format('d/m/Y'),
                'type' => 'item_inquiry'
            ]);
        }

        return redirect()->back()->with('success', 'Réduction proposée avec succès !');
    }

    /**
     * Rejeter une demande de réduction
     */
    public function rejectDiscount(Request $request, Discount $discount)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        // Vérifier que l'utilisateur est bien le vendeur
        if (Auth::id() !== $discount->seller_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette réduction.');
        }

        $discount->update([
            'status' => 'rejected',
            'reason' => $request->rejection_reason ?: 'Demande de réduction refusée'
        ]);

        // Envoyer un message de refus
        $rejectionMessage = $request->rejection_reason ?: 'Je ne peux pas accorder de réduction sur ce produit pour le moment.';
        
        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $discount->user_id,
            'item_id' => $discount->item_id,
            'subject' => "Réduction refusée - {$discount->item->name}",
            'content' => $rejectionMessage,
            'type' => 'item_inquiry'
        ]);

        return redirect()->back()->with('success', 'Demande de réduction refusée.');
    }

    /**
     * Appliquer une réduction lors de l'ajout au panier
     */
    public function applyDiscount(Request $request, Discount $discount)
    {
        // Vérifier que l'utilisateur est bien le bénéficiaire
        if (Auth::id() !== $discount->user_id) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à utiliser cette réduction.'], 403);
        }

        // Vérifier que la réduction est valide
        if (!$discount->isValid()) {
            return response()->json(['error' => 'Cette réduction n\'est plus valide.'], 400);
        }

        // Marquer la réduction comme utilisée
        $discount->apply();

        return response()->json([
            'success' => true,
            'final_price' => $discount->final_price,
            'discount_amount' => $discount->discount_amount,
            'message' => 'Réduction appliquée avec succès !'
        ]);
    }

    /**
     * Afficher une demande de réduction
     */
    public function show(Discount $discount)
    {
        // Vérifier que l'utilisateur est soit le vendeur soit l'acheteur
        if (Auth::id() !== $discount->seller_id && Auth::id() !== $discount->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette demande.');
        }

        return view('discounts.show', compact('discount'));
    }

    /**
     * Obtenir les réductions disponibles pour un produit et un utilisateur
     */
    public function getAvailableDiscounts(Item $item)
    {
        $discounts = Discount::valid()
            ->forUser(Auth::id())
            ->where('item_id', $item->id)
            ->get();

        return response()->json($discounts);
    }
}