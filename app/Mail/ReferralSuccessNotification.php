<?php

namespace App\Mail;

use App\Models\User;
use App\Models\ReferralCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralSuccessNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $referrer;
    public $newUser;
    public $referralCode;
    public $pointsEarned;
    public $stats;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $referrer, 
        User $newUser, 
        ReferralCode $referralCode, 
        int $pointsEarned,
        array $stats
    ) {
        $this->referrer = $referrer;
        $this->newUser = $newUser;
        $this->referralCode = $referralCode;
        $this->pointsEarned = $pointsEarned;
        $this->stats = $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Nouveau parrainage réussi ! +' . $this->pointsEarned . ' points - VintApp',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.referral-success',
            with: [
                'referrer' => $this->referrer,
                'newUser' => $this->newUser,
                'referralCode' => $this->referralCode,
                'pointsEarned' => $this->pointsEarned,
                'stats' => $this->stats,
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