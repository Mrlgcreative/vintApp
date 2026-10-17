# wallet-service — Contrat d'événements

## Émis
- `escrow.credited` : escrow du vendeur crédité (order_id, seller_id, amount).
- `withdrawal.requested` : demande de décaissement (withdrawal_id, amount, provider, destination).
- `withdrawal.completed` / `withdrawal.failed` : acquittement d'un décaissement.

## Consommés
- `payment.completed` (payment-service) → top-up / crédit.
- `order.paid` (order-service) → crédit escrow vendeur.
- `order.delivered` (order-service) → déblocage escrow → wallet main.
