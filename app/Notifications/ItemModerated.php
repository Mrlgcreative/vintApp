<?php

namespace App\Notifications;

use App\Mail\ItemModeratedMail;
use App\Models\Item;
use App\Services\FirebasePushService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ItemModerated extends Notification
{
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
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $titles = [
            'approved' => 'Article approuvé',
            'rejected' => 'Article rejeté',
            'blocked' => 'Article bloqué',
            'suspended' => 'Article suspendu',
            'unsuspended' => 'Article rétabli',
        ];

        $messages = [
            'approved' => "Votre article \"{$this->item->name}\" a été approuvé et publié.",
            'rejected' => "Votre article \"{$this->item->name}\" a été rejeté."
                . ($this->reason ? " Raison : {$this->reason}" : ''),
            'blocked' => "Votre article \"{$this->item->name}\" a été bloqué."
                . ($this->reason ? " Raison : {$this->reason}" : ''),
            'suspended' => "Votre article \"{$this->item->name}\" a été suspendu."
                . ($this->reason ? " Raison : {$this->reason}" : '')
                . ($this->days ? " Suspension jusqu'au " . now()->addDays($this->days)->format('d/m/Y') . "." : ''),
            'unsuspended' => "Votre article \"{$this->item->name}\" est de nouveau visible sur la plateforme.",
        ];

        $this->sendFCMNotification();
        $this->sendMailNotification();

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
                'approved' => 'Article approuvé',
                'rejected' => 'Article rejeté',
                'blocked' => 'Article bloqué',
                'suspended' => 'Article suspendu',
                'unsuspended' => 'Article rétabli',
            ];

            $bodies = [
                'approved' => "Votre article \"{$this->item->name}\" a été approuvé et publié.",
                'rejected' => "Votre article \"{$this->item->name}\" a été rejeté."
                    . ($this->reason ? " Raison : {$this->reason}" : ''),
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

    /**
     * Email envoyé en arrière-plan (file d'attente) : ne bloque jamais l'action
     * et ne casse rien si aucun worker ne tourne ou si le SMTP échoue.
     */
    protected function sendMailNotification(): void
    {
        try {
            $user = $this->item->user;

            if (!$user || empty($user->email)) {
                return;
            }

            Mail::queue((new ItemModeratedMail($this->item, $this->action, $this->reason, $this->days))
                ->to($user->email));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email modération', [
                'error' => $e->getMessage(),
                'item_id' => $this->item->id,
                'action' => $this->action,
            ]);
        }
    }
}
