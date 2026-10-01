# order-service

Microservice du **marché / commandes** de VintApp.

> À extraire : `Order`, `Cart`, `DeliveryAddress`, `Review`, `Boost`,
> `OrderService`, `OrderController`, `create_orders_from_transaction` (helpers).

## Périmètre

- Création de commandes (le panier → commande).
- Cycle de vie d'une commande : pending → confirmed → shipped → delivered → completed.
- Annulation & remboursement côté commande.
- Livraison et suivi (tracking).

## Hors périmètre

- Catalogue produits (`Item`, catégories, marques, stock) → `items-service`
  (consomme `item.*`).
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
  Database/Migrations/ # tables: orders, carts, reviews, boosts
  Routes/
  Docs/
```

## Événements émis

- `order.created`
- `order.paid`
- `order.delivered`

## Événements consommés

- `payment.completed` (marquage payé)
- `item.updated` / `item.deleted` (disponibilité et prix, depuis items-service)

## Frontière de données

Tables de propriété : `orders`, `carts`, `delivery_addresses`, `reviews`,
`boosts`.

`items-service` possède le catalogue : `order-service` ne fait que lire les
événements `vintapp.catalog` et conserver l'`item_id` comme clé métier.
