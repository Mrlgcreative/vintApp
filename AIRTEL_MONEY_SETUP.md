# 📱 Configuration Airtel Money API - Guide Complet

## 🎯 URL de Callback pour Airtel Money

### ✅ URL à fournir à Airtel :

```
https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
```

**Important** : Cette URL est déjà configurée dans votre application Laravel !

---

## 📋 Informations requises par Airtel

### 1. Données à fournir lors de l'inscription :

| Champ | Valeur |
|-------|--------|
| **Application Name** | VintApp |
| **Webhook URL (Callback)** | `https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money` |
| **Success URL** | `https://uncomely-uneffusing-averie.ngrok-free.dev/payment/success` |
| **Cancel URL** | `https://uncomely-uneffusing-averie.ngrok-free.dev/payment/cancel` |
| **Environment** | Sandbox (test) ou Production |
| **Description** | Plateforme de vente de vêtements vintage |

### 2. URLs importantes :

```
Production:
- Callback: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
- Success: https://uncomely-uneffusing-averie.ngrok-free.dev/payment/success
- Cancel:  https://uncomely-uneffusing-averie.ngrok-free.dev/payment/cancel

Développement (ngrok):
- Callback: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
```

---

## 🔧 Configuration Laravel

### 1. Vérifier que le callback existe

Votre route de callback est déjà configurée dans `routes/api.php` :

```php
// Routes de callback pour les paiements
Route::prefix('payment-callbacks')->group(function () {
    Route::post('/airtel_money', [PaymentCallbackController::class, 'handleCallback'])
        ->name('payment.callback.airtel');
});
```

### 2. Configurer le .env

Une fois que vous recevez vos credentials d'Airtel, ajoutez-les dans `.env` :

```env
# Airtel Money API
AIRTEL_MONEY_ENABLED=true
AIRTEL_MONEY_API_KEY=votre_api_key_ici
AIRTEL_MONEY_API_SECRET=votre_api_secret_ici
AIRTEL_MONEY_MERCHANT_ID=votre_merchant_id
AIRTEL_MONEY_CLIENT_ID=votre_client_id
AIRTEL_MONEY_CLIENT_SECRET=votre_client_secret
AIRTEL_MONEY_ENVIRONMENT=sandbox  # ou production
```

---

## 📡 Format de Callback attendu

Airtel Money enverra une requête POST à votre webhook avec ce format :

```json
{
  "transaction": {
    "id": "MP210123.1234.A12345",
    "status": "success",
    "amount": 5000,
    "currency": "CDF",
    "reference": "ORDER-123456",
    "phone": "243900000000",
    "timestamp": "2025-01-10T14:30:00Z"
  }
}
```

---

## 🛠️ Vérification du Webhook

### Tester que votre webhook est accessible :

#### Avec curl (PowerShell) :

```powershell
curl -X POST "https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money" `
  -H "Content-Type: application/json" `
  -d '{\"transaction\":{\"id\":\"TEST123\",\"status\":\"success\"}}'
```

#### Avec un navigateur :

Ouvrir : https://uncomely-uneffusing-averie.ngrok-free.dev/api/health

**Résultat attendu** :
```json
{
  "status": "success",
  "message": "VintApp API is running",
  "version": "1.0.0"
}
```

---

## 📊 Vérifier le contrôleur

Assurons-nous que `PaymentCallbackController` gère bien Airtel Money :

```php
// app/Http/Controllers/PaymentCallbackController.php

public function handleCallback(Request $request, $provider)
{
    // $provider sera 'airtel_money'
    
    // Valider le provider
    if ($provider !== 'airtel_money') {
        return response()->json(['error' => 'Invalid provider'], 400);
    }
    
    // Récupérer les données du callback
    $data = $request->all();
    
    // Logger pour debug
    \Log::info('Airtel Money Callback', $data);
    
    // Traiter le paiement
    $transaction = $data['transaction'] ?? null;
    
    if ($transaction && $transaction['status'] === 'success') {
        // Mettre à jour la commande
        // Envoyer notification
        // etc.
    }
    
    // Répondre à Airtel
    return response()->json([
        'status' => 'success',
        'message' => 'Callback received'
    ]);
}
```

---

## 🔒 Sécurité

### 1. Vérifier la signature du callback

Airtel envoie généralement une signature pour valider l'authenticité :

```php
public function verifyAirtelSignature(Request $request)
{
    $signature = $request->header('X-Airtel-Signature');
    $payload = $request->getContent();
    
    $expectedSignature = hash_hmac(
        'sha256',
        $payload,
        config('services.airtel.api_secret')
    );
    
    return hash_equals($expectedSignature, $signature);
}
```

### 2. Valider l'IP source

```php
// Whitelist des IPs Airtel (à obtenir de leur documentation)
$allowedIps = [
    '41.x.x.x',
    '196.x.x.x',
];

if (!in_array($request->ip(), $allowedIps)) {
    return response()->json(['error' => 'Unauthorized'], 403);
}
```

---

## 🧪 Tests

### 1. Test manuel avec Postman

**Endpoint** : `POST https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money`

**Headers** :
```
Content-Type: application/json
```

**Body** :
```json
{
  "transaction": {
    "id": "TEST-123456",
    "status": "success",
    "amount": 5000,
    "currency": "CDF",
    "reference": "ORDER-789",
    "phone": "243900000000",
    "timestamp": "2025-01-10T14:30:00Z"
  }
}
```

**Résultat attendu** :
```json
{
  "status": "success",
  "message": "Callback received"
}
```

### 2. Vérifier les logs Laravel

```bash
tail -f storage/logs/laravel.log
```

Vous devriez voir :
```
[2025-01-10 14:30:00] local.INFO: Airtel Money Callback {"transaction":{"id":"TEST-123456",...}}
```

---

## 📱 URLs pour différents environnements

### Développement (ngrok)
```
Callback: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
```

### Production (quand vous aurez un domaine)
```
Callback: https://votredomaine.com/api/payment-callbacks/airtel_money
```

---

## 🐛 Dépannage

### Erreur : "URL not reachable"

**Solution** : Vérifier que ngrok est bien démarré
```bash
ngrok http 8000
```

### Erreur : "Invalid SSL certificate"

**Cause** : Airtel requiert HTTPS avec certificat valide

**Solution** : 
- ✅ ngrok fournit automatiquement HTTPS
- ✅ Votre URL est déjà en HTTPS

### Erreur : "Callback endpoint not found"

**Solution** : Vérifier que Laravel est bien démarré
```bash
php artisan serve
```

---

## ✅ Checklist avant soumission à Airtel

- [x] URL ngrok active et en HTTPS
- [x] Route de callback configurée dans `api.php`
- [ ] PaymentCallbackController implémenté
- [ ] Laravel en cours d'exécution (`php artisan serve`)
- [ ] ngrok en cours d'exécution
- [x] URL de callback complète avec `/api/payment-callbacks/airtel_money`
- [ ] Test manuel avec Postman réussi
- [ ] Logs Laravel fonctionnels

---

## 📞 Informations de contact Airtel Money

- **Documentation** : https://developers.airtel.africa/
- **Support** : developer.support@airtel.com
- **Dashboard** : https://developer.airtel.africa/

---

## 🎉 Résumé

### URL à fournir à Airtel :

```
https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
```

### Prochaines étapes :

1. ✅ Soumettre l'URL de callback à Airtel
2. ⏳ Attendre l'approbation et recevoir les credentials
3. ⏳ Ajouter les credentials dans `.env`
4. ⏳ Tester avec un vrai paiement Airtel Money

---

**Support VintApp** : gloirelumingu10@gmail.com
