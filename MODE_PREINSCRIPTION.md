# 🔒 Mode Pré-inscription - Documentation

## Vue d'ensemble

Le **Mode Pré-inscription** est un système qui verrouille l'application entière (similaire au mode maintenance) et redirige tous les visiteurs vers une page de pré-inscription. Ce mode est idéal pour :

- **Lancement d'application** : Collecter des inscriptions avant le lancement officiel
- **Refonte majeure** : Maintenir l'intérêt pendant une migration ou refonte
- **Lancement de nouvelles fonctionnalités** : Créer de l'anticipation
- **Phase de test fermé** : Contrôler l'accès aux testeurs

---

## 🎯 Fonctionnement

### Quand le mode pré-inscription est **ACTIVÉ** :

1. **Tous les visiteurs** (non connectés) sont automatiquement redirigés vers `/preregistration`
2. **Seuls les administrateurs** connectés peuvent accéder à l'application complète
3. Les visiteurs voient uniquement :
   - La page de pré-inscription
   - La page de connexion (pour les admins)
   - Les pages de confirmation/succès de pré-inscription

### Quand le mode pré-inscription est **DÉSACTIVÉ** :

- L'application fonctionne normalement
- Tous les utilisateurs peuvent accéder à toutes les pages publiques
- La page de pré-inscription reste accessible mais optionnelle

---

## 🚀 Activation/Désactivation

### Via l'interface admin

1. Connectez-vous en tant qu'administrateur
2. Allez dans **Admin > Paramètres** (`/admin/settings`)
3. Trouvez la section **"Mode Pré-inscription"**
4. Cliquez sur le bouton **"Activer"** ou **"Désactiver"**
5. Confirmez l'action dans la boîte de dialogue

### Via la base de données

```sql
-- Activer le mode pré-inscription
UPDATE settings SET value = '1' WHERE key = 'preregistration_enabled';

-- Désactiver le mode pré-inscription
UPDATE settings SET value = '0' WHERE key = 'preregistration_enabled';
```

### Via Tinker

```bash
php artisan tinker
```

```php
// Activer
\App\Models\Setting::set('preregistration_enabled', true);

// Désactiver
\App\Models\Setting::set('preregistration_enabled', false);
```

---

## ⚙️ Architecture Technique

### Middleware : `CheckPreregistrationMode`

**Fichier** : `app/Http/Middleware/CheckPreregistrationMode.php`

Ce middleware est appliqué globalement sur toutes les routes web et :

1. Vérifie si `preregistration_enabled = true` dans la base de données
2. Si OUI :
   - Vérifie si l'utilisateur est un admin connecté → **Autorise l'accès**
   - Vérifie si la route est dans la liste blanche → **Autorise l'accès**
   - Sinon → **Redirige vers `/preregistration`**
3. Si NON :
   - Laisse passer toutes les requêtes normalement

### Routes autorisées (liste blanche)

Même en mode pré-inscription, ces routes restent accessibles :

- `preregistration.*` - Toutes les routes de pré-inscription
- `login` - Connexion admin
- `logout` - Déconnexion

### Enregistrement du middleware

**Fichier** : `bootstrap/app.php`

```php
// Middleware global web
$middleware->web(append: [
    // ... autres middlewares
    \App\Http\Middleware\CheckPreregistrationMode::class,
]);
```

---

## 📊 Page de paramètres

### Section dans `/admin/settings`

La section affiche :

1. **Statut actuel** :
   - 🔒 MODE PRÉ-INSCRIPTION ACTIF (vert)
   - ✅ MODE NORMAL (rouge)

2. **Bouton de toggle** :
   - Confirmation avec message d'avertissement explicite
   - Changement instantané via AJAX
   - Rechargement automatique de la page

3. **Statistiques** :
   - Total d'inscriptions
   - Limite configurée
   - Places restantes

4. **Actions rapides** :
   - Configurer (paramètres détaillés)
   - Gérer les inscriptions
   - Voir la page publique

---

## 🔐 Sécurité

### Protection des admins

- Les admins connectés ont **TOUJOURS** accès à l'application
- Vérification via `auth()->check() && auth()->user()->isAdmin()`
- Aucun risque d'être verrouillé hors de l'application

### Gestion des sessions

- La connexion reste accessible en mode pré-inscription
- Les admins peuvent se déconnecter sans problème
- La déconnexion ne redirige pas vers la pré-inscription

---

## 📝 Configuration avancée

### Paramètres de pré-inscription

Accessibles via **Admin > Paramètres > Pré-inscription** :

| Paramètre | Description |
|-----------|-------------|
| `preregistration_enabled` | Active/désactive le mode (true/false) |
| `preregistration_title` | Titre de la page |
| `preregistration_subtitle` | Sous-titre |
| `preregistration_message` | Message de bienvenue |
| `preregistration_benefits` | Liste des avantages (JSON) |
| `preregistration_limit` | Nombre max d'inscriptions (0 = illimité) |
| `preregistration_require_phone` | Téléphone obligatoire (true/false) |
| `preregistration_require_confirmation` | Email de confirmation (true/false) |
| `preregistration_notification_email` | Email admin pour notifications |
| `preregistration_closed_message` | Message si fermé manuellement |

---

## 🧪 Tests

### Test d'activation

1. **En tant qu'admin** :
   ```
   1. Activer le mode pré-inscription
   2. Ouvrir un navigateur privé
   3. Aller sur http://127.0.0.1:8000
   4. Vérifier la redirection vers /preregistration
   5. Essayer d'accéder à /items ou /brands
   6. Vérifier que tout redirige vers /preregistration
   ```

2. **Accès admin** :
   ```
   1. Dans votre session admin normale
   2. Vérifier que vous pouvez accéder à toutes les pages
   3. Tester /admin, /items, /brands, etc.
   4. Tout doit fonctionner normalement
   ```

3. **Désactivation** :
   ```
   1. Désactiver le mode pré-inscription
   2. En navigation privée, recharger la page
   3. Vérifier que l'application est accessible
   ```

---

## 🆚 Différence avec le Mode Maintenance

| Caractéristique | Mode Maintenance | Mode Pré-inscription |
|----------------|------------------|----------------------|
| **Objectif** | Bloquer l'accès pendant maintenance | Collecter des inscriptions avant lancement |
| **Page affichée** | Message "En maintenance" | Formulaire d'inscription |
| **Accès admin** | ✅ Autorisé | ✅ Autorisé |
| **Collecte de données** | ❌ Non | ✅ Oui (emails, noms, téléphones) |
| **Gestion des inscrits** | ❌ N/A | ✅ Interface admin complète |
| **Notifications** | ❌ Non | ✅ Emails de confirmation |
| **Statistiques** | ❌ Non | ✅ Dashboard avec stats |

---

## 📧 Workflow complet

```
1. Admin active le mode pré-inscription
   ↓
2. Visiteur accède à l'app
   ↓
3. Redirection automatique → /preregistration
   ↓
4. Visiteur remplit le formulaire
   ↓
5. Email de confirmation envoyé
   ↓
6. Visiteur confirme son email
   ↓
7. Statut : "confirmé" dans la base
   ↓
8. Admin voit les inscriptions dans /admin/waiting-users
   ↓
9. Admin approuve manuellement ou automatiquement
   ↓
10. Lorsque prêt, admin désactive le mode pré-inscription
   ↓
11. Application accessible à tous
   ↓
12. Admin peut convertir les inscrits en vrais comptes
```

---

## 🐛 Dépannage

### Problème : "Je suis bloqué hors de l'application"

**Solution** :
```bash
# Désactiver via Tinker
php artisan tinker
\App\Models\Setting::set('preregistration_enabled', false);
exit
```

### Problème : "Les admins sont aussi redirigés"

**Causes possibles** :
1. L'admin n'est pas connecté
2. La méthode `isAdmin()` ne fonctionne pas correctement
3. Le middleware n'est pas appliqué correctement

**Vérification** :
```bash
php artisan tinker
auth()->check(); // Doit retourner true
auth()->user()->isAdmin(); // Doit retourner true
```

### Problème : "Le mode ne s'active/désactive pas"

**Solution** :
```bash
# Vider tous les caches
php artisan optimize:clear

# Vérifier la base de données
php artisan tinker
\App\Models\Setting::get('preregistration_enabled');
```

---

## 🎨 Personnalisation

### Modifier la page de pré-inscription

**Fichier** : `resources/views/preregistration/index.blade.php`

Vous pouvez personnaliser :
- Le design (CSS)
- Les champs du formulaire
- Les messages
- Les animations

### Modifier la page "Fermé"

**Fichier** : `resources/views/preregistration/closed.blade.php`

Affiché quand le mode est désactivé mais que quelqu'un accède directement à `/preregistration`.

---

## 📚 Ressources

- **Code source middleware** : `app/Http/Middleware/CheckPreregistrationMode.php`
- **Contrôleur** : `app/Http/Controllers/PreRegistrationController.php`
- **Routes** : `routes/web.php` (section preregistration)
- **Modèle** : `app/Models/UserWaiting.php`
- **Migration** : `database/migrations/*_add_preregistration_settings_to_settings_table.php`

---

## ✅ Checklist de mise en production

- [ ] Configurer SMTP pour les emails (`config/mail.php`)
- [ ] Tester l'envoi d'emails de confirmation
- [ ] Personnaliser les messages de la page de pré-inscription
- [ ] Définir une limite d'inscriptions (ou laisser illimité)
- [ ] Configurer l'email de notification admin
- [ ] Tester le workflow complet (inscription → confirmation → approbation)
- [ ] Vérifier que les admins peuvent toujours accéder à l'app
- [ ] Documenter pour votre équipe comment activer/désactiver
- [ ] Prévoir une stratégie de conversion des inscrits en utilisateurs réels

---

**Dernière mise à jour** : 6 octobre 2025
**Version** : 1.0
