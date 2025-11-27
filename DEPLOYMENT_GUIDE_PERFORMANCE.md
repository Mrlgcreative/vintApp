# 🚀 GUIDE DE DÉPLOIEMENT - Optimisations Performance

## ⚡ Étapes de Déploiement

### 1. Exécuter les migrations (Index DB)

```bash
php artisan migrate
```

### 2. Nettoyer le cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 3. Optimiser pour production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload -o
```

### 4. Tester les optimisations

```bash
php test_performance_optimizations.php
```

---

## 📋 Checklist Post-Déploiement

### Configuration .env

-   [ ] `CACHE_STORE` configuré (database ou redis)
-   [ ] `PERFORMANCE_CACHE_ENABLED=true`
-   [ ] `HTTP_COMPRESSION_ENABLED=true`
-   [ ] `HTTP_CACHE_ENABLED=true`

### Fichiers créés

-   [ ] `app/Services/CacheService.php`
-   [ ] `app/Http/Middleware/CacheResponse.php`
-   [ ] `app/Http/Middleware/CompressResponse.php`
-   [ ] `app/Traits/ImageOptimization.php`
-   [ ] `database/migrations/2025_11_27_140000_add_performance_indexes.php`

### Fichiers modifiés

-   [ ] `app/Http/Controllers/ItemController.php`
-   [ ] `routes/api.php`
-   [ ] `app/Http/Kernel.php`

### Tests

-   [ ] API `/api/items` répond avec header `X-Cache`
-   [ ] API répond avec header `Content-Encoding: gzip`
-   [ ] Les index DB sont créés
-   [ ] Cache fonctionne (vérifier dans tinker)

---

## 🧪 Commandes de Test

### Tester le cache

```bash
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');
```

### Vérifier les index

```bash
php artisan tinker
>>> DB::select("SHOW INDEX FROM items WHERE Key_name LIKE 'idx_%'");
```

### Tester compression API

```bash
curl -H "Accept-Encoding: gzip" -I https://your-domain.com/api/items
# Devrait afficher: Content-Encoding: gzip
```

### Tester rate limiting

```bash
# Faire 100+ requêtes rapidement
for i in {1..110}; do curl https://your-domain.com/api/items; done
# Devrait bloquer après 100 requêtes
```

---

## 🔍 Monitoring

### Vérifier les performances

```bash
# Temps de réponse
curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com/api/items

# Headers de cache
curl -I https://your-domain.com/api/items | grep "X-Cache"

# Compression
curl -I https://your-domain.com/api/items | grep "Content-Encoding"
```

### Créer curl-format.txt

```
time_namelookup:  %{time_namelookup}\n
time_connect:  %{time_connect}\n
time_starttransfer:  %{time_starttransfer}\n
time_total:  %{time_total}\n
```

---

## ⚠️ Problèmes Courants

### Le cache ne fonctionne pas

```bash
# Vérifier les permissions
chmod -R 775 storage/framework/cache

# Vérifier la config
php artisan config:clear
php artisan cache:clear
```

### Migration échoue

```bash
# Vérifier les tables
php artisan migrate:status

# Rollback et réessayer
php artisan migrate:rollback --step=1
php artisan migrate
```

### Rate limiting trop strict

Modifier dans `routes/api.php`:

```php
Route::middleware('throttle:200,1')->get(...); // Augmenter à 200/min
```

---

## 📊 Résultats Attendus

| Métrique             | Avant  | Après  |
| -------------------- | ------ | ------ |
| Temps réponse /items | ~800ms | ~150ms |
| Requêtes DB          | 50+    | 5-10   |
| Taille JSON          | 500KB  | 150KB  |
| Charge serveur       | 100%   | 40%    |

---

## 🎯 Prochaines Étapes

1. ✅ Tester en staging
2. ✅ Monitorer les performances
3. ✅ Ajuster les TTL de cache si nécessaire
4. ✅ Configurer Redis si non fait
5. ✅ Activer OPcache

---

**Bon déploiement ! 🚀**
