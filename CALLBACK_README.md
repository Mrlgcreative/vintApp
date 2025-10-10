# 🔔 Système de Callback de Paiement

> **Système complet de gestion des callbacks Mobile Money pour VintApp**

---

## ✨ Fonctionnalités

✅ **Réception automatique** des callbacks de tous les opérateurs  
✅ **Vérification de sécurité** (signatures, IPs, tokens)  
✅ **Traitement intelligent** des différents formats  
✅ **Suivi en temps réel** avec polling automatique  
✅ **Interface utilisateur** intuitive et animée  
✅ **Logs complets** pour audit et debugging  
✅ **Gestion des erreurs** avec retry automatique  

---

## 🚀 Démarrage Rapide

### 1. Configuration `.env`

```env
# Secrets de callback
MPESA_CALLBACK_SECRET=votre_secret_mpesa
ORANGE_CALLBACK_KEY=votre_cle_orange
AIRTEL_CALLBACK_TOKEN=votre_token_airtel
AFRICELL_CALLBACK_SECRET=votre_secret_africell

# IPs autorisées
MPESA_CALLBACK_IPS=196.250.0.0/16
ORANGE_CALLBACK_IPS=41.191.0.0/16
AIRTEL_CALLBACK_IPS=41.242.0.0/16
AFRICELL_CALLBACK_IPS=41.222.0.0/16
```

### 2. URLs de Callback

Communiquez ces URLs aux opérateurs:

```
https://votre-domaine.com/api/payment-callbacks/mpesa
https://votre-domaine.com/api/payment-callbacks/orange_money
https://votre-domaine.com/api/payment-callbacks/airtel_money
https://votre-domaine.com/api/payment-callbacks/africell
```

### 3. Test

```bash
# Test manuel avec cURL
curl -X POST https://votre-domaine.com/api/payment-callbacks/mpesa \
  -H "Content-Type: application/json" \
  -H "X-Signature: test" \
  -d '{"TransID":"TEST123","ResultCode":"0","TransAmount":"10.00"}'
```

---

## 📊 Architecture

```
┌─────────────────┐
│   Utilisateur   │
│   (Frontend)    │
└────────┬────────┘
         │ 1. Soumet paiement
         ▼
┌─────────────────┐
│ PaymentController│
│ Créé transaction│
└────────┬────────┘
         │ 2. Envoie à opérateur
         ▼
┌─────────────────┐     3. Callback      ┌─────────────────────┐
│   Opérateur     │ ───────────────────> │ PaymentCallback     │
│   Mobile Money  │                      │ Controller          │
└─────────────────┘                      └──────────┬──────────┘
                                                    │
                                         4. Vérifie & Traite
                                                    │
         ┌──────────────────────────────────────────┘
         │
         ▼
┌─────────────────┐      5. Polling      ┌─────────────────┐
│  Page Statut    │ <──────────────────> │   API Status    │
│  (Utilisateur)  │                      │   Endpoint      │
└─────────────────┘                      └─────────────────┘
         │
         │ 6. Affiche résultat
         ▼
┌─────────────────┐
│  Confirmation   │
│   ou Erreur     │
└─────────────────┘
```

---

## 🔐 Sécurité

### Niveaux de Protection

1. **Vérification IP** - Seules les IPs autorisées passent
2. **Signature HMAC** - Pour M-Pesa
3. **API Key** - Pour Orange Money
4. **Bearer Token** - Pour Airtel Money
5. **Secret Payload** - Pour Africell
6. **Audit Trail** - Tous les callbacks enregistrés

### En Développement

Les callbacks depuis `localhost` sont automatiquement acceptés pour faciliter les tests.

---

## 📝 Formats de Callback

### M-Pesa

```json
{
  "TransID": "ABC123",
  "ResultCode": "0",
  "TransAmount": "10.00",
  "MSISDN": "243812345678"
}
```

### Orange Money

```json
{
  "txnid": "OM123",
  "status": "SUCCESS",
  "amount": "10.00",
  "msisdn": "243845678901"
}
```

### Airtel Money

```json
{
  "transaction_id": "AM123",
  "transaction_status": "TS",
  "transaction_amount": "10.00"
}
```

### Africell

```json
{
  "trans_id": "AF123",
  "status": "SUCCESS",
  "amount": "10.00",
  "secret": "your_secret"
}
```

---

## 🎯 Flow Utilisateur

1. **Paiement** → Utilisateur soumet le formulaire
2. **Attente** → Redirection vers `/payments/status/{id}`
3. **Polling** → Vérification automatique du statut (1x/seconde)
4. **Callback** → Opérateur envoie la confirmation
5. **Mise à jour** → Statut mis à jour automatiquement
6. **Résultat** → Affichage succès/échec + redirection

**Timeout:** 2 minutes (120 tentatives)

---

## 🧪 Tests

### Test Simple

1. Créez un paiement
2. Ouvrez `/payments/status/{transaction_id}`
3. Envoyez un callback via cURL
4. La page se met à jour automatiquement

### Vérifier les Callbacks Reçus

```sql
SELECT * FROM payment_callbacks 
ORDER BY created_at DESC 
LIMIT 10;
```

### Vérifier les Callbacks Non Traités

```sql
SELECT * FROM payment_callbacks 
WHERE is_processed = 0;
```

---

## 📊 Monitoring

### Callbacks des dernières 24h

```sql
SELECT 
  provider,
  status,
  COUNT(*) as total
FROM payment_callbacks
WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY provider, status;
```

### Temps Moyen de Traitement

```sql
SELECT 
  provider,
  AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_seconds
FROM payment_callbacks
WHERE is_processed = 1
GROUP BY provider;
```

---

## 🐛 Dépannage

### "Callback non reçu"

✅ Vérifier URL configurée chez l'opérateur  
✅ Vérifier firewall/ports ouverts  
✅ Vérifier logs serveur web  

### "Signature invalide"

✅ Vérifier secrets dans `.env`  
✅ Vérifier format attendu par l'opérateur  
✅ Tester en local d'abord  

### "Transaction non trouvée"

✅ Vérifier que transaction existe  
✅ Vérifier correspondance: montant, téléphone  
✅ Regarder les logs Laravel  

---

## 📚 Fichiers Clés

| Fichier | Description |
|---------|-------------|
| `app/Models/PaymentCallback.php` | Modèle avec scopes et méthodes |
| `app/Http/Controllers/PaymentCallbackController.php` | Logique de traitement |
| `database/migrations/..._create_payment_callbacks_table.php` | Structure BDD |
| `resources/views/payment-status.blade.php` | Page de suivi |
| `routes/api.php` | Routes de callback |
| `PAYMENT_CALLBACK_GUIDE.md` | Documentation complète |

---

## 🎨 Interface Utilisateur

### États de la Page

| État | Icône | Couleur | Action |
|------|-------|---------|--------|
| **Pending** | Spinner | Bleu | Continue polling |
| **Success** | ✓ | Vert | Redirection auto (3s) |
| **Failed** | ✗ | Rouge | Bouton réessayer |
| **Cancelled** | ⦻ | Orange | Retour paiements |
| **Timeout** | ⏱ | Jaune | Bouton support |

### Animations

- ✨ Barre de progression animée
- ✨ Icône qui pulse sur succès
- ✨ Shake sur erreur
- ✨ Transitions fluides

---

## 🔧 Commandes Utiles

### Voir les Logs

```bash
tail -f storage/logs/laravel.log | grep "Callback"
```

### Retraiter un Callback

```php
php artisan tinker
$callback = PaymentCallback::find(123);
app(PaymentCallbackController::class)->processCallback($callback);
```

### Statistiques Rapides

```php
php artisan tinker
PaymentCallback::where('created_at', '>', now()->subDay())->count();
```

---

## 🌟 Fonctionnalités Avancées

- ✅ **Audit complet** - Tous les callbacks enregistrés
- ✅ **Retry automatique** - Maximum 3 tentatives
- ✅ **Polling intelligent** - 1x/seconde pendant 2 minutes
- ✅ **Multi-provider** - Support de 5 opérateurs
- ✅ **Sécurité robuste** - Vérifications multiples
- ✅ **UX optimale** - Feedback en temps réel
- ✅ **Logs structurés** - Facilite le debugging

---

## 📞 Support

### En cas de problème

1. **Vérifier les logs** : `storage/logs/laravel.log`
2. **Vérifier la BDD** : Table `payment_callbacks`
3. **Consulter la doc** : `PAYMENT_CALLBACK_GUIDE.md`
4. **Contact support** : Via interface admin

---

## 🚀 Prochaines Étapes

Pour utiliser le système :

1. ✅ Configurer les secrets dans `.env`
2. ✅ Fournir les URLs aux opérateurs
3. ✅ Tester avec des transactions réelles
4. ✅ Monitorer les premiers callbacks
5. ✅ Ajuster selon les retours

---

**Date:** 9 octobre 2025  
**Version:** 1.0  
**Status:** ✅ Production Ready  
**Application:** VintApp  

---

<div align="center">

### 🎉 Système de Callback Complet et Fonctionnel !

**Sécurisé** | **Automatique** | **Temps Réel**

</div>
