# payment-service

Microservice de **traitement des paiements** de VintApp : initiation,
webhooks opérateurs, remboursements.

> Extraire de : `PaymentController`, `PaymentCallbackController`,
> `PaymentService`, SDK embarqués (`CinetPay`, `MaishaPay`, `PawaPay`,
> `AfribaPay`, `MobileMoneyService`), tables `payments`, `payment_callbacks`.

## Périmètre

- Enregistrement d'une intention de paiement.
- Réception et vérification des **webhooks** opérateurs (HMAC, clé API, token
  Bearer, champ payload — fail-closed).
- Déduplication des webhooks rejoués.
- Remboursements.
- Émission des événements `payment.completed` / `payment.failed`
  (dispatch local, transport file à brancher — voir `docs/EVENTS.md`).

## Hors périmètre

- Solde des wallets et escrow → `wallet-service`.
- Création/marquage de commandes → `order-service` (consomme `payment.completed`).
- Identité des utilisateurs → `auth-service` (consommé via introspection).

## API

Format de réponse : `{success, message, data, meta}`.

| Méthode | Route | Auth | Rôle |
|---|---|---|---|
| POST | `/v1/webhooks/{provider}` | signature opérateur | notification opérateur |
| GET | `/v1/payments` | Bearer (via auth-service) | paiements de l'appelant |
| POST | `/v1/payments` | Bearer | enregistre une intention de paiement |
| GET | `/v1/payments/{payment}` | Bearer | détail (propriétaire ou admin) |
| POST | `/v1/payments/{payment}/refund` | Bearer | remboursement (admin) |

### Ce qui n'est pas fait

L'API d'un opérateur n'est **pas** appelée : `POST /v1/payments` enregistre
l'intention, et le statut n'évolue que par webhook. Aucun appel de décaissement,
de remboursement ou d'initiation n'est effectué. Le service gère donc la
réception et la consolidation des notifications, pas l'orchestration des
opérateurs chez eux. Idem pour le transport d'événements et la consommation de
`withdrawal.requested` : voir `docs/EVENTS.md`.

Opérateurs gérés : `mpesa`, `orange_money`, `airtel_money`, `africell`,
`cinetpay`, `maishapay`, `pawapay`, `afribapay`, `kpay`.

## Règles de sécurité (fail-closed)

1. **Signature obligatoire.** Un secret d'opérateur absent ou laissé à un
   placeholder (`DEMO_SECRET`, `CHANGE_ME`…) ⇒ webhook `403`. Le comportement
   ne dépend **pas** de `APP_ENV` : pas de « ça passe en local ».
2. **HMAC sur le corps brut**, pas sur `$request->all()` re-sérialisé, sinon
   la chaîne signée diffère. Comparaisons en `hash_equals`.
3. **Rattachement strict par référence.** Un webhook authentifié ne peut solder
   que le paiement portant sa propre `reference` / `external_reference`, et
   seulement pour le même opérateur. Pas de repli « montant + téléphone » :
   ce fallback du monolithe permettait à un opérateur authentifié de solder le
   paiement d'un autre client.
4. **Idempotence.** Clé de déduplication unique en base + cache ; un rejeu est
   acquitté sans deuxième effet.
5. **Identité fail-closed.** `/v1/*` passe par `auth-service` ; si le service
   est injoignable ou non configuré ⇒ `503`, jamais de mode dégradé. Le
   `user_id` provient de l'introspection, jamais du client.
6. **Statut inconnu ⇒ `failed`.** On ne crédite jamais un paiement sur un code
   opérateur qu'on ne sait pas interpréter.

## Frontière de données

Tables possédées : `payments`, `payment_callbacks`, `refunds`,
`processed_webhook_events`.

Base **propre** (`vintapp_payments`). `user_id`, `order_id`, `wallet_id`,
`seller_id` sont des clés métier, jamais des FK vers un autre service.

## Configuration

`.env` :

```
AUTH_SERVICE_URL=http://127.0.0.1:8101
AUTH_SERVICE_SHARED_SECRET=...   # obligatoire, sinon /v1/* => 503
MPESA_CALLBACK_SECRET=...        # par opérateur ; vide => webhook refusé
ORANGE_CALLBACK_KEY=...
AIRTEL_CALLBACK_TOKEN=...
AFRICELL_CALLBACK_SECRET=...
CINETPAY_SHOP_KEY=...
```

Le mapping opérateur → mode de vérification est dans `config/payments.php`.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --port=8102
```

## Tests

```bash
vendor/bin/phpunit
```

22 tests, 46 assertions (SQLite `:memory:`). Les tests
`WebhookSignatureTest` couvrent le fail-closed (secret absent, placeholder,
signature invalide, opérateur inconnu) ; `WebhookMatchingTest` couvre le
rattachement strict, l'isolation entre opérateurs et le rejeu ;
`IdentityTest` couvre le refus fail-closed et la portée par utilisateur.

## Note d'écart avec le monolithe

Le monolithe faire de MaishaPay sans signature (la doc officielle ne fournit
pas de HMAC sur `callbackUrl`), en s'appuyant sur la correspondance de
référence. Ici MaishaPay exige un token `Authorization: Bearer`, ce qui est
plus strict : si l'opérateur n'envoie pas cet en-tête, le callback est refusé
et l'anomalie est visible dans les logs, au lieu d'être absorbée.
