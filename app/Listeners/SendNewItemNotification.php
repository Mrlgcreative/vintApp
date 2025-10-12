<?php

namespace App\Listeners;

use App\Events\ItemCreated;
use App\Mail\NewItemNotification;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendNewItemNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ItemCreated $event): void
    {
        // Récupérer tous les abonnés actifs qui veulent recevoir les notifications de nouveaux articles
        $subscribers = NewsletterSubscriber::receivingNewItems()->get();

        foreach ($subscribers as $subscriber) {
            // Envoyer l'email
            Mail::to($subscriber->email)->send(new NewItemNotification($event->item, $subscriber));
            
            // Incrémenter le compteur
            $subscriber->incrementEmailsSent();
        }
    }
}
