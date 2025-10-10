# 🔔 Système de Callback de Paiement - VintApp

## 📋 Vue d'ensemble

Ce document décrit le système de callback complet pour les paiements Mobile Money dans VintApp. Le système permet de recevoir, valider et traiter les notifications de paiement des opérateurs de télécommunication.

---

## 🏗️ Architecture

### Composants Principaux

1. **PaymentCallback Model** - Stocke tous les callbacks reçus
2. **PaymentCallbackController** - Traite les callbacks et met à jour les transactions
3. **Routes API** - Endpoints publics pour recevoir les callbacks
4. **Page de Statut** - Interface utilisateur pour suivre le paiement en temps réel
5. **Système de Polling** - Vérification automatique du statut toutes les secondes

---

## 🔄 Flow du Paiement

```
1. Utilisateur soumet le paiement → PaymentController
2. Transaction créée (status: pending)
3. Requête envoyée à l'opérateur Mobile Money
4. Utilisateur redirigé vers /payments/status/{transaction_id}
5. Page de statut démarre le polling (1x/seconde)
6. Opérateur envoie callback → /api/payment-callbacks/{provider}
7. Callback vérifié et traité
8. Transaction mise à jour (status: completed/failed)
9. Page de statut détecte le changement et affiche le résultat
```

---

## 📡 Endpoints de Callback

### 1. Recevoir un Callback

**Endpoint:**
```
POST /api/payment-callbacks/{provider}
```

**Providers supportés:**
- `mpesa` - Vodacom M-Pesa
- `orange_money` - Orange Money
- `airtel_money` - Airtel Money
- `africell` - Africell Money
- `illicocash` - Illicocash

**Exemple d'URL:**
```
POST https://votre-domaine.com/api/payment-callbacks/mpesa
```

### 2. Vérifier le Statut

**Endpoint:**
```
GET /api/payment-callbacks/status?transaction_id={id}
```

**Réponse:**
```json
{
  "status": "success",
  "transaction": {
    "id": 123,
    "status": "completed",
    "amount": "10.00",
    "currency": "USD",
    "created_at": "2025-10-09T19:00:00Z",
    "completed_at": "2025-10-09T19:00:45Z"
  }
}
```

---

## 🔐 Sécurité des Callbacks

### Vérification par Provider

#### M-Pesa
```php
// Vérifie la signature HMAC
Header: X-Signature
Secret: MPESA_CALLBACK_SECRET (dans .env)
```

#### Orange Money
```php
// Vérifie la clé API
Header: X-Api-Key
Key: ORANGE_CALLBACK_KEY (dans .env)
```

#### Airtel Money
```php
// Vérifie le token Bearer
Header: Authorization: Bearer {token}
Token: AIRTEL_CALLBACK_TOKEN (dans .env)
```

#### Africell
```php
// Vérifie le secret dans le payload
Field: secret
Secret: AFRICELL_CALLBACK_SECRET (dans .env)
```

### Vérification des IPs

Les IPs autorisées sont configurées dans `.env`:

```env
# M-Pesa
MPESA_CALLBACK_IPS=196.250.0.0/16,41.76.0.0/16

# Orange Money
ORANGE_CALLBACK_IPS=41.191.0.0/16

# Airtel Money
AIRTEL_CALLBACK_IPS=41.242.0.0/16

# Africell
AFRICELL_CALLBACK_IPS=41.222.0.0/16
```

**Note:** En développement (localhost), toutes les IPs sont acceptées.

---

## 📊 Table payment_callbacks

### Structure

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | bigint | ID auto-incrémenté |
| `transaction_id` | bigint | FK vers transactions |
| `external_transaction_id` | varchar | ID chez l'opérateur |
| `provider` | varchar | Opérateur (mpesa, orange_money, etc.) |
| `status` | varchar | success, failed, pending, cancelled |
| `amount` | decimal | Montant du paiement |
| `currency` | varchar(3) | Devise (USD, CDF, etc.) |
| `phone_number` | varchar(20) | Numéro Mobile Money |
| `callback_type` | varchar | ipn, webhook, polling |
| `raw_payload` | text | Données brutes reçues |
| `parsed_data` | json | Données structurées |
| `signature` | varchar | Signature de sécurité |
| `ip_address` | varchar(45) | IP de l'opérateur |
| `is_verified` | boolean | Signature/IP validée |
| `is_processed` | boolean | Callback traité |
| `processed_at` | timestamp | Date de traitement |
| `processing_error` | text | Erreur éventuelle |
| `retry_count` | integer | Nombre de tentatives |

### Index

- `(provider, status)` - Recherche par opérateur et statut
- `is_processed` - Callbacks non traités
- `created_at` - Tri chronologique
- `external_transaction_id` - Recherche par ID externe

---

## 🎯 Formats de Callback par Provider

### M-Pesa

**Format attendu:**
```json
{
  "TransID": "ABC123XYZ",
  "TransAmount": "10.00",
  "MSISDN": "243812345678",
  "BillRefNumber": "ORDER-001",
  "ResultCode": "0",
  "ResultDesc": "Transaction successful"
}
```

**Codes de statut:**
- `0` ou `SUCCESS` → success
- `PENDING` → pending
- `CANCELLED` → cancelled
- Autre → failed

### Orange Money

**Format attendu:**
```json
{
  "txnid": "OM123456789",
  "amount": "10.00",
  "msisdn": "243845678901",
  "order_id": "ORDER-001",
  "status": "SUCCESS",
  "message": "Payment successful"
}
```

**Codes de statut:**
- `SUCCESS`, `SUCCESSFUL`, `COMPLETED` → success
- `PENDING`, `PROCESSING` → pending
- `CANCELLED`, `CANCELED` → cancelled
- Autre → failed

### Airtel Money

**Format attendu:**
```json
{
  "transaction_id": "AM987654321",
  "transaction_amount": "10.00",
  "msisdn": "243979876543",
  "reference": "ORDER-001",
  "transaction_status": "TS",
  "status_message": "Transaction successful"
}
```

**Codes de statut:**
- `TS`, `SUCCESS`, `SUCCESSFUL` → success
- `TIP`, `PENDING` → pending
- `TF`, `FAILED` → failed

### Africell

**Format attendu:**
```json
{
  "trans_id": "AF111222333",
  "amount": "10.00",
  "phone": "243901234567",
  "ref": "ORDER-001",
  "status": "SUCCESS",
  "msg": "Payment successful",
  "secret": "your_secret_key"
}
```

**Codes de statut:**
- `SUCCESS`, `SUCCESSFUL` → success
- `PENDING` → pending
- `FAILED`, `ERROR` → failed

---

## 🔧 Configuration

### Variables d'Environnement

Ajoutez ces variables dans votre fichier `.env`:

```env
# Secrets de callback
MPESA_CALLBACK_SECRET=votre_secret_mpesa
ORANGE_CALLBACK_KEY=votre_cle_orange
AIRTEL_CALLBACK_TOKEN=votre_token_airtel
AFRICELL_CALLBACK_SECRET=votre_secret_africell

# IPs autorisées (format CIDR)
MPESA_CALLBACK_IPS=196.250.0.0/16
ORANGE_CALLBACK_IPS=41.191.0.0/16
AIRTEL_CALLBACK_IPS=41.242.0.0/16
AFRICELL_CALLBACK_IPS=41.222.0.0/16

# Timeout du polling (secondes)
PAYMENT_POLLING_TIMEOUT=120
```

### Configuration des URLs de Callback

Fournissez ces URLs aux opérateurs Mobile Money:

```
M-Pesa:         https://votre-domaine.com/api/payment-callbacks/mpesa
Orange Money:   https://votre-domaine.com/api/payment-callbacks/orange_money
Airtel Money:   https://votre-domaine.com/api/payment-callbacks/airtel_money
Africell:       https://votre-domaine.com/api/payment-callbacks/africell
Illicocash:     https://votre-domaine.com/api/payment-callbacks/illicocash
```

---

## 🧪 Tests

### Test Manual avec cURL

#### M-Pesa
```bash
curl -X POST https://votre-domaine.com/api/payment-callbacks/mpesa \
  -H "Content-Type: application/json" \
  -H "X-Signature: your_hmac_signature" \
  -d '{
    "TransID": "TEST123",
    "TransAmount": "10.00",
    "MSISDN": "243812345678",
    "BillRefNumber": "ORDER-001",
    "ResultCode": "0",
    "ResultDesc": "Test successful"
  }'
```

#### Orange Money
```bash
curl -X POST https://votre-domaine.com/api/payment-callbacks/orange_money \
  -H "Content-Type: application/json" \
  -H "X-Api-Key: your_api_key" \
  -d '{
    "txnid": "TEST123",
    "amount": "10.00",
    "msisdn": "243845678901",
    "order_id": "ORDER-001",
    "status": "SUCCESS"
  }'
```

### Test de Polling

```javascript
// Dans la console du navigateur sur /payments/status/{id}
// Vérifier que le polling fonctionne
console.log('Polling actif:', pollingInterval !== null);
console.log('Tentatives:', pollingCount);
```

---

## 📝 Logs

### Emplacement
```
storage/logs/laravel.log
```

### Types de Logs

#### Callback Reçu
```
[INFO] Callback reçu pour mpesa
{
  "callback_id": 123,
  "ip": "196.250.1.100",
  "payload": {...}
}
```

#### Callback Traité
```
[INFO] Callback 123 traité avec succès pour transaction 456
```

#### Erreurs
```
[ERROR] Erreur traitement callback mpesa
{
  "error": "Transaction not found",
  "trace": "..."
}
```

---

## 🔍 Monitoring

### Callbacks Non Traités

```sql
SELECT * FROM payment_callbacks 
WHERE is_processed = 0 
  AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

### Callbacks en Erreur

```sql
SELECT * FROM payment_callbacks 
WHERE processing_error IS NOT NULL 
  AND retry_count < 3
ORDER BY created_at DESC;
```

### Statistiques par Provider

```sql
SELECT 
  provider,
  status,
  COUNT(*) as total,
  AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time
FROM payment_callbacks
WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY provider, status;
```

---

## 🚨 Gestion des Erreurs

### Transaction Non Trouvée

Le callback est enregistré mais marqué avec l'erreur "Transaction not found".  
**Action:** Vérifier manuellement et rapprocher avec l'opérateur.

### Signature Invalide

Le callback est rejeté avec HTTP 403.  
**Action:** Vérifier les secrets dans `.env` et les headers envoyés.

### Tentatives Multiples

Si `retry_count > 3`, arrêter le traitement automatique.  
**Action:** Investigation manuelle requise.

---

## 🎨 Interface Utilisateur

### Page de Statut (`/payments/status/{id}`)

**Fonctionnalités:**
- ✅ Affichage en temps réel du statut
- ✅ Polling automatique (1x/seconde, max 120 tentatives)
- ✅ Barre de progression animée
- ✅ Instructions claires pour l'utilisateur
- ✅ Gestion des timeouts
- ✅ Redirection automatique après succès
- ✅ FAQ intégrée

**États Possibles:**
1. **Pending** - En attente de confirmation
2. **Completed** - Paiement réussi (redirection auto)
3. **Failed** - Paiement échoué (bouton réessayer)
4. **Cancelled** - Paiement annulé
5. **Timeout** - Temps d'attente écoulé

---

## 🔄 Workflow de Réconciliation

Si un paiement est en pending après 2 minutes:

1. Vérifier `payment_callbacks` pour ce `transaction_id`
2. Si callback existe avec status success → MAJ manuelle
3. Si pas de callback → Contacter l'opérateur
4. Si callback failed → Confirmer avec l'utilisateur

---

## 📞 Support et Dépannage

### Problèmes Fréquents

#### 1. "Callback non reçu"
- Vérifier que l'URL de callback est correctement configurée chez l'opérateur
- Vérifier que le serveur est accessible depuis Internet
- Vérifier les logs du serveur web (nginx/apache)

#### 2. "Signature invalide"
- Vérifier les secrets dans `.env`
- Vérifier le format de la signature attendue
- Tester en local avec des données de test

#### 3. "Transaction non trouvée"
- Vérifier que la transaction existe dans la base
- Vérifier les correspondances: external_reference, phone_number, amount

---

## 🚀 Améliorations Futures

- [ ] Système de retry automatique avec backoff exponentiel
- [ ] Notification email/SMS en cas d'échec persistant
- [ ] Dashboard admin pour visualiser les callbacks
- [ ] Webhooks vers systèmes tiers
- [ ] Support pour plus d'opérateurs
- [ ] Système de queue pour traitement asynchrone
- [ ] Alertes automatiques si taux d'échec > 5%

---

**Date de création:** 9 octobre 2025  
**Version:** 1.0  
**Application:** VintApp  
**Auteur:** GitHub Copilot

---

## 📚 Ressources Complémentaires

- **Model:** `app/Models/PaymentCallback.php`
- **Controller:** `app/Http/Controllers/PaymentCallbackController.php`
- **Migration:** `database/migrations/2025_10_09_191211_create_payment_callbacks_table.php`
- **Routes:** `routes/api.php` (lignes callback)
- **View:** `resources/views/payment-status.blade.php`
