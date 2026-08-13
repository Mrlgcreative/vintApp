# Gestion des commandes — Documentation API mobile

> Documentation complète du processus de gestion des commandes de vintApp, destinée à l'implémentation côté mobile.
> Tous les endpoints API sont sous le préfixe `/api` avec authentification **Sanctum Bearer token**.

---

## Sommaire

1. [Vue d'ensemble : deux flux de création](#1-vue-densemble--deux-flux-de-création)
2. [Machine à états de la commande](#2-machine-à-états-de-la-commande)
3. [Authentification](#3-authentification)
4. [Format des réponses standardisées](#4-format-des-réponses-standardisées)
5. [Endpoints existants (commandes, paiements, remboursements)](#5-endpoints-existants-commandes-paiements-remboursements)
6. [Nouveaux endpoints mobile](#6-nouveaux-endpoints-mobile)
7. [Suivi des sessions mobile & utilisateurs en ligne](#7-suivi-des-sessions-mobile--utilisateurs-en-ligne)
8. [Détails métier à reproduire côté mobile](#8-détails-métier-à-reproduire-côté-mobile)
9. [Résultats des tests](#9-résultats-des-tests)
10. [Pistes d'amélioration](#10-pistes-damélioration)

---

## 1. Vue d'ensemble : deux flux de création

**Flux A — Commande directe (API `v1/orders`)** : l'acheteur crée une commande `pending`, puis confirme manuellement le paiement via `confirm-payment`.

```
POST /api/v1/orders               → order (status: pending)
POST /api/v1/orders/{id}/confirm-payment → order (status: confirmed, paid_at)
```

**Flux B — Panier + paiement d'abord (web, à transposer mobile)** : l'acheteur paie d'abord (CinetPay/MaishaPay/PawaPay), le callback crée ensuite des commandes `confirmed` via `create_orders_from_transaction()` (`app/helpers.php:374`).

```
POST /api/v1/payments/initiate (ou /maishapay)  → transaction (status: pending)
polling statut / callback opérateur             → status: completed
create_orders_from_transaction()                → orders (status: confirmed) + wallet pending vendeur crédité
```

> Dans le flux B, le mobile n'a rien à faire de plus que le polling du statut de la transaction : la création des commandes, le décrément du stock et le crédit du wallet pending du vendeur sont gérés par le serveur.

---

## 2. Machine à états de la commande

Statuts (`orders.status`) : `pending → confirmed → shipped → delivered → completed` (+ `cancelled`).

| Statut | Signification | Qui peut passer à l'étape suivante | Timestamp posé |
|---|---|---|---|
| `pending` | En attente de paiement | Acheteur | `created_at` |
| `confirmed` | Paiement confirmé | Vendeur | `paid_at` |
| `shipped` | Expédiée | Vendeur | `shipped_at` |
| `delivered` | Livrée | Acheteur | `delivered_at` |
| `completed` | Réception confirmée + fonds distribués | Acheteur | `confirmed_by_buyer_at`, `buyer_confirmation_note` |
| `cancelled` | Annulée (stock restauré, **commande supprimée**) | Acheteur, uniquement si `pending` | — |

### Règles de validation côté service (`app/Services/OrderService.php`)

Toutes les violations lancent une `DomainException` → **HTTP 400** avec le message en français.

| Méthode | Règles |
|---|---|
| `create()` :23 | Interdit d'acheter son propre article ; article doit être `status = active` ; quantité ≤ stock disponible |
| `confirmPayment()` :70 | Exige `pending` |
| `markShipped()` :84 | Exige `confirmed` |
| `markDelivered()` :98 | Exige `shipped` |
| `cancel()` :112 | Exige `pending` ; restaure le stock ; **supprime** la ligne |
| `confirmDelivery()` :133 | Exige `shipped` ou `delivered` ; `confirmed_by_buyer_at` pas déjà défini ; déclenche `distributeFunds()` |

### Distribution des fonds (`distributeFunds()` :160)

Appelée à la confirmation de réception par l'acheteur :

- Commission plateforme : setting `platform_commission_percentage` (défaut **10 %**)
- Frais transport : setting `transport_fee_percentage` (défaut **5 %**)
- Débit du **wallet `pending` du vendeur** → crédit wallet `main` du vendeur + sous-wallets entreprise `commission` et `transport`
- Création de 3 transactions : `SELLER-*`, `COMMISSION-*`, `TRANSPORT-*`
- ⚠️ **Si le wallet pending du vendeur n'existe pas ou est insuffisant, la méthode retourne silencieusement sans distribuer** (aucune erreur). À anticiper côté mobile (état « fonds bloqués »).

---

## 3. Authentification

| Endpoint | Méthode | Description |
|---|---|---|
| `/api/register` | POST | Inscription → renvoie `token` |
| `/api/login` | POST | Connexion → renvoie `token` (ou `pending_token` si 2FA activée) |
| `/api/two-factor/verify` | POST | Validation du code 2FA → renvoie le token complet |
| `/api/logout` | POST | Déconnexion (révoque le token courant) |
| `/api/user` | GET | Utilisateur authentifié |

En-tête : `Authorization: Bearer <token>`.

---

## 4. Format des réponses standardisées

Géré par `app/Http/Controllers/Api/ApiController.php`.

```jsonc
// Succès (200 / 201)
{ "success": true, "message": "...", "data": { ... } }

// Paginé
{ "success": true, "message": "...", "data": [ ... ], "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 41 } }

// Erreur (400 / 401 / 403 / 404 / 422 / 500)
{ "success": false, "message": "...", "errors": { ... } }
```

Codes usuels : `400` règle métier (`DomainException`), `401` non authentifié, `403` non autorisé, `404` introuvable, `422` erreur de validation, `500` erreur serveur.

---

## 5. Endpoints existants (commandes, paiements, remboursements)

### Commandes — `app/Http/Controllers/Api/Orders/OrderController.php`

| Méthode | URL | Rôle | Description |
|---|---|---|---|
| GET | `/api/v1/orders` | Acheteur | Liste des commandes (paginé, `per_page` défaut 15) |
| POST | `/api/v1/orders` | Acheteur | Créer une commande (statut `pending`) |
| GET | `/api/v1/orders/sales` | Vendeur | Liste des ventes (paginé) |
| GET | `/api/v1/orders/{id}` | Acheteur ou vendeur | Détail d'une commande |
| POST | `/api/v1/orders/{id}/confirm-payment` | Acheteur | Confirmer le paiement (`pending` → `confirmed`) |
| POST | `/api/v1/orders/{id}/mark-shipped` | Vendeur | Marquer expédiée (`confirmed` → `shipped`) |
| POST | `/api/v1/orders/{id}/mark-delivered` | Vendeur | Marquer livrée (`shipped` → `delivered`) |
| POST | `/api/v1/orders/{id}/confirm-delivery` | Acheteur | Confirmer la réception (`shipped`/`delivered` → `completed`, distribue les fonds). Body : `note` (optionnel) |
| DELETE | `/api/v1/orders/{id}` | Acheteur | Annuler une commande `pending` (restaure le stock) |

### Création d'une commande — body attendu (`app/Http/Requests/CreateOrderRequest.php`)

```jsonc
{
  "item_id": 5,                                    // obligatoire, doit exister
  "quantity": 1,                                   // obligatoire, 1..100
  "payment_method": "cinetpay",                    // obligatoire : cinetpay|mobile_money|card|wallet|cash_on_delivery
  "delivery_address": "Av. de la Paix 12",         // obligatoire, min 10 caractères
  "delivery_city": "Kinshasa",                     // obligatoire
  "delivery_phone": "+243812345678",               // obligatoire, regex ^[+]?[0-9]{8,15}$
  "delivery_notes": "optionnel",
  "coupon_code": "optionnel"
}
```

- ⚠️ `payment_method` et `coupon_code` sont **validés mais jamais enregistrés** par `OrderService::create`.
- Prix total = `item.price × quantity` (aucune remise appliquée à ce stade).
- Clés `delivery_*` mappées vers `shipping_*` (fallback avec les préfixes `shipping_`/`delivery_`).
- `delivery_address_id` (nullable) accepté si l'adresse vient de la nouvelle API adresses.

### Structure de l'objet order

```
order_number (auto: ORD-YYYY-XXXXXXXX), buyer_id, seller_id, item_id,
quantity, unit_price, total_amount, currency (USD|FC), status,
shipping_address, shipping_city, shipping_phone, notes,
delivery_address_id, paid_at, shipped_at, delivered_at,
confirmed_by_buyer_at, buyer_confirmation_note, scan_token,
relations: item, buyer, seller, deliveryAddress
```

### Paiements — `app/Http/Controllers/Api/Payments/PaymentController.php`

| Méthode | URL | Description |
|---|---|---|
| GET | `/api/v1/payments` | Historique des paiements |
| GET | `/api/v1/payments/{transactionId}` | Détail d'un paiement |
| GET | `/api/v1/payments/stats` | Statistiques |
| POST | `/api/v1/payments/initiate` | Initier un paiement mobile money. Body : `provider` (`orange_money|mpesa|airtel_money|africell|illicocash`), `amount`, `phone`, `purpose`, `currency` (`USD|CDF`). Renvoie `status: pending` |
| POST | `/api/v1/payments/maishapay` | Paiement MaishaPay. Body : `amount`, `phone`, `currency`, `operator` (`VODACOM|AIRTEL|ORANGE|AFRICELL`), `purpose` |
| GET | `/api/v1/payments/maishapay/status/{transactionId}` | Statut d'un paiement MaishaPay (complète automatiquement la transaction et crée les commandes) |
| POST | `/api/v1/payments/refund/{orderId}` | Demander un remboursement. Body : `reason` (min 10), `refund_type` (`partial|full`), `refund_amount`, `evidence_photos[]` |
| GET | `/api/v1/payments/refund/{refundId}/status` | Statut d'un remboursement |

**Éligibilité remboursement** : `confirmed_by_buyer_at` présent, aucun remboursement existant, et < 30 jours après confirmation.

### Callbacks opérateurs (publics, sans auth)

- `POST /api/payment-callbacks/{provider}` — callback mobile money
- `GET /api/payment-callbacks/status` — polling statut
- `POST /api/payment-callbacks/{transaction}/force-complete` — complétion manuelle
- `POST /api/v1/pawapay/callback/{deposit|checkout|payout|refund}` — callbacks PawaPay

---

## 6. Nouveaux endpoints mobile

Endpoints ajoutés pour compléter le parcours mobile (tous sous `auth:sanctum`).

### 6.1 Panier — `app/Http/Controllers/Api/Cart/CartController.php`

Le panier mobile utilise une clé stable `api-{userId}` (pas de session web). La logique réplique le panier web : contrôle du stock, interdiction d'acheter son propre article, remises `Discount` actives appliquées.

| Méthode | URL | Description |
|---|---|---|
| GET | `/api/v1/cart` | Contenu du panier |
| GET | `/api/v1/cart/summary` | Sous-total + frais transport + total |
| POST | `/api/v1/cart` | Ajouter un article. Body : `item_id` (obligatoire), `quantity` (1..100) |
| PUT | `/api/v1/cart/{itemId}` | Mettre à jour la quantité. Body : `quantity` |
| DELETE | `/api/v1/cart/{itemId}` | Retirer un article |
| DELETE | `/api/v1/cart` | Vider le panier |

**Exemple de réponse `/api/v1/cart/summary`** :

```jsonc
{
  "success": true,
  "message": "Résumé du panier",
  "data": {
    "items_count": 1,
    "items_quantity": 3,
    "subtotal": 300,
    "transport_fee_percentage": 5,
    "transport_fee": 15,
    "total": 315,
    "currency": "USD"
  }
}
```

**Exemple d'article du panier** :

```jsonc
{
  "item_id": 5,
  "name": "Article",
  "price": 90,
  "currency": "USD",
  "quantity": 2,
  "image": "path/to/image.jpg",
  "original_price": 100,        // si remise active
  "discount_id": 12,            // si remise active
  "discount_percentage": 10,    // si remise active
  "has_discount": true          // si remise active
}
```

### 6.2 Adresses de livraison — `app/Http/Controllers/Api/DeliveryAddress/DeliveryAddressController.php`

CRUD complet. La première adresse créée devient automatiquement l'adresse par défaut.

| Méthode | URL | Description |
|---|---|---|
| GET | `/api/v1/delivery-addresses` | Liste des adresses (défaut en premier) |
| POST | `/api/v1/delivery-addresses` | Créer une adresse |
| GET | `/api/v1/delivery-addresses/{id}` | Détail d'une adresse |
| PUT | `/api/v1/delivery-addresses/{id}` | Mettre à jour une adresse |
| DELETE | `/api/v1/delivery-addresses/{id}` | Supprimer une adresse (une autre devient défaut si nécessaire) |
| POST | `/api/v1/delivery-addresses/{id}/default` | Définir comme adresse par défaut |

**Body (création / mise à jour)** :

```jsonc
{
  "full_name": "John Doe",           // obligatoire
  "phone": "+243812345678",          // obligatoire
  "email": "john@example.com",       // obligatoire
  "city": "Kinshasa",                // obligatoire
  "commune": "Gombe",                // obligatoire
  "address": "Av. de la Paix 12",    // obligatoire
  "latitude": -4.325,                // optionnel (-90..90)
  "longitude": 15.308,               // optionnel (-180..180)
  "notes": "optionnel",
  "is_default": true                 // optionnel, boolean
}
```

### 6.3 Annulation de commande

Ajoutée à `Api/Orders/OrderController.php:253` :

- `DELETE /api/v1/orders/{id}` → `OrderService::cancel()`. Acheteur uniquement, statut `pending` uniquement. Restaure le stock, supprime la commande, renvoie `success: true`.

---

## 7. Suivi des sessions mobile & utilisateurs en ligne

Le serveur trace automatiquement l'activité des utilisateurs authentifiés (web **et** mobile) via le middleware `App\Http\Middleware\TrackUserSession`, appliqué aux groupes `web` **et** `api` (`bootstrap/app.php`).

### Comment ça marche

- **Web** : identifiant de session = `session_id` PHP (cookie).
- **Mobile (API Sanctum)** : identifiant = `sanctum-{tokenId}` (une session par token/appareil). Aucune session PHP requise — le mobile est donc tracé sans aucune action de sa part.
- À chaque requête authentifiée : `upsert` dans `user_sessions` (colonne `session_id` unique) avec `ip_address`, `device_type`, `browser`, `os`, `user_agent`, `last_activity = now()`, `is_active = true`, **et** mise à jour de `users.last_seen` (alimente `User::isOnline()` et les stats 7 jours).
- Géolocalisation (latitude/longitude, ville, pays) récupérée **une seule fois par session** (IP publique) via `ip-api.com` — les IP locales ne sont pas géolocalisées.

### Définition « en ligne »

`UserSession::getActiveSessions()` : `is_active = true` **et** `last_activity >= now() - 5 minutes`.

### Déconnexion

- `POST /api/logout` → le token Sanctum courant est révoqué **et** la session `sanctum-{tokenId}` est marquée inactive (`is_active = false`, `logout_at`).
- Logout web (`AuthService::logout`) → même marquage pour la session PHP.

### Endpoint admin — `GET /api/v1/admin/online-users`

`App\Http\Controllers\Admin\AdminController@apiOnlineUsers` — middleware `auth:sanctum,web` + `admin`.

**Réponse** :

```jsonc
{
  "success": true,
  "message": "Utilisateurs connectés",
  "data": {
    "users": [
      {
        "user_id": 1,
        "user_name": "John Doe",
        "user_email": "john@example.com",
        "device_type": "mobile",          // mobile | tablet | desktop
        "device_icon": "fa-mobile-alt",
        "browser": "Chrome",
        "os": "Android",
        "last_activity": "2026-08-13T09:39:00+00:00",   // ISO 8601
        "ip_address": "154.70.0.1",
        "location": "Kinshasa, République démocratique du Congo"
      }
    ],
    "stats": {
      "total_online": 1,    // nombre de sessions actives (< 5 min)
      "unique_users": 1     // nombre d'utilisateurs distincts
    }
  }
}
```

> Le mobile n'a rien à implémenter pour apparaître « en ligne » : chaque appel API authentifié met à jour la session automatiquement. Le détail est aussi visible côté web admin sur `/admin/users/online` (liste temps réel, carte Leaflet, stats par appareil/rôle, déconnexion forcée).

---

## 8. Détails métier à reproduire côté mobile

### Notifications

Chaque transition déclenche un broadcast `OrderNotification` (channel privé `user.{userId}`, événement `order.notification`) + notification en base. Types : `new_order`, `payment_confirmed`, `order_shipped`, `order_delivered`, `completed`. Le mobile peut écouter via Pusher/Reverb ou interroger `GET /api/v1/notifications`.

### Livraison locale (`LocalDeliveryController`)

- Cycle : `proposed → accepted → in_transit → delivered/cancelled`
- Le vendeur propose une livraison (type `hand_delivery|pickup|meetup`, coordonnées GPS, distance max **50 km**)
- Tarif : 2 $ pour les 5 premiers km, puis 0,50 $/km
- Code de vérification 6 caractères fourni à l'acheteur ; le vendeur le saisit à la livraison → `order.status = delivered`
- ⚠️ **Aucun endpoint API** : uniquement des routes web JSON-friendly. À exposer si besoin.

### QR code

`scan_token` auto-généré (32 caractères) + `scan_url`. Permet de confirmer la réception d'une commande par scan. Utile pour la fonctionnalité mobile « confirmer réception en scannant ».

### Timeline d'une commande

Reconstruisible depuis les timestamps : `created_at` → `paid_at` → `shipped_at` → `delivered_at` → `confirmed_by_buyer_at` (voir `resources/views/orders/show.blade.php`).

### Points de vigilance

1. `distributeFunds()` échoue silencieusement si le wallet pending du vendeur est vide — prévoir l'état « fonds non distribués ».
2. `payment_method` et `coupon_code` validés mais non persistés par `OrderService::create`.
3. Le prix de la commande = prix plein de l'article ; les remises `Discount` s'appliquent au **panier** (web) et ne sont pas répercutées dans `OrderService::create`.

---

## 9. Résultats des tests

Exécutés via `php artisan test --filter='ApiOrdersTest|ApiCartTest|ApiDeliveryAddressTest'` (DB de test `vintapp_test`, `RefreshDatabase`).

```
PASS Tests\Feature\ApiOrdersTest
  ✓ user can list his orders
  ✓ user cannot see someone elses order
  ✓ buyer can cancel pending order
  ✓ only buyer can cancel order
  ✓ confirmed order cannot be cancelled

PASS Tests\Feature\ApiCartTest
  ✓ authenticated user can add item to cart
  ✓ user cannot add own item to cart
  ✓ quantity cannot exceed stock
  ✓ user can list and summarize cart
  ✓ user can update and remove cart item
  ✓ user can clear cart
  ✓ guest cannot access cart

PASS Tests\Feature\ApiDeliveryAddressTest
  ✓ user can create and list delivery addresses
  ✓ first address becomes default
  ✓ user cannot see others address
  ✓ user can update address
  ✓ user can set default and delete address
  ✓ validation fails without required fields
  ✓ guest cannot access delivery addresses

Tests: 19 passed (62 assertions)
```

`OrderServiceTest` (unitaire) : 6 tests passés — cycle de vie complet et distribution des fonds intacts.

`ApiAdminTest` : 8 tests passés — dont le tracking mobile (`sanctum-{tokenId}` créée dans `user_sessions`, `last_seen` mis à jour), le logout qui marque la session inactive, et la structure de `GET /api/v1/admin/online-users`.

---

## 10. Pistes d'amélioration

- **Persister `payment_method`** : soit l'ajouter au modèle Order, soit le stocker dans la Transaction du paiement.
- **Coupons/remises à l'achat direct** : gérer `coupon_code` dans `OrderService::create` (actuellement ignoré).
- **Exposer l'API livraison locale** : routes `local-delivery/*` en API pour le mobile (proposer, accepter, transit, livré avec code).
- **API `OrderTracking`** : le modèle existe mais aucun endpoint de suivi GPS n'est exposé.
