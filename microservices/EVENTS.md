# Contrat d'événements inter-services

Ce fichier définit les événements échangés entre microservices via la file de
messages. Chaque événement est un **fait** immuable, publié par le service qui
possède la donnée source. La déduplication repose sur `event_id` (UUID) —
toute écriture résultant d'un événement doit être idempotente sur cet ID.

## Nomenclature

Format recommandé du payload :

```json
{
  "event_id": "uuid",
  "type": "payment.completed",
  "occurred_at": "ISO-8601",
  "data": { }
}
```

## Événements

| Type | Émetteur | Consommateur (réaction) | Données clés |
|---|---|---|---|
| `payment.completed` | payment-service | order-service (commande payée), wallet-service (top-up/escrow), authenticity-service (frais vérif) | `payment_id`, `order_id?`, `wallet_id?`, `amount`, `currency`, `provider`, `transaction_ref` |
| `payment.failed` | payment-service | order-service (annule/notifie), wallet-service | `payment_id`, `order_id?`, `amount`, `reason` |
| `item.created` | items-service | order-service (disponibilité), caches de recherche | `item_id`, `seller_id`, `category_id`, `brand_id`, `price`, `currency`, `quantity`, `status` |
| `item.updated` | items-service | order-service (prix/stock), caches | idem + `changed[]` |
| `item.deleted` | items-service | order-service (retrait), caches | idem |
| `order.created` | order-service | marketing-service (réservations points) | `order_id`, `buyer_id`, `seller_id`, `amount` |
| `order.paid` | order-service | wallet-service (crédit escrow vendeur) | `order_id`, `buyer_id`, `seller_id`, `total_amount`, `currency` |
| `order.delivered` | order-service | wallet-service (déblocage escrow → main) | `order_id`, `seller_id`, `total_amount` |
| `order.completed` | order-service | marketing-service (attribution points / parrainage) | `order_id`, `buyer_id`, `amount`, `item_meta` |
| `escrow.credited` | wallet-service | order-service | `order_id`, `seller_id`, `amount` |
| `withdrawal.requested` | wallet-service | payment-service (décaissement) | `withdrawal_id`, `wallet_id`, `amount`, `provider`, `destination` |
| `verification.approved` | authenticity-service | marketing-service, order-service (badge vendeur) | `check_id`, `item_id`, `approved` |

## Règles d'écriture

- Une table n'est écrite que par le service qui la **possède**.
- Réaction à un événement = idempotente (clef `event_id`).
- Fail-closed : tout événement/webhook invalide ou incomplet → refus + log.
