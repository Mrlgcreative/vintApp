# payment-service

Microservice de **traitement des paiements** de VintApp.

> À extraire du code monolithique : `PaymentController`, `PaymentCallbackController`,
> `PaymentService`, SDK embarqués (`CinetPay`, `MaishaPay`, `PawaPay`, `AfribaPay`,
> `MobileMoneyService`), `Payment`, `Transaction` (partiellement), `PaymentCallback`.

## Périmètre

- Initiation de paiements (CinetPay, M-Pesa, Orange Money, Airtel Money,
  Africell, Illicocash, MaishaPay, PawaPay, AfribaPay).
- Réception & vérification des **webhooks** opérateurs (signatures HMAC,
  fail-closed — cf. correctifs appliqués dans le monolithe).
- Remboursements (`Refund`).
- Émission des événements `payment.completed` / `payment.failed`.

## Hors périmètre

- Solde des wallets et escrow → `wallet-service`.
- Création de commandes suite au paiement → `order-service` (consomme
  `payment.completed`).

## Structure

```
src/
  App/
    Http/Controllers/   # endpoints d'initiation + webhooks par opérateur
    Http/Middleware/    # vérification de signature par provider
    Events/             # PaymentCompleted, PaymentFailed
    Listeners/          # (aucun côté payment ; publie plutôt vers la file)
    Jobs/               # traitement async des callbacks (idempotence)
    Providers/
  Config/               # config des providers (unifie services.php + payments.php)
  Database/Migrations/  # tables: payments, payment_callbacks, refunds
  Routes/               # routes webhooks (+ admin force-complete)
  Docs/                 # spec OpenAPI + EVENTS.md
```

## Événements émis

- `payment.completed`
- `payment.failed`

## Événements consommés

- (aucun pour son cœur ; peut écouter des demandes de retrait via wallet-service)

## Frontière de données

Tables de propriété : `payments`, `payment_callbacks`, `refunds`.
Ne **pas** écrire directement dans `orders`, `wallets`, `items`, `carts` :
on publie un événement et les services concernés réagissent.
