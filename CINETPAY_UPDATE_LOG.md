# 📝 CinetPay - Mise à jour de la table Payments

## ✅ Migration effectuée avec succès

**Date**: 16 novembre 2025  
**Migration**: `2025_11_16_072013_add_cinetpay_fields_to_payments_table.php`

---

## 🔄 Colonnes ajoutées à la table `payments`

### Nouvelles colonnes CinetPay

| Colonne            | Type          | Description                                              |
| ------------------ | ------------- | -------------------------------------------------------- |
| `user_id`          | foreignId     | ID utilisateur (pour rechargements wallet sans commande) |
| `designation`      | string        | Description du paiement                                  |
| `cpm_trans_id`     | string        | ID de transaction CinetPay (indexé)                      |
| `cpm_result`       | string        | Code résultat ('00' = succès)                            |
| `cpm_trans_status` | string        | Statut transaction (ACCEPTED, REFUSED)                   |
| `payment_token`    | string        | Token de paiement                                        |
| `cpm_amount`       | decimal(15,2) | Montant confirmé par CinetPay                            |
| `metadata`         | text          | Données JSON supplémentaires                             |
| `error_message`    | text          | Message d'erreur en cas d'échec                          |
| `ip_address`       | string        | Adresse IP de l'utilisateur                              |

### Colonnes modifiées

-   `buyer_id` → Maintenant **nullable** (pour rechargements wallet)
-   `seller_id` → Maintenant **nullable** (pour rechargements wallet)
-   `order_id` → Maintenant **nullable** (pour rechargements wallet)
-   `method` → Ajout des valeurs: `cinetpay`, `card`, `wallet`
-   `status` → Ajout de la valeur: `cancelled`

### Index ajoutés

-   Index composite: `(user_id, status)` pour optimiser les requêtes
-   Index composite: `(order_id, status)` pour optimiser les requêtes
-   Index simple: `cpm_trans_id` pour recherche rapide

---

## 🔄 Colonnes ajoutées à la table `orders`

| Colonne                  | Type   | Description                     |
| ------------------------ | ------ | ------------------------------- |
| `payment_transaction_id` | string | Lien vers l'ID de transaction   |
| `payment_status`         | enum   | pending, paid, failed, refunded |

---

## 📊 Structure finale de la table `payments`

```sql
CREATE TABLE payments (
    -- Identifiants
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NULLABLE,              -- 🆕 Nouveau
    buyer_id BIGINT NULLABLE,             -- ✏️ Modifié (nullable)
    seller_id BIGINT NULLABLE,            -- ✏️ Modifié (nullable)
    order_id BIGINT NULLABLE,             -- ✏️ Modifié (nullable)

    -- Montants
    amount DECIMAL(10,2),
    cpm_amount DECIMAL(15,2) NULLABLE,    -- 🆕 Nouveau
    currency VARCHAR(255) DEFAULT 'EUR',

    -- Description
    designation VARCHAR(255) NULLABLE,    -- 🆕 Nouveau

    -- Méthode et statut
    method ENUM(...) DEFAULT 'cinetpay',  -- ✏️ Modifié (valeurs ajoutées)
    status ENUM(...) DEFAULT 'pending',   -- ✏️ Modifié (cancelled ajouté)

    -- Transactions
    transaction_id VARCHAR(255) NULLABLE,
    cpm_trans_id VARCHAR(255) NULLABLE,   -- 🆕 Nouveau (indexé)
    cpm_result VARCHAR(255) NULLABLE,     -- 🆕 Nouveau
    cpm_trans_status VARCHAR(255) NULLABLE, -- 🆕 Nouveau
    payment_token VARCHAR(255) NULLABLE,  -- 🆕 Nouveau

    -- Données supplémentaires
    payment_details JSON NULLABLE,
    metadata TEXT NULLABLE,               -- 🆕 Nouveau
    error_message TEXT NULLABLE,          -- 🆕 Nouveau
    ip_address VARCHAR(45) NULLABLE,      -- 🆕 Nouveau

    -- Dates
    paid_at TIMESTAMP NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    -- Index
    INDEX idx_user_status (user_id, status),    -- 🆕 Nouveau
    INDEX idx_order_status (order_id, status),  -- 🆕 Nouveau
    INDEX idx_cpm_trans (cpm_trans_id)          -- 🆕 Nouveau
);
```

---

## 🎯 Cas d'utilisation supportés

### 1. Paiement de commande (existant)

```php
Payment::create([
    'buyer_id' => $buyer->id,
    'seller_id' => $seller->id,
    'order_id' => $order->id,
    'amount' => 10000,
    'method' => 'cinetpay',
    'status' => 'pending',
]);
```

### 2. Rechargement wallet (nouveau) ✨

```php
Payment::create([
    'user_id' => $user->id,
    // buyer_id, seller_id, order_id sont NULL
    'amount' => 5000,
    'designation' => 'Rechargement wallet',
    'method' => 'cinetpay',
    'status' => 'pending',
]);
```

---

## ✅ Compatibilité

-   ✅ **Rétro-compatible**: Les paiements existants continuent de fonctionner
-   ✅ **Flexible**: Support des paiements avec ou sans commande
-   ✅ **Optimisé**: Index pour requêtes rapides
-   ✅ **Sécurisé**: Validation des montants CinetPay

---

## 🚀 Prochaines étapes

1. ✅ Migration exécutée
2. ⏳ Ajouter le bouton "Payer" aux vues
3. ⏳ Tester un paiement de commande
4. ⏳ Tester un rechargement wallet

**Consultez**: `CINETPAY_QUICK_START.md` pour les instructions complètes
