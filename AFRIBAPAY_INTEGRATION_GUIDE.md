# Guide d'Intégration AfribaPay pour VintApp

## 📋 Vue d'ensemble

AfribaPay est une passerelle de paiement Mobile Money couvrant 12 pays africains avec support de 5 devises différentes.

### Pays supportés

-   🇧🇯 Bénin (XOF)
-   🇧🇫 Burkina Faso (XOF)
-   🇨🇲 Cameroun (XAF)
-   🇨🇩 RDC (CDF, USD)
-   🇨🇮 Côte d'Ivoire (XOF)
-   🇬🇦 Gabon (XAF)
-   🇬🇳 Guinée Conakry (GNF)
-   🇲🇱 Mali (XOF)
-   🇳🇪 Niger (XOF)
-   🇸🇳 Sénégal (XOF)
-   🇹🇬 Togo (XOF)

### Opérateurs Mobile Money

-   Airtel Money
-   MTN Money
-   Orange Money
-   Moov Money
-   Mpesa
-   Vodacom
-   Wave Money
-   T-Money
-   Afri Money
-   Et plus...

## 🚀 Installation et Configuration

### 1. Variables d'environnement (.env)

```env
# AfribaPay Payment Gateway
AFRIBAPAY_TOKEN=your_bearer_token_here
AFRIBAPAY_ENVIRONMENT=sandbox  # ou production
AFRIBAPAY_WEBHOOK_SECRET=your_webhook_secret_here
```

### 2. Configuration (config/services.php)

Déjà configuré dans `config/services.php` :

```php
'afribapay' => [
    'token' => env('AFRIBAPAY_TOKEN'),
    'environment' => env('AFRIBAPAY_ENVIRONMENT', 'sandbox'),
    'webhook_secret' => env('AFRIBAPAY_WEBHOOK_SECRET'),
],
```

## 📂 Structure des fichiers créés

### Services

-   ✅ `app/Services/AfribaPay.php` - SDK AfribaPay complet

### Controllers

-   ✅ `PaymentController::initiateAfribaPayment()` - Initier un paiement
-   ✅ `PaymentController::verifyAfribaOTP()` - Vérifier l'OTP
-   ✅ `PaymentController::handleAfribaNotification()` - Webhook
-   ✅ `PaymentController::handleAfribaReturn()` - Page de retour
-   ✅ `PaymentController::showAfribaStatus()` - Page de statut
-   ✅ `PaymentController::checkAfribaStatus()` - API AJAX status

### Routes

-   ✅ `POST /payments/afribapay/initiate` - Initier paiement
-   ✅ `POST /payments/afribapay/{payment}/verify-otp` - Vérifier OTP
-   ✅ `GET /payments/afribapay/{payment}/status` - Page statut
-   ✅ `GET /payments/afribapay/{payment}/check-status` - API statut
-   ✅ `GET /payments/afribapay/return` - Page de retour
-   ✅ `POST /payments/afribapay/notify` - Webhook

### Vues (à créer)

-   ⏳ `resources/views/payments/afribapay-form.blade.php` - Formulaire de paiement
-   ⏳ `resources/views/payments/afribapay-otp.blade.php` - Saisie OTP
-   ⏳ `resources/views/payments/afribapay-status.blade.php` - Vérification statut
-   ⏳ `resources/views/payments/afribapay-return.blade.php` - Confirmation finale

## 🔧 Utilisation du SDK

### Exemple d'initiation de paiement

```php
use App\Services\AfribaPay;

$afribaPay = new AfribaPay();

// Initier un paiement
$payment = $afribaPay->initiatePayment([
    'reference' => 'AFRIBA-' . time(),
    'amount' => 5000,
    'currency' => 'CDF',
    'country_code' => 'CD',
    'phone_number' => '243120000001',  // Format: 243XXXXXXXXX
    'operator_code' => 'airtel',
    'description' => 'Achat article VintApp',
    'callback_url' => route('payments.afribapay.notify'),
    'return_url' => route('payments.afribapay.return'),
    'customer_name' => 'Jean Dupont',
    'customer_email' => 'jean@example.com',
]);
```

### Vérifier un OTP

```php
$result = $afribaPay->verifyOTP('transaction_id', '123456');
```

### Vérifier le statut

```php
$status = $afribaPay->checkStatus('transaction_id');
```

### Obtenir les pays/opérateurs

```php
$countries = $afribaPay->getCountries();
```

## 📱 Flux de Paiement

### 1. Sans OTP (automatique)

```
User clique "Payer"
  ↓
Formulaire: Téléphone + Opérateur
  ↓
Initiation du paiement
  ↓
Redirection vers page de statut
  ↓
Polling automatique du statut (AJAX)
  ↓
SUCCESS → Commandes créées
  ↓
Page de confirmation
```

### 2. Avec OTP (Orange, LigdiCash, etc.)

```
User clique "Payer"
  ↓
Formulaire: Téléphone + Opérateur
  ↓
Initiation du paiement
  ↓
Page OTP affichée (avec USSD code)
  ↓
User saisit OTP reçu par SMS/USSD
  ↓
Vérification OTP
  ↓
SUCCESS → Commandes créées
  ↓
Page de confirmation
```

## 🧪 Tests en Sandbox

### Numéros de test par devise

#### CDF (RDC)

-   ✅ `243120000011` → SUCCESS
-   ⏳ `243120000012` → PENDING
-   ❌ `243120000013` → FAILED

#### USD (RDC)

-   ✅ `243120000001` → SUCCESS
-   ⏳ `243120000002` → PENDING
-   ❌ `243120000003` → FAILED

#### XOF (Côte d'Ivoire)

-   ✅ `2252100000001` → SUCCESS
-   ⏳ `2252100000002` → PENDING
-   ❌ `2252100000003` → FAILED

#### XAF (Cameroun)

-   ✅ `237660000001` → SUCCESS
-   ⏳ `237660000002` → PENDING
-   ❌ `237660000003` → FAILED

#### GNF (Guinée)

-   ✅ `224600000001` → SUCCESS
-   ⏳ `224600000002` → PENDING
-   ❌ `224600000003` → FAILED

### OTP de test

| Code OTP                     | Résultat             |
| ---------------------------- | -------------------- |
| `000000` → `444444`          | ❌ FAILED (réservés) |
| Tout autre code à 6 chiffres | ✅ SUCCESS           |

## 🔐 Sécurité

### Vérification du Webhook

```php
// Dans handleAfribaNotification()
$signature = $request->header('X-Afriba-Signature');
$expectedSignature = hash_hmac('sha256', $request->getContent(), config('services.afribapay.webhook_secret'));

if (!hash_equals($expectedSignature, $signature)) {
    return response()->json(['error' => 'Invalid signature'], 403);
}
```

## 📊 Structure de la table payments

Champs existants suffisants :

-   `transaction_id` → Référence AfribaPay
-   `metadata` → JSON contenant:
    -   `afribapay_transaction_id`
    -   `afribapay_response`
    -   `otp_verified`
    -   `phone_number`
    -   `operator_code`
    -   `country_code`

## 🎨 Exemple de formulaire checkout

```blade
<form action="{{ route('payments.afribapay.initiate') }}" method="POST">
    @csrf

    {{-- Champs cachés du panier --}}
    <input type="hidden" name="cart_items" value="{{ json_encode($cartItems) }}">
    <input type="hidden" name="total_amount" value="{{ $total }}">
    <input type="hidden" name="delivery_address_id" value="{{ $addressId }}">

    {{-- Sélection devise --}}
    <select name="currency" required>
        <option value="CDF">CDF (Franc Congolais)</option>
        <option value="USD">USD (Dollar)</option>
        <option value="XOF">XOF (Franc CFA)</option>
        <option value="XAF">XAF (Franc CFA Central)</option>
        <option value="GNF">GNF (Franc Guinéen)</option>
    </select>

    {{-- Sélection pays --}}
    <select name="country_code" id="country" required>
        <option value="CD">🇨🇩 R.D. Congo</option>
        <option value="CI">🇨🇮 Côte d'Ivoire</option>
        <option value="CM">🇨🇲 Cameroun</option>
        <!-- ... autres pays ... -->
    </select>

    {{-- Sélection opérateur --}}
    <select name="operator_code" required>
        <option value="airtel">Airtel Money</option>
        <option value="orange">Orange Money</option>
        <option value="mpesa">Mpesa</option>
        <option value="vodacom">Vodacom</option>
        <!-- Dynamique selon pays/devise -->
    </select>

    {{-- Numéro de téléphone --}}
    <input type="tel" name="phone_number"
           placeholder="Ex: 243812345678"
           pattern="[0-9]{10,15}"
           required>

    <button type="submit">Payer {{ number_format($total) }} {{ $currency }}</button>
</form>
```

## ⚡ Avantages AfribaPay

1. ✅ **Large couverture** - 12 pays, 5 devises
2. ✅ **RDC en CDF ET USD** - Unique !
3. ✅ **API simple** - REST avec Bearer token
4. ✅ **Statut en temps réel** - Polling facile
5. ✅ **Support OTP** - Sécurisé pour certains opérateurs
6. ✅ **Sandbox complet** - Numéros de test par statut

## 📞 Support AfribaPay

-   Documentation: https://api-sandbox.afribapay.com/docs
-   Email: support@afribapay.com

## ✅ Checklist d'intégration

-   [x] SDK créé (`app/Services/AfribaPay.php`)
-   [x] Configuration ajoutée (`.env`, `config/services.php`)
-   [x] Méthodes Controller créées
-   [x] Routes enregistrées
-   [ ] Migration ajoutée (optionnel, metadata JSON suffit)
-   [ ] Vues créées (formulaire, OTP, statut, retour)
-   [ ] Tests effectués en sandbox
-   [ ] Webhook testé avec ngrok
-   [ ] Documentation utilisateur créée
-   [ ] Passage en production

## 🎯 Prochaines étapes

1. Créer les vues Blade (formulaire, OTP, statut)
2. Tester avec numéros sandbox
3. Intégrer au checkout existant
4. Configurer webhook avec ngrok
5. Tester flux complet
6. Obtenir token production
7. Déployer en production

---

**Auteur**: Claude AI
**Date**: 16 novembre 2025
**Version**: 1.0.0
