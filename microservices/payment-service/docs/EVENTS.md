# payment-service — Contrat d'événements

## Transport : outbox transactionnelle → Redis Streams

```
webhook opérateur ─► WebhookProcessor
                     │
                     └─ transaction unique ─┬─ UPDATE payments (completed)
                                             └─ INSERT outbox_messages   ← même transaction
                                                                       │
                          php artisan events:relay (ou superviseur) ─────┘
                                             │
                                             └─ XADD vintapp.payment {payload}
```

`payment-service` n'écrit **jamais** directement sur le bus. Un `payment.completed`
est d'abord écrit dans `outbox_messages` dans la même transaction que le
changement de statut du paiement : les deux réussissent ou les deux sont
annulés. Un `payment.completed` sans paiement `completed` est donc impossible,
et inversement.

C'est le point critique : un « publish after commit » classique perd
l'événement si le worker meurt entre le commit SQL et la publication. Ici la
ligne en base **est** la preuve de l'événement, et le relay la rejoue.

### Pourquoi Redis Streams plutôt que Pub/Sub

Avec Pub/Sub, un message émis pendant que `order-service` est arrêté est perdu
sans trace. Un stream conserve les entrées jusqu'à `XACK` du consommateur, ce qui
compte pour une commande déjà payée dont le crédit n'a pas encore été propagé.

### Ordre dans le relay

`OutboxRelay` publie **puis** marque `published_at`. Si le process meurt entre les
deux, le message sera republié : les consommateurs doivent donc dédupliquer sur
`event_id` (règle déjà présente dans `EVENTS.md` racine). L'inverse — marquer
puis publier — perdrait des événements.

### Commande

```bash
php artisan events:relay            # boucle jusqu'à vider l'outbox
php artisan events:relay --once     # une passe (cron, supervisord, k8s CronJob)
```

`EVENT_OUTBOX_BATCH` borne une passe, `EVENT_OUTBOX_BACKOFF` espace les
reprises après échec. Un message en échec est reprogrammé, pas supprimé, et
`attempts` + `last_error` restent consultables.

### Configuration

| Variable | Défaut | Rôle |
|---|---|---|
| `EVENT_PUBLISHER` | `log` | `redis-stream`, `log`, `null` |
| `EVENT_REDIS_CONNECTION` | `default` | connexion Laravel Redis |
| `EVENT_STREAM_MAX_LENGTH` | `10000` | troncature approximative du stream |
| `EVENT_OUTBOX_BATCH` | `100` | messages par passe |
| `EVENT_OUTBOX_BACKOFF` | `30` | secondes avant reprise |

Un `EVENT_PUBLISHER` inconnu lève une exception au démarrage : une faute de
frappe ne doit pas laisser croire que les événements partent.

## Émis

## Émis

### `payment.completed`
```json
{
  "event_id": "uuid",
  "type": "payment.completed",
  "occurred_at": "ISO-8601",
  "data": {
    "payment_id": 1,
    "order_id": 42,
    "wallet_id": null,
    "amount": 5000,
    "currency": "USD",
    "provider": "mpesa",
    "transaction_ref": "MPESA-XYZ-001"
  }
}
```
Consommateurs (une fois le transport branché) :
- **order-service** : marque la commande payée, émet `order.paid`.
- **wallet-service** : top-up / crédit escrow.
- **authenticity-service** : frais de vérification.

### `payment.failed`
```json
{
  "event_id": "uuid",
  "type": "payment.failed",
  "occurred_at": "ISO-8601",
  "data": {
    "payment_id": 1,
    "order_id": 42,
    "amount": 5000,
    "currency": "USD",
    "provider": "mpesa",
    "reason": "Statut opérateur inconnu"
  }
}
```
Consommateurs : **order-service** (annulation / notification).

## Consommés

- `withdrawal.requested` (wallet-service) → déclenche un décaissement vers
  l'opérateur. L'acquittement (`withdrawal.completed` / `withdrawal.failed`) est
  émis par wallet-service après vérification du statut.

> Non implémenté : `payment-service` n'écoute pas encore cet événement et
> n'appelle aucune API de décaissement opérateur.

## Reste à faire côté consommateurs

Aucun consommateur n'est branché dans ce dépôt. Pour lire les événements :

```
XREADGROUP  GROUP <service> <consumer> STREAMS vintapp.payment >
XACK       vintapp.payment <group> <id>
```

`order-service` et `wallet-service` devront créer leur groupe, dédupliquer sur
`event_id` et acquitter seulement après avoir écrit dans **leur** base. Aucune
écriture dans la base d'un autre service : le crédit escrow passe par
`order.paid` / `escrow.credited`.

## Idempotence

Une écriture résultant d'un événement doit être idempotente sur `event_id`.
Côté émission, `payment.completed` n'est pas réémis pour un paiement déjà
`completed` (un opérateur peut notifier le succès deux fois).

## Sécurité (fail-closed)

- Signature/clé absente ou placeholder `DEMO_*` ⇒ **refus** (403), quel que soit
  l'environnement.
- Comparaisons en temps constant (`hash_equals`).
- Rejeu détecté par clé de déduplication unique ; le rejeu est acquitté sans
  déclencher d'effet.
- Rattachement d'un webhook à un paiement par **référence + opérateur** uniquement.
