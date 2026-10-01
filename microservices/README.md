# VintApp — Microservices

Ce dossier regroupe les **futurs microservices** de la plateforme, extraits du
monolithe Laravel (`/home/aizen/Bureau/sky/vintApp`). L'architecture du monolithe
n'est volontairement **pas modifiée** tant que les services ne sont pas prêts.

## Principe

Approche *strangler fig* : chaque microservice est développé dans ce dossier,
testé indépendamment, puis **greffé** progressivement à la place de la logique
monolithique correspondante (via API/RPC + file de messages), avant que le code
du monolithe soit retiré.

## Services prévus

| Service | Domaine | Sources monolithiques à extraire |
|---|---|---|
| `payment-service` | Tous paiements & webhooks (CinetPay, M-Pesa, Orange, Airtel, Africell, Illicocash, MaishaPay, PawaPay, AfribaPay) | `PaymentController`, `PaymentCallbackController`, `PaymentService`, SDK `CinetPay/MaishaPay/PawaPay/AfribaPay/MobileMoneyService` |
| `wallet-service` | Soldes wallets, transactions, escrow, conversions, retraits | `Wallets`, `WalletTransaction`, `Transactions`, `WithdrawalRequest`, `WalletService`, `Admin\WalletController` |
| `order-service` | Catalogue, commandes, escrow, livraison, boosts, reviews | `Item`, `Order`, `OrderService`, `OrderController` |
| `auth-service` | Auth, rôles/permissions, 2FA, profils users | `User`, `AuthService`, Sanctum/Firebase auth |
| `marketing-service` | Affiliation, parrainage, points/VintPass, notifications | `AffiliateService`, `VintPassService`, `NotificationService`, `FirebasePushService` |
| `authenticity-service` | Vérification d'authenticité, profils experts, certifications | `ProductAuthenticityCheck`, `AuthenticityVerificationService`, `VerificationPaymentService` |

## Contrats transverses (événements partagés)

Les services communiquent via **Redis Streams**. Chaque service écrit d'abord
l'événement dans une outbox transactionnelle (même transaction que la donnée
métier), puis un relay le publie : un événement n'est jamais publié sans que la
donnée source soit committée, et jamais perdu si le bus est indisponible.

Le nom du stream est un contrat : `vintapp.payment`, littéralement. Attention
au préfixe Redis Laravel qui le transformerait en
`vintapp_payment_database_vintapp.payment` : le bus doit passer par une
connexion sans préfixe.

Infrastructure locale :

```bash
docker compose -f microservices/docker-compose.yml up -d
docker exec vintapp-redis redis-cli ping
```

Événements de référence :

- `payment.completed`   → publié par payment-service
- `payment.failed`      → publié par payment-service
- `order.created`       → publié par order-service
- `order.paid`          → publié par order-service (après `payment.completed`)
- `escrow.credited`     → publié par wallet-service
- `withdrawal.requested`→ publié par wallet-service
- `verification.approved` → publié par authenticity-service

## Règles de conception

1. **Frontière de données** : chaque service possède sa propre base. Les
   identifiants partagés (user_id, order_id, transaction_id) sont des clés
   métier, jamais des FK vers les tables d'un autre service.
2. **Idempotence** : toute écriture déclenchée par un événement/webhook doit
   être idempotente (clef de déduplication).
3. **Fail-closed** : en cas de secret/signature manquants → refuser (voir les
   correctifs de sécurité déjà appliqués dans le monolithe).
4. **Contrats d'interface** : chaque service expose sa spec OpenAPI dans
   `src/docs/` et sa liste d'événements émis/consommés dans `src/EVENTS.md`.

## État d'avancement

En cours de structuration. Chaque sous-dossier détaille son périmètre et son
plan d'extraction.
