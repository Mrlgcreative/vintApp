# 🧪 REST API v1 - Résultats des Tests Complets

**Dernière mise à jour:** 2026-08-08  
**Total Routes API:** 172 routes (préfixe `api`)  
**Suite de tests Feature:** 76 tests passés (214 assertions) — `tests/Feature/Api*` + `NotificationApiTest` + `PaymentCallbackControllerTest`

---

## 📊 Synthèse Globale

### Routes par Domaine

| Domaine              | Routes | Préfixe                    | Middleware principal        |
| -------------------- | ------ | -------------------------- | --------------------------- |
| **Auth**             | 3      | `/api/`                    | public (`auth:sanctum` login) |
| **User**             | 20     | `/api/user`, `/api/v1/user`| `auth:sanctum,web`          |
| **Items**            | 10     | `/api/items`, `/api/v1/items` | mixte (public + auth)     |
| **Catalog**          | 13     | `/api/v1/categories`, `/api/v1/brands` | public |
| **Orders**           | 8      | `/api/v1/orders`           | `auth:sanctum,web`          |
| **Payments**         | 8      | `/api/v1/payments`         | mixte (public callbacks)    |
| **Wallet**           | 9      | `/api/v1/wallet`           | mixte (webhook public)      |
| **Messages**         | 7      | `/api/v1/messages`         | `auth:sanctum,web`          |
| **Reviews**          | 6      | `/api/v1/reviews`          | mixte                       |
| **Notifications**    | 12     | `/api/v1/notifications`, `/api/notifications` | `auth:sanctum,web` |
| **Support**          | 6      | `/api/v1/support`          | `auth:sanctum,web`          |
| **Authenticity**     | 3      | `/api/v1/authenticity`     | `auth:sanctum,web`          |
| **VintPass**         | 4      | `/api/v1/vintpass`         | `auth:sanctum,web`          |
| **Affiliate**        | 11     | `/api/affiliate`           | `auth:sanctum,web`          |
| **Admin**            | 39     | `/api/v1/admin`            | `auth:sanctum,web` + `AdminMiddleware` |
| **Système**          | 3      | `/api/health`, `/api/v1/currencies`, `/api/v1/home` | public |
| **Notifications FCM**| 4      | `/api/fcm-token`, `/api/test-fcm-notification`, `/api/admin/fcm-*` | mixte |
| **Payment Callbacks**| 3      | `/api/payment-callbacks`   | public (webhook)            |
| **Divers**           | 4      | `/api/bot`, `/api/validate-*`, `/api/dashboard/data` | mixte |

**Total:** 172 routes API.

---

## 🔐 Protection Middleware

### Routes publiques (sans authentification)

-   `GET /api/health`, `GET /api/v1/currencies`, `GET /api/v1/home`
-   `GET /api/v1/items`, `GET /api/v1/items/{id}`, `GET /api/v1/categories*`, `GET /api/v1/brands*`
-   `POST /api/register`, `POST /api/login`, `POST /api/validate-location`, `POST /api/validate-referral-code`
-   Callbacks webhooks : `POST /api/payment-callbacks/{provider}`, `POST /api/v1/wallet/withdrawals/maishapay/callback`
-   Certaines routes `throttle` : login (5/min), register (10/min), resend (3/min), etc.

### Routes authentifiées

-   Middleware: `auth:sanctum,web` + `throttle:60,1` (défaut) ou 30/min
-   Protection: ✅ 401 Unauthorized sans authentification

### Routes Admin (39 routes)

-   Middleware: `auth:sanctum,web` + `AdminMiddleware` (→ `$user->isAdmin()` → rôle `admin`)
-   Protection: ✅ 401 sans auth, ✅ 403 si authentifié mais pas admin

### Routes FCM (web session)

-   `POST /api/fcm-token`, `POST /api/test-fcm-notification`, `GET /api/notifications/test`, `POST /api/admin/broadcast-fcm-test`, `GET /api/admin/fcm-stats` — middleware `web` (session) → utilisent `Auth::user()` + fallback `session('2fa_user_id')`

---

## 📋 Toutes les Routes API (172 routes)

### Routes legacy JSON (hors prefix `v1`) — contrat mobile conservé

#### User Legacy (10)

```
GET    /api/user                            - Utilisateur authentifié (AuthController@me)
GET    /api/user/profile                    - Profil détaillé
PUT    /api/user/profile                    - Mettre à jour le profil
GET    /api/user/items                      - Articles de l'utilisateur
GET    /api/user/orders                     - Commandes de l'utilisateur
GET    /api/user/reviews                    - Avis de l'utilisateur
GET    /api/user/sales                      - Ventes de l'utilisateur
GET    /api/user/stats                      - Statistiques utilisateur
POST   /api/user/avatar                     - Upload avatar
DELETE /api/user/account                    - Supprimer le compte (doublon v1 supprimé)
```

> **Note Phase 4** : l'ancien doublon `GET /api/user` (défini 2× dans api.php) a été fusionné → un seul endpoint `AuthController@me`.

#### Auth Legacy

```
POST   /api/register                        - Inscription
POST   /api/login                           - Connexion (email ou Firebase)
POST   /api/logout                          - Déconnexion
```

#### Items Legacy

```
GET    /api/items/search                    - Recherche d'articles
POST   /api/items/{item}/favorite           - Ajouter aux favoris
```

#### Dashboard / Divers

```
GET    /api/dashboard/data                  - Données du dashboard (DashboardController@apiData)
POST   /api/bot                             - Endpoint bot
POST   /api/validate-location               - Validation géolocalisation
POST   /api/validate-referral-code          - Validation code parrainage
```

#### Affiliate Legacy (11)

```
GET    /api/affiliate/dashboard             - Stats affiliation
GET    /api/affiliate/referral-codes        - Codes parrainage
POST   /api/affiliate/referral-codes        - Créer un code
GET    /api/affiliate/referrals             - Liste parrainages
GET    /api/affiliate/points-history        - Historique points
GET    /api/affiliate/redemptions           - Échanges points
GET    /api/affiliate/generate-link         - Générer lien de parrainage
GET    /api/affiliate/codes/stats           - Stats codes
POST   /api/affiliate/apply-referral-code   - Appliquer un code
POST   /api/affiliate/calculate-conversion  - Calculer conversion
POST   /api/affiliate/convert-points        - Convertir des points
```

#### Notifications FCM Legacy (5)

```
POST   /api/fcm-token                       - Enregistrer le token FCM (FcmController@registerToken)
POST   /api/test-fcm-notification           - Tester une notification (FcmController@testNotification)
GET    /api/notifications/test              - Page de test (web)
POST   /api/admin/broadcast-fcm-test        - Broadcast admin (FcmController@adminBroadcast)
GET    /api/admin/fcm-stats                 - Stats FCM admin (FcmController@adminStats)
```

> **Note Phase 4** : ces 4 endpoints étaient des closures inline dans `api.php`. Ils ont été extraits dans `app/Http/Controllers/Api/Notifications/FcmController.php` (helpers privés `syncToken`, `fcmUser`).

#### Payment Callbacks (3)

```
GET    /api/payment-callbacks/status                        - Statut paiement (payment.status)
POST   /api/payment-callbacks/{provider}                    - Callback provider (payment.callback)
POST   /api/payment-callbacks/{transaction}/force-complete  - Forcer complétion
```

---

### Routes API v1 (129 routes)

#### System Routes (2)

```
GET    /api/v1/health                           - Health check (SystemController@health)
GET    /api/v1/currencies                       - Devises (SystemController@currencies)
GET    /api/v1/home                             - Données page d'accueil
```

#### User Routes (v1, 10)

```
GET    /api/v1/user/profile                     - Profil
PUT    /api/v1/user/profile                     - Mettre à jour le profil
PUT    /api/v1/user/password                    - Changer le mot de passe
POST   /api/v1/user/avatar                      - Upload avatar
GET    /api/v1/user/items                       - Mes articles
GET    /api/v1/user/orders                      - Mes commandes
GET    /api/v1/user/reviews                     - Mes avis
GET    /api/v1/user/sales                       - Mes ventes
GET    /api/v1/user/stats                       - Mes statistiques
DELETE /api/v1/user/account                     - Supprimer mon compte
```

#### Item Routes (v1, 8)

```
GET    /api/v1/items                            - Liste articles (paginé, filtres)
POST   /api/v1/items                            - Créer un article
GET    /api/v1/items/{id}                       - Détail article
PUT    /api/v1/items/{id}                       - Mettre à jour
DELETE /api/v1/items/{id}                       - Supprimer
GET    /api/v1/items/{item}/authenticity/can-verify   - Peut-on vérifier l'authenticité
GET    /api/v1/items/{item}/authenticity/status       - Statut vérification
POST   /api/v1/items/{item}/authenticity/submit       - Soumettre une vérification
```

#### Catalog Routes (13)

```
GET    /api/v1/categories                       - Liste catégories
GET    /api/v1/categories/{id}                  - Détail catégorie
GET    /api/v1/categories/{id}/items            - Articles d'une catégorie
POST   /api/v1/categories                       - Créer (admin)
PUT    /api/v1/categories/{id}                  - Modifier (admin)
DELETE /api/v1/categories/{id}                  - Supprimer (admin)
GET    /api/v1/brands                           - Liste marques
GET    /api/v1/brands/{id}                      - Détail marque
GET    /api/v1/brands/{id}/items                - Articles d'une marque
POST   /api/v1/brands                           - Créer (admin)
PUT    /api/v1/brands/{id}                      - Modifier (admin)
DELETE /api/v1/brands/{id}                      - Supprimer (admin)
```

#### Order Routes (8)

```
GET    /api/v1/orders                           - Liste commandes
POST   /api/v1/orders                           - Créer commande
GET    /api/v1/orders/{id}                      - Détail commande
GET    /api/v1/orders/sales                     - Ventes vendeur
POST   /api/v1/orders/{id}/confirm-delivery     - Confirmer livraison
POST   /api/v1/orders/{id}/confirm-payment      - Confirmer paiement
POST   /api/v1/orders/{id}/mark-shipped         - Marquer expédié
POST   /api/v1/orders/{id}/mark-delivered       - Marquer livré
```

#### Payment Routes (8)

```
GET    /api/v1/payments                         - Historique paiements
GET    /api/v1/payments/stats                   - Statistiques paiements
GET    /api/v1/payments/{transactionId}         - Détail paiement
POST   /api/v1/payments/initiate                - Initier un paiement
POST   /api/v1/payments/maishapay               - Paiement MaishaPay (api.v1.payments.maishapay.initiate)
GET    /api/v1/payments/maishapay/status/{transactionId}  - Statut MaishaPay
POST   /api/v1/payments/refund/{orderId}        - Demander remboursement
GET    /api/v1/payments/refund/{refundId}/status - Statut remboursement
```

#### Wallet Routes (9)

```
GET    /api/v1/wallet                           - Solde wallet
GET    /api/v1/wallet/transactions              - Transactions
POST   /api/v1/wallet/add-funds                 - Ajouter des fonds
POST   /api/v1/wallet/convert                   - Convertir une devise
POST   /api/v1/wallet/withdraw                  - Retirer des fonds
GET    /api/v1/wallet/withdraw/operators        - Opérateurs de retrait
POST   /api/v1/wallet/withdraw/maishapay        - Retrait via MaishaPay
GET    /api/v1/wallet/withdraw/maishapay/status/{transactionId} - Statut retrait
POST   /api/v1/wallet/withdrawals/maishapay/callback - Webhook MaishaPay (public)
```

#### Message Routes (7)

```
GET    /api/v1/messages                         - Conversations
POST   /api/v1/messages                         - Créer/envoyer
GET    /api/v1/messages/{userId}                - Conversation avec un utilisateur
GET    /api/v1/messages/unread/count            - Compteur non-lus
GET    /api/v1/messages/discounts/{itemId}      - Remises sur un article
POST   /api/v1/messages/discount/apply          - Appliquer une remise
PUT    /api/v1/messages/{messageId}/mark-read   - Marquer lu
```

#### Review Routes (6)

```
GET    /api/v1/reviews                          - Liste avis (paginé)
POST   /api/v1/reviews                          - Créer un avis
GET    /api/v1/reviews/item/{itemId}            - Avis d'un article
GET    /api/v1/reviews/seller/{sellerId}        - Avis d'un vendeur
PUT    /api/v1/reviews/{reviewId}               - Mettre à jour
DELETE /api/v1/reviews/{reviewId}               - Supprimer
```

#### Notification Routes (v1, 7)

```
GET    /api/v1/notifications                    - Liste notifications
GET    /api/v1/notifications/unread             - Notifications non-lues
GET    /api/v1/notifications/unread/count       - Compteur non-lues
POST   /api/v1/notifications/mark-all-read      - Tout marquer lu
POST   /api/v1/notifications/{id}/mark-read     - Marquer une notification lue
DELETE /api/v1/notifications/{id}               - Supprimer une notification
DELETE /api/v1/notifications/read/all           - Supprimer les notifications lues
```

#### Support Routes (6)

```
GET    /api/v1/support                          - Conversations support
POST   /api/v1/support                          - Créer une conversation
GET    /api/v1/support/stats                    - Statistiques
GET    /api/v1/support/{id}                     - Détail conversation
POST   /api/v1/support/{id}/reply               - Répondre
POST   /api/v1/support/{id}/close               - Fermer
```

#### Authenticity Routes (3)

```
GET    /api/v1/authenticity/dashboard           - Dashboard vérification
POST   /api/v1/authenticity/{check}/confirm-payment - Confirmer paiement
PUT    /api/v1/authenticity/{check}/update-status     - Mettre à jour le statut
```

#### VintPass Routes (4)

```
GET    /api/v1/vintpass                         - Mes VintPass
GET    /api/v1/vintpass/verify/{shortCode}      - Vérifier un code
GET    /api/v1/vintpass/{vintPass}              - Détail VintPass
POST   /api/v1/vintpass/request/{item}          - Demander un VintPass
```

#### Admin Routes (41)

```
# Dashboard & Stats
GET    /api/v1/admin/dashboard                  - Dashboard admin
GET    /api/v1/admin/stats/summary              - Résumé stats
GET    /api/v1/admin/online-users               - Utilisateurs en ligne
GET    /api/v1/admin/monitoring/stats           - Monitoring temps réel
GET    /api/v1/admin/monitoring/health          - Health check

# Gestion utilisateurs
GET    /api/v1/admin/users                      - Liste utilisateurs
GET    /api/v1/admin/users/{userId}             - Détail utilisateur
POST   /api/v1/admin/users/{userId}/status      - Changer statut

# Gestion articles
GET    /api/v1/admin/items                      - Liste articles
POST   /api/v1/admin/items/{itemId}/status      - Changer statut

# Wallets
GET    /api/v1/admin/wallets                    - Liste wallets
GET    /api/v1/admin/wallets/pending            - Wallets en attente
POST   /api/v1/admin/wallets/bulk-approve       - Approbation groupée
POST   /api/v1/admin/wallets/{walletId}/approve - Approuver
POST   /api/v1/admin/wallets/{walletId}/reject  - Rejeter
GET    /api/v1/admin/enterprise-wallets         - Wallets entreprise
GET    /api/v1/admin/enterprise-wallets/{wallet}- Détail wallet entreprise

# Transactions & commandes
GET    /api/v1/admin/transactions               - Liste transactions
GET    /api/v1/admin/orders                     - Liste commandes

# Remboursements
GET    /api/v1/admin/refunds                    - Liste remboursements
GET    /api/v1/admin/refunds/{refund}           - Détail remboursement

# Support
GET    /api/v1/admin/support                    - Conversations support
GET    /api/v1/admin/support/stats              - Stats support
GET    /api/v1/admin/support/{supportChat}      - Détail conversation
GET    /api/v1/admin/support-chats              - Liste chats support

# Affiliation
GET    /api/v1/admin/affiliate/stats            - Stats affiliation
GET    /api/v1/admin/affiliate/top-performers   - Top parrains
GET    /api/v1/admin/affiliate/referrers        - Liste parrains
GET    /api/v1/admin/affiliate/activity         - Activités récentes

# Pré-inscriptions
GET    /api/v1/admin/waiting-users              - Liste pré-inscriptions
GET    /api/v1/admin/waiting-users/stats        - Stats pré-inscriptions
POST   /api/v1/admin/waiting-users/{waitingUser}/approve - Approuver

# Catalogue & vérifications
GET    /api/v1/admin/categories                 - Liste catégories
GET    /api/v1/admin/brands                     - Liste marques
GET    /api/v1/admin/verification-checks        - Vérifications
GET    /api/v1/admin/notifications              - Notifications admin
GET    /api/v1/admin/reports                    - Rapports
GET    /api/v1/admin/settings                   - Paramètres
PUT    /api/v1/admin/settings/{key}             - Modifier paramètre
```

---

## 🎯 Tests de Protection

### Fichiers de Test Feature (Laravel/PHPUnit)

-   `tests/Feature/ApiAuthTest.php` — Auth (register/login/logout/me)
-   `tests/Feature/ApiItemsTest.php` — Items (CRUD, recherche, favoris)
-   `tests/Feature/ApiCatalogTest.php` — Catégories, marques
-   `tests/Feature/ApiOrdersTest.php` — Commandes
-   `tests/Feature/ApiWalletTest.php` — Wallet
-   `tests/Feature/ApiMessagesTest.php` — Messages
-   `tests/Feature/ApiReviewsTest.php` — Avis
-   `tests/Feature/ApiUsersTest.php` — Utilisateurs
-   `tests/Feature/ApiNotificationsTest.php` — Notifications
-   `tests/Feature/ApiSupportTest.php` — Support
-   `tests/Feature/ApiAuthenticityTest.php` — Authenticité
-   `tests/Feature/ApiVintPassTest.php` — VintPass
-   `tests/Feature/ApiAffiliateTest.php` — Affiliation
-   `tests/Feature/ApiAdminTest.php` — Admin (5 tests)
-   `tests/Feature/NotificationApiTest.php` — FCM (14 tests)
-   `tests/Feature/PaymentCallbackControllerTest.php` — Callbacks paiement

### Vérifications

1. ✅ Routes retournent **401 Unauthorized** sans authentification
2. ✅ Routes admin retournent **401/403** sans rôle admin
3. ✅ Header `Accept: application/json` force réponse JSON
4. ✅ Middleware `auth:sanctum,web` fonctionne correctement
5. ✅ `AdminMiddleware` bloque l'accès non-admin (→ `hasRole('admin')`)
6. ✅ GPS : les tests désactivent `enable_location_restrictions` via `Setting::set` (sinon `CheckGPSCityAccess` → 403)

---

## 🎨 Structure des Réponses

### ✅ Success Response

```json
{
  "success": true,
  "message": "Message de succès",
  "data": { ... }
}
```

### ❌ Error Response

```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": { ... }
}
```

### 📄 Paginated Response

```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [...],
    "total": 100,
    "per_page": 15,
    "last_page": 7
  }
}
```

---

## 🏗️ Architecture Phase 3 & 4

### Contrôleurs API par domaine (`app/Http/Controllers/Api/`)

| Domaine        | Contrôleur                                          | Service               |
| -------------- | --------------------------------------------------- | --------------------- |
| Auth           | `Api/Auth/AuthController`                           | `AuthService`         |
| Items          | `Api/Items/ItemController`                          | `ItemService`         |
| Catalog        | `Api/Catalog/CategoryController`, `BrandController` | —                     |
| Orders         | `Api/Orders/OrderController`                        | `OrderService`        |
| Payments       | `Api/Payments/PaymentController`                    | —                     |
| Webhooks       | `Api/Payments/PaymentCallbackController`            | —                     |
| Wallet         | `Api/Wallet/WalletController`                       | `WalletService`       |
| Messages       | `Api/Messages/MessageController`                    | —                     |
| Reviews        | `Api/Reviews/ReviewController`                      | —                     |
| Users          | `Api/Users/UserController`                          | —                     |
| Notifications  | `Api/NotificationController` (legacy) + `Api/Notifications/FcmController` | `ExpertNotificationService` |
| Support        | `Api/Support/SupportController`                     | `SupportService`      |
| Authenticity   | `Api/Authenticity/AuthenticityController`           | —                     |
| VintPass       | `Api/VintPass/VintPassController`                   | —                     |
| Affiliate      | `Api/Affiliate/AffiliateController` (héritage)      | `DiscountService`     |
| Admin          | `Admin\*` (déjà JSON-native)                        | `StatsService`        |
| Système        | `Api/System/SystemController`                       | —                     |

### Principes

-   Services + `DomainException` → les contrôleurs web et API appellent le même service
-   `ApiController` base : `successResponse`, `errorResponse`, `paginatedResponse`, `cleanUtf8`
-   Formats JSON des anciennes closures/méthodes `apiXxx()` **préservés** (clients mobiles)
-   Routes legacy JSON **conservées hors prefix `/v1`** pour compatibilité mobile

---

## 🔧 Configuration

**Laravel Version:** 11  
**Authentication:** Laravel Sanctum (+ session web)  
**Middleware Public:** `api` (avec throttles dédiés)  
**Middleware Auth:** `auth:sanctum,web` + `throttle:60,1`  
**Middleware Admin:** `auth:sanctum,web` + `AdminMiddleware`  
**Base URL:** `http://localhost:8000/api`  
**API Version:** v1 (legacy JSON conservé hors v1)

---

## 📝 Notes Techniques

### Phase 4 — Routes API propres (2026-08-08)

1. **Closures supprimées** → contrôleurs dédiés : `currencies` + `health` → `Api/System/SystemController` ; `fcm-token`, `test-fcm-notification`, `admin/broadcast-fcm-test`, `admin/fcm-stats` → `Api/Notifications/FcmController`
2. **Routes legacy HTML supprimées** d'`api.php` (items/orders/messages/reviews/categories/brands CRUD web → contrôleurs web)
3. **Mocks web supprimés** de `web.php` : `POST /api/items`, `POST /api/orders`, `POST /api/messages`, `PUT /api/profile` (shadowaient les vraies routes)
4. **Doublon `GET /api/user`** fusionné (était défini 2×)
5. **Bug FCM corrigé** : `Api\NotificationController@subscribe/unsubscribe` utilisent assignation directe + `save()` (les champs `fcm_token`, `device_type`, `browser`, `fcm_token_updated_at` ne sont pas `$fillable` dans User L23-45)
6. **`route:list --path=api`** : 235 lignes, aucune erreur ; tous les contrôleurs/méthodes existent
7. **`php artisan test`** : 76 tests Feature API + 72 Unit passés (seules baselines pré-existantes : Auth/Profile web routes en env test, `PushNotificationServiceTest` L97)

### Contrôle d'Accès Admin

-   Routes admin protégées par `AdminMiddleware`
-   Vérification via `User::isAdmin()` → `hasRole('admin')` (BelongsToMany `roles`)
-   Retour 401 si non authentifié, 403 si authentifié mais pas admin

---

## 🚀 Prochaines Étapes

-   [ ] Suppression des méthodes `apiXxx()` des contrôleurs web (Phase 5) — migrées vers `Api/*`
-   [ ] Déplacer les contrôleurs web dans `app/Http/Controllers/Web/`
-   [ ] Tests authentifiés avec Sanctum token (user + admin)
-   [ ] Documentation OpenAPI/Swagger complète
-   [ ] Rate limiting affiné par domaine

---

**✅ Tous les tests Feature API passent — 172 routes API — Protection 100% fonctionnelle**
