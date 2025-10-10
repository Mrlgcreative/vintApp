# 🗺️ Guide des Restrictions Géographiques - VintApp

## Vue d'Ensemble

Le système de restrictions géographiques permet à l'administrateur de contrôler dans quelles villes les vendeurs peuvent publier des articles.

## 🎛️ Paramètre Global

### Activation/Désactivation

**Accès** : Admin → Paramètres → Section "Restrictions Géographiques"

#### Mode ACTIVÉ ✅
- Les vendeurs ne peuvent publier des articles **QUE** dans les villes autorisées par l'admin
- Liste des villes disponibles : celles ajoutées dans "Gestion des Zones"
- Contrôle total sur les zones de vente
- Idéal pour : marchés régionaux, tests de nouvelles zones, conformité légale

#### Mode DÉSACTIVÉ ❌
- Les vendeurs peuvent publier des articles dans **N'IMPORTE QUELLE** ville du monde
- Recherche mondiale via OpenStreetMap Nominatim
- 195 pays disponibles avec autocomplete
- Idéal pour : marketplace international, expansion globale

## 📍 Gestion des Villes Autorisées

### Ajouter une Ville Autorisée

1. Allez dans **Admin → Gestion des Zones**
2. Cliquez sur **"Ajouter une ville"**
3. Sélectionnez le pays (195 pays disponibles)
4. Tapez le nom de la ville (min 3 caractères)
5. Sélectionnez la ville dans l'autocomplete
6. Les coordonnées GPS sont remplies automatiquement
7. Cliquez sur **"Ajouter"**

### Modifier/Supprimer une Ville

1. Dans la liste des villes autorisées
2. Cliquez sur l'icône **"Éditer"** ou **"Supprimer"**
3. Confirmez l'action

## 🔧 Architecture Technique

### Fichiers Modifiés

#### 1. **Migration : `2025_10_09_174222_create_settings_table.php`**
```php
- Crée la table 'settings' avec structure clé/valeur
- Ajoute le paramètre 'enable_location_restrictions' (défaut: activé)
- Type: boolean
- Description: "Active ou désactive les restrictions géographiques"
```

#### 2. **Modèle : `app/Models/Setting.php`** (existant)
```php
- Méthodes: get(), set(), clearCache()
- Cache de 1 heure (3600 secondes)
- Support du cryptage pour valeurs sensibles
```

#### 3. **Controller : `app/Http/Controllers/Admin/AdminController.php`**
```php
// Nouvelle méthode : Toggle restrictions
public function toggleLocationRestrictions(Request $request)
{
    Setting::updateOrCreate(
        ['key' => 'enable_location_restrictions'],
        ['value' => $request->enabled ? '1' : '0', ...]
    );
}

// Nouvelle méthode : Récupérer le statut
public function getLocationRestrictionsStatus()
{
    return Setting::get('enable_location_restrictions', true);
}
```

#### 4. **Controller : `app/Http/Controllers/ItemController.php`**
```php
// Méthode create() modifiée
public function create()
{
    $locationRestrictionsEnabled = Setting::get('enable_location_restrictions', true);
    
    $allowedCities = $locationRestrictionsEnabled 
        ? AllowedCity::active()->get()
        : collect(); // Vide si désactivé
    
    return view('items.create', compact('allowedCities', 'locationRestrictionsEnabled'));
}
```

#### 5. **Routes : `routes/web.php`**
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        // Routes pour restrictions géographiques
        Route::post('/location-restrictions/toggle', [AdminController::class, 'toggleLocationRestrictions'])
            ->name('location-restrictions.toggle');
        Route::get('/location-restrictions/status', [AdminController::class, 'getLocationRestrictionsStatus'])
            ->name('location-restrictions.status');
    });
});
```

#### 6. **Vue : `resources/views/admin/settings/index.blade.php`**
```html
<!-- Nouvelle section après Mode Maintenance -->
<div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-6">
    <h3>🗺️ Restrictions Géographiques</h3>
    
    <!-- Statut dynamique -->
    <div id="location-status-icon">...</div>
    <div id="location-status-text">...</div>
    
    <!-- Bouton toggle -->
    <button onclick="toggleLocationRestrictions()">
        Activer/Désactiver
    </button>
</div>

<!-- JavaScript -->
<script>
async function toggleLocationRestrictions() {
    // Confirmation
    // Appel API POST /admin/settings/location-restrictions/toggle
    // Mise à jour UI
}

async function loadLocationRestrictionsStatus() {
    // Appel API GET /admin/settings/location-restrictions/status
    // Mise à jour UI selon l'état
}
</script>
```

## 🔄 Flux de Fonctionnement

### Flux 1 : Activation des Restrictions

```
1. Admin clique sur "Activer" dans Paramètres
   ↓
2. Confirmation modal : "Les vendeurs ne pourront publier QUE dans les villes autorisées"
   ↓
3. POST /admin/settings/location-restrictions/toggle { enabled: true }
   ↓
4. AdminController::toggleLocationRestrictions()
   ↓
5. Setting::updateOrCreate(['key' => 'enable_location_restrictions'], ['value' => '1'])
   ↓
6. Cache::forget('setting.enable_location_restrictions')
   ↓
7. Response JSON { success: true, enabled: true }
   ↓
8. UI mise à jour : Bouton devient "Désactiver" (rouge)
   ↓
9. Notification : "Restrictions géographiques activées avec succès"
```

### Flux 2 : Création d'Article (Restrictions ACTIVÉES)

```
1. Vendeur accède à /items/create
   ↓
2. ItemController::create()
   ↓
3. $locationRestrictionsEnabled = Setting::get('enable_location_restrictions', true) // = true
   ↓
4. $allowedCities = AllowedCity::active()->get() // Récupère SEULEMENT les villes autorisées
   ↓
5. Vue affiche : Dropdown avec villes autorisées UNIQUEMENT
   ↓
6. Vendeur sélectionne une ville dans la liste restreinte
   ↓
7. POST /items avec city_id validé contre allowed_cities
   ↓
8. Article créé avec localisation contrôlée
```

### Flux 3 : Création d'Article (Restrictions DÉSACTIVÉES)

```
1. Vendeur accède à /items/create
   ↓
2. ItemController::create()
   ↓
3. $locationRestrictionsEnabled = Setting::get('enable_location_restrictions', true) // = false
   ↓
4. $allowedCities = collect() // Collection vide
   ↓
5. Vue affiche : Autocomplete mondiale (195 pays, toutes villes via OpenStreetMap)
   ↓
6. Vendeur tape n'importe quelle ville (Paris, Tokyo, New York, etc.)
   ↓
7. API OpenStreetMap Nominatim cherche les villes correspondantes
   ↓
8. Vendeur sélectionne une ville (coordonnées GPS auto-remplies)
   ↓
9. POST /items avec city_name + coordinates
   ↓
10. Article créé avec localisation mondiale
```

## 🧪 Tests à Effectuer

### Test 1 : Toggle Activation/Désactivation

```bash
# Statut initial
GET /admin/settings/location-restrictions/status
→ { success: true, enabled: true }

# Désactiver
POST /admin/settings/location-restrictions/toggle
Body: { enabled: false }
→ { success: true, message: "Restrictions géographiques désactivées avec succès", enabled: false }

# Vérifier en base
SELECT * FROM settings WHERE key = 'enable_location_restrictions';
→ value = '0'

# Réactiver
POST /admin/settings/location-restrictions/toggle
Body: { enabled: true }
→ { success: true, message: "Restrictions géographiques activées avec succès", enabled: true }
```

### Test 2 : Création d'Article (Restrictions ON)

```
1. Activer les restrictions
2. Aller sur /items/create
3. Vérifier que SEULEMENT les villes autorisées apparaissent
4. Essayer de créer un article avec une ville non autorisée
   → Devrait échouer (validation)
5. Créer un article avec une ville autorisée
   → Devrait réussir
```

### Test 3 : Création d'Article (Restrictions OFF)

```
1. Désactiver les restrictions
2. Aller sur /items/create
3. Vérifier l'autocomplete mondiale (195 pays)
4. Taper "Paris" → Devrait afficher Paris, France
5. Taper "Tokyo" → Devrait afficher Tokyo, Japon
6. Créer un article avec n'importe quelle ville
   → Devrait réussir
```

### Test 4 : Cache

```bash
# Vérifier que le cache est vidé après toggle
php artisan tinker
>>> Cache::has('setting.enable_location_restrictions')
→ false (devrait être false après toggle)

# Le cache se recrée au prochain get()
>>> Setting::get('enable_location_restrictions')
→ "1" ou "0"

>>> Cache::has('setting.enable_location_restrictions')
→ true (cache recréé pour 1 heure)
```

## 📊 Base de Données

### Table : `settings`

```sql
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `category` varchar(255) NULL,
  `label` varchar(255) NULL,
  `description` text NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `is_encrypted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Enregistrement : `enable_location_restrictions`

```sql
INSERT INTO `settings` VALUES (
  NULL,
  'enable_location_restrictions',
  '1',
  'boolean',
  'general',
  'Restrictions géographiques',
  'Active ou désactive les restrictions géographiques pour les articles',
  0,
  0,
  NOW(),
  NOW()
);
```

## 🔐 Sécurité

### Contrôle d'Accès

- **Middleware** : `auth`, `admin`
- **Routes protégées** : Seuls les admins peuvent modifier ce paramètre
- **Validation** : `enabled` doit être un booléen

### Logs

```php
// Activation
Log::info('Restrictions géographiques activées par l\'admin: admin@example.com');

// Désactivation
Log::info('Restrictions géographiques désactivées par l\'admin: admin@example.com');

// Erreur
Log::error('Erreur lors du toggle des restrictions géographiques: message_erreur');
Log::error('Stack trace: ...');
```

## 📈 Cas d'Usage

### Scénario 1 : Lancement par Région
**Problème** : VintApp lance dans 5 villes pilotes uniquement  
**Solution** : Activer les restrictions, ajouter les 5 villes  
**Résultat** : Les vendeurs ne peuvent publier que dans ces 5 villes

### Scénario 2 : Expansion Internationale
**Problème** : Après succès local, expansion mondiale  
**Solution** : Désactiver les restrictions  
**Résultat** : Les vendeurs peuvent publier dans 195 pays

### Scénario 3 : Conformité Légale
**Problème** : Certains pays interdisent certains types de ventes  
**Solution** : Activer les restrictions, exclure les pays problématiques  
**Résultat** : Conformité garantie

### Scénario 4 : Test A/B
**Problème** : Tester l'impact des restrictions sur les ventes  
**Solution** : Toggle ON/OFF et comparer les métriques  
**Résultat** : Données pour décision business

## 🛠️ Maintenance

### Vider le Cache Manuellement

```bash
# Via Artisan
php artisan cache:forget setting.enable_location_restrictions

# Via Tinker
php artisan tinker
>>> Cache::forget('setting.enable_location_restrictions')
>>> Setting::clearCache()
```

### Réinitialiser le Paramètre

```bash
php artisan tinker
>>> Setting::where('key', 'enable_location_restrictions')->update(['value' => '1'])
>>> Cache::forget('setting.enable_location_restrictions')
```

### Vérifier l'État Actuel

```bash
php artisan tinker
>>> Setting::get('enable_location_restrictions')
→ "1" (activé) ou "0" (désactivé)
```

## 🚀 Améliorations Futures

### Phase 2 : Restrictions par Catégorie
- Permettre des restrictions différentes par catégorie d'article
- Ex : Électronique → mondial, Nourriture → local uniquement

### Phase 3 : Restrictions par Utilisateur
- Certains vendeurs vérifiés peuvent vendre mondialement
- Nouveaux vendeurs limités à leur région

### Phase 4 : Analytics
- Dashboard admin : taux d'articles par zone
- Alertes si une zone devient inactive
- Suggestions de nouvelles zones à ouvrir

### Phase 5 : Géofencing Dynamique
- Restrictions basées sur l'IP du vendeur
- Auto-détection de la ville du vendeur
- Limiter aux villes dans un rayon X km

---

**Version** : 1.0  
**Date** : 9 octobre 2025  
**Auteur** : VintApp Development Team  
**Status** : ✅ Production Ready
