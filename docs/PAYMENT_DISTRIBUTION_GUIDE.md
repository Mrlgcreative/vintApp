# 💰 Guide du Système de Distribution des Paiements

## Vue d'ensemble

Le système de distribution automatique gère la répartition de l'argent lors de la confirmation de réception par l'acheteur.

## 🔄 Flux de Paiement

### 1️⃣ Paiement Initial (PaymentController)

Lorsque l'acheteur paie un produit :

```
Acheteur paie 170 USD
    ↓
Argent stocké dans Wallet Pending du vendeur
    ↓
Commande créée avec status: confirmed
    ↓
En attente de confirmation de réception
```

**Emplacement code** : `app/Http/Controllers/PaymentController.php` - méthode `simulatePayment()`

**Wallet utilisé** : `pending` (type = 'pending')

### 2️⃣ Confirmation de Réception (OrderController)

Lorsque l'acheteur clique sur "Commande Reçue" :

```
Récupération des pourcentages depuis settings :
- Commission plateforme : 10% (par défaut)
- Frais de transport : 5% (par défaut)

Calcul de la distribution :
Total : 170 USD
├─ Commission (10%) : 17.00 USD
├─ Transport (5%) : 8.50 USD
└─ Vendeur : 144.50 USD

Distribution :
├─ Débit Wallet Pending : -170 USD
├─ Crédit Wallet Main (vendeur) : +144.50 USD
└─ Crédit Wallet Enterprise (plateforme) : +25.50 USD (17 + 8.50)
```

**Emplacement code** : `app/Http/Controllers/OrderController.php` - méthode `confirmDelivery()`

## ⚙️ Configuration des Pourcentages

Les pourcentages de commission et transport sont configurables dans l'admin :

### Accès Admin

```
URL : /admin/settings
Section : Paiement
```

### Paramètres

| Clé | Label | Défaut | Type |
|-----|-------|--------|------|
| `platform_commission_percentage` | Commission Plateforme (%) | 10 | float |
| `transport_fee_percentage` | Frais de Transport (%) | 5 | float |

### Modification

1. Connectez-vous en tant qu'admin
2. Accédez à **Admin** > **Paramètres**
3. Cherchez la section **Paiement**
4. Modifiez les pourcentages
5. Cliquez sur **Enregistrer**

## 📊 Exemple de Calcul

### Exemple 1 : Vente à 170 USD

```
Configuration :
- Commission : 10%
- Transport : 5%

Calcul :
Total       : 170.00 USD
Commission  : 170 × 0.10 = 17.00 USD
Transport   : 170 × 0.05 = 8.50 USD
─────────────────────────────────
Vendeur     : 170 - 17 - 8.50 = 144.50 USD
Plateforme  : 17 + 8.50 = 25.50 USD
```

### Exemple 2 : Vente à 50 USD avec 15% commission, 8% transport

```
Configuration :
- Commission : 15%
- Transport : 8%

Calcul :
Total       : 50.00 USD
Commission  : 50 × 0.15 = 7.50 USD
Transport   : 50 × 0.08 = 4.00 USD
─────────────────────────────────
Vendeur     : 50 - 7.50 - 4.00 = 38.50 USD
Plateforme  : 7.50 + 4.00 = 11.50 USD
```

## 🗄️ Structure des Wallets

### Types de Wallets

| Type | User ID | Description | Retirable |
|------|---------|-------------|-----------|
| `main` | user_id | Wallet principal du vendeur | ✅ Oui |
| `pending` | user_id | Argent en attente de confirmation | ❌ Non |
| `enterprise` | NULL | Wallet de la plateforme (commissions + transport) | ❌ Admin seulement |

### Transactions Créées

Lors de la confirmation, **3 transactions** sont créées :

#### 1. Transaction Vendeur
```php
[
    'transaction_id' => 'SELLER-XXXXX',
    'user_id' => seller_id,
    'wallet_id' => wallet_main_id,
    'amount' => 144.50,  // Montant après déductions
    'currency' => 'USD',
    'type' => 'deposit',
    'purpose' => 'Vente confirmée - Commande #123 (Montant net après commission 10% et transport 5%)'
]
```

#### 2. Transaction Commission
```php
[
    'transaction_id' => 'COMMISSION-XXXXX',
    'user_id' => null,  // Plateforme
    'wallet_id' => enterprise_wallet_id,
    'amount' => 17.00,
    'currency' => 'USD',
    'type' => 'deposit',
    'purpose' => 'Commission plateforme (10%) - Commande #123'
]
```

#### 3. Transaction Transport
```php
[
    'transaction_id' => 'TRANSPORT-XXXXX',
    'user_id' => null,  // Plateforme
    'wallet_id' => enterprise_wallet_id,
    'amount' => 8.50,
    'currency' => 'USD',
    'type' => 'deposit',
    'purpose' => 'Frais de transport (5%) - Commande #123'
]
```

## 🔐 Sécurité

### Vérifications Effectuées

Avant la distribution, le système vérifie :

1. ✅ L'utilisateur est bien l'acheteur de la commande
2. ✅ Le statut de la commande est `shipped` ou `delivered`
3. ✅ La commande n'a pas déjà été confirmée
4. ✅ Le wallet pending contient suffisamment d'argent
5. ✅ Transaction atomique avec `DB::beginTransaction()`

### En Cas d'Erreur

```php
try {
    DB::beginTransaction();
    // ... distribution ...
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Erreur confirmation: ' . $e->getMessage());
    return response()->json(['success' => false, 'error' => 'Erreur serveur'], 500);
}
```

## 📝 Logs

### Logs Créés

Chaque distribution génère des logs détaillés :

```php
Log::info("Distribution calculée pour commande #123", [
    'total' => 170.00,
    'commission_percent' => 10,
    'commission_amount' => 17.00,
    'transport_percent' => 5,
    'transport_amount' => 8.50,
    'seller_amount' => 144.50,
    'currency' => 'USD'
]);

Log::info("Distribution effectuée", [
    'seller_id' => 45,
    'order_id' => 123,
    'total_amount' => 170.00,
    'seller_amount' => 144.50,
    'commission_amount' => 17.00,
    'transport_amount' => 8.50,
    'platform_amount' => 25.50,
    'currency' => 'USD',
    'pending_balance' => 0.00,
    'main_balance' => 144.50,
    'enterprise_balance' => 25.50
]);
```

**Emplacement** : `storage/logs/laravel.log`

## 📧 Notifications

### Notification au Vendeur

Message envoyé après distribution :

```
Titre : "Commande confirmée reçue - Paiement distribué"

Message : "John Doe a confirmé avoir reçu la commande #123. 
Montant reçu: 144.50 USD 
(Total: 170.00 - Commission: 17.00 - Transport: 8.50)"
```

## 🧪 Tests Manuels

### Scénario de Test Complet

1. **Configuration** (Admin)
   ```
   - Aller dans Admin > Paramètres
   - Section Paiement
   - Vérifier : Commission = 10%, Transport = 5%
   ```

2. **Achat** (Acheteur)
   ```
   - Ajouter un produit à 170 USD au panier
   - Payer via Mobile Money
   - Vérifier : Commande créée, statut = confirmed
   ```

3. **Vérification Wallet Pending** (Base de données)
   ```sql
   SELECT balance FROM wallets 
   WHERE user_id = <seller_id> 
   AND type = 'pending' 
   AND currency = 'USD';
   -- Résultat attendu : 170.00
   ```

4. **Expédition** (Vendeur)
   ```
   - Marquer la commande comme expédiée
   - Statut devient : shipped
   ```

5. **Confirmation** (Acheteur)
   ```
   - Cliquer sur "Commande Reçue"
   - Ajouter une note (optionnel)
   - Confirmer
   ```

6. **Vérification Distribution** (Base de données)
   ```sql
   -- Wallet Main Vendeur
   SELECT balance FROM wallets 
   WHERE user_id = <seller_id> 
   AND type = 'main' 
   AND currency = 'USD';
   -- Résultat attendu : 144.50
   
   -- Wallet Enterprise
   SELECT balance FROM wallets 
   WHERE user_id IS NULL 
   AND type = 'enterprise' 
   AND currency = 'USD';
   -- Résultat attendu : 25.50
   
   -- Wallet Pending (vidé)
   SELECT balance FROM wallets 
   WHERE user_id = <seller_id> 
   AND type = 'pending' 
   AND currency = 'USD';
   -- Résultat attendu : 0.00
   ```

7. **Vérification Transactions** (Base de données)
   ```sql
   SELECT transaction_id, amount, purpose 
   FROM transactions 
   WHERE transaction_id LIKE 'SELLER-%' 
   OR transaction_id LIKE 'COMMISSION-%' 
   OR transaction_id LIKE 'TRANSPORT-%'
   ORDER BY created_at DESC 
   LIMIT 3;
   -- 3 transactions : vendeur (144.50), commission (17.00), transport (8.50)
   ```

## 🔍 Commandes Artisan Utiles

### Vérifier les Settings
```bash
php artisan tinker --execute="DB::table('settings')->whereIn('key', ['platform_commission_percentage', 'transport_fee_percentage'])->get(['key', 'value', 'label']);"
```

### Vérifier les Wallets
```bash
php artisan tinker --execute="DB::table('wallets')->select('id', 'user_id', 'type', 'currency', 'balance')->get();"
```

### Voir les Dernières Transactions
```bash
php artisan tinker --execute="DB::table('transactions')->orderBy('created_at', 'desc')->limit(5)->get(['transaction_id', 'amount', 'currency', 'purpose']);"
```

### Tester le Calcul de Distribution
```bash
php artisan tinker
$total = 170;
$commission = DB::table('settings')->where('key', 'platform_commission_percentage')->value('value');
$transport = DB::table('settings')->where('key', 'transport_fee_percentage')->value('value');
$commissionAmount = round($total * ($commission / 100), 2);
$transportAmount = round($total * ($transport / 100), 2);
$sellerAmount = $total - $commissionAmount - $transportAmount;
echo "Total: $total USD\nCommission ({$commission}%): $commissionAmount USD\nTransport ({$transport}%): $transportAmount USD\nVendeur: $sellerAmount USD\n";
```

## ⚠️ Points d'Attention

### 1. Multi-Devise
- Le système gère **USD** et **CDF**
- Un wallet existe pour chaque devise
- La distribution se fait dans la devise de la commande

### 2. Solde Insuffisant
Si le wallet pending n'a pas assez de fonds :
```php
if ($sellerPendingWallet && $sellerPendingWallet->balance >= $order->total_amount) {
    // Distribution OK
} else {
    Log::warning("Solde insuffisant dans le wallet pending pour la commande #{$order->id}");
    // Pas de distribution, mais commande marquée confirmée
}
```

### 3. Atomicité
Toutes les opérations sont dans une transaction :
- Rollback automatique en cas d'erreur
- Pas de distribution partielle possible

### 4. Wallet Enterprise
- `user_id = NULL` (appartient à la plateforme)
- Contient commission + transport
- Seul l'admin peut retirer

## 📋 Migration Settings

La migration qui a créé les settings :

**Fichier** : `database/migrations/2025_10_11_195511_add_commission_and_transport_settings_to_settings_table.php`

```php
DB::table('settings')->insert([
    [
        'key' => 'platform_commission_percentage',
        'value' => '10',
        'type' => 'float',
        'category' => 'paiement',
        'label' => 'Commission Plateforme (%)',
        'description' => 'Pourcentage de commission de la plateforme sur chaque vente',
        'is_public' => false,
        'is_encrypted' => false,
    ],
    [
        'key' => 'transport_fee_percentage',
        'value' => '5',
        'type' => 'float',
        'category' => 'paiement',
        'label' => 'Frais de Transport (%)',
        'description' => 'Pourcentage des frais de transport sur chaque vente',
        'is_public' => false,
        'is_encrypted' => false,
    ],
]);
```

## 🎯 Formule de Distribution

```
Argent - Commission% - Transport% = Vendeur
```

**Exemple avec 170 USD, 10% commission, 5% transport :**

```
170 - (170 × 0.10) - (170 × 0.05) = 170 - 17 - 8.50 = 144.50 USD
```

## 📞 Support

En cas de problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier les settings : Section "Commandes Artisan Utiles"
3. Tester le calcul manuellement avec tinker
4. Vérifier que la migration a bien été exécutée

---

**Date de création** : 2025-01-XX  
**Dernière mise à jour** : 2025-01-XX  
**Version** : 1.0
