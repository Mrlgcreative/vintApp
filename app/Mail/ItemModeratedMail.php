<?php

namespace App\Mail;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ItemModeratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Item $item;
    public string $action;
    public ?string $reason;
    public ?int $days;

    /**
     * Create a new message instance.
     */
    public function __construct(Item $item, string $action, ?string $reason, ?int $days)
    {
        $this->item = $item;
        $this->action = $action;
        $this->reason = $reason;
        $this->days = $days;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->action) {
            'approved' => 'Votre article a été approuvé',
            'rejected' => 'Votre article a été rejeté',
            'blocked' => 'Votre article a été bloqué',
            'suspended' => 'Votre article a été suspendu',
            'unsuspended' => 'Votre article a été rétabli',
            default => 'Mise à jour de votre article',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.item-moderated',
            with: [
                'item' => $this->item,
                'action' => $this->action,
                'reason' => $this->reason,
                'days' => $this->days,
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
