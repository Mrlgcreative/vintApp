# 📖 VintApp — Documentation API Complète

> **Version** : 1.0.0  
> **Base URL** : `https://your-domain.com/api`  
> **Dernière mise à jour** : Mars 2026

---

## Table des matières

1. [Informations générales](#1-informations-générales)
2. [Authentification](#2-authentification)
3. [Format des réponses](#3-format-des-réponses)
4. [Routes publiques](#4-routes-publiques)
5. [Utilisateurs](#5-utilisateurs)
6. [Articles (Items)](#6-articles-items)
7. [Commandes (Orders)](#7-commandes-orders)
8. [Messages](#8-messages)
9. [Avis (Reviews)](#9-avis-reviews)
10. [Portefeuille (Wallet)](#10-portefeuille-wallet)
11. [Paiements](#11-paiements)
12. [Notifications](#12-notifications)
13. [Support](#13-support)
14. [Catégories](#14-catégories)
15. [Marques (Brands)](#15-marques-brands)
16. [Vérification d'authenticité](#16-vérification-dauthenticité)
17. [VintPass](#17-vintpass)
18. [Programme d'affiliation](#18-programme-daffiliation)
19. [Dashboard](#19-dashboard)
20. [Chatbot](#20-chatbot)
21. [Notifications Push (FCM)](#21-notifications-push-fcm)
22. [Administration](#22-administration)
23. [Callbacks de paiement](#23-callbacks-de-paiement)
24. [Codes d'erreur](#24-codes-derreur)

---

## 1. Informations générales

### Base URL
```
Production : https://your-domain.com/api
```

### Headers requis
| Header | Valeur | Obligatoire |
|--------|--------|:-----------:|
| `Accept` | `application/json` | ✅ |
| `Content-Type` | `application/json` | ✅ (POST/PUT) |
| `Authorization` | `Bearer {token}` | ✅ (routes protégées) |

### Devises supportées
| Code | Nom | Symbole | Drapeau |
|------|-----|---------|---------|
| `USD` | Dollar américain | `$` | 🇺🇸 |
| `CDF` | Franc congolais | `FC` | 🇨🇩 |

> **Taux de conversion** : 1 USD = 2 500 CDF

### Rate Limiting
Les routes sont protégées par un rate limiter. Les limites sont indiquées par section.

| Ressource | Limite |
|-----------|--------|
| Lecture articles | 100 req/min |
| Écriture articles | 20 req/min |
| Utilisateurs | 60 req/min |
| Commandes | 40 req/min |
| Messages | 50 req/min |
| Avis | 20 req/min |
| Notifications | 60 req/min |
| Dashboard | 30 req/min |
| Affiliation | 30 req/min |
| Callbacks paiement | 100 req/min |

---

## 2. Authentification

L'API utilise **Laravel Sanctum** avec des tokens Bearer.

### Inscription

```
POST /api/register
```

**Body :**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Réponse `201` :**
```json
{
  "success": true,
  "message": "Inscription réussie",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": null
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

### Connexion

```
POST /api/login
```

**Body :**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Réponse `200` :**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": null,
    "email_verified_at": "2026-01-01T00:00:00.000Z",
    "role": "user"
  },
  "token": "2|xyz789...",
  "token_type": "Bearer"
}
```

### Déconnexion

```
POST /api/logout
```
> 🔒 Authentification requise

**Réponse `200` :**
```json
{
  "success": true,
  "message": "Déconnexion réussie"
}
```

### Utilisateur authentifié

```
GET /api/user
```
> 🔒 Authentification requise

**Réponse `200` :**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "avatar": "avatars/john.jpg",
    "email_verified_at": "2026-01-01T00:00:00.000Z",
    "role": "user"
  }
}
```

---

## 3. Format des réponses

Toutes les réponses API suivent un format standardisé via le trait `ApiResponses`.

### Réponse de succès
```json
{
  "success": true,
  "message": "Opération réussie",
  "data": { ... }
}
```

### Réponse paginée
```json
{
  "success": true,
  "message": "Liste récupérée",
  "data": [ ... ],
  "pagination": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7
  }
}
```

### Réponse d'erreur
```json
{
  "success": false,
  "message": "Description de l'erreur",
  "errors": {
    "field": ["Message d'erreur de validation"]
  }
}
```

---

## 4. Routes publiques

Ces routes sont accessibles **sans authentification**.

### Health Check

```
GET /api/health
```
> ⏱️ Cache : 60 secondes

**Réponse `200` :**
```json
{
  "status": "success",
  "message": "VintApp API is running",
  "version": "1.0.0",
  "timestamp": "2026-03-04T12:00:00.000Z"
}
```

### Page d'accueil

```
GET /api/v1/home
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "categories": [ ... ],
    "spotlight_items": [ ... ],
    "boosted_items": [ ... ],
    "latest_items": [ ... ],
    "stats": { ... },
    "hero_slides": [ ... ],
    "hero_settings": { ... }
  }
}
```

### Devises supportées

```
GET /api/v1/currencies
```
> ⏱️ Cache : 60 secondes

### Validation de localisation

```
POST /api/validate-location
```

**Body (option 1 — par ville) :**
```json
{
  "city": "Kinshasa"
}
```

**Body (option 2 — par coordonnées GPS) :**
```json
{
  "latitude": -4.3217,
  "longitude": 15.3127
}
```

**Réponse `200` :**
```json
{
  "success": true,
  "allowed": true,
  "city": "Kinshasa",
  "message": "Ville autorisée"
}
```

### Validation de code de parrainage

```
POST /api/validate-referral-code
```
> ⚡ Limite : 10 req/min

**Body :**
```json
{
  "code": "VINT-ABC123"
}
```

---

## 5. Utilisateurs

> 🔒 Toutes les routes nécessitent une authentification  
> ⚡ Limite : 60 req/min

### Profil utilisateur

```
GET /api/v1/user/profile
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+243...",
      "city": "Kinshasa",
      "bio": "...",
      "avatar": "avatars/john.jpg"
    },
    "stats": {
      "items_count": 12,
      "average_rating": 4.5
    }
  }
}
```

### Mettre à jour le profil

```
PUT /api/v1/user/profile
```

**Body :**
```json
{
  "name": "John Updated",
  "email": "john.new@example.com",
  "phone": "+243999000000",
  "city": "Lubumbashi",
  "bio": "Vendeur passionné"
}
```
> Tous les champs sont optionnels.

### Changer le mot de passe

```
PUT /api/v1/user/password
```

**Body :**
```json
{
  "current_password": "oldPassword123",
  "password": "newPassword456",
  "password_confirmation": "newPassword456"
}
```

### Uploader un avatar

```
POST /api/v1/user/avatar
```
> `Content-Type: multipart/form-data`

| Champ | Type | Max |
|-------|------|-----|
| `avatar` | image (jpeg, png, jpg) | 2 048 Ko |

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "avatar_url": "https://domain.com/storage/avatars/john.jpg"
  }
}
```

### Statistiques utilisateur

```
GET /api/v1/user/stats
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "items_count": 12,
    "sales_count": 8,
    "purchases_count": 5,
    "total_revenue": 150.00,
    "average_rating": 4.5,
    "favorites_count": 20
  }
}
```

### Articles de l'utilisateur

```
GET /api/v1/user/items?per_page=12
```

### Commandes de l'utilisateur

```
GET /api/v1/user/orders?per_page=10
```

### Ventes de l'utilisateur

```
GET /api/v1/user/sales?per_page=10
```

### Avis reçus

```
GET /api/v1/user/reviews?per_page=10
```

### Supprimer le compte

```
DELETE /api/v1/user/account
```

---

## 6. Articles (Items)

### Routes publiques (sans authentification)

> ⏱️ Cache : 60 secondes

#### Lister les articles

```
GET /api/v1/items
```

**Paramètres de requête :**
| Paramètre | Type | Description |
|-----------|------|-------------|
| `category_id` | integer | Filtrer par catégorie |
| `brand_id` | integer | Filtrer par marque |
| `min_price` | number | Prix minimum |
| `max_price` | number | Prix maximum |
| `condition` | string | État : `new`, `like_new`, `good`, `fair` |
| `search` | string | Recherche textuelle |
| `sort_by` | string | Champ de tri |
| `sort_order` | string | `asc` ou `desc` |
| `per_page` | integer | Résultats par page (défaut: 15) |

**Réponse `200` :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "iPhone 14 Pro",
      "description": "Excellent état...",
      "price": 800.00,
      "currency": "USD",
      "condition": "like_new",
      "category": { "id": 1, "name": "Téléphones" },
      "brand": { "id": 1, "name": "Apple" },
      "user": { "id": 1, "name": "John" },
      "images": ["items/iphone1.jpg"],
      "created_at": "2026-01-15T10:00:00Z"
    }
  ],
  "pagination": {
    "total": 150,
    "per_page": 15,
    "current_page": 1,
    "last_page": 10
  }
}
```

#### Détails d'un article

```
GET /api/v1/items/{id}
```

### Routes protégées (authentification requise)

> ⚡ Limite écriture : 20 req/min

#### Créer un article

```
POST /api/v1/items
```
> `Content-Type: multipart/form-data`

**Body :**
| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `name` | string | ✅ | Nom de l'article |
| `description` | string | ✅ | Description détaillée |
| `price` | number | ✅ | Prix de vente |
| `currency` | string | ✅ | `USD` ou `CDF` |
| `quantity` | integer | ✅ | Quantité disponible |
| `condition` | string | ✅ | `new`, `like_new`, `good`, `fair` |
| `category_id` | integer | ✅ | ID de la catégorie |
| `brand_id` | integer | ❌ | ID de la marque |
| `color` | string | ❌ | Couleur |
| `size` | string | ❌ | Taille |
| `images[]` | file[] | ❌ | Images de l'article |

> L'article est créé avec le statut `pending_verification` et doit être approuvé par un administrateur.

**Réponse `201` :**
```json
{
  "success": true,
  "message": "Article créé avec succès",
  "data": { ... }
}
```

#### Modifier un article

```
PUT /api/v1/items/{id}
```
> Propriétaire uniquement

#### Supprimer un article

```
DELETE /api/v1/items/{id}
```
> Propriétaire uniquement

#### Toggle favori

```
POST /api/items/{item}/favorite
```
> ⚡ Limite : 30 req/min

**Réponse `200` :**
```json
{
  "success": true,
  "message": "Article ajouté aux favoris",
  "is_favorite": true
}
```

#### Recherche

```
GET /api/items/search?q=iphone&category=1&min_price=100&max_price=1000&condition=new
```

---

## 7. Commandes (Orders)

> 🔒 Authentification requise  
> ⚡ Limite : 40 req/min

### Lister mes commandes (acheteur)

```
GET /api/v1/orders?per_page=15
```

### Lister mes ventes (vendeur)

```
GET /api/v1/orders/sales?per_page=15
```

### Créer une commande

```
POST /api/v1/orders
```

**Body :**
| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `item_id` | integer | ✅ | ID de l'article |
| `quantity` | integer | ✅ | Quantité |
| `shipping_address` | string | ✅ | Adresse de livraison |
| `shipping_city` | string | ✅ | Ville de livraison |
| `shipping_phone` | string | ✅ | Téléphone de livraison |
| `delivery_address_id` | integer | ❌ | Adresse enregistrée |
| `notes` | string | ❌ | Notes supplémentaires |

> Le stock est automatiquement décrémenté et le vendeur est notifié.

**Réponse `201` :**
```json
{
  "success": true,
  "data": {
    "id": 42,
    "status": "pending",
    "total": 800.00,
    "currency": "USD",
    "item": { ... },
    "buyer": { ... },
    "seller": { ... }
  }
}
```

### Détails d'une commande

```
GET /api/v1/orders/{id}
```
> Accessible uniquement par l'acheteur ou le vendeur de la commande.

### Confirmer le paiement

```
POST /api/v1/orders/{id}/confirm-payment
```

### Marquer comme expédiée

```
POST /api/v1/orders/{id}/mark-shipped
```

### Marquer comme livrée

```
POST /api/v1/orders/{id}/mark-delivered
```

### Confirmer la réception

```
POST /api/v1/orders/{id}/confirm-delivery
```

**Body optionnel :**
```json
{
  "note": "Produit en excellent état"
}
```

> Déclenche la distribution des fonds : montant net au vendeur, commission à la plateforme, frais de transport.

---

## 8. Messages

> 🔒 Authentification requise  
> ⚡ Limite : 50 req/min

### Liste des conversations

```
GET /api/v1/messages
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": [
    {
      "contact": { "id": 2, "name": "Jane", "avatar": "..." },
      "last_message": { "content": "Bonjour!", "created_at": "..." },
      "unread_count": 3
    }
  ]
}
```

### Messages d'une conversation

```
GET /api/v1/messages/{userId}
```
> Marque automatiquement les messages comme lus.

### Envoyer un message

```
POST /api/v1/messages
```
> `Content-Type: multipart/form-data` (si pièce jointe)

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `recipient_id` | integer | ✅ | ID du destinataire |
| `content` | string | ❌ | Texte du message |
| `attachment` | file | ❌ | Pièce jointe (max 10 Mo) |
| `item_id` | integer | ❌ | Article lié |

### Marquer un message comme lu

```
PUT /api/v1/messages/{messageId}/mark-read
```
> Destinataire uniquement.

### Nombre de messages non lus

```
GET /api/v1/messages/unread/count
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": { "count": 5 }
}
```

### Appliquer une réduction (vendeur)

```
POST /api/v1/messages/discount/apply
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `item_id` | integer | ✅ | ID de l'article |
| `buyer_id` | integer | ✅ | ID de l'acheteur |
| `discount_percentage` | integer | ✅ | Pourcentage (1-50) |
| `expires_hours` | integer | ❌ | Durée de validité en heures (1-168) |

### Réductions disponibles

```
GET /api/v1/messages/discounts/{itemId}
```

---

## 9. Avis (Reviews)

> 🔒 Authentification requise  
> ⚡ Limite : 20 req/min

### Tous les avis

```
GET /api/v1/reviews
```

### Avis d'un article

```
GET /api/v1/reviews/item/{itemId}
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "reviews": [ ... ],
    "average_rating": 4.3,
    "total_reviews": 12
  }
}
```

### Avis d'un vendeur

```
GET /api/v1/reviews/seller/{sellerId}
```

### Créer un avis

```
POST /api/v1/reviews
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `order_id` | integer | ✅ | ID de la commande |
| `rating` | integer | ✅ | Note de 1 à 5 |
| `comment` | string | ❌ | Commentaire |

**Réponse `201` :**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "rating": 5,
    "comment": "Excellent vendeur !",
    "reviewer": { ... },
    "order": { ... }
  }
}
```

### Modifier un avis

```
PUT /api/v1/reviews/{reviewId}
```
> Auteur uniquement.

### Supprimer un avis

```
DELETE /api/v1/reviews/{reviewId}
```
> Auteur uniquement.

---

## 10. Portefeuille (Wallet)

> 🔒 Authentification requise

### Consulter les soldes

```
GET /api/v1/wallet
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "usd_wallet": {
      "id": 1,
      "balance": 150.00,
      "currency": "USD"
    },
    "cdf_wallet": {
      "id": 2,
      "balance": 375000.00,
      "currency": "CDF"
    },
    "total_usd_equivalent": 300.00
  }
}
```

### Historique des transactions

```
GET /api/v1/wallet/transactions?per_page=15
```

### Recharger le portefeuille

```
POST /api/v1/wallet/add-funds
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `wallet_id` | integer | ✅ | ID du wallet |
| `amount` | number | ✅ | Montant (min: 1) |
| `payment_method` | string | ✅ | Opérateur mobile money |

**Opérateurs supportés :**
| Valeur | Opérateur |
|--------|-----------|
| `mpesa` | M-Pesa / Vodacom |
| `orange_money` | Orange Money |
| `airtel_money` | Airtel Money |
| `africell` | Africell |
| `illicocash` | Illicocash |

### Retirer des fonds

```
POST /api/v1/wallet/withdraw
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `wallet_id` | integer | ✅ | ID du wallet |
| `amount` | number | ✅ | Montant (min: 0.01) |
| `phone_number` | string | ✅ | Numéro (+243... ou 0...) |
| `payment_method` | string | ✅ | Opérateur mobile money |

### Retrait via MaishaPay

```
POST /api/v1/wallet/withdraw/maishapay
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `wallet_id` | integer | ✅ | ID du wallet |
| `amount` | number | ✅ | Montant (min: 100 CDF) |
| `phone_number` | string | ✅ | Numéro de téléphone |
| `operator` | string | ❌ | `VODACOM`, `ORANGE`, `AIRTEL`, `AFRICELL` (auto-détecté) |

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "withdrawal": { ... },
    "transaction": { ... },
    "maishapay_reference": "MP-xxx",
    "operator": "VODACOM"
  }
}
```

### Vérifier le statut d'un retrait MaishaPay

```
GET /api/v1/wallet/withdraw/maishapay/status/{transactionId}
```

### Opérateurs de payout disponibles

```
GET /api/v1/wallet/withdraw/operators
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "operators": [
      { "code": "VODACOM", "name": "Vodacom M-Pesa", "prefix": ["081", "082", "083"] }
    ],
    "maishapay_enabled": true,
    "country_code": "+243",
    "country": "RD Congo"
  }
}
```

### Convertir entre devises

```
POST /api/v1/wallet/convert
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `from_wallet_id` | integer | ✅ | Wallet source |
| `to_wallet_id` | integer | ✅ | Wallet destination |
| `amount` | number | ✅ | Montant (min: 0.01) |

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "from_currency": "USD",
    "to_currency": "CDF",
    "amount": 10.00,
    "converted_amount": 25000.00,
    "rate": 2500
  }
}
```

---

## 11. Paiements

> 🔒 Authentification requise

### Historique des paiements

```
GET /api/v1/payments?per_page=15
```

### Détails d'un paiement

```
GET /api/v1/payments/{transactionId}
```

### Statistiques de paiement

```
GET /api/v1/payments/stats
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "total_payments": 45,
    "successful_payments": 42,
    "total_amount": 5000.00,
    "pending_refunds": 1
  }
}
```

### Initier un paiement mobile money

```
POST /api/v1/payments/initiate
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `provider` | string | ✅ | `orange_money`, `mpesa`, `airtel_money`, `africell`, `illicocash` |
| `amount` | number | ✅ | Montant (min: 1) |
| `phone` | string | ✅ | Numéro de téléphone |
| `purpose` | string | ✅ | Raison du paiement |
| `currency` | string | ❌ | `USD` ou `CDF` (défaut: USD) |

### Paiement via MaishaPay

```
POST /api/v1/payments/maishapay
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `amount` | number | ✅ | Montant (min: 0.01) |
| `phone` | string | ✅ | Numéro de téléphone |
| `currency` | string | ❌ | `CDF` ou `USD` |
| `operator` | string | ❌ | Opérateur (auto-détecté) |
| `purpose` | string | ❌ | Raison du paiement |

### Vérifier le statut MaishaPay

```
GET /api/v1/payments/maishapay/status/{transactionId}
```

### Demander un remboursement

```
POST /api/v1/payments/refund/{orderId}
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `reason` | string | ✅ | Raison (min: 10 caractères) |
| `refund_type` | string | ✅ | `partial` ou `full` |
| `refund_amount` | number | ❌ | Montant (si partiel) |
| `evidence_photos[]` | file[] | ❌ | Photos de preuve |

> Le remboursement doit être demandé dans les **30 jours** suivant la commande.

### Statut d'un remboursement

```
GET /api/v1/payments/refund/{refundId}/status
```

---

## 12. Notifications

> 🔒 Authentification requise  
> ⚡ Limite : 60 req/min

### Liste des notifications

```
GET /api/v1/notifications?per_page=15
```

### Notifications non lues

```
GET /api/v1/notifications/unread?per_page=15
```

### Nombre de non lues

```
GET /api/v1/notifications/unread/count
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": { "count": 8 }
}
```

### Marquer une notification comme lue

```
POST /api/v1/notifications/{id}/mark-read
```

### Marquer toutes comme lues

```
POST /api/v1/notifications/mark-all-read
```

### Supprimer une notification

```
DELETE /api/v1/notifications/{id}
```

### Supprimer toutes les notifications lues

```
DELETE /api/v1/notifications/read/all
```

---

## 13. Support

> 🔒 Authentification requise

### Mes tickets

```
GET /api/v1/support?per_page=10
```

### Créer un ticket

```
POST /api/v1/support
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `subject` | string | ✅ | Sujet du ticket |
| `category` | string | ✅ | `technical`, `account`, `payment`, `order`, `general` |
| `message` | string | ✅ | Description du problème |
| `priority` | string | ❌ | `low`, `medium`, `high`, `urgent` |
| `attachments[]` | file[] | ❌ | Pièces jointes |

**Réponse `201` :**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "subject": "Problème de paiement",
    "category": "payment",
    "status": "open",
    "priority": "medium",
    "created_at": "2026-03-04T12:00:00Z"
  }
}
```

### Détails d'un ticket

```
GET /api/v1/support/{id}
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "chat": { ... },
    "messages": [ ... ]
  }
}
```

### Répondre à un ticket

```
POST /api/v1/support/{id}/reply
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `message` | string | ✅ | Contenu de la réponse |
| `attachments[]` | file[] | ❌ | Pièces jointes |

### Fermer un ticket

```
POST /api/v1/support/{id}/close
```

### Statistiques support

```
GET /api/v1/support/stats
```

---

## 14. Catégories

### Routes publiques (cache 60s)

```
GET /api/v1/categories
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Téléphones",
      "description": "Smartphones et accessoires",
      "icon": "phone",
      "items_count": 45,
      "children": [ ... ]
    }
  ]
}
```

### Détails d'une catégorie

```
GET /api/v1/categories/{id}
```

### Articles d'une catégorie

```
GET /api/v1/categories/{id}/items
```

| Paramètre | Type | Description |
|-----------|------|-------------|
| `brand_id` | integer | Filtrer par marque |
| `condition` | string | Filtrer par état |
| `price_min` | number | Prix minimum |
| `price_max` | number | Prix maximum |
| `sort` | string | Tri |
| `per_page` | integer | Résultats par page |

### Routes protégées (admin)

> 🔒 Authentification requise | ⚡ Limite : 20 req/min

```
POST   /api/v1/categories          → Créer
PUT    /api/v1/categories/{id}     → Modifier
DELETE /api/v1/categories/{id}     → Supprimer
```

---

## 15. Marques (Brands)

### Routes publiques (cache 60s)

```
GET /api/v1/brands
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Apple",
      "description": "...",
      "logo": "brands/apple.png",
      "items_count": 30
    }
  ]
}
```

### Détails d'une marque

```
GET /api/v1/brands/{id}
```

### Articles d'une marque

```
GET /api/v1/brands/{id}/items
```

| Paramètre | Type | Description |
|-----------|------|-------------|
| `category_id` | integer | Filtrer par catégorie |
| `condition` | string | Filtrer par état |
| `price_min` | number | Prix minimum |
| `price_max` | number | Prix maximum |
| `sort` | string | Tri |
| `per_page` | integer | Résultats par page |

### Routes protégées

> 🔒 Authentification requise | ⚡ Limite : 20 req/min

```
POST   /api/v1/brands          → Créer
PUT    /api/v1/brands/{id}     → Modifier
DELETE /api/v1/brands/{id}     → Supprimer
```

**Body (POST/PUT) :**
| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `name` | string | ✅ | Nom de la marque |
| `description` | string | ❌ | Description |
| `website` | string | ❌ | Site web |
| `logo` | file | ❌ | Logo |
| `country` | string | ❌ | Pays d'origine |
| `type` | string | ❌ | Type de marque |

---

## 16. Vérification d'authenticité

> 🔒 Authentification requise

### Vérifier l'éligibilité

```
GET /api/v1/items/{item}/authenticity/can-verify
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "can_verify": true,
    "reason": null,
    "existing_check": null
  }
}
```

### Soumettre une demande de vérification

```
POST /api/v1/items/{item}/authenticity/submit
```
> ⚡ Limite : 20 req/min

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `documents[]` | file[] | ❌ | Documents de preuve |
| `notes` | string | ❌ | Notes supplémentaires |

### Statut de la vérification

```
GET /api/v1/items/{item}/authenticity/status
```

### Confirmer le paiement de la vérification

```
POST /api/v1/authenticity/{check}/confirm-payment
```

### Dashboard des vérifications

```
GET /api/v1/authenticity/dashboard
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "checks": [ ... ],
    "stats": {
      "total": 10,
      "pending": 3,
      "verified": 7
    }
  }
}
```

### Mettre à jour le statut (expert)

```
PUT /api/v1/authenticity/{check}/update-status
```
> ⚡ Limite : 20 req/min

---

## 17. VintPass

Le VintPass est un certificat d'authenticité numérique pour les articles vérifiés.

### Route publique — Vérifier un VintPass

```
GET /api/v1/vintpass/verify/{shortCode}
```
> ⏱️ Cache : 60 secondes | 🌐 Public

**Exemple :** `GET /api/v1/vintpass/verify/VP-ABC123`

### Routes protégées

> 🔒 Authentification requise

#### Mes VintPass

```
GET /api/v1/vintpass
```

#### Détails d'un VintPass

```
GET /api/v1/vintpass/{vintPassId}
```

#### Demander un VintPass

```
POST /api/v1/vintpass/request/{item}
```

**Réponse `201` :**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "short_code": "VP-ABC123",
    "item": { ... },
    "status": "active",
    "qr_code_url": "...",
    "created_at": "2026-03-04T12:00:00Z"
  }
}
```

---

## 18. Programme d'affiliation

> 🔒 Authentification requise  
> ⚡ Limite : 30 req/min

### Dashboard affiliation

```
GET /api/affiliate/dashboard
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "total_referrals": 15,
    "total_points": 3000,
    "conversions": 8,
    "total_earnings": 45.00
  }
}
```

### Codes de parrainage

```
GET  /api/affiliate/referral-codes           → Liste des codes
POST /api/affiliate/referral-codes           → Générer un code automatique
POST /api/affiliate/referral-codes/custom    → Créer un code personnalisé
GET  /api/affiliate/codes/stats              → Stats par code
```

### Filleuls

```
GET /api/affiliate/referrals
```

### Points

```
GET  /api/affiliate/points-history           → Historique des points
POST /api/affiliate/convert-points           → Convertir en cash
POST /api/affiliate/calculate-conversion     → Simuler la conversion
GET  /api/affiliate/redemptions              → Historique des conversions
```

**Convertir les points :**
```json
{
  "points": 1000,
  "currency": "USD"
}
```

### Appliquer un code de parrainage

```
POST /api/affiliate/apply-referral-code
```

```json
{
  "referral_code": "VINT-ABC123"
}
```

### Générer un lien de parrainage

```
GET /api/affiliate/generate-link
```

---

## 19. Dashboard

> 🔒 Authentification requise  
> ⚡ Limite : 30 req/min

### Analytics

```
GET /api/dashboard/analytics
```

### Dashboard utilisateur

```
GET /api/dashboard/user
```

### Données dashboard (JSON)

```
GET /api/dashboard/data
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "stats": {
      "total_items": 12,
      "total_sales": 8,
      "total_revenue": 1500.00
    },
    "sales_chart": [ ... ],
    "recent_items": [ ... ],
    "recent_orders": [ ... ]
  }
}
```

---

## 20. Chatbot

### Poser une question

```
POST /api/bot
```

**Body :**
```json
{
  "question": "Comment vendre un article sur VintApp ?"
}
```

**Réponse `200` :**
```json
{
  "answer": "Pour vendre un article sur VintApp, suivez ces étapes..."
}
```

---

## 21. Notifications Push (FCM)

### Enregistrer un token FCM

```
POST /api/fcm-token
```
> 🌐 Session web requise

| Champ | Type | Obligatoire | Description |
|-------|------|:-----------:|-------------|
| `token` | string | ✅ | Token FCM |
| `device_type` | string | ❌ | Type d'appareil (max 20 car.) |

### Tester une notification FCM

```
POST /api/test-fcm-notification
```
> 🔒 Auth web requise

| Champ | Type | Description |
|-------|------|-------------|
| `type` | string | `approved` ou `rejected` (défaut: `approved`) |

### Push Notifications (Web)

```
POST /api/notifications/subscribe     → S'abonner
POST /api/notifications/unsubscribe   → Se désabonner
POST /api/notifications/closed        → Notification fermée
POST /api/notifications/test          → Tester
POST /api/notifications/broadcast-test → Test broadcast
```

### Admin — Broadcast FCM

```
POST /api/admin/broadcast-fcm-test
```
> 🔒 Admin requis | Session web

| Champ | Type | Obligatoire |
|-------|------|:-----------:|
| `title` | string (max 255) | ✅ |
| `message` | string (max 500) | ✅ |

### Admin — Stats FCM

```
GET /api/admin/fcm-stats
```
> 🔒 Admin requis

**Réponse `200` :**
```json
{
  "success": true,
  "stats": {
    "total_users": 500,
    "devices_with_fcm": 320
  }
}
```

---

## 22. Administration

> 🔒 Authentification requise + Rôle `admin`  
> Préfixe : `/api/v1/admin`

### Dashboard admin

```
GET /api/v1/admin/dashboard
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "total_users": 500,
    "total_transactions": 1200,
    "revenue_usd": 45000.00,
    "revenue_cdf": 112500000,
    "total_orders": 300,
    "total_items": 800,
    "open_support_tickets": 12
  }
}
```

### Résumé des statistiques

```
GET /api/v1/admin/stats/summary
```

### Alertes admin

```
GET /api/v1/admin/notifications
```

**Réponse `200` :**
```json
{
  "success": true,
  "data": {
    "notifications": [
      { "type": "pending_wallets", "count": 5, "message": "5 wallets en attente" }
    ],
    "total_count": 15
  }
}
```

### Rapports

```
GET /api/v1/admin/reports?period=30
```

### Utilisateurs en ligne

```
GET /api/v1/admin/online-users
```

### Gestion des utilisateurs

```
GET  /api/v1/admin/users                     → Liste (+ ?search, ?per_page)
GET  /api/v1/admin/users/{userId}            → Détails
POST /api/v1/admin/users/{userId}/status     → Changer le statut
```

**Actions de statut :**
| Action | Description |
|--------|-------------|
| `activate` | Activer le compte |
| `deactivate` | Désactiver le compte |
| `suspend` | Suspendre le compte |
| `delete` | Supprimer le compte |

### Gestion des portefeuilles

```
GET  /api/v1/admin/wallets                         → Liste (+ ?type, ?per_page)
GET  /api/v1/admin/wallets/pending                  → En attente
POST /api/v1/admin/wallets/{walletId}/approve       → Approuver
POST /api/v1/admin/wallets/{walletId}/reject        → Rejeter (+ reason)
POST /api/v1/admin/wallets/bulk-approve             → Approbation en masse
```

**Approbation en masse :**
```json
{
  "wallet_ids": [1, 2, 3, 4]
}
```

### Transactions admin

```
GET /api/v1/admin/transactions?status=pending&per_page=20
```

### Commandes admin

```
GET /api/v1/admin/orders?status=pending&per_page=20
```

### Articles admin

```
GET  /api/v1/admin/items?status=pending&category=1&search=iphone&per_page=20
POST /api/v1/admin/items/{itemId}/status
```

**Statuts d'article :**
| Statut | Description |
|--------|-------------|
| `pending` | En attente de validation |
| `active` | Actif et visible |
| `sold` | Vendu |
| `inactive` | Désactivé |

### Marques et catégories admin

```
GET /api/v1/admin/brands?per_page=20
GET /api/v1/admin/categories?per_page=20
```

### Support admin

```
GET /api/v1/admin/support-chats?status=open&priority=high&per_page=20
GET /api/v1/admin/support
GET /api/v1/admin/support/stats
GET /api/v1/admin/support/{supportChat}
```

### Vérifications d'authenticité admin

```
GET /api/v1/admin/verification-checks?status=pending&per_page=20
```

### Paramètres système

```
GET /api/v1/admin/settings
PUT /api/v1/admin/settings/{key}
```

```json
{
  "value": "new_value"
}
```

### Portefeuilles entreprise

```
GET /api/v1/admin/enterprise-wallets
GET /api/v1/admin/enterprise-wallets/{wallet}
```

### Programme d'affiliation admin

```
GET /api/v1/admin/affiliate/stats
GET /api/v1/admin/affiliate/top-performers
GET /api/v1/admin/affiliate/referrers
GET /api/v1/admin/affiliate/activity
```

### Remboursements admin

```
GET /api/v1/admin/refunds
GET /api/v1/admin/refunds/{refund}
```

### Utilisateurs en attente

```
GET  /api/v1/admin/waiting-users
GET  /api/v1/admin/waiting-users/stats
POST /api/v1/admin/waiting-users/{waitingUser}/approve
```

### Monitoring

```
GET /api/v1/admin/monitoring/stats
GET /api/v1/admin/monitoring/health
```

---

## 23. Callbacks de paiement

> 🌐 Routes publiques (appelées par les opérateurs)

### Callback universel

```
POST /api/payment-callbacks/{provider}
```
> ⚡ Limite : 100 req/min

| Provider | Valeurs |
|----------|---------|
| `provider` | `mpesa`, `orange_money`, `airtel_money`, `africell`, `illicocash` |

> Le body dépend de chaque opérateur. La signature est vérifiée côté serveur.

### Vérifier le statut (polling)

```
GET /api/payment-callbacks/status?transaction_id=TX-123
```
> ⚡ Limite : 30 req/min

**Réponse `200` :**
```json
{
  "status": "success",
  "transaction": {
    "id": "TX-123",
    "status": "completed",
    "amount": 50.00,
    "currency": "USD",
    "provider": "mpesa"
  }
}
```

### Callback MaishaPay (Webhook)

```
POST /api/v1/payments/maishapay/callback
```
> 🌐 Public — sans authentification

### Callback retrait MaishaPay (Webhook)

```
POST /api/v1/wallet/withdrawals/maishapay/callback
```
> 🌐 Public — sans authentification

---

## 24. Codes d'erreur

| Code HTTP | Signification |
|:---------:|---------------|
| `200` | Succès |
| `201` | Ressource créée |
| `400` | Requête invalide |
| `401` | Non authentifié |
| `403` | Accès interdit |
| `404` | Ressource non trouvée |
| `422` | Erreur de validation |
| `429` | Trop de requêtes (rate limit) |
| `500` | Erreur serveur |

### Exemple d'erreur de validation `422` :
```json
{
  "success": false,
  "message": "Les données fournies sont invalides.",
  "errors": {
    "email": ["Le champ email est obligatoire."],
    "password": ["Le mot de passe doit contenir au moins 8 caractères."]
  }
}
```

### Exemple d'erreur `401` :
```json
{
  "success": false,
  "message": "Les informations de connexion fournies sont incorrectes."
}
```

### Exemple d'erreur `429` :
```json
{
  "message": "Too Many Attempts.",
  "retry_after": 60
}
```

---

## Annexes

### Opérateurs Mobile Money supportés

| Opérateur | Code API | Préfixes | Pays |
|-----------|----------|----------|------|
| Vodacom M-Pesa | `mpesa` / `VODACOM` | 081, 082, 083 | 🇨🇩 RDC |
| Orange Money | `orange_money` / `ORANGE` | 084, 085 | 🇨🇩 RDC |
| Airtel Money | `airtel_money` / `AIRTEL` | 097, 099 | 🇨🇩 RDC |
| Africell | `africell` / `AFRICELL` | 090, 091 | 🇨🇩 RDC |
| Illicocash | `illicocash` | — | 🇨🇩 RDC |

### États des articles

| Valeur | Description |
|--------|-------------|
| `pending_verification` | En attente de vérification admin |
| `pending` | En attente |
| `active` | Actif et visible |
| `sold` | Vendu |
| `inactive` | Désactivé |

### États des commandes

| Statut | Description |
|--------|-------------|
| `pending` | En attente de paiement |
| `paid` | Payée |
| `shipped` | Expédiée |
| `delivered` | Livrée |
| `confirmed` | Réception confirmée |
| `cancelled` | Annulée |

### Catégories de support

| Valeur | Description |
|--------|-------------|
| `technical` | Problème technique |
| `account` | Problème de compte |
| `payment` | Problème de paiement |
| `order` | Problème de commande |
| `general` | Question générale |

### Priorités de support

| Valeur | Description |
|--------|-------------|
| `low` | Basse |
| `medium` | Moyenne |
| `high` | Haute |
| `urgent` | Urgente |

---

> 📝 **Note** : Cette documentation couvre **+150 endpoints API**. Pour toute question ou problème, utilisez le système de support intégré ou contactez l'équipe de développement.
