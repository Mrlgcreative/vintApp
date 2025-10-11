# 🔄 Cycle de Vie d'une Commande

## ⚠️ Clarification Importante

Il ne faut PAS confondre :
- **Statut de la commande** (`orders.status`) : L'état de la commande
- **Type de wallet** (`wallets.type`) : Le type de portefeuille

Ce sont deux concepts **complètement différents** !

## 📊 Statuts de Commande

### Liste des Statuts

| Statut | Moment | Description | Argent |
|--------|--------|-------------|--------|
| `pending` | Après paiement | ✅ **Payée**, en attente d'expédition | 💰 Wallet Pending |
| `processing` | Vendeur prépare | En cours de préparation | 💰 Wallet Pending |
| `shipped` | Vendeur expédie | Expédiée, en transit | 💰 Wallet Pending |
| `delivered` | Transporteur livre | Livrée, non confirmée | 💰 Wallet Pending |
| `completed` | Acheteur confirme | ✅ **Confirmée par acheteur** | 💰 Wallet Main (distribué) |
| `cancelled` | Annulée | Annulée par acheteur/vendeur | 💰 Remboursé |
| `refunded` | Remboursée | Argent retourné | 💰 Remboursé |

### 🔑 Différence Clé

**STATUS DE LA COMMANDE ≠ TYPE DE WALLET**

```
Commande status = "pending"  → Argent dans Wallet TYPE = "pending"
Commande status = "shipped"  → Argent dans Wallet TYPE = "pending"
Commande status = "delivered" → Argent dans Wallet TYPE = "pending"
Commande status = "completed" → Argent dans Wallet TYPE = "main" (distribué)
```

## 🔄 Flux Complet (Avant Correction)

### ❌ PROBLÈME : Ancien Flux (Incorrect)

```
1. Acheteur paie
   ↓
2. PaymentController crée commande avec status = "confirmed" ❌ ERREUR
   ↓
3. Argent va dans Wallet Pending
   ↓
4. Commande apparaît comme "confirmée" sans que l'acheteur ait confirmé ❌
```

**Problème** : Le client n'a PAS encore confirmé la réception, mais la commande dit "confirmed" !

## ✅ Flux Correct (Après Correction)

### 1️⃣ Paiement (Status: pending)

```
Acheteur clique "Payer maintenant"
    ↓
PaymentController.simulatePayment()
    ↓
Création de la commande
    status: "pending" ✅ (payée, en attente d'expédition)
    paid_at: now()
    ↓
Argent stocké dans Wallet Pending du vendeur
```

**Code** : `app/Http/Controllers/PaymentController.php` ligne 329
```php
'status' => 'pending', // Payée, en attente d'expédition
```

### 2️⃣ Préparation (Status: processing)

```
Vendeur clique "Commencer la préparation"
    ↓
OrderController.updateStatus('processing')
    ↓
Status: "pending" → "processing"
    ↓
Argent RESTE dans Wallet Pending
```

### 3️⃣ Expédition (Status: shipped)

```
Vendeur clique "Marquer comme expédiée"
    ↓
OrderController.ship()
    ↓
Status: "processing" → "shipped"
    shipped_at: now()
    ↓
Argent RESTE dans Wallet Pending
```

### 4️⃣ Livraison (Status: delivered)

```
Transporteur/Vendeur clique "Marquer comme livrée"
    ↓
OrderController.markAsDelivered()
    ↓
Status: "shipped" → "delivered"
    delivered_at: now()
    ↓
Argent RESTE dans Wallet Pending
    ↓
Bouton "Commande Reçue" apparaît pour l'acheteur ✅
```

### 5️⃣ Confirmation Acheteur (Status: completed) 🎯

```
Acheteur clique "Commande Reçue"
    ↓
OrderController.confirmDelivery() ← ICI LA DISTRIBUTION
    ↓
Status: "delivered" → "completed" ✅
    confirmed_by_buyer_at: now()
    ↓
DISTRIBUTION DE L'ARGENT :
    ├─ Débit Wallet Pending (-170 USD)
    ├─ Crédit Wallet Main vendeur (+144.50 USD)
    └─ Crédit Wallet Enterprise (+25.50 USD)
    ↓
3 transactions créées (vendeur, commission, transport)
    ↓
Notification au vendeur
```

**Code** : `app/Http/Controllers/OrderController.php` ligne 346-540

## 📋 Tableau Récapitulatif

| Étape | Action | Qui | Status Avant | Status Après | Argent |
|-------|--------|-----|--------------|--------------|--------|
| 1 | Paiement | Acheteur | - | `pending` | → Wallet Pending |
| 2 | Préparation | Vendeur | `pending` | `processing` | Wallet Pending |
| 3 | Expédition | Vendeur | `processing` | `shipped` | Wallet Pending |
| 4 | Livraison | Vendeur | `shipped` | `delivered` | Wallet Pending |
| 5 | Confirmation | **Acheteur** | `delivered` | `completed` | **Distribution !** |

## 🎯 Consentement de l'Acheteur

### Question : "Comment confirmer une commande sans le consentement du client ?"

**Réponse** : Maintenant, on NE confirme PLUS automatiquement !

#### Avant (❌ Incorrect)
```php
// PaymentController.php
'status' => 'confirmed', // ❌ Confirmé dès le paiement = MAUVAIS
```
- Le client paie → Statut "confirmed"
- **PROBLÈME** : Le client n'a pas encore reçu le produit !

#### Après (✅ Correct)
```php
// PaymentController.php
'status' => 'pending', // ✅ En attente d'expédition
```
- Le client paie → Statut "pending"
- Le vendeur expédie → Statut "shipped"
- Le client reçoit → Statut "delivered"
- Le client confirme → Statut "completed" ✅

### Le Consentement Explicite

Le client doit **ACTIVEMENT** cliquer sur le bouton **"Commande Reçue"** pour :
1. Confirmer qu'il a bien reçu le produit
2. Déclencher la distribution de l'argent
3. Transférer l'argent du vendeur du wallet pending au wallet main

**Sans ce clic, l'argent RESTE dans le wallet pending** = sécurisé !

## 🔐 Sécurité du Wallet Pending

### Pourquoi le Wallet Pending ?

Le wallet pending protège **les deux parties** :

#### Protection Acheteur
- Si le colis n'arrive jamais → Pas de confirmation → Argent bloqué
- Possibilité de dispute
- Remboursement possible

#### Protection Vendeur
- Argent réservé (dans pending)
- Pas de "paiement annulé" après expédition
- Garantie de paiement si livraison confirmée

#### Protection Plateforme
- Traçabilité complète
- Arbitrage possible en cas de litige
- Commission prélevée uniquement sur ventes confirmées

## 📱 Interface Utilisateur

### Vue Acheteur

#### Après Paiement (Status: pending)
```
📦 Commande #123
Status: En attente d'expédition
```

#### Après Expédition (Status: shipped)
```
📦 Commande #123
Status: Expédiée
🚚 En transit vers vous
```

#### Après Livraison (Status: delivered)
```
📦 Commande #123
Status: Livrée
⏰ En attente de votre confirmation

[Bouton: Commande Reçue] ← CLIC ICI POUR CONFIRMER
```

#### Après Confirmation (Status: completed)
```
📦 Commande #123
Status: Terminée
✅ Vous avez confirmé la réception le 11 Jan 2025
```

### Vue Vendeur

#### Après Paiement (Status: pending)
```
💰 170 USD dans Wallet Pending
📦 Commande #123 - À expédier
[Bouton: Marquer comme expédiée]
```

#### Après Expédition (Status: shipped)
```
💰 170 USD dans Wallet Pending
📦 Commande #123 - Expédiée
🚚 En transit
```

#### Après Confirmation (Status: completed)
```
💰 144.50 USD dans Wallet Main (retirable)
📦 Commande #123 - Terminée
✅ Confirmée par l'acheteur
Commission: 17 USD | Transport: 8.50 USD
```

## 🧪 Test du Nouveau Flux

### Scénario Complet

1. **Paiement** (Acheteur)
   ```sql
   SELECT status, paid_at, confirmed_by_buyer_at 
   FROM orders 
   WHERE id = 123;
   -- status = "pending"
   -- paid_at = 2025-01-11 10:00:00
   -- confirmed_by_buyer_at = NULL ✅
   ```

2. **Vérifier Wallet Pending**
   ```sql
   SELECT balance FROM wallets 
   WHERE user_id = <seller_id> 
   AND type = 'pending';
   -- balance = 170.00 ✅
   ```

3. **Vérifier Wallet Main**
   ```sql
   SELECT balance FROM wallets 
   WHERE user_id = <seller_id> 
   AND type = 'main';
   -- balance = 0.00 (pas encore distribué) ✅
   ```

4. **Expédition** (Vendeur)
   ```sql
   SELECT status, shipped_at 
   FROM orders 
   WHERE id = 123;
   -- status = "shipped"
   -- shipped_at = 2025-01-11 12:00:00
   ```

5. **Confirmation** (Acheteur clique "Commande Reçue")
   ```sql
   SELECT status, confirmed_by_buyer_at 
   FROM orders 
   WHERE id = 123;
   -- status = "completed" ✅
   -- confirmed_by_buyer_at = 2025-01-11 15:00:00 ✅
   ```

6. **Vérifier Distribution**
   ```sql
   -- Wallet Pending (vidé)
   SELECT balance FROM wallets 
   WHERE user_id = <seller_id> AND type = 'pending';
   -- balance = 0.00 ✅
   
   -- Wallet Main (montant net)
   SELECT balance FROM wallets 
   WHERE user_id = <seller_id> AND type = 'main';
   -- balance = 144.50 ✅
   
   -- Wallet Enterprise (commission + transport)
   SELECT balance FROM wallets 
   WHERE user_id IS NULL AND type = 'enterprise';
   -- balance = 25.50 ✅
   ```

## 🔍 Différence Statut vs Type

### Exemple Concret

```
Commande #123:
├─ status: "pending" (état de la commande)
├─ Argent: Wallet type "pending" (type de portefeuille)
└─ Le mot "pending" apparaît 2 fois mais signifie des choses différentes !

Commande status = "pending" 
    → Signifie: "Commande payée, en attente d'expédition"

Wallet type = "pending"
    → Signifie: "Portefeuille sécurisé, argent bloqué jusqu'à confirmation"
```

### Autre Exemple

```
Commande #456:
├─ status: "completed" (état de la commande = terminée)
├─ Argent: Wallet type "main" (type de portefeuille = principal/retirable)
└─ Pas de confusion ici, deux mots différents
```

## 📊 Migration des Commandes Existantes

Si vous avez déjà des commandes avec status = "confirmed" après paiement, vous pouvez les corriger :

```sql
-- Trouver les commandes "confirmed" mais non confirmées par l'acheteur
UPDATE orders 
SET status = 'pending'
WHERE status = 'confirmed' 
AND confirmed_by_buyer_at IS NULL
AND paid_at IS NOT NULL;
```

## ⚠️ Points d'Attention

### 1. Ne JAMAIS distribuer sans confirmation acheteur
```php
// ❌ MAUVAIS
if ($order->status === 'pending') {
    // Distribuer l'argent
}

// ✅ BON
if ($order->status === 'completed' && $order->confirmed_by_buyer_at) {
    // Distribuer l'argent
}
```

### 2. Vérifier le consentement explicite
```php
// Dans OrderController::confirmDelivery()
if ($order->buyer_id !== Auth::id()) {
    return response()->json(['error' => 'Non autorisé'], 403);
}
```

### 3. Atomicité de la distribution
```php
DB::beginTransaction();
try {
    // Distribution
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack(); // Annule TOUT si erreur
}
```

## 📝 Résumé

### Avant (❌)
```
Paiement → Status "confirmed" → Argent Wallet Pending
(Pas de consentement acheteur !)
```

### Après (✅)
```
Paiement → Status "pending" → Argent Wallet Pending
    ↓
Expédition → Status "shipped" → Argent Wallet Pending
    ↓
Livraison → Status "delivered" → Argent Wallet Pending
    ↓
Acheteur clique "Commande Reçue" (CONSENTEMENT EXPLICITE)
    ↓
Status "completed" → DISTRIBUTION AUTOMATIQUE
    ↓
Argent Wallet Main (vendeur) + Wallet Enterprise (plateforme)
```

**La distribution n'a lieu QUE si l'acheteur confirme activement !** ✅

---

**Date de correction** : 2025-01-11  
**Problème corrigé** : Statut "confirmed" automatique sans consentement  
**Solution** : Statut "pending" après paiement, "completed" après confirmation acheteur
