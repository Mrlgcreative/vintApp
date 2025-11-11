<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use App\Models\Refund;

class RefundApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $refund;

    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $isForBuyer = $notifiable->id === $this->refund->buyer_id;
        $subject = $isForBuyer ? 'Votre demande de remboursement a été approuvée' : 'Remboursement approuvé pour votre article';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($isForBuyer ? 'Bonne nouvelle !' : 'Information importante')
            ->line($isForBuyer 
                ? "Votre demande de remboursement pour la commande #{$this->refund->order->order_number} a été approuvée par notre équipe."
                : "Un remboursement a été approuvé pour votre article \"{$this->refund->order->item->name}\" (commande #{$this->refund->order->order_number})."
            )
            ->line("Montant du remboursement : {$this->refund->formatted_refund_amount}")
            ->line($isForBuyer 
                ? "Le remboursement sera traité sous 3-5 jours ouvrables."
                : "Le montant sera déduit de vos prochaines ventes ou vous serez contacté si nécessaire."
            )
            ->action('Voir les détails', route($isForBuyer ? 'orders.show' : 'orders.show', $this->refund->order))
            ->line($isForBuyer ? 'Merci pour votre confiance !' : 'Merci pour votre compréhension.');
    }

    public function toDatabase($notifiable)
    {
        $isForBuyer = $notifiable->id === $this->refund->buyer_id;

        return [
            'type' => 'refund_approved',
            'title' => $isForBuyer ? 'Remboursement approuvé' : 'Remboursement approuvé pour votre article',
            'message' => $isForBuyer 
                ? "Votre demande de remboursement de {$this->refund->formatted_refund_amount} pour la commande #{$this->refund->order->order_number} a été approuvée."
                : "Un remboursement de {$this->refund->formatted_refund_amount} a été approuvé pour votre article \"{$this->refund->order->item->name}\".",
            'refund_id' => $this->refund->id,
            'order_id' => $this->refund->order_id,
            'amount' => $this->refund->refund_amount,
            'currency' => $this->refund->currency,
            'action_url' => route('orders.show', $this->refund->order),
        ];
    }
}