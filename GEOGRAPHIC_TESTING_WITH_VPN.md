# 🧪 Guide de Test - Restriction Géographique avec VPN/Ngrok

## ⚠️ Problème identifié

Lorsque vous utilisez un **VPN** ou **ngrok** pour exposer votre application, voici ce qui se passe :

```
Votre ordinateur (RDC) → VPN → Internet → Ngrok → Votre app
                          ↑
                    IP détectée = IP du VPN (USA/Europe/etc.)
```

Le middleware détecte l'IP du VPN, pas votre vraie IP de RDC, donc vous êtes bloqué.

---

## 🎯 3 Solutions pour tester

### ✅ Solution 1 : Désactiver temporairement le middleware (RECOMMANDÉ pour tests)

**Étape 1 :** Ajoutez cette ligne dans votre fichier `.env` :
```env
DISABLE_GEO_RESTRICTION=true
```

**Étape 2 :** Videz le cache :
```bash
php artisan config:clear
php artisan cache:clear
```

**Étape 3 :** Testez votre application normalement

**Étape 4 :** Pour réactiver la restriction (en production) :
```env
DISABLE_GEO_RESTRICTION=false
```
ou simplement supprimer la ligne.

---

### ✅ Solution 2 : Tester depuis votre connexion locale (sans VPN)

**Étape 1 :** Désactivez votre VPN temporairement

**Étape 2 :** Utilisez l'URL locale :
```
http://127.0.0.1:8000
```

**Résultat :** Le middleware détecte `127.0.0.1` et vous autorise automatiquement (bypass localhost en environnement local).

**Pour tester le blocage :** Vous devrez déployer sur un serveur réel.

---

### ✅ Solution 3 : Utiliser la route de debug

**Étape 1 :** Accédez à cette URL :
```
http://127.0.0.1:8000/test-location-debug
```

**Étape 2 :** Vous verrez un JSON avec toutes les informations détectées :
```json
{
    "ip": "127.0.0.1",
    "environment": "local",
    "position": {
        "country": "United States",
        "country_code": "US",
        "city": "Mountain View",
        "region": "California",
        "is_localhost": true
    },
    "cache_key": "location_access_127.0.0.1",
    "cached_result": true
}
```

**Étape 3 :** Analysez les résultats :
- Si `is_localhost: true` → bypass automatique
- Si `country` n'est pas RDC → vous serez bloqué
- Si `cached_result: true` → la vérification est en cache

---

## 🔍 Diagnostics

### Test 1 : Vérifier votre IP détectée
```bash
# Dans ngrok
curl http://votre-url.ngrok.io/test-location-debug
```

### Test 2 : Vider le cache
```bash
php artisan cache:clear
```

### Test 3 : Vérifier la configuration
```bash
php artisan config:show app.disable_geo_restriction
```

### Test 4 : Voir les logs
```bash
tail -f storage/logs/laravel.log
```

Cherchez les lignes avec "Access blocked" pour voir les tentatives bloquées.

---

## 🚀 Recommandations pour les tests

### Pendant le développement (avec VPN/ngrok)
```env
# .env
DISABLE_GEO_RESTRICTION=true
```
✅ Vous pouvez tester toutes les fonctionnalités sans être bloqué.

### Pour tester le blocage réel
1. **Option A :** Déployer sur un serveur réel (Heroku, DigitalOcean, etc.)
2. **Option B :** Demander à quelqu'un hors RDC de tester
3. **Option C :** Utiliser un VPN et activer la restriction :
   ```env
   DISABLE_GEO_RESTRICTION=false
   ```
   Puis vider le cache et tester.

### En production (déploiement final)
```env
# .env (PRODUCTION)
DISABLE_GEO_RESTRICTION=false
# Ou supprimer complètement la ligne
```
✅ La restriction géographique sera active.

---

## 📋 Checklist de test

### Tests avec restriction DÉSACTIVÉE (`DISABLE_GEO_RESTRICTION=true`)

- [ ] ✅ Accès à la page d'accueil fonctionne
- [ ] ✅ Accès à `/admin/settings/locations` fonctionne
- [ ] ✅ Ajouter une ville fonctionne
- [ ] ✅ Toggle statut ville fonctionne
- [ ] ✅ Supprimer ville fonctionne
- [ ] ✅ Ajouter région fonctionne
- [ ] ✅ Interface responsive (mobile/desktop) OK

### Tests avec restriction ACTIVÉE (`DISABLE_GEO_RESTRICTION=false`)

**Via VPN hors RDC :**
- [ ] ✅ Page d'accueil → Redirigé vers `/city-restricted`
- [ ] ✅ Page `/city-restricted` s'affiche correctement
- [ ] ✅ Liste des villes disponibles s'affiche
- [ ] ✅ Bouton "Me notifier" → `/preregistration`
- [ ] ✅ Bouton "Nous contacter" → Email support

**Connexion admin (même avec VPN) :**
- [ ] ✅ Admin authentifié peut accéder à tout (bypass)

**Localhost (127.0.0.1) :**
- [ ] ✅ Accès autorisé automatiquement (bypass)

---

## 🛠️ Commandes utiles

### Vider tous les caches
```bash
php artisan optimize:clear
```
ou
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Voir la configuration actuelle
```bash
php artisan tinker
```
Puis :
```php
config('app.disable_geo_restriction');
// Retourne: true ou false
```

### Tester manuellement la détection IP
```bash
php artisan tinker
```
Puis :
```php
use Stevebauman\Location\Facades\Location;

$ip = '8.8.8.8'; // IP Google (USA)
$position = Location::get($ip);
dd($position);
```

### Vider le cache d'une IP spécifique
```bash
php artisan tinker
```
Puis :
```php
$ip = '123.456.789.0'; // Votre IP
Cache::forget("location_access_{$ip}");
echo "Cache vidé pour {$ip}";
```

---

## 🎯 Scénarios de test recommandés

### Scénario 1 : Développement local (VOS tests actuels)
```env
APP_ENV=local
DISABLE_GEO_RESTRICTION=true
```
✅ Tout fonctionne sans blocage

### Scénario 2 : Test de l'interface admin
1. Connectez-vous en tant qu'admin
2. Accédez à `/admin/settings/locations`
3. Testez toutes les fonctionnalités CRUD
4. La restriction ne s'applique PAS aux admins

### Scénario 3 : Test du blocage (avec un ami hors RDC)
1. Activez : `DISABLE_GEO_RESTRICTION=false`
2. Videz le cache : `php artisan cache:clear`
3. Demandez à un ami (Europe/USA/etc.) d'accéder à votre URL ngrok
4. Il devrait voir la page `/city-restricted`

### Scénario 4 : Production réelle
1. Déployez sur un serveur
2. `.env` : `DISABLE_GEO_RESTRICTION=false` (ou supprimer la ligne)
3. Testez depuis RDC → ✅ Accès autorisé
4. Testez depuis l'étranger → ❌ Bloqué

---

## 🔐 Sécurité

### Variables d'environnement importantes

```env
# Développement
APP_ENV=local
DISABLE_GEO_RESTRICTION=true

# Production
APP_ENV=production
DISABLE_GEO_RESTRICTION=false
```

### ⚠️ IMPORTANT
**Ne jamais commit `.env` dans Git !**

Le fichier `.env.example` peut contenir :
```env
DISABLE_GEO_RESTRICTION=false
```
(valeur par défaut pour la production)

---

## 📊 Tableau récapitulatif

| Environnement | Variable | VPN/Ngrok | Localhost | Admin | Résultat |
|---------------|----------|-----------|-----------|-------|----------|
| Local | `DISABLE_GEO_RESTRICTION=true` | ✅ | ✅ | ✅ | Toujours autorisé |
| Local | `DISABLE_GEO_RESTRICTION=false` | ❌ | ✅ | ✅ | Bloqué sauf localhost/admin |
| Production | `DISABLE_GEO_RESTRICTION=false` | ❌ | ✅ | ✅ | Restriction active |
| Production | `DISABLE_GEO_RESTRICTION=true` | ✅ | ✅ | ✅ | ⚠️ Pas de restriction (déconseillé) |

---

## 💡 Résumé pour VOS tests actuels

**Vous utilisez VPN + ngrok → Ajoutez simplement ceci dans `.env` :**

```env
DISABLE_GEO_RESTRICTION=true
```

**Puis videz le cache :**
```bash
php artisan config:clear
php artisan cache:clear
```

**✅ Vous pourrez tester toute l'interface sans être bloqué !**

**En production (déploiement final), changez en :**
```env
DISABLE_GEO_RESTRICTION=false
```

---

## 🆘 Dépannage

### "Je suis toujours bloqué même avec DISABLE_GEO_RESTRICTION=true"
**Solution :**
```bash
php artisan config:clear
php artisan cache:clear
```

### "La route /test-location-debug ne fonctionne pas"
**Solution :** Vérifiez que vous êtes en environnement `local` :
```env
APP_ENV=local
```

### "Je veux voir exactement ce qui est détecté"
**Solution :**
1. Accédez à `/test-location-debug`
2. Regardez le JSON retourné
3. Vérifiez `country`, `city`, `region`

---

**Dernière mise à jour : 8 octobre 2025**
