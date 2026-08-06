<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Item;
use App\Models\Discount;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
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

        // Créer une notification pour le vendeur
        $this->notificationService->createMessageNotification(
            $user->id,
            $seller->id,
            $finalMessage
        );

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

        // Créer une notification pour l'acheteur
        $seller = Auth::user();
        Notification::create([
            'user_id' => $discount->user_id,
            'type' => 'discount_applied',
            'title' => 'Reduction accordee !',
            'message' => $seller->name . " vous accorde une reduction de {$discount->discount_percentage}% sur \"{$discount->item->name}\"",
            'data' => [
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'item_name' => $discount->item->name,
                'discount_percentage' => $discount->discount_percentage,
                'final_price' => $discount->final_price,
                'url' => '/messages/' . $seller->id,
            ],
        ]);

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

        // Créer une notification pour l'acheteur
        $seller = Auth::user();
        Notification::create([
            'user_id' => $discount->user_id,
            'type' => 'discount_offered',
            'title' => 'Reduction refusee',
            'message' => $seller->name . " n'a pas pu accorder de reduction sur \"{$discount->item->name}\"",
            'data' => [
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'item_name' => $discount->item->name,
                'url' => '/messages/' . $seller->id,
            ],
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
}