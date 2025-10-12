<?php

namespace App\Mail;

use App\Models\Item;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewItemNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $subscriber;

    /**
     * Create a new message instance.
     */
    public function __construct(Item $item, NewsletterSubscriber $subscriber)
    {
        $this->item = $item;
        $this->subscriber = $subscriber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🆕 Nouvel article : ' . $this->item->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.new-item',
            with: [
                'item' => $this->item,
                'subscriber' => $this->subscriber,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
