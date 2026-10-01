# order-service — Contrat d'événements

## Émis
- `order.created` : commande créée (buyer, seller, amount).
- `order.paid` : commande payée (après payment.completed).
- `order.delivered` : livrée.
- `order.completed` : commande finalisée (permet points/parrainage).

## Consommés
- `payment.completed` (payment-service) → marquage payé + émet `order.paid`.
- `item.updated` / `item.deleted` (items-service, stream `vintapp.catalog`) →
  disponibilité et prix. Le catalogue appartient à items-service.
