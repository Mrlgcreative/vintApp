<?php

namespace App\Mail;

use App\Models\UserWaiting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userWaiting;
    public $setupUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(UserWaiting $userWaiting, string $setupUrl)
    {
        $this->userWaiting = $userWaiting;
        $this->setupUrl = $setupUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Votre compte VintApp est prêt ! Définissez votre mot de passe',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.set-password',
            with: [
                'name' => $this->userWaiting->name,
                'setupUrl' => $this->setupUrl,
                'expiresAt' => $this->userWaiting->password_setup_token_expires_at,
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
