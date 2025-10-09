# 📊 Guide d'utilisation de l'API Forex - Taux de change en temps réel

## 🎯 Vue d'ensemble

VintApp utilise maintenant une **API Forex réelle** pour récupérer les taux de change **USD/CDF en temps réel**, remplaçant l'ancien système de taux fixe (2500 CDF).

### ✅ Fonctionnalités

- ✅ **Taux en temps réel** depuis ExchangeRate-API (gratuit, sans clé)
- ✅ **Cache intelligent** (1 heure par défaut, configurable)
- ✅ **Taux de secours** automatique en cas d'échec de l'API
- ✅ **Support multi-providers** (ExchangeRate-API, CurrencyAPI, Fixer.io)
- ✅ **Rafraîchissement manuel** pour les admins
- ✅ **Indicateurs visuels** (taux réel vs secours)
- ✅ **Logs détaillés** pour le débogage

---

## 🔧 Configuration

### 1. Variables d'environnement (`.env`)

```env
# API Forex pour les taux de change
FOREX_API_PROVIDER=exchangerate-api      # Provider à utiliser
EXCHANGERATE_API_KEY=                    # Clé API (optionnel pour ExchangeRate-API)
FOREX_BASE_CURRENCY=USD                  # Devise de base
FOREX_CACHE_DURATION=3600                # Durée du cache en secondes (1h par défaut)
```

### 2. Providers disponibles

| Provider | Gratuit | Clé requise | URL |
|----------|---------|-------------|-----|
| **ExchangeRate-API** | ✅ Oui | ❌ Non* | https://open.exchangerate-api.com |
| CurrencyAPI | ✅ Oui | ✅ Oui | https://currencyapi.com |
| Fixer.io | ❌ Non | ✅ Oui | https://fixer.io |

*Note: ExchangeRate-API offre une version gratuite sans clé (1500 req/mois). Pour plus de requêtes, obtenir une clé sur https://www.exchangerate-api.com

---

## 📡 Endpoints disponibles

### 1. **Récupérer le taux actuel**

```http
GET /exchange/rate
```

**Réponse JSON:**
```json
{
  "status": "success",
  "from": "USD",
  "to": "CDF",
  "rate": 2451.09,
  "cached": true,
  "fallback": false,
  "updated_at": "2025-10-09T10:16:09+00:00"
}
```

### 2. **Convertir un montant**

```http
POST /wallet/convert
Content-Type: application/json

{
  "from_wallet_id": 123,
  "to_wallet_id": 124,
  "amount": 100
}
```

**Réponse JSON:**
```json
{
  "status": "success",
  "from": "USD",
  "to": "CDF",
  "amount": 100,
  "rate": 2451.09,
  "converted_amount": 245109.00,
  "timestamp": "2025-10-09T10:16:09+00:00"
}
```

### 3. **Rafraîchir le taux (Admin uniquement)**

```http
POST /exchange/refresh-rate
Authorization: Bearer {admin_token}
```

**Réponse JSON:**
```json
{
  "status": "success",
  "message": "Taux de change mis à jour",
  "rate": 2451.09,
  "updated_at": "2025-10-09T10:16:09+00:00"
}
```

### 4. **Historique des taux**

```http
GET /exchange/history?start_date=2025-10-01&end_date=2025-10-09
```

---

## 🎨 Interface utilisateur

### Indicateurs visuels dans le wallet

1. **Badge "Taux réel"** (vert) : Taux récupéré depuis l'API
2. **Badge "Taux de secours"** (jaune) : Taux de secours utilisé en cas d'échec
3. **Bouton de rafraîchissement** : Icône qui tourne pendant le chargement
4. **Temps écoulé** : "Il y a 15min", "Il y a 2h", etc.

### Exemple d'affichage

```
┌─────────────────────────────────────────────────────────────┐
│ ℹ️ Taux actuel: 1 USD = 2,451 CDF  ✅ Taux réel  🔄 Il y a 5min │
│                                                              │
│ → USD → CDF: × 2,451    ← CDF → USD: ÷ 2,451               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 Logique de cache et rafraîchissement

### Stratégie de cache

1. **Premier appel** : Récupère depuis l'API → Met en cache (1h)
2. **Appels suivants** : Utilise le cache si valide
3. **Expiration** : Après 1h, nouveau call à l'API
4. **Échec API** : Utilise le taux de secours (2650 CDF)

### Taux de secours

Le taux de secours est défini dans `ExchangeRateController::getFallbackRate()` :

```php
private function getFallbackRate()
{
    return 2650.00; // 1 USD = 2650 CDF (taux approximatif octobre 2025)
}
```

**⚠️ Important** : Mettre à jour ce taux régulièrement pour refléter le marché de Kinshasa.

---

## 🛠️ Changer de provider

### Option 1 : Utiliser CurrencyAPI

1. Créer un compte sur https://currencyapi.com
2. Obtenir une clé API gratuite
3. Modifier `.env` :
```env
FOREX_API_PROVIDER=currencyapi
EXCHANGERATE_API_KEY=votre_cle_api_ici
```

### Option 2 : Utiliser Fixer.io (payant)

1. S'inscrire sur https://fixer.io
2. Obtenir une clé API
3. Modifier `.env` :
```env
FOREX_API_PROVIDER=fixer
EXCHANGERATE_API_KEY=votre_cle_api_ici
```

---

## 📝 Logs et débogage

### Vérifier les logs

```bash
tail -f storage/logs/laravel.log | grep -i "forex\|exchange\|rate"
```

### Exemples de logs

```
[2025-10-09 10:16:09] INFO: Taux USD/CDF récupéré depuis ExchangeRate-API: 2451.092793
[2025-10-09 10:20:15] WARNING: Utilisation du taux de change de secours: 2650
[2025-10-09 10:25:30] ERROR: Erreur API Forex (exchangerate-api): Connection timeout
```

---

## 🧪 Tests

### Tester la récupération du taux

```bash
# Via Tinker
php artisan tinker
>>> app(ExchangeRateController::class)->getRate()->getContent();

# Via cURL
curl http://localhost:8000/exchange/rate
```

### Tester la conversion

```bash
curl -X POST http://localhost:8000/wallet/convert \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_token" \
  -d '{"from_wallet_id": 1, "to_wallet_id": 2, "amount": 100}'
```

### Vider le cache manuellement

```bash
php artisan cache:forget usd_cdf_rate
```

---

## ⚡ Optimisations possibles

### 1. Stocker l'historique en base de données

Créer une table `exchange_rates` :

```sql
CREATE TABLE exchange_rates (
    id BIGINT PRIMARY KEY,
    from_currency VARCHAR(3),
    to_currency VARCHAR(3),
    rate DECIMAL(15, 6),
    source VARCHAR(50),
    created_at TIMESTAMP
);
```

### 2. Utiliser Redis pour le cache

Modifier `config/cache.php` pour utiliser Redis au lieu de File :

```php
'default' => env('CACHE_DRIVER', 'redis'),
```

### 3. Ajouter un scheduler pour mise à jour automatique

Dans `app/Console/Kernel.php` :

```php
protected function schedule(Schedule $schedule)
{
    // Rafraîchir le taux toutes les heures
    $schedule->call(function () {
        Cache::forget('usd_cdf_rate');
        app(ExchangeRateController::class)->fetchLiveRate();
    })->hourly();
}
```

---

## 🚨 Gestion des erreurs

### Scénarios d'échec

| Scénario | Action | Résultat |
|----------|--------|----------|
| API timeout | Utilise cache si disponible | Taux en cache |
| Cache expiré + API KO | Utilise taux de secours | 2650 CDF |
| Rate limit API | Utilise cache | Taux en cache |
| Mauvaise clé API | Bascule vers version gratuite | Taux réel |

---

## 📊 Comparaison Ancien vs Nouveau système

| Critère | Ancien (fixe) | Nouveau (API) |
|---------|---------------|---------------|
| Taux | 2500 CDF (fixe) | 2451.09 CDF (réel) |
| Mise à jour | Manuelle | Automatique (1h) |
| Fiabilité | 100% | 99% (fallback) |
| Coût | Gratuit | Gratuit |
| Précision | ❌ Inexact | ✅ Précis |

---

## 🎯 Conclusion

Le nouveau système Forex offre :

- ✅ **Taux précis** reflétant le marché réel
- ✅ **Fiabilité** avec système de secours
- ✅ **Performance** grâce au cache
- ✅ **Flexibilité** avec plusieurs providers
- ✅ **Transparence** avec indicateurs visuels

**Prochain taux récupéré:** Automatiquement dans 1 heure

---

## 📞 Support

Pour toute question ou problème :

1. Vérifier les logs : `storage/logs/laravel.log`
2. Vider le cache : `php artisan cache:clear`
3. Tester l'API : `curl http://localhost:8000/exchange/rate`
4. Vérifier `.env` : Variables FOREX correctement définies

---

**Dernière mise à jour:** 9 octobre 2025  
**Version:** 1.0.0  
**Auteur:** VintApp Team
