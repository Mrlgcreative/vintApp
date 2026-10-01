# payment-service — Contrat d'événements

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
    "wallet_id": 7,
    "amount": 5000,
    "currency": "USD",
    "provider": "orange_money",
    "transaction_ref": "VIN-..."
  }
}
```
Consommateurs : order-service (marque `order.paid`), wallet-service (top-up /
escrow), authenticity-service (frais de vérification).

### `payment.failed`
```json
{
  "event_id": "uuid",
  "type": "payment.failed",
  "occurred_at": "ISO-8601",
  "data": { "payment_id": 1, "order_id": 42, "amount": 5000, "reason": "declined" }
}
```
Consommateurs : order-service (annulation/notification).

## Consommés

- `withdrawal.requested` (wallet-service) → déclenche un décaissement vers
  l'opérateur. L'acquittement final (`withdrawal.completed` / `withdrawal.failed`)
  est émis par wallet-service après vérification du statut.

## Sécurité (fail-closed)

- Signature HMAC/clé absente ou placeholder (`DEMO_*`) → **refus**.
- Comparaisons en temps constant (`hash_equals`).
- Idempotence des webhooks via `event_id`/replay (table `payment_callbacks`).
