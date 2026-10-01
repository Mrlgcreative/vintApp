# payment-service — Contrat d'événements

> **Statut : contrat cible, non encore branché.** Les événements sont
> aujourd'hui des events Laravel locaux (`App\Events\PaymentCompleted` /
> `PaymentFailed`) dispatchés par `WebhookProcessor`, sans file ni transport :
> aucun listener ne les consomme encore. Les enveloppes `event_id` /
> `occurred_at` décrites ci-dessous sont le format à adopter lors du
> raccordement à la file de messages. Même constat côté `auth-service`, qui
> dispatche `UserRegistered` / `UserAuthenticated` localement.
>
> En l'état, un `payment.completed` n'atteint donc **pas** `order-service` ni
> `wallet-service` : ne pas considérer un webhook reçu comme un crédit propagé.

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
