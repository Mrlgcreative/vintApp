# Diagramme des liens entre contrôleurs

## 🔄 Architecture globale

```
┌─────────────────────────────────────────────────────────────────┐
│                        SYSTÈME WALLET & PAYMENT                 │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────┐         ┌──────────────────┐         ┌──────────────────┐
│                  │         │                  │         │                  │
│ WalletController │◄───────►│ PaymentService   │◄───────►│PaymentController │
│                  │         │                  │         │                  │
└────────┬─────────┘         └──────────────────┘         └────────┬─────────┘
         │                                                          │
         │                                                          │
         │ Crée Transaction                                        │ Crée Transaction
         │ (status: pending)                                       │ (status: pending)
         │                                                          │
         ▼                                                          ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                             Transaction Model                            │
│  • id, user_id, amount, status (pending/completed/failed)               │
│  • provider (orange_money, mpesa, etc.)                                 │
│  • reference, description                                               │
└─────────────────────────────────────────────────────────────────────────┘
         │                                                          │
         │                                                          │
         └──────────────────────────┬───────────────────────────────┘
                                    │
                                    │ Lit Transactions
                                    │ (status: pending)
                                    ▼
                      ┌──────────────────────────────┐
                      │  PendingWalletController     │
                      │  • index()                   │
                      │  • show()                    │
                      │  • confirmTransfer()         │
                      │  • cancelTransaction()       │
                      └──────────────────────────────┘
```

## 📊 Flux de recharge wallet

```
[Utilisateur]
    │
    │ 1. Demande recharge
    ▼
[WalletController::rechargeWithMobilePayment()]
    │
    │ 2. Injecte PaymentService
    ▼
[PaymentService::payWithOrangeMoney()]
    │
    │ 3. Appelle méthode de paiement
    ▼
[PaymentController::payWithOrangeMoney()]
    │
    │ 4. Appel API Orange Money
    ▼
[API Orange Money]
    │
    │ 5. Retourne status: pending
    ▼
[Transaction créée]
    • status: pending
    • type: credit_pending
    • provider: orange_money
    │
    │ 6. Visible dans admin
    ▼
[PendingWalletController::index()]
    │
    │ 7. Admin voit la transaction
    ▼
[Admin confirme ou attend callback]
    │
    ├─► Callback reçu
    │   [PaymentController::handleCallback()]
    │   └─► Transaction updated: completed
    │
    └─► Admin confirme manuellement
        [PendingWalletController::confirmTransfer()]
        └─► Fonds transférés au wallet principal
```

## 🔗 Liens entre modèles

```
┌─────────┐         ┌──────────────┐         ┌─────────────────┐
│  User   │────────►│   Wallet     │────────►│  Transaction    │
└─────────┘ 1:N     └──────────────┘ 1:N     └─────────────────┘
   │                       │
   │                       ├─► Main Wallet (balance principal)
   │                       └─► Pending Wallet (fonds en attente)
   │
   └──────► Orders, Items, Reviews, etc.
```

## 📡 Routes et méthodes

### WalletController
```
GET  /wallet                            → Liste des wallets USD/CDF
GET  /wallet/{id}/transactions          → Historique
POST /wallet/{id}/recharge/mobile       → Recharge ⭐
  │
  └──► Appelle PaymentService
       └──► Qui utilise PaymentController
```

### PaymentController
```
POST /payments/illicocash      → Paiement Illicocash
POST /payments/orange-money    → Paiement Orange Money ⭐
POST /payments/airtel-money    → Paiement Airtel Money
POST /payments/mpesa           → Paiement M-Pesa
POST /payments/africell        → Paiement Africell
POST /payments/callback        → Callback opérateurs
POST /payments/simulate        → Simulation (tests)
```

### PendingWalletController
```
GET  /pending-wallets                      → Liste des wallets pending
GET  /pending-wallets/{id}                 → Détails wallet
POST /pending-wallets/{id}/confirm-transfer → Confirmer ⭐
POST /pending-wallets/{id}/cancel-transaction → Annuler
```

## 🎯 Points de contact

### 1. WalletController ↔ PaymentService
```php
// WalletController.php
private $paymentService;

public function __construct(PaymentService $paymentService) {
    $this->paymentService = $paymentService;
}

public function rechargeWithMobilePayment() {
    $response = $this->paymentService->payWithOrangeMoney($data);
}
```

### 2. PaymentController ↔ PaymentService
```php
// PaymentController.php
protected $paymentService;

public function __construct(PaymentService $paymentService) {
    $this->paymentService = $paymentService;
}

public function processPayment() {
    $result = $this->paymentService->{$methodName}($data);
}
```

### 3. PendingWalletController ↔ Transaction Model
```php
// PendingWalletController.php
$query = Wallet::where('type', 'pending')
    ->with(['transactions' => function ($q) {
        $q->where('status', 'pending');
    }]);
```

## 🔄 Cycle de vie d'une transaction

```
┌──────────────┐
│   CREATED    │ Transaction créée (WalletController ou PaymentController)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   PENDING    │ En attente de confirmation opérateur
└──────┬───────┘
       │
       ├─────► Callback reçu (succès)
       │       └──► COMPLETED
       │
       ├─────► Callback reçu (échec)
       │       └──► FAILED
       │
       └─────► Admin confirme
               └──► COMPLETED

       Admin annule
       └──► CANCELLED
```

## 💰 États des wallets

```
Main Wallet (principal)
├─► balance: 1000.00 USD
├─► status: active
└─► type: main

Pending Wallet (en attente)
├─► balance: 50.00 USD (fonds non confirmés)
├─► status: pending
└─► type: pending
    │
    └─► Après confirmation → Transféré au Main Wallet
```

## 🔐 Sécurité et validations

```
WalletController
├─► Vérifie ownership: $wallet->user_id === Auth::id()
└─► Valide montants: min:0.01, max:balance

PaymentController
├─► Vérifie provider activé: config('payments.providers.xxx.enabled')
├─► Valide phone: min:9, max:9
└─► CSRF Token requis

PendingWalletController
├─► Vérifie type: $wallet->type === 'pending'
├─► Vérifie status transaction: $transaction->status === 'pending'
└─► Transaction DB pour atomicité
```

## 📱 Opérateurs supportés

```
┌────────────────┐
│  Orange Money  │ ✅ Activé
└────────────────┘

┌────────────────┐
│     M-Pesa     │ ✅ Activé
└────────────────┘

┌────────────────┐
│ Airtel Money   │ ✅ Activé
└────────────────┘

┌────────────────┐
│   Africell     │ ✅ Activé
└────────────────┘

┌────────────────┐
│  Illicocash    │ ✅ Activé
└────────────────┘
```

## 🧪 Scénarios de test

### Test 1: Recharge complète
```
1. POST /wallet/1/recharge/mobile (Orange Money, 100$)
2. Transaction créée (status: pending)
3. GET /pending-wallets → Transaction visible
4. POST /payments/callback (success)
5. Transaction updated (status: completed)
6. GET /wallet → Balance +100$
```

### Test 2: Recharge avec confirmation admin
```
1. POST /wallet/1/recharge/mobile (M-Pesa, 50$)
2. Transaction créée (status: pending)
3. GET /pending-wallets/1 → Voir détails
4. POST /pending-wallets/1/confirm-transfer
5. Fonds transférés au wallet principal
6. GET /wallet → Balance +50$
```

### Test 3: Échec de paiement
```
1. POST /wallet/1/recharge/mobile (Airtel, 25$)
2. Transaction créée (status: pending)
3. POST /payments/callback (failed)
4. Transaction updated (status: failed)
5. GET /wallet → Balance inchangée
```

---

**Note** : Ce diagramme ASCII est simplifié. Pour une visualisation complète, utiliser des outils comme draw.io ou Mermaid.
