<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service RGPD — Portabilité des données.
 *
 * Rassemble l'ensemble des données personnelles d'un utilisateur
 * dans une structure JSON normalisée et interopérable, conformément
 * à l'article 20 du RGPD (droit à la portabilité).
 */
class UserDataExportService
{
    /**
     * Version du format d'export (incrémenter à chaque breaking change).
     */
    private const FORMAT_VERSION = '1.0';

    /**
     * Génère l'ensemble structuré des données de l'utilisateur.
     */
    public function export(User $user): array
    {
        $exportedAt = now()->toIso8601String();

        return [
            'meta' => [
                'format' => 'vintapp-user-data-export',
                'format_version' => self::FORMAT_VERSION,
                'exported_at' => $exportedAt,
                'user_id' => $user->id,
                'generator' => 'VintApp RGPD Portability',
                'license' => 'Données personnelles de l\'utilisateur — drogue RGPD article 20',
            ],
            'identite' => $this->identity($user),
            'profil' => $this->profile($user),
            'roles' => $this->roles($user),
            'localisation_vendeur' => $this->sellerLocation($user),
            'adresses_livraison' => $this->deliveryAddresses($user),
            'annonces_produits' => $this->items($user),
            'favoris' => $this->favorites($user),
            'commandes' => [
                'achetees' => $this->purchasedOrders($user),
                'vendues' => $this->soldOrders($user),
            ],
            'portefeuille' => $this->wallets($user),
            'messages' => $this->messages($user),
            'avis_evaluations' => $this->reviews($user),
            'notifications' => $this->notifications($user),
            'sessions_connexion' => $this->sessions($user),
        ];
    }

    /**
     * Données d'identité primaires (le modèle User).
     */
    private function identity(User $user): array
    {
        return [
            'créé_le' => $this->date($user->created_at),
            'mise_a_jour_le' => $this->date($user->updated_at),
            'email_verifie_le' => $this->date($user->email_verified_at),
        ];
    }

    /**
     * Champs du profil utilisateur.
     */
    private function profile(User $user): array
    {
        $columns = [
            'name', 'username', 'first_name', 'last_name', 'email', 'phone',
            'phone_verified_at', 'gender', 'birth_date', 'bio', 'location',
            'city', 'country', 'avatar', 'referral_code', 'currency',
            'language', 'theme', 'email_notifications', 'push_notifications',
            'sms_notifications', 'fcm_token', 'device_type', 'browser',
            'fcm_token_updated_at', 'is_active', 'last_seen_at',
        ];

        $data = [];
        foreach ($columns as $column) {
            if (array_key_exists($column, $user->getAttributes())) {
                $value = $user->getAttribute($column);
                $data[$column] = $value instanceof Carbon
                    ? $value->toIso8601String()
                    : $value;
            }
        }

        return $data;
    }

    /**
     * Rôles et permissions attribués.
     */
    private function roles(User $user): array
    {
        return $user->roles()->get(['slug', 'name'])->map(fn ($role) => [
            'slug' => $role->slug,
            'name' => $role->name,
        ])->values()->toArray();
    }

    /**
     * Localisation déclarée comme vendeur (lat/lng/city/commune).
     */
    private function sellerLocation(User $user): ?array
    {
        $keys = ['latitude', 'longitude', 'city', 'commune', 'country', 'address', 'is_location_set'];
        $location = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $user->getAttributes())) {
                $location[$key] = $user->getAttribute($key);
            }
        }

        return empty($location) ? null : $location;
    }

    /**
     * Adresses de livraison enregistrées.
     */
    private function deliveryAddresses(User $user): array
    {
        return $user->deliveryAddresses()->get()->map(fn ($a) => $a->toArray())->toArray();
    }

    /**
     * Annonces / produits publiés par l'utilisateur.
     */
    private function items(User $user): array
    {
        return $user->items()->with('category:id,name,slug')->get()->map(function ($item) {
            $data = $item->toArray();
            $data['category'] = $item->category ? [
                'name' => $item->category->name,
                'slug' => $item->category->slug,
            ] : null;

            return $data;
        })->values()->toArray();
    }

    /**
     * Favoris (produits enregistrés).
     */
    private function favorites(User $user): array
    {
        return $user->favorites()->with('item:id,title,slug,price,currency')->get()->map(fn ($f) => [
            'ajouté_le' => $this->date($f->created_at),
            'produit' => $f->item ? [
                'id' => $f->item->id,
                'titre' => $f->item->title,
                'slug' => $f->item->slug,
                'prix' => $f->item->price,
                'devise' => $f->item->currency,
            ] : ['id' => $f->item_id],
        ])->toArray();
    }

    /**
     * Commandes passées (en tant qu'acheteur).
     */
    private function purchasedOrders(User $user): array
    {
        return $user->ordersAsBuyer()->with('item:id,title,slug,price,currency')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'reference' => $order->reference ?? null,
                'statut' => $order->status,
                'sous_total' => $order->subtotal ?? null,
                'frais' => $order->shipping_fee ?? $order->delivery_fee ?? null,
                'total' => $order->total_amount ?? $order->total,
                'devise' => $order->currency ?? null,
                'produit' => $order->item ? [
                    'id' => $order->item->id,
                    'titre' => $order->item->title,
                    'prix' => $order->item->price,
                    'devise' => $order->item->currency,
                ] : ['id' => $order->item_id],
                'vendeur_id' => $order->seller_id,
                'créé_le' => $this->date($order->created_at),
            ];
        })->values()->toArray();
    }

    /**
     * Commandes reçues (en tant que vendeur).
     */
    private function soldOrders(User $user): array
    {
        return $user->ordersAsSeller()->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'reference' => $order->reference ?? null,
                'statut' => $order->status,
                'sous_total' => $order->subtotal ?? null,
                'frais' => $order->shipping_fee ?? $order->delivery_fee ?? null,
                'total' => $order->total_amount ?? $order->total,
                'devise' => $order->currency ?? null,
                'produit_id' => $order->item_id,
                'acheteur_id' => $order->buyer_id,
                'créé_le' => $this->date($order->created_at),
            ];
        })->values()->toArray();
    }

    /**
     * Wallets et transactions associées.
     */
    private function wallets(User $user): array
    {
        $wallets = $user->wallets()->with('transactions')->get();

        return [
            'wallets' => $wallets->map(function ($wallet) {
                return [
                    'devise' => $wallet->currency,
                    'type' => $wallet->type,
                    'solde' => $wallet->balance,
                    'actif' => $wallet->active ?? null,
                    'transactions' => $wallet->transactions->sortByDesc('created_at')->values()->map(function ($t) {
                        return [
                            'type' => $t->type,
                            'montant' => $t->amount,
                            'solde_apres' => $t->balance_after,
                            'description' => $t->description,
                            'reference' => $t->reference,
                            'statut' => $t->status,
                            'fournisseur' => $t->provider,
                            'créé_le' => $this->date($t->created_at),
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }

    /**
     * Messages échangés (envoyés et reçus).
     */
    private function messages(User $user): array
    {
        $sent = $user->sentMessages()->get()->map(fn ($m) => [
            'direction' => 'envoyé',
            'destinataire_id' => $m->receiver_id,
            'sujet' => $m->subject,
            'contenu' => $m->content,
            'type' => $m->type,
            'créé_le' => $this->date($m->created_at),
        ])->toArray();

        $received = $user->receivedMessages()->get()->map(fn ($m) => [
            'direction' => 'reçu',
            'expéditeur_id' => $m->sender_id,
            'sujet' => $m->subject,
            'contenu' => $m->content,
            'type' => $m->type,
            'créé_le' => $this->date($m->created_at),
        ])->toArray();

        return array_merge($sent, $received);
    }

    /**
     * Avis / évaluations donnés et reçus.
     */
    private function reviews(User $user): array
    {
        $given = $user->reviewsGiven()->get()->map(fn ($r) => [
            'direction' => 'donné',
            'destinataire_id' => $r->seller_id,
            'produit_id' => $r->item_id,
            'note' => $r->rating,
            'commentaire' => $r->comment,
            'créé_le' => $this->date($r->created_at),
        ])->toArray();

        $received = $user->receivedReviews()->get()->map(fn ($r) => [
            'direction' => 'reçu',
            'auteur_id' => $r->reviewer_id,
            'produit_id' => $r->item_id,
            'note' => $r->rating,
            'commentaire' => $r->comment,
            'créé_le' => $this->date($r->created_at),
        ])->toArray();

        return array_merge($given, $received);
    }

    /**
     * Notifications reçues.
     */
    private function notifications(User $user): array
    {
        return $user->notifications()->get()->map(fn ($n) => [
            'type' => $n->type,
            'titre' => $n->title,
            'message' => $n->message,
            'lue_le' => $this->date($n->read_at),
            'créé_le' => $this->date($n->created_at),
        ])->toArray();
    }

    /**
     * Sessions / connexions (logs de sécurité).
     */
    private function sessions(User $user): array
    {
        return $user->userSessions()->get()->map(fn ($s) => [
            'ip' => $s->ip_address,
            'agent' => $s->user_agent,
            'type_appareil' => $s->device_type,
            'navigateur' => $s->browser,
            'os' => $s->os,
            'ville' => $s->city,
            'pays' => $s->country,
            'connexion_le' => $this->date($s->login_at),
            'deconnexion_le' => $this->date($s->logout_at),
            'derniere_activite' => $this->date($s->last_activity),
            'active' => $s->is_active,
        ])->toArray();
    }

    /**
     * Formate une date en ISO 8601 si non nulle.
     */
    private function date($value): ?string
    {
        if (!$value) {
            return null;
        }

        return $value instanceof Carbon
            ? $value->toIso8601String()
            : Carbon::parse($value)->toIso8601String();
    }
}
