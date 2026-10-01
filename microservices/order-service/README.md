# order-service

Microservice du **marché / commandes** de VintApp.

> À extraire : `Item`, `Order`, `Cart`, `DeliveryAddress`, `Review`, `Boost`,
> `OrderService`, `OrderController`, `create_orders_from_transaction` (helpers).

## Périmètre

- Catalogue produits (`Item`), stock, boosts.
- Création de commandes (le panier → commande).
- Cycle de vie d'une commande : pending → confirmed → shipped → delivered → completed.
- Annulation & remboursement côté commande.
- Livraison et suivi (tracking).

## Hors périmètre

- Réception du paiement → `payment-service` (consomme `payment.completed`).
- Crédit/débit du wallet → `wallet-service` (demande via `escrow.credited`).

## Structure

```
src/
  App/
    Http/Controllers/
    Http/Middleware/
    Events/            # OrderCreated, OrderPaid, OrderDelivered
    Listeners/         # écoute payment.completed -> OrderPaid
    Jobs/
    Providers/
  Config/
  Database/Migrations/ # tables: items, orders, carts, reviews, boosts
  Routes/
  Docs/
```

## Événements émis

- `order.created`
- `order.paid`
- `order.delivered`

## Événements consommés

- `payment.completed` (marquage payé)

## Frontière de données

Tables de propriété : `items`, `orders`, `carts`, `delivery_addresses`,
`reviews`, `boosts`.
