<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonitoringAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    protected array $anomaly;

    /**
     * Créer une nouvelle instance d'email d'alerte.
     */
    public function __construct(array $anomaly)
    {
        $this->anomaly = $anomaly;
    }

    /**
     * Obtenir l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        $severityLabel = match ($this->anomaly['severity']) {
            'critical' => 'CRITIQUE',
            'warning'  => 'AVERTISSEMENT',
            default    => 'INFO',
        };

        $label = $this->anomaly['label'] ?? 'Anomalie';

        return new Envelope(
            subject: "[VintApp] Alerte monitoring ({$severityLabel}) - {$label}",
        );
    }

    /**
     * Définition du contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.monitoring-alert',
            with: [
                'anomaly' => $this->anomaly,
            ],
        );
    }

    /**
     * Pièces jointes.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
