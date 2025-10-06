# 💰 Système de Décaissement Mobile Money (Cash-Out API)

## 📋 Vue d'ensemble

Ce document décrit le système complet de **décaissement automatique** (cash-out) vers les opérateurs mobile money au Congo (RDC). Le système permet aux utilisateurs de retirer leurs fonds directement vers leur compte mobile money.

---

## 🎯 Fonctionnalités

### ✅ Ce qui a été implémenté

1. **API de décaissement automatique** pour 5 opérateurs :
   - 🟠 **Orange Money**
   - 🔴 **Airtel Money**
   - 🟢 **M-Pesa** (Vodacom)
   - 🔵 **Africell Money**
   - 💳 **Illicocash**

2. **Gestion des transactions** :
   - Création automatique de `WalletTransaction` (status: processing)
   - Création de `WithdrawalRequest` pour tracking détaillé
   - Débit immédiat du wallet (fonds bloqués pendant le traitement)
   - Remboursement automatique en cas d'échec

3. **Système de webhooks** :
   - Réception des callbacks des opérateurs
   - Vérification des signatures pour sécurité
   - Mise à jour automatique des statuts
   - Remboursement automatique si échec

4. **Mode simulation** :
   - Tests sans vraies APIs (90% succès, 10% échec)
   - Logs détaillés pour débogage
   - Configuration via `.env`

5. **Administration** :
   - Méthode `retryFailedWithdrawal()` pour réessayer les échecs
   - Logs complets dans `storage/logs/laravel.log`

---

## 📊 Architecture du système

```
┌─────────────┐
│   User      │
│  (Frontend) │
└──────┬──────┘
       │ 1. Demande de retrait (POST)
       │    - amount
       │    - phone_number
       │    - payment_method
       ▼
┌─────────────────────────┐
│   WalletController      │
│  storeWithdrawFunds()   │
└──────┬──────────────────┘
       │ 2. Créer Transaction + WithdrawalRequest
       │ 3. Débiter le wallet
       │ 4. Appeler API Mobile Money
       ▼
┌─────────────────────────┐
│  MobileMoneyService     │
│    cashOut()            │
└──────┬──────────────────┘
       │ 5. HTTP Request vers opérateur
       ▼
┌─────────────────────────┐
│  Opérateur Mobile Money │
│  (Orange, Airtel, etc.) │
└──────┬──────────────────┘
       │ 6. Traite le paiement
       │ 7. Envoie webhook (asynchrone)
       ▼
┌─────────────────────────┐
│   WalletController      │
│ handleWithdrawalWebhook()│
└──────┬──────────────────┘
       │ 8. Vérifie signature
       │ 9. Met à jour statut
       │ 10. Rembourse si échec
       ▼
┌─────────────────────────┐
│   User notifié          │
│   (Email/SMS optional)  │
└─────────────────────────┘
```

---

## 🗄️ Structure de la base de données

### Table `wallet_transactions`

```sql
- id (bigint)
- wallet_id (bigint) → FK vers wallets
- type (varchar) → 'debit' pour les retraits
- amount (decimal)
- balance_after (decimal)
- description (varchar)
- reference (varchar) → Ex: WTH-1728234567-4521
- status (varchar) → 'processing', 'completed', 'failed'
- provider (varchar) → 'orange_money', 'airtel_money', etc.
- metadata (json) → {phone_number, payment_method, withdrawal_date}
- created_at
- updated_at
```

### Table `withdrawal_requests`

```sql
- id (bigint)
- wallet_transaction_id (bigint) → FK vers wallet_transactions
- phone_number (varchar) → +243812345678
- payment_method (varchar) → 'orange_money', etc.
- amount (decimal)
- currency (varchar) → 'USD' ou 'CDF'
- status (varchar) → 'processing', 'completed', 'failed'
- provider_reference (varchar) → Référence de l'opérateur
- provider_response (json) → Réponse complète de l'API
- created_at
- updated_at
```

---

## 🔐 Configuration (.env)

### Orange Money

```env
ORANGE_MONEY_MERCHANT_KEY=your_merchant_key
ORANGE_MONEY_API_KEY=your_api_key
ORANGE_MONEY_WEBHOOK_SECRET=your_webhook_secret
```

### Airtel Money

```env
AIRTEL_MONEY_CLIENT_ID=your_client_id
AIRTEL_MONEY_CLIENT_SECRET=your_client_secret
AIRTEL_MONEY_PIN=your_pin
AIRTEL_MONEY_WEBHOOK_TOKEN=your_webhook_token
```

### M-Pesa (Vodacom)

```env
MPESA_API_KEY=your_api_key
MPESA_PUBLIC_KEY=your_public_key
MPESA_SERVICE_CODE=your_service_code
```

### Africell Money

```env
AFRICELL_MERCHANT_ID=your_merchant_id
AFRICELL_API_SECRET=your_api_secret
AFRICELL_WEBHOOK_SECRET=your_webhook_secret
```

### Illicocash

```env
ILLICOCASH_MERCHANT_CODE=your_merchant_code
ILLICOCASH_API_TOKEN=your_api_token
ILLICOCASH_WEBHOOK_SECRET=your_webhook_secret
```

### Ajouter dans `config/services.php`

```php
return [
    // ... autres services

    'orange_money' => [
        'merchant_key' => env('ORANGE_MONEY_MERCHANT_KEY'),
        'api_key' => env('ORANGE_MONEY_API_KEY'),
        'webhook_secret' => env('ORANGE_MONEY_WEBHOOK_SECRET'),
    ],

    'airtel_money' => [
        'client_id' => env('AIRTEL_MONEY_CLIENT_ID'),
        'client_secret' => env('AIRTEL_MONEY_CLIENT_SECRET'),
        'pin' => env('AIRTEL_MONEY_PIN'),
        'webhook_token' => env('AIRTEL_MONEY_WEBHOOK_TOKEN'),
    ],

    'mpesa' => [
        'api_key' => env('MPESA_API_KEY'),
        'public_key' => env('MPESA_PUBLIC_KEY'),
        'service_code' => env('MPESA_SERVICE_CODE'),
    ],

    'africell' => [
        'merchant_id' => env('AFRICELL_MERCHANT_ID'),
        'api_secret' => env('AFRICELL_API_SECRET'),
        'webhook_secret' => env('AFRICELL_WEBHOOK_SECRET'),
    ],

    'illicocash' => [
        'merchant_code' => env('ILLICOCASH_MERCHANT_CODE'),
        'api_token' => env('ILLICOCASH_API_TOKEN'),
        'webhook_secret' => env('ILLICOCASH_WEBHOOK_SECRET'),
    ],
];
```

---

## 🚀 Utilisation

### 1. Demande de retrait (Frontend)

**Formulaire** : `resources/views/wallet/withdraw-funds.blade.php`

```html
<form action="{{ route('wallet.store-withdraw-funds', $wallet) }}" method="POST">
    @csrf
    
    <input type="number" name="amount" step="0.01" required>
    <input type="tel" name="phone_number" pattern="^(\+?243|0)?[0-9]{9}$" required>
    
    <select name="payment_method" required>
        <option value="orange_money">🟠 Orange Money</option>
        <option value="airtel_money">🔴 Airtel Money</option>
        <option value="mpesa">🟢 M-Pesa</option>
        <option value="africell">🔵 Africell Money</option>
        <option value="illicocash">💳 Illicocash</option>
    </select>
    
    <textarea name="description"></textarea>
    
    <button type="submit">Retirer</button>
</form>
```

### 2. Traitement backend

**Route** : `POST /wallet/{wallet}/withdraw-funds`

**Controller** : `WalletController@storeWithdrawFunds`

```php
// Le système fait automatiquement :
// 1. Validation des données
// 2. Création de la transaction
// 3. Débit du wallet
// 4. Appel de l'API mobile money
// 5. Log de l'opération
```

### 3. Webhook des opérateurs

**Routes webhook** :

- `POST /wallet/withdrawals/webhook/orange_money`
- `POST /wallet/withdrawals/webhook/airtel_money`
- `POST /wallet/withdrawals/webhook/mpesa`
- `POST /wallet/withdrawals/webhook/africell`
- `POST /wallet/withdrawals/webhook/illicocash`

**Exemple de payload Orange Money** :

```json
{
    "reference": "WTH-1728234567-4521",
    "payment_token": "OM-TXN-123456",
    "status": "success",
    "amount": 1000,
    "currency": "CDF",
    "payment_date": "2025-10-06T14:30:00Z"
}
```

**Le système fait automatiquement** :

1. ✅ Vérifie la signature du webhook
2. ✅ Trouve la transaction correspondante
3. ✅ Met à jour le statut (completed/failed)
4. ✅ Rembourse le wallet si échec
5. ✅ Log toutes les actions

---

## 🔄 Gestion des statuts

### Cycle de vie d'un retrait

```
┌─────────────┐
│  pending    │ ← Ancien système (non utilisé maintenant)
└─────────────┘

┌─────────────┐
│ processing  │ ← État initial après demande
└──────┬──────┘
       │
       ├─────────────────┐
       │                 │
       ▼                 ▼
┌─────────────┐   ┌─────────────┐
│  completed  │   │   failed    │
└─────────────┘   └──────┬──────┘
                         │
                         ▼
                  ┌─────────────┐
                  │  refunded   │ (transaction créée)
                  └─────────────┘
```

### Correspondance des statuts par opérateur

| Opérateur      | Statut succès           | Statut échec          |
|----------------|-------------------------|-----------------------|
| Orange Money   | `success`, `paid`       | `failed`, `declined`  |
| Airtel Money   | `status.success: true`  | `status.success: false` |
| M-Pesa         | `ResponseCode: 0`       | `ResponseCode: != 0`  |
| Africell       | `status: success`       | `status: failed`      |
| Illicocash     | `status: success`       | `status: error`       |

---

## 🛠️ Administration

### Réessayer un retrait échoué

**Route** : `POST /wallet/withdrawals/{withdrawalRequest}/retry`

**Middleware** : `auth, admin`

**Usage** :

```php
// Dans une vue admin
<form action="{{ route('wallet.withdrawals.retry', $withdrawalRequest) }}" method="POST">
    @csrf
    <button type="submit">Réessayer le retrait</button>
</form>
```

**Le système va** :

1. Vérifier que le statut est `failed`
2. Passer à `processing`
3. Réappeler l'API mobile money
4. Mettre à jour les réponses
5. Logger l'opération

---

## 📝 Logs

### Localisation

```
storage/logs/laravel.log
```

### Exemples de logs

**Initiation d'un retrait** :

```
[2025-10-06 14:30:00] local.INFO: Initiation cash-out {
    "provider": "orange_money",
    "phone": "+243812345678",
    "amount": 1000,
    "currency": "CDF",
    "transaction_id": 42
}
```

**Réception webhook** :

```
[2025-10-06 14:35:00] local.INFO: Withdrawal webhook received {
    "provider": "orange_money",
    "payload": {...}
}
```

**Statut extrait** :

```
[2025-10-06 14:35:01] local.INFO: Webhook status extracted {
    "reference": "WTH-1728234567-4521",
    "status": "completed",
    "provider_reference": "OM-TXN-123456"
}
```

**Retrait complété** :

```
[2025-10-06 14:35:02] local.INFO: Withdrawal completed successfully {
    "transaction_id": 42,
    "amount": 1000,
    "phone": "+243812345678"
}
```

**Erreur API** :

```
[2025-10-06 14:40:00] local.ERROR: Cash-out API error {
    "error": "Insufficient balance at operator",
    "transaction_id": 43
}
```

---

## 🧪 Tests

### Mode simulation

Par défaut, si les credentials ne sont pas configurés dans `.env`, le système utilise le **mode simulation**.

**Caractéristiques** :

- ✅ Simule 90% de réussite
- ❌ Simule 10% d'échec aléatoire
- 📝 Logs détaillés
- ⏱️ Délai de 1 seconde simulé
- 🔧 Parfait pour le développement

**Référence simulée** : `SIM-ORANGE_MONEY-1728234567-4521`

### Test manuel

```bash
# 1. Naviguer vers le formulaire de retrait
http://localhost:8000/wallet/1/withdraw-funds

# 2. Remplir :
#    - Montant: 1000 CDF
#    - Téléphone: 0812345678
#    - Opérateur: Orange Money

# 3. Soumettre

# 4. Vérifier les logs
tail -f storage/logs/laravel.log

# 5. Simuler un webhook (optionnel)
curl -X POST http://localhost:8000/wallet/withdrawals/webhook/orange_money \
  -H "Content-Type: application/json" \
  -d '{
    "reference": "WTH-1728234567-4521",
    "status": "success",
    "payment_token": "TEST-12345"
  }'
```

---

## ⚠️ Gestion des erreurs

### Erreurs possibles

| Erreur | Cause | Solution |
|--------|-------|----------|
| **Solde insuffisant** | `amount > wallet->balance` | Afficher message d'erreur |
| **Numéro invalide** | Format incorrect | Validation regex frontend |
| **API timeout** | Opérateur indisponible | Status `failed`, notification admin |
| **Signature invalide** | Webhook falsifié | Rejeter le webhook, log warning |
| **Transaction introuvable** | Référence inconnue | Log error, retourner 404 |

### Remboursement automatique

Si un retrait échoue (via webhook), le système :

1. ✅ Incrémente le wallet du montant
2. ✅ Crée une transaction de type `credit` avec référence `REFUND-{original_reference}`
3. ✅ Met à jour le statut à `failed`
4. ✅ Log l'opération

**Code** :

```php
if ($webhookStatus === 'failed') {
    DB::transaction(function () use ($transaction) {
        $wallet = $transaction->wallet;
        $wallet->increment('balance', $transaction->amount);
        
        $wallet->transactions()->create([
            'type' => 'credit',
            'amount' => $transaction->amount,
            'balance_after' => $wallet->fresh()->balance,
            'description' => 'Remboursement suite à échec de retrait - Ref: ' . $transaction->reference,
            'reference' => 'REFUND-' . $transaction->reference,
            'status' => 'completed',
        ]);
    });
}
```

---

## 🔒 Sécurité

### 1. Protection CSRF

Les webhooks sont **exemptés** de CSRF :

```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'wallet/withdrawals/webhook/*',
];
```

### 2. Vérification des signatures

Chaque opérateur a sa méthode de vérification :

**Orange Money** : HMAC SHA256

```php
$signature = hash_hmac('sha256', $payload, $secret);
```

**Airtel Money** : Token dans header

```php
$token = $request->header('X-Airtel-Webhook-Token');
```

**M-Pesa** : Clé publique RSA

```php
// TODO: Implémenter vérification RSA
```

### 3. Validation des données

```php
$validated = $request->validate([
    'amount' => 'required|numeric|min:0.01|max:' . $wallet->balance,
    'phone_number' => 'required|string|regex:/^(\+?243|0)?[0-9]{9}$/|min:9|max:15',
    'payment_method' => 'required|string|in:orange_money,airtel_money,mpesa,africell,illicocash',
]);
```

### 4. Autorisation

```php
if ($wallet->user_id !== Auth::id()) {
    abort(403, 'Accès non autorisé');
}
```

---

## 📈 Métriques et monitoring

### Données à surveiller

1. **Taux de réussite par opérateur**

```sql
SELECT 
    provider,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    ROUND(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as success_rate
FROM withdrawal_requests
GROUP BY provider;
```

2. **Montant total par devise**

```sql
SELECT 
    currency,
    COUNT(*) as transactions,
    SUM(amount) as total_amount,
    AVG(amount) as avg_amount
FROM withdrawal_requests
WHERE status = 'completed'
GROUP BY currency;
```

3. **Temps de traitement moyen**

```sql
SELECT 
    provider,
    AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_processing_time_seconds
FROM withdrawal_requests
WHERE status = 'completed'
GROUP BY provider;
```

---

## 🎨 Interface utilisateur

### Messages de statut

**En cours** :

```php
return redirect()->route('wallet.index')
    ->with('success', 'Demande de retrait en cours de traitement ! Les fonds seront envoyés vers ' . $phone . ' sous quelques minutes.');
```

**Échec immédiat** :

```php
return redirect()->route('wallet.index')
    ->with('error', 'Le décaissement a échoué : ' . $message);
```

**Échec reporté (réessai possible)** :

```php
return redirect()->route('wallet.index')
    ->with('warning', 'La demande de retrait a été enregistrée mais l\'envoi a échoué. Notre équipe va réessayer manuellement.');
```

### Notifications (optionnel)

```php
// Dans handleWithdrawalWebhook() si completed
event(new WithdrawalCompleted($transaction));

// Listener envoie email/SMS
Mail::to($user)->send(new WithdrawalCompletedMail($transaction));
```

---

## 🚧 Prochaines étapes

### Améliorations possibles

1. **✅ Interface admin complète**
   - Vue des retraits en attente
   - Dashboard avec métriques
   - Actions en masse (réessayer, annuler)

2. **✅ Notifications en temps réel**
   - WebSockets pour notifier l'utilisateur
   - Push notifications mobiles
   - SMS de confirmation

3. **✅ Gestion avancée des erreurs**
   - Retry automatique avec backoff exponentiel
   - Circuit breaker si opérateur down
   - Fallback vers autre opérateur

4. **✅ Cache des statuts**
   - Redis pour tracker les retraits en cours
   - Queue jobs pour vérification périodique
   - Timeout automatique après 24h

5. **✅ Reporting avancé**
   - Export CSV/Excel des transactions
   - Graphiques de performance
   - Alertes si taux d'échec > seuil

---

## 📞 Support

### En cas de problème

1. **Vérifier les logs** : `storage/logs/laravel.log`
2. **Vérifier la configuration** : `.env` et `config/services.php`
3. **Tester en mode simulation** : Retirer les credentials de `.env`
4. **Contacter le support de l'opérateur** : Vérifier que l'IP du serveur est whitelistée

### Contacts opérateurs (RDC)

- **Orange Money** : support@orange.cd
- **Airtel Money** : support@airtel.cd
- **M-Pesa** : support@vodacom.cd
- **Africell** : support@africell.cd
- **Illicocash** : support@illicocash.com

---

## ✅ Checklist de déploiement

Avant de mettre en production :

- [ ] Obtenir les credentials de TOUS les opérateurs
- [ ] Configurer `.env` avec les vraies clés
- [ ] Tester chaque opérateur en environnement sandbox
- [ ] Configurer les webhooks chez chaque opérateur
- [ ] Whitelister l'IP du serveur de production
- [ ] Activer les logs de production
- [ ] Configurer les alertes monitoring
- [ ] Tester le flow complet end-to-end
- [ ] Documenter les procédures d'urgence
- [ ] Former l'équipe admin sur l'interface

---

**Document créé le** : 6 octobre 2025  
**Version** : 1.0  
**Auteur** : VintApp Development Team  
**Statut** : ✅ Système fonctionnel en mode simulation

