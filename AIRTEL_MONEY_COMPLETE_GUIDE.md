# 🎯 Configuration Airtel Money - Guide Complet VintApp

## 📋 Informations à fournir à Airtel

### 1. URL de Callback (Webhook)
```
https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
```

**Format** : `https://partner_domain/callback_path`
- ✅ Protocole HTTPS
- ✅ Domaine : `uncomely-uneffusing-averie.ngrok-free.dev`
- ✅ Chemin : `/api/payment-callbacks/airtel_money`

---

## 🔐 Configuration Laravel (.env)

### Variables à ajouter après avoir reçu vos credentials :

```env
# ===== AIRTEL MONEY API =====

# Environment (staging ou production)
AIRTEL_ENVIRONMENT=staging

# URLs de base
AIRTEL_BASE_URL_STAGING=https://openapiuat.airtel.africa
AIRTEL_BASE_URL_PRODUCTION=https://openapi.airtel.africa

# Credentials (à recevoir d'Airtel)
AIRTEL_CLIENT_ID=votre_client_id_ici
AIRTEL_CLIENT_SECRET=votre_client_secret_ici
AIRTEL_API_KEY=votre_api_key_ici
AIRTEL_API_SECRET=votre_api_secret_ici

# Configuration du callback
AIRTEL_CALLBACK_URL=${APP_URL}/api/payment-callbacks/airtel_money
AIRTEL_CALLBACK_SIGNATURE_ENABLED=true

# Clé privée pour la signature HMAC SHA256 (fournie par Airtel)
AIRTEL_PRIVATE_KEY=votre_private_key_pour_signature

# Configuration additionnelle
AIRTEL_TIMEOUT=30
AIRTEL_PIN=votre_pin_si_requis
```

---

## 📡 Format du Callback Airtel

### Ce qu'Airtel va envoyer à votre webhook :

```json
{
  "transaction": {
    "id": "MP210610.1234.A12345",
    "status_code": "TS",
    "airtel_money_id": "CDA210610123456",
    "message": "Transaction successful",
    "amount": 5000,
    "currency": "CDF"
  },
  "reference": "ORDER-123456"
}
```

### Headers envoyés par Airtel :

```
X-Airtel-Signature: base64_encoded_hmac_signature
Content-Type: application/json
```

---

## 🔒 Vérification de la signature HMAC SHA256

### Algorithme utilisé par Airtel :
- **Méthode** : HmacSHA256
- **Format de sortie** : Base64
- **Clé** : Clé privée fournie dans les paramètres de l'application

### Implémentation PHP :

```php
// app/Http/Controllers/PaymentCallbackController.php

public function verifyAirtelSignature(Request $request)
{
    // 1. Récupérer la signature envoyée par Airtel
    $receivedSignature = $request->header('X-Airtel-Signature');
    
    // 2. Récupérer le contenu brut de la requête
    $payload = $request->getContent();
    
    // 3. Générer la signature attendue avec votre clé privée
    $privateKey = config('services.airtel.private_key');
    $expectedSignature = base64_encode(
        hash_hmac('sha256', $payload, $privateKey, true)
    );
    
    // 4. Comparer les signatures de manière sécurisée
    return hash_equals($expectedSignature, $receivedSignature);
}

public function handleCallback(Request $request, $provider)
{
    // Vérifier que c'est bien Airtel
    if ($provider !== 'airtel_money') {
        return response()->json(['error' => 'Invalid provider'], 400);
    }
    
    // Si la signature est activée, la vérifier
    if (config('services.airtel.callback_signature_enabled')) {
        if (!$this->verifyAirtelSignature($request)) {
            \Log::warning('Airtel callback signature mismatch', [
                'ip' => $request->ip(),
                'received_signature' => $request->header('X-Airtel-Signature')
            ]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }
    }
    
    // Récupérer les données
    $data = $request->all();
    
    // Logger pour debug
    \Log::info('Airtel Money Callback', [
        'data' => $data,
        'signature_verified' => true
    ]);
    
    // Traiter la transaction
    $transaction = $data['transaction'] ?? null;
    $reference = $data['reference'] ?? null;
    
    if ($transaction && $reference) {
        // Rechercher la commande par référence
        $order = \App\Models\Order::where('reference', $reference)->first();
        
        if ($order) {
            // Mettre à jour le statut selon le status_code
            switch ($transaction['status_code']) {
                case 'TS': // Transaction Success
                    $order->status = 'paid';
                    $order->payment_status = 'completed';
                    $order->transaction_id = $transaction['id'];
                    break;
                    
                case 'TF': // Transaction Failed
                    $order->payment_status = 'failed';
                    break;
                    
                case 'TA': // Transaction Ambiguous
                    $order->payment_status = 'pending';
                    break;
                    
                case 'TIP': // Transaction In Progress
                    $order->payment_status = 'processing';
                    break;
            }
            
            $order->save();
            
            // Créer un enregistrement de paiement
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'amount' => $transaction['amount'],
                'currency' => $transaction['currency'] ?? 'CDF',
                'payment_method' => 'airtel_money',
                'transaction_id' => $transaction['id'],
                'status' => $transaction['status_code'],
                'airtel_money_id' => $transaction['airtel_money_id'] ?? null,
                'message' => $transaction['message'] ?? null,
            ]);
            
            // Envoyer notification à l'utilisateur
            // ...
        }
    }
    
    // Répondre à Airtel
    return response()->json([
        'status' => 'success',
        'message' => 'Callback received and processed'
    ], 200);
}
```

---

## 📝 Configuration dans config/services.php

Ajoutez cette section :

```php
// config/services.php

'airtel' => [
    'environment' => env('AIRTEL_ENVIRONMENT', 'staging'),
    
    'base_urls' => [
        'staging' => env('AIRTEL_BASE_URL_STAGING', 'https://openapiuat.airtel.africa'),
        'production' => env('AIRTEL_BASE_URL_PRODUCTION', 'https://openapi.airtel.africa'),
    ],
    
    'credentials' => [
        'client_id' => env('AIRTEL_CLIENT_ID'),
        'client_secret' => env('AIRTEL_CLIENT_SECRET'),
        'api_key' => env('AIRTEL_API_KEY'),
        'api_secret' => env('AIRTEL_API_SECRET'),
    ],
    
    'callback' => [
        'url' => env('AIRTEL_CALLBACK_URL'),
        'signature_enabled' => env('AIRTEL_CALLBACK_SIGNATURE_ENABLED', true),
    ],
    
    'private_key' => env('AIRTEL_PRIVATE_KEY'),
    'timeout' => env('AIRTEL_TIMEOUT', 30),
    'pin' => env('AIRTEL_PIN'),
],
```

---

## 🧪 Test de la signature HMAC

### Script de test :

```php
// test_airtel_signature.php

<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Données de test (exemple de callback Airtel)
$payload = json_encode([
    'transaction' => [
        'id' => 'MP210610.1234.A12345',
        'status_code' => 'TS',
        'airtel_money_id' => 'CDA210610123456',
        'message' => 'Transaction successful',
        'amount' => 5000,
        'currency' => 'CDF'
    ],
    'reference' => 'ORDER-123456'
]);

// Votre clé privée (exemple)
$privateKey = 'your_private_key_here';

// Générer la signature
$signature = base64_encode(hash_hmac('sha256', $payload, $privateKey, true));

echo "📋 Payload:\n";
echo $payload . "\n\n";

echo "🔐 Signature HMAC SHA256 (Base64):\n";
echo $signature . "\n\n";

echo "✅ Cette signature doit être envoyée dans le header X-Airtel-Signature\n";
```

Exécution :
```bash
php test_airtel_signature.php
```

---

## 📊 Status Codes Airtel

| Code | Signification | Action |
|------|---------------|--------|
| **TS** | Transaction Success | ✅ Paiement réussi - Marquer la commande comme payée |
| **TF** | Transaction Failed | ❌ Paiement échoué - Informer l'utilisateur |
| **TA** | Transaction Ambiguous | ⚠️ Statut ambigu - Garder en attente |
| **TIP** | Transaction In Progress | 🔄 En cours - Attendre le statut final |

---

## 🔍 Activer/Désactiver la signature

Dans le dashboard Airtel :
1. Aller dans **Paramètres de l'application**
2. Onglet **Sécurité**
3. Trouver **"Callback Signature"**
4. Activer ou désactiver selon vos besoins

**Recommandation** : ✅ **TOUJOURS ACTIVER** en production pour la sécurité

---

## 🚀 Endpoint API Paiement

### POST /merchant/v2/payments/

**URL complète** :
- Staging: `https://openapiuat.airtel.africa/merchant/v2/payments/`
- Production: `https://openapi.airtel.africa/merchant/v2/payments/`

**Headers** :
```
Authorization: Bearer {access_token}
Content-Type: application/json
X-Country: CD  (pour RDC)
X-Currency: CDF
```

**Body** :
```json
{
  "reference": "ORDER-123456",
  "subscriber": {
    "country": "CD",
    "currency": "CDF",
    "msisdn": "900000000"  // ⚠️ SANS le code pays (+243)
  },
  "transaction": {
    "amount": 5000,
    "country": "CD",
    "currency": "CDF",
    "id": "UNIQUE-TRANSACTION-ID"
  }
}
```

**⚠️ Important** : N'envoyez PAS le code pays dans msisdn
- ✅ Correct : `"msisdn": "900000000"`
- ❌ Incorrect : `"msisdn": "+243900000000"`
- ❌ Incorrect : `"msisdn": "243900000000"`

---

## ✅ Checklist avant mise en production

- [ ] Credentials Airtel reçus et configurés dans `.env`
- [ ] URL de callback enregistrée dans le dashboard Airtel
- [ ] Signature HMAC activée dans les paramètres Airtel
- [ ] Clé privée configurée dans `.env`
- [ ] Route de callback testée et fonctionnelle
- [ ] Logs Laravel configurés pour debug
- [ ] Gestion des différents status codes implémentée
- [ ] Notifications utilisateur configurées
- [ ] Tests effectués en staging
- [ ] Transition vers production planifiée

---

## 📞 Support Airtel

- **Documentation** : https://developers.airtel.africa/
- **Dashboard** : https://developer.airtel.africa/
- **Support** : developer.support@airtel.com

---

## 🎉 URL FINALE À SOUMETTRE

```
https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
```

**Cette URL respecte le format** : `https://partner_domain/callback_path` ✅

---

**Créé le** : 10 janvier 2025  
**Status** : ✅ Prêt pour soumission à Airtel  
**Support VintApp** : gloirelumingu10@gmail.com
