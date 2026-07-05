<?php

namespace App\Notifications;

use App\Models\Item;
use App\Services\FirebasePushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ItemModerated extends Notification implements ShouldQueue
{
    use Queueable;

    protected Item $item;
    protected string $action;
    protected ?string $reason;
    protected ?int $days;
    protected string $adminName;

    public function __construct(Item $item, string $action, ?string $reason, ?int $days, string $adminName)
    {
        $this->item = $item;
        $this->action = $action;
        $this->reason = $reason;
        $this->days = $days;
        $this->adminName = $adminName;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->action) {
            'blocked' => 'Votre article a été bloqué',
            'suspended' => 'Votre article a été suspendu',
            'unsuspended' => 'Votre article a été rétabli',
            default => 'Mise à jour de votre article',
        };

        $lines = match ($this->action) {
            'blocked' => [
                "Votre article \"{$this->item->name}\" a été bloqué par notre équipe de modération.",
                "Il n'est plus visible sur la plateforme.",
                $this->reason ? "Raison : {$this->reason}" : null,
            ],
            'suspended' => [
                "Votre article \"{$this->item->name}\" a été suspendu temporairement.",
                $this->reason ? "Raison : {$this->reason}" : null,
                $this->days
                    ? "Cette suspension prendra fin le " . now()->addDays($this->days)->format('d/m/Y') . "."
                    : "La suspension est pour une durée indéterminée.",
            ],
            'unsuspended' => [
                "Bonne nouvelle ! Votre article \"{$this->item->name}\" est de nouveau visible sur la plateforme.",
            ],
            default => ["Votre article \"{$this->item->name}\" a été mis à jour."],
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Information importante');

        foreach (array_filter($lines) as $line) {
            $mail->line($line);
        }

        return $mail
            ->action('Voir mon article', route('items.show', $this->item))
            ->line('Merci pour votre compréhension.');
    }

    public function toArray(object $notifiable): array
    {
        $titles = [
            'blocked' => 'Article bloqué',
            'suspended' => 'Article suspendu',
            'unsuspended' => 'Article rétabli',
        ];

        $messages = [
            'blocked' => "Votre article \"{$this->item->name}\" a été bloqué."
                . ($this->reason ? " Raison : {$this->reason}" : ''),
            'suspended' => "Votre article \"{$this->item->name}\" a été suspendu."
                . ($this->reason ? " Raison : {$this->reason}" : '')
                . ($this->days ? " Suspension jusqu'au " . now()->addDays($this->days)->format('d/m/Y') . "." : ''),
            'unsuspended' => "Votre article \"{$this->item->name}\" est de nouveau visible sur la plateforme.",
        ];

        $this->sendFCMNotification();

        return [
            'type' => 'item_moderated',
            'action' => $this->action,
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'item_image' => $this->item->images[0] ?? null,
            'reason' => $this->reason,
            'admin_name' => $this->adminName,
            'title' => $titles[$this->action] ?? 'Mise à jour de l\'article',
            'message' => $messages[$this->action] ?? "Votre article \"{$this->item->name}\" a été mis à jour.",
            'action_url' => route('items.show', $this->item),
        ];
    }

    protected function sendFCMNotification(): void
    {
        try {
            $user = $this->item->user;

            if (!$user || !$user->fcm_token) {
                return;
            }

            $fcmService = app(FirebasePushService::class);

            $titles = [
                'blocked' => 'Article bloqué',
                'suspended' => 'Article suspendu',
                'unsuspended' => 'Article rétabli',
            ];

            $bodies = [
                'blocked' => "Votre article \"{$this->item->name}\" a été bloqué."
                    . ($this->reason ? " Raison : {$this->reason}" : ''),
                'suspended' => "Votre article \"{$this->item->name}\" a été suspendu."
                    . ($this->reason ? " Raison : {$this->reason}" : ''),
                'unsuspended' => "Votre article \"{$this->item->name}\" est de nouveau visible.",
            ];

            $fcmService->sendNotification(
                $user->fcm_token,
                $titles[$this->action] ?? 'Mise à jour de votre article',
                $bodies[$this->action] ?? '',
                [
                    'type' => 'item_moderated',
                    'action' => $this->action,
                    'item_id' => (string) $this->item->id,
                    'item_name' => $this->item->name,
                    'reason' => $this->reason ?? '',
                    'url' => route('items.show', $this->item),
                ],
                $this->item->images[0] ?? null
                    ? asset('storage/' . $this->item->images[0])
                    : null
            );

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification FCM (modération)', [
                'error' => $e->getMessage(),
                'item_id' => $this->item->id,
                'action' => $this->action,
            ]);
        }
    }
}
