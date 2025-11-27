# 🔐 Guide d'Intégration CinetPay - VintApp

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Structure de la base de données](#structure-de-la-base-de-données)
5. [Flux de paiement](#flux-de-paiement)
6. [Intégration dans les vues](#intégration-dans-les-vues)
7. [Tests](#tests)
8. [Production](#production)
9. [Dépannage](#dépannage)

---

## 🎯 Vue d'ensemble

CinetPay est un gateway de paiement africain supportant:

-   💳 **Cartes bancaires** (Visa, Mastercard)
-   📱 **Mobile Money** (Orange, MTN, Moov, Airtel)
-   🏦 **Virements bancaires**

### Fonctionnalités implémentées

✅ Paiement des commandes  
✅ Rechargement de wallet  
✅ Webhook IPN sécurisé  
✅ Vérification de transaction  
✅ Prévention de fraude

---

## 🚀 Installation

### Étape 1: Exécuter la migration

```bash
php artisan migrate
```

Cela créera:

-   Table `payments` - Stockage des transactions
-   Colonnes `payment_transaction_id` et `payment_status` dans `orders`

### Étape 2: Ajouter les credentials au .env

Copiez ces lignes dans votre fichier `.env`:

```env
# CinetPay Configuration
CINETPAY_SITE_ID=124598
CINETPAY_API_KEY=39955468c7a8c0cef1.68322505
CINETPAY_PLATFORM=TEST
CINETPAY_VERSION=V2
```

> **⚠️ Important**: Les credentials ci-dessus sont pour le **mode TEST**. Pour la production, obtenez vos propres credentials sur https://cinetpay.com/

---

## ⚙️ Configuration

### 1. Namespace CinetPay

Le SDK a été copié dans `app/Services/CinetPay.php`. Aucune modification requise.

### 2. Routes configurées

```php
// Initier un paiement de commande
POST /payments/orders/{order}/pay

// Initier un rechargement wallet
POST /payments/wallet/topup

// Webhook IPN (appelé par CinetPay)
POST /payments/cinetpay/notify

// Page de retour (redirection utilisateur)
GET /payments/cinetpay/return
```

### 3. Modèle Payment

```php
use App\Models\Payment;

// Créer un paiement
$payment = Payment::create([
    'transaction_id' => 'VIN-20240115120000-123',
    'user_id' => auth()->id(),
    'order_id' => $order->id,
    'amount' => 10000,
    'currency' => 'XOF',
    'designation' => 'Paiement commande #123',
]);

// Marquer comme complété
$payment->markAsCompleted([
    'cpm_result' => '00',
    'payment_method' => 'ORANGE_MONEY_CI',
]);

// Vérifier le statut
if ($payment->isCompleted()) {
    // Paiement réussi
}
```

---

## 🗄️ Structure de la base de données

### Table `payments`

| Colonne            | Type          | Description                                       |
| ------------------ | ------------- | ------------------------------------------------- |
| `id`               | bigint        | ID auto-incrémenté                                |
| `transaction_id`   | string        | ID unique de transaction (ex: VIN-20240115-123)   |
| `user_id`          | bigint        | Utilisateur effectuant le paiement                |
| `order_id`         | bigint        | Commande associée (nullable pour wallet)          |
| `amount`           | decimal(15,2) | Montant à payer                                   |
| `currency`         | string(3)     | Devise (XOF, XAF, etc.)                           |
| `designation`      | string        | Description du paiement                           |
| `status`           | enum          | pending, processing, completed, failed, cancelled |
| `payment_method`   | enum          | card, mobile_money, bank, wallet                  |
| `cpm_trans_id`     | string        | ID CinetPay (retourné par API)                    |
| `cpm_result`       | string        | Code résultat ('00' = succès)                     |
| `cpm_trans_status` | string        | Statut transaction (ACCEPTED, REFUSED)            |
| `cpm_amount`       | decimal(15,2) | Montant confirmé par CinetPay                     |
| `metadata`         | text          | JSON avec données additionnelles                  |
| `paid_at`          | timestamp     | Date de paiement effectif                         |
| `ip_address`       | string        | IP de l'utilisateur                               |

### Ajouts table `orders`

| Colonne                  | Type   | Description                     |
| ------------------------ | ------ | ------------------------------- |
| `payment_transaction_id` | string | Lien vers le paiement           |
| `payment_status`         | enum   | pending, paid, failed, refunded |

---

## 🔄 Flux de paiement

### Diagramme du flux

```
┌────────────┐         ┌──────────────┐         ┌──────────────┐
│ Utilisateur│         │   VintApp    │         │   CinetPay   │
└─────┬──────┘         └──────┬───────┘         └──────┬───────┘
      │                       │                        │
      │ 1. Clic "Payer"       │                        │
      ├──────────────────────>│                        │
      │                       │                        │
      │                       │ 2. Créer Payment       │
      │                       │    (status: pending)   │
      │                       │                        │
      │                       │ 3. Générer formulaire  │
      │                       │    CinetPay            │
      │<──────────────────────┤                        │
      │                       │                        │
      │ 4. Soumettre paiement │                        │
      ├───────────────────────┼───────────────────────>│
      │                       │                        │
      │                       │                        │
      │                       │ 5. IPN Notification    │
      │                       │<───────────────────────┤
      │                       │                        │
      │                       │ 6. Vérifier transaction│
      │                       │────────────────────────>│
      │                       │<────────────────────────┤
      │                       │                        │
      │                       │ 7. Update DB           │
      │                       │    (status: completed) │
      │                       │                        │
      │ 8. Redirection retour │                        │
      │<──────────────────────┼────────────────────────┤
      │                       │                        │
      │ 9. Afficher statut    │                        │
      │<──────────────────────┤                        │
      │                       │                        │
```

### Explication détaillée

#### Phase 1: Initiation du paiement

```php
// OrderController ou vue de commande
Route::post('/payments/orders/{order}/pay', [PaymentController::class, 'initiateOrderPayment']);
```

1. **Vérifications préalables**:

    - L'utilisateur est authentifié
    - La commande appartient à l'utilisateur
    - La commande n'est pas déjà payée

2. **Création du paiement**:

    ```php
    $transactionId = 'VIN-' . date('YmdHis') . '-' . $order->id;

    $payment = Payment::create([
        'transaction_id' => $transactionId,
        'user_id' => auth()->id(),
        'order_id' => $order->id,
        'amount' => $order->total_amount,
        'status' => 'pending',
    ]);
    ```

3. **Affichage du formulaire CinetPay**:
    ```php
    $cinetPay = new CinetPay($siteId, $apiKey, $platform, $version);
    $cinetPay->setTransId($transactionId)
             ->setAmount($amount)
             ->setDesignation($description)
             ->setNotifyUrl(route('payments.cinetpay.notify'))
             ->setReturnUrl(route('payments.cinetpay.return'))
             ->displayPayButton('form', 2, 'large');
    ```

#### Phase 2: Traitement du paiement (IPN)

**⚠️ CRITIQUE**: Le webhook IPN est le **SEUL** endroit où la BD doit être mise à jour.

```php
// Route: POST /payments/cinetpay/notify
public function handleNotification(Request $request)
{
    // 1. Récupérer l'ID de transaction
    $transactionId = $request->input('cpm_trans_id');

    // 2. Vérifier le statut via l'API CinetPay
    $cinetPay = new CinetPay(...);
    $cinetPay->setTransId($transactionId)->getPayStatus();

    // 3. Prévention de fraude: vérifier le montant
    if ($cinetPay->_cpm_amount != $payment->amount) {
        Log::error('Fraud alert: Amount mismatch');
        return response('Bad Request', 400);
    }

    // 4. Éviter le traitement en double
    if ($payment->status === 'completed') {
        return response('OK', 200);
    }

    // 5. Mettre à jour selon le résultat
    if ($cinetPay->_cpm_result == '00') {
        // SUCCÈS
        $payment->markAsCompleted([...]);
        $order->update(['payment_status' => 'paid']);
    } else {
        // ÉCHEC
        $payment->markAsFailed($cinetPay->_cpm_result);
    }

    return response('OK', 200);
}
```

**Codes de résultat CinetPay**:

-   `00` - Paiement réussi ✅
-   `01` - Paiement échoué ❌
-   `02` - Transaction en attente ⏳
-   Autres codes - Erreurs spécifiques

#### Phase 3: Retour utilisateur

```php
// Route: GET /payments/cinetpay/return
public function handleReturn(Request $request)
{
    $transactionId = $request->input('transaction_id');
    $payment = Payment::where('transaction_id', $transactionId)->first();

    // NE PAS mettre à jour la BD ici
    // Juste rediriger avec message

    if ($payment->isCompleted()) {
        return redirect()->route('orders.show', $payment->order_id)
            ->with('success', 'Paiement réussi !');
    }

    return redirect()->route('orders.show', $payment->order_id)
        ->with('warning', 'Paiement en cours...');
}
```

---

## 🎨 Intégration dans les vues

### 1. Ajouter un bouton "Payer" sur la page de commande

**Fichier**: `resources/views/orders/show.blade.php`

```blade
@if($order->payment_status === 'pending')
<div class="mt-6">
    <form action="{{ route('payments.order.initiate', $order) }}" method="POST">
        @csrf
        <button type="submit" class="w-full bg-primary hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-lg transition">
            💳 Payer {{ number_format($order->total_amount, 0, ',', ' ') }} XOF
        </button>
    </form>

    <div class="mt-3 flex items-center justify-center text-sm text-gray-600">
        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
        </svg>
        Paiement sécurisé par CinetPay
    </div>
</div>
@elseif($order->payment_status === 'paid')
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
    ✅ Commande payée le {{ $order->updated_at->format('d/m/Y à H:i') }}
</div>
@endif
```

### 2. Rechargement de wallet

**Fichier**: `resources/views/wallet/index.blade.php` ou créer `wallet/topup.blade.php`

```blade
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">Recharger mon wallet</h2>

    <form action="{{ route('payments.wallet.topup') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">
                Montant (XOF) <span class="text-red-500">*</span>
            </label>
            <input
                type="number"
                name="amount"
                min="5"
                step="1"
                required
                class="w-full border-gray-300 rounded-lg focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                placeholder="Minimum 5 XOF"
            >
            <p class="text-xs text-gray-500 mt-1">Montant minimum: 5 XOF</p>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition">
            Continuer vers le paiement
        </button>
    </form>
</div>
```

### 3. Page de checkout (générée automatiquement)

La vue `resources/views/payments/checkout.blade.php` est déjà créée avec:

-   Résumé de la commande
-   Détails du paiement
-   Formulaire CinetPay intégré
-   Moyens de paiement acceptés

---

## 🧪 Tests

### 1. Test en mode TEST (Sandbox)

```bash
# Vérifier la configuration
php artisan tinker

>>> config('services.cinetpay.platform')
=> "TEST"

>>> config('services.cinetpay.site_id')
=> "124598"
```

### 2. Test de paiement de commande

1. Créer une commande de test
2. Aller sur la page de la commande
3. Cliquer sur "Payer"
4. Sur la page CinetPay TEST:
    - **Carte de test**: `4242424242424242` (Visa)
    - **Expiration**: N'importe quelle date future
    - **CVV**: `123`

### 3. Test du webhook IPN

```bash
# Surveiller les logs en temps réel
tail -f storage/logs/laravel.log | grep CinetPay
```

Vous devriez voir:

```
[INFO] CinetPay IPN Notification: {...}
[INFO] CinetPay: Paiement confirmé - VIN-20240115120000-1
```

### 4. Test de rechargement wallet

```php
// Dans Tinker
php artisan tinker

>>> $user = User::find(1);
>>> $payment = Payment::create([
    'transaction_id' => 'TEST-' . time(),
    'user_id' => $user->id,
    'amount' => 1000,
    'currency' => 'XOF',
    'designation' => 'Test wallet topup',
    'status' => 'pending',
]);
```

### 5. Simuler un callback IPN

Créer un fichier de test `tests/Feature/CinetPayWebhookTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CinetPayWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_payment_webhook()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['buyer_id' => $user->id]);

        $payment = Payment::create([
            'transaction_id' => 'TEST-123',
            'user_id' => $user->id,
            'order_id' => $order->id,
            'amount' => 10000,
            'status' => 'pending',
        ]);

        // Simuler le webhook CinetPay
        $response = $this->postJson('/payments/cinetpay/notify', [
            'cpm_trans_id' => 'TEST-123',
            'cpm_amount' => 10000,
            'cpm_result' => '00',
            'cpm_trans_status' => 'ACCEPTED',
        ]);

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals('completed', $payment->status);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
    }
}
```

Lancer le test:

```bash
php artisan test --filter=CinetPayWebhookTest
```

---

## 🚀 Production

### 1. Obtenir les credentials de production

1. Créer un compte sur https://cinetpay.com/
2. Compléter la vérification KYC
3. Récupérer vos credentials de production:
    - `SITE_ID`
    - `API_KEY`

### 2. Mettre à jour le .env

```env
CINETPAY_SITE_ID=votre_site_id_prod
CINETPAY_API_KEY=votre_api_key_prod
CINETPAY_PLATFORM=PROD
CINETPAY_VERSION=V2
```

### 3. Configurer les URLs de callback

Dans votre dashboard CinetPay, configurer:

-   **URL de notification**: `https://vintapp.com/payments/cinetpay/notify`
-   **URL de retour**: `https://vintapp.com/payments/cinetpay/return`

### 4. Sécurité en production

#### a) Vérifier l'origine du webhook

Dans `PaymentController@handleNotification`:

```php
// Whitelist des IPs CinetPay (à demander au support)
$allowedIps = ['196.1.95.124', '41.78.184.22'];

if (!in_array($request->ip(), $allowedIps)) {
    Log::warning('CinetPay IPN from unauthorized IP: ' . $request->ip());
    return response('Unauthorized', 403);
}
```

#### b) Signature de requête (optionnel)

CinetPay peut signer les requêtes IPN avec un hash. Vérifier:

```php
$expectedHash = hash('sha256', $transactionId . $amount . $apiKey);
$receivedHash = $request->input('signature');

if ($expectedHash !== $receivedHash) {
    return response('Invalid signature', 400);
}
```

#### c) HTTPS obligatoire

```php
// app/Http/Middleware/ForceHttps.php
if (!$request->secure() && app()->environment('production')) {
    return redirect()->secure($request->getRequestUri());
}
```

### 5. Monitoring

```php
// Ajouter dans app/Providers/AppServiceProvider.php

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

public function boot()
{
    // Logger tous les paiements complétés
    Payment::completed()->chunk(100, function ($payments) {
        foreach ($payments as $payment) {
            Log::channel('payments')->info("Payment completed", [
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'user_id' => $payment->user_id,
            ]);
        }
    });
}
```

Créer un channel de log dédié dans `config/logging.php`:

```php
'payments' => [
    'driver' => 'daily',
    'path' => storage_path('logs/payments.log'),
    'level' => 'info',
    'days' => 90,
],
```

---

## 🔧 Dépannage

### Problème 1: Le webhook IPN n'est jamais appelé

**Symptômes**: Le paiement reste en statut "pending" après paiement réussi.

**Solutions**:

1. Vérifier que l'URL de notification est accessible publiquement

    ```bash
    curl -X POST https://vintapp.com/payments/cinetpay/notify
    ```

2. Désactiver temporairement les middlewares auth/CSRF sur la route IPN:

    ```php
    // app/Http/Middleware/VerifyCsrfToken.php
    protected $except = [
        'payments/cinetpay/notify',
    ];
    ```

3. Vérifier les logs CinetPay dans leur dashboard

### Problème 2: Erreur "Transaction ID manquant"

**Cause**: Le formulaire CinetPay n'envoie pas le `cpm_trans_id`.

**Solution**: Vérifier que `setTransId()` est bien appelé avant `displayPayButton()`.

### Problème 3: Montant incohérent (Fraud Alert)

**Cause**: Le montant payé ne correspond pas au montant enregistré en BD.

**Solutions**:

1. Vérifier que le montant est en XOF (pas de virgule)
2. Vérifier que le montant n'a pas été modifié côté client
3. Logs pour débugger:
    ```php
    Log::info('Amount comparison', [
        'db_amount' => $payment->amount,
        'api_amount' => $cinetPay->_cpm_amount,
    ]);
    ```

### Problème 4: Paiements en double

**Symptômes**: L'utilisateur est débité plusieurs fois.

**Solution**: Le controller vérifie déjà:

```php
if ($payment->status === 'completed') {
    Log::info("Payment already processed");
    return response('OK', 200);
}
```

Ajouter un index unique sur `transaction_id` (déjà fait dans la migration).

### Problème 5: Classe CinetPay introuvable

**Erreur**: `Class 'App\Services\CinetPay' not found`

**Solution**:

```bash
# Vérifier que le fichier existe
ls -l app/Services/CinetPay.php

# Vider le cache
php artisan clear-compiled
composer dump-autoload
```

---

## 📊 Codes d'erreur CinetPay

| Code  | Description                 | Action                   |
| ----- | --------------------------- | ------------------------ |
| `00`  | Paiement réussi             | Marquer comme complété   |
| `01`  | Paiement échoué             | Marquer comme échoué     |
| `02`  | Transaction en attente      | Attendre callback        |
| `03`  | Transaction annulée         | Marquer comme annulé     |
| `624` | Montant invalide            | Vérifier montant ≥ 5 XOF |
| `625` | Transaction ID déjà utilisé | Générer nouvel ID        |

---

## 🎯 Checklist de déploiement

Avant de passer en production:

-   [ ] Migration exécutée (`php artisan migrate`)
-   [ ] Credentials de production configurés dans `.env`
-   [ ] Routes CinetPay testées
-   [ ] Webhook IPN accessible publiquement (HTTPS)
-   [ ] CSRF désactivé pour la route IPN
-   [ ] Logs activés (`storage/logs/laravel.log`)
-   [ ] Tests de paiement effectués en mode TEST
-   [ ] Vérification de montant activée (anti-fraude)
-   [ ] Prévention de double traitement activée
-   [ ] Monitoring des paiements configuré
-   [ ] Support technique CinetPay contacté pour validation

---

## 📞 Support

-   **Documentation CinetPay**: https://docs.cinetpay.com/
-   **Dashboard CinetPay**: https://www.cinetpay.com/login
-   **Support CinetPay**: support@cinetpay.com
-   **WhatsApp**: +225 05 45 50 50 50

---

## 📝 Notes additionnelles

### Logique métier à ajouter

Après un paiement réussi, vous devez implémenter:

1. **Transfert de fonds au vendeur**:

    ```php
    // Dans handleNotification() après markAsCompleted()
    $order->seller->wallet->credit($order->total_amount * 0.95); // 95% au vendeur
    ```

2. **Commission de la plateforme**:

    ```php
    $commission = $order->total_amount * 0.05; // 5% commission
    Transaction::create([
        'type' => 'commission',
        'amount' => $commission,
        'order_id' => $order->id,
    ]);
    ```

3. **Notifications**:

    ```php
    // Notifier le vendeur
    $order->seller->notify(new PaymentReceived($order));

    // Notifier l'acheteur
    $order->buyer->notify(new PaymentConfirmed($order));
    ```

4. **Email de confirmation**:
    ```php
    Mail::to($order->buyer->email)->send(new PaymentReceipt($order, $payment));
    ```

---

**Créé le**: {{ now()->format('d/m/Y') }}  
**Version**: 1.0  
**Auteur**: VintApp Dev Team
