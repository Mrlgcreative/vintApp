<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Assistant conversationnel VintApp, propulsé par l'API xkiro
 * (endpoint /v1/chat/completions, compatible OpenAI).
 */
class AiAssistantService
{
    /** Nombre de messages d'historique renvoyés au modèle. */
    private const MAX_HISTORY = 10;

    /**
     * Source unique des actions proposables : clé d'action → nom de route.
     *
     * Ajouter une action ici suffit : l'enum du modèle, la validation des
     * réponses et les boutons du widget en découlent automatiquement.
     */
    private const ACTION_ROUTES = [
        'edit_profile' => 'profile.edit',
        'security' => 'profile.security',
        'support' => 'support.create',
    ];

    /**
     * Clés d'action valides (dérivées de ACTION_ROUTES).
     *
     * @return array<int, string>
     */
    public static function actions(): array
    {
        return array_keys(self::ACTION_ROUTES);
    }

    /**
     * Destinations des actions, résolues depuis ACTION_ROUTES.
     * Consommé par le widget (voir resources/views/partials/ai-assistant.blade.php).
     *
     * @return array<string, string>
     */
    public static function actionUrls(): array
    {
        $urls = [];

        foreach (self::ACTION_ROUTES as $action => $routeName) {
            $urls[$action] = route($routeName);
        }

        return $urls;
    }

    /**
     * Envoie une conversation au modèle et renvoie la réponse + les actions proposées.
     *
     * @param  array<int, array{role: string, content: string}>  $history
     * @param  User|null  $user  Utilisateur connecté (contexte limité à ses propres données)
     * @return array{answer: string, actions: array<int, array{label: string, action: string}>}
     */
    public function ask(string $message, array $history = [], ?User $user = null): array
    {
        $apiKey = config('services.xkiro.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('Assistant IA non configuré (XKIRO_API_KEY manquant).');
        }

        $messages = [['role' => 'system', 'content' => $this->systemPrompt($user)]];

        foreach (array_slice($history, -self::MAX_HISTORY) as $entry) {
            $content = trim((string) ($entry['content'] ?? ''));
            if ($content !== '') {
                $messages[] = [
                    'role' => ($entry['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
                    'content' => $content,
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('services.xkiro.timeout', 30))
                ->acceptJson()
                ->retry(2, 200)
                ->post(rtrim(config('services.xkiro.base_url'), '/') . '/chat/completions', [
                    'model' => config('services.xkiro.model'),
                    'messages' => $messages,
                    'max_tokens' => 600,
                    'tools' => $this->tools(),
                    'tool_choice' => 'auto',
                ]);
        } catch (\Throwable $e) {
            // DNS / timeout / TLS ou erreur serveur xkiro après relance.
            Log::error('Assistant IA: appel xkiro impossible', ['error' => $e->getMessage()]);

            throw new RuntimeException("Le service d'assistance est momentanément indisponible.");
        }

        if (! $response->successful()) {
            Log::error('Assistant IA: appel xkiro en échec', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException("Le service d'assistance est momentanément indisponible.");
        }

        $modelMessage = $response->json('choices.0.message') ?? [];
        $answer = trim((string) ($modelMessage['content'] ?? ''));
        $actions = $this->extractActions($modelMessage['tool_calls'] ?? []);

        if ($answer === '' && $actions !== []) {
            $answer = 'Voici ce que je vous propose :';
        }

        return ['answer' => $answer, 'actions' => $actions];
    }

    /**
     * Déclaration de la fonction propose_actions(label, action) exposée au modèle.
     */
    private function tools(): array
    {
        return [[
            'type' => 'function',
            'function' => [
                'name' => 'propose_actions',
                'description' => "Propose à l'utilisateur une action concrète à effectuer dans l'application. Appelle cette fonction une fois par action ; accompagne toujours les appels d'une phrase d'explication.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'label' => [
                            'type' => 'string',
                            'description' => "Texte court du bouton, à l'impératif et en français (ex : « Ajouter un numéro de téléphone »).",
                        ],
                        'action' => [
                            'type' => 'string',
                            'description' => "Destination : edit_profile (modifier/compléter le profil : téléphone, adresse, ville, photo, bio), security (sécurité du compte : 2FA, vérification email), support (contacter le support humain).",
                            'enum' => self::actions(),
                        ],
                    ],
                    'required' => ['label', 'action'],
                    'additionalProperties' => false,
                ],
            ],
        ]];
    }

    /**
     * Extrait les actions valides des tool_calls renvoyés par le modèle.
     *
     * @param  array<int, array<string, mixed>>  $toolCalls
     * @return array<int, array{label: string, action: string}>
     */
    private function extractActions(array $toolCalls): array
    {
        $actions = [];

        foreach ($toolCalls as $call) {
            if (($call['function']['name'] ?? '') !== 'propose_actions') {
                continue;
            }

            $args = json_decode((string) ($call['function']['arguments'] ?? ''), true);
            $label = trim((string) ($args['label'] ?? ''));
            $action = (string) ($args['action'] ?? '');

            if ($label !== '' && in_array($action, self::actions(), true)) {
                $actions[] = ['label' => $label, 'action' => $action];
            }
        }

        return array_slice($actions, 0, 5);
    }

    /**
     * Prompt système (rôle VintApp) + instantané des données de l'utilisateur.
     */
    private function systemPrompt(?User $user): string
    {
        $prompt = <<<'TXT'
        Tu es l'assistant officiel de VintApp, une marketplace africaine d'articles d'occasion (mode, électronique, maison, etc.). Tu aides les utilisateurs à vendre, acheter, suivre leurs commandes, gérer leur portefeuille et leur compte, et tu réponds à toute question sur le fonctionnement de VintApp.

        Règles :
        - Réponds toujours en français, de façon claire, brève et polie.
        - Tu peux consulter et expliquer à l'utilisateur ses propres informations personnelles, son portefeuille, ses commandes, ses ventes, ses articles, ses notifications et son historique de transactions (fournis ci-dessous).
        - Tu peux lui proposer des améliorations concrètes de son profil (compléter les champs manquants, activer la 2FA, etc.).
        - Pour toute action que l'utilisateur peut effectuer dans l'application, appelle la fonction `propose_actions` : une fois par action, avec un `label` court et un `action` valide. Accompagne toujours ces appels d'une phrase d'explication. N'invente pas d'autre fonction que `propose_actions`.
        - Ne propose et ne mentionne que les actions réellement disponibles via `propose_actions` ; ne promets jamais une action que tu ne peux pas fournir.
        - Base-toi uniquement sur les informations fournies ci-dessous ; n'invente jamais une commande, un montant ou un statut.
        - Si tu ne sais pas, ou si la demande nécessite une action humaine (litige, remboursement), invite l'utilisateur à contacter le support.
        - Ne divulgue jamais d'informations concernant d'autres utilisateurs.
        TXT;

        return $prompt . "\n\n" . $this->userContext($user);
    }

    /**
     * Instantané compact, strictement limité à l'utilisateur courant.
     */
    private function userContext(?User $user): string
    {
        if (! $user) {
            return "Utilisateur non connecté : tu ne peux pas accéder à ses commandes, son profil ni ses transactions.";
        }

        $lines = $this->profileContext($user);
        $lines[] = '';
        $lines[] = '== Portefeuille et transactions ==';
        $lines[] = 'Solde USD : ' . number_format((float) $user->getTotalBalance('USD'), 2, ',', ' ')
            . ' | Solde CDF : ' . number_format((float) $user->getTotalBalance('CDF'), 2, ',', ' ');

        $transactions = Transaction::where('user_id', $user->id)->latest()->take(8)->get();
        if ($transactions->isEmpty()) {
            $lines[] = 'Historique des transactions : aucune transaction.';
        } else {
            $lines[] = 'Dernières transactions :';
            foreach ($transactions as $transaction) {
                $lines[] = sprintf(
                    '- %s : %s %s, statut %s, type %s (%s)%s',
                    $transaction->transaction_ref ?: ($transaction->transaction_id ?: '—'),
                    number_format((float) $transaction->amount, 2, ',', ' '),
                    $transaction->currency,
                    $transaction->status,
                    $transaction->type,
                    $transaction->created_at->format('d/m/Y'),
                    $transaction->description ? ' — ' . $transaction->description : ''
                );
            }
        }

        $lines[] = '';
        $lines[] = '== Commandes et articles ==';

        $buyerOrders = Order::where('buyer_id', $user->id)->latest()->take(5)->with('item:id,name')->get();
        if ($buyerOrders->isEmpty()) {
            $lines[] = 'Achats : aucune commande.';
        } else {
            $lines[] = 'Derniers achats :';
            foreach ($buyerOrders as $order) {
                $lines[] = sprintf(
                    '- %s : %s, %s %s, statut %s (%s)',
                    $order->order_number,
                    $order->item?->name ?? 'article',
                    number_format((float) $order->total_amount, 2, ',', ' '),
                    $order->currency,
                    $order->status,
                    $order->created_at->format('d/m/Y')
                );
            }
        }

        $sellerOrders = Order::where('seller_id', $user->id)->latest()->take(5)->with('item:id,name')->get();
        if ($sellerOrders->isNotEmpty()) {
            $lines[] = 'Dernières ventes :';
            foreach ($sellerOrders as $order) {
                $lines[] = sprintf(
                    '- %s : %s, statut %s (%s)',
                    $order->order_number,
                    $order->item?->name ?? 'article',
                    $order->status,
                    $order->created_at->format('d/m/Y')
                );
            }
        }

        $items = Item::where('user_id', $user->id)->latest()->take(5)->get(['name', 'price', 'currency', 'status']);
        if ($items->isNotEmpty()) {
            $lines[] = 'Articles en vente :';
            foreach ($items as $item) {
                $lines[] = sprintf(
                    '- %s : %s %s (statut %s)',
                    $item->name,
                    number_format((float) $item->price, 2, ',', ' '),
                    $item->currency,
                    $item->status
                );
            }
        }

        $lines[] = '';
        $lines = array_merge($lines, $this->notificationsContext($user));

        return implode("\n", $lines);
    }

    /**
     * Notifications récentes de l'utilisateur (titre + message + état de lecture).
     *
     * @return array<int, string>
     */
    private function notificationsContext(User $user): array
    {
        $unread = $user->notifications()->whereNull('read_at')->count();
        $notifications = $user->notifications()->latest()->take(10)->get();

        $lines = ['== Notifications ==', 'Non lues : ' . $unread];

        if ($notifications->isEmpty()) {
            $lines[] = 'Aucune notification.';

            return $lines;
        }

        $lines[] = 'Dernières notifications :';
        foreach ($notifications as $notification) {
            $title = trim((string) $notification->title);
            $message = trim(preg_replace('/\s+/', ' ', (string) $notification->message));

            $lines[] = sprintf(
                '- [%s] %s : %s%s (%s)',
                $notification->read_at ? 'lue' : 'non lue',
                $notification->type,
                $title !== '' ? $title : 'Notification',
                $message !== '' ? ' — ' . mb_substr($message, 0, 160) : '',
                $notification->created_at->format('d/m/Y')
            );
        }

        return $lines;
    }

    /**
     * Informations personnelles + champs manquants (pour proposer des améliorations).
     *
     * Ne contient aucun secret (mot de passe, code 2FA, codes de vérification, jetons).
     */
    private function profileContext(User $user): array
    {
        $hasAvatar = ! empty($user->avatar) || ! empty($user->avatar_url);
        $city = $user->city ?: ($user->location ?: null);

        $lines = [
            '== Profil personnel ==',
            'Nom : ' . $user->name,
            'Email : ' . $user->email . ($user->email_verified_at ? ' (vérifié)' : ' (non vérifié)'),
            'Téléphone : ' . ($user->phone ?: 'non renseigné'),
            'Adresse : ' . ($user->address ?: 'non renseignée'),
            'Ville : ' . ($city ?: 'non renseignée'),
            'Photo de profil : ' . ($hasAvatar ? 'oui' : 'non renseignée'),
            'Bio : ' . ($user->bio ?: 'non renseignée'),
            'Double authentification (2FA) : ' . ($user->google2fa_enabled ? 'activée' : 'désactivée'),
        ];

        $missing = [];
        if (! $user->phone) {
            $missing[] = 'le numéro de téléphone';
        }
        if (! $user->address) {
            $missing[] = "l'adresse";
        }
        if (! $city) {
            $missing[] = 'la ville';
        }
        if (! $hasAvatar) {
            $missing[] = 'la photo de profil';
        }
        if (! $user->bio) {
            $missing[] = 'la bio';
        }
        if (! $user->email_verified_at) {
            $missing[] = "la vérification de l'email";
        }
        if (! $user->google2fa_enabled) {
            $missing[] = 'la double authentification (2FA)';
        }

        $lines[] = $missing
            ? 'Profil à compléter (propose ces actions via propose_actions) : ' . implode(', ', $missing) . '.'
            : "Profil complet : félicite l'utilisateur, rien à améliorer.";

        return $lines;
    }
}
