# wallet-service

Microservice du **portefeuille / grand livre de fonds** de VintApp.

> À extraire : `Wallet`, `WalletTransaction`, `Transaction` (parties wallet),
> `WithdrawalRequest`, `Distribution`, `WalletService`, `Admin\WalletController`,
> `MobileMoneyService` (parties cash-out/retrait).

## Périmètre

- Soldes des wallets (main/pending/enterprise + sous-types commission/
  transport/boost).
- Transactions & historique (`Transaction`, `WalletTransaction`).
- Conversions USD ↔ CDF.
- Escrow : crédit/débit des wallets `pending` à la confirmation/livraison.
- Retraits (cash-out) et remboursements de retrait.
- Commission plateforme et distribution des parts (seller/carrier/service).

## Hors périmètre

- Réception des paiements entrants → `payment-service`.
- Commandes/livraison → `order-service`.

## Structure

```
src/
  App/
    Http/Controllers/
    Http/Middleware/
    Events/            # EscrowCredited, WithdrawalRequested
    Listeners/         # écoute payment.completed (pour créditer)
    Jobs/              # traitement des webhooks de décaissement (idempotence)
    Providers/
  Config/
  Database/Migrations/ # tables: wallets, wallet_transactions, distribution
  Routes/
  Docs/
```

## Événements émis

- `escrow.credited`
- `withdrawal.requested`

## Événements consommés

- `payment.completed` (crédit wallet / top-up)
- `order.delivered` (déblocage escrow → wallet main)

## Frontière de données

Tables de propriété : `wallets`, `wallet_transactions`, `distributions`,
`withdrawal_requests`.
