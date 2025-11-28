# 🚀 Système de Pré-inscription VintApp

## 📋 Vue d'ensemble

Le système de pré-inscription permet de collecter des demandes d'inscription avant le lancement officiel de l'application. Les utilisateurs s'inscrivent, confirment leur email, sont approuvés par les administrateurs, puis leurs comptes sont automatiquement créés.

## 🎯 Fonctionnalités

### Pour les utilisateurs (Public)
- ✅ Formulaire de pré-inscription avec validation
- ✅ Confirmation par email
- ✅ Page de statut publique
- ✅ Statistiques en temps réel

### Pour les administrateurs
- ✅ Dashboard complet avec statistiques
- ✅ Gestion de la liste des pré-inscrits
- ✅ Filtres et recherche avancés
- ✅ Approbation/rejet individuel ou en masse
- ✅ Export CSV
- ✅ Historique détaillé
- ✅ Notes administrateur

### Transition automatique
- ✅ Commande Artisan pour créer les comptes
- ✅ Génération de mots de passe temporaires
- ✅ Création automatique des wallets (USD + CDF)
- ✅ Notifications par email

## 📁 Structure de la base de données

### Table `users_waiting`

```sql
- id: bigint (PK)
- name: string
- email: string (unique)
- phone: string (nullable)
- country: string (default: 'Congo (RDC)')
- message: text (nullable)
- confirmation_token: string (unique)
- status: enum ['pending', 'confirmed', 'approved', 'rejected', 'converted']
- email_confirmed_at: timestamp
- notified_at: timestamp
- approved_at: timestamp
- rejected_at: timestamp
- converted_at: timestamp
- converted_user_id: bigint (FK -> users.id)
- admin_notes: text
- ip_address: string
- user_agent: string
- created_at, updated_at, deleted_at
```

### Statuts

| Statut | Description |
|--------|-------------|
| `pending` | En attente de confirmation email |
| `confirmed` | Email confirmé, en attente d'approbation |
| `approved` | Approuvé par admin, prêt pour conversion |
| `rejected` | Rejeté par admin |
| `converted` | Converti en compte utilisateur réel |

## 🌐 Routes publiques

### Formulaire de pré-inscription
```
GET  /preregistration
POST /preregistration
```

### Confirmation d'email
```
GET /preregistration/confirm/{token}
```

### Statistiques publiques
```
GET /preregistration/stats
```

### Pages de status
```
GET /preregistration/success
GET /preregistration/already-confirmed
```

## 🔐 Routes administrateur

Toutes les routes admin nécessitent l'authentification (`auth` middleware).

### Gestion des pré-inscriptions
```
GET    /admin/waiting-users          # Liste
GET    /admin/waiting-users/{id}     # Détails
POST   /admin/waiting-users/{id}/approve      # Approuver
POST   /admin/waiting-users/{id}/reject       # Rejeter
POST   /admin/waiting-users/{id}/resend-confirmation  # Renvoyer email
DELETE /admin/waiting-users/{id}     # Supprimer
POST   /admin/waiting-users/bulk-action      # Actions en masse
GET    /admin/waiting-users/export/csv       # Export CSV
```

## 💻 Utilisation de la commande de conversion

### Commande Artisan

```bash
php artisan users:convert-waiting [options]
```

### Options

| Option | Description |
|--------|-------------|
| `--all` | Convertir tous les utilisateurs approuvés |
| `--id=X` | Convertir un ID spécifique (peut être répété) |
| `--limit=N` | Limiter le nombre de conversions |
| `--notify` | Envoyer un email avec les credentials |

### Exemples d'utilisation

#### Convertir tous les utilisateurs approuvés
```bash
php artisan users:convert-waiting --all
```

#### Convertir un utilisateur spécifique
```bash
php artisan users:convert-waiting --id=1
```

#### Convertir plusieurs utilisateurs
```bash
php artisan users:convert-waiting --id=1 --id=5 --id=10
```

#### Convertir avec limite et notifications
```bash
php artisan users:convert-waiting --all --limit=50 --notify
```

### Résultat de la commande

La commande affiche :
- Nombre d'utilisateurs à convertir
- Barre de progression
- Tableau récapitulatif (succès/échecs)
- **IMPORTANT**: Tableau des credentials générés

### Format des mots de passe temporaires

Format: `XXXXXX-999999`
- 6 lettres majuscules
- Tiret
- 6 chiffres

Exemple: `ABCDEF-123456`

### Que fait la conversion ?

Pour chaque utilisateur approuvé :

1. ✅ Crée un compte `User` avec :
   - name, email, phone, password (hashé)
   - email_verified_at = now()
   - is_active = true

2. ✅ Crée 2 wallets :
   - Wallet USD (balance: 0)
   - Wallet CDF (balance: 0)

3. ✅ Met à jour `UserWaiting` :
   - status = 'converted'
   - converted_at = now()
   - converted_user_id = ID du nouveau user

4. ✅ Envoie un email de bienvenue (si --notify)

5. ✅ Log tout dans `storage/logs/laravel.log`

## 📧 Emails (à configurer)

### Templates à créer

```php
// app/Mail/PreRegistrationConfirmation.php
// Envoyé après inscription pour confirmer l'email

// app/Mail/PreRegistrationApproved.php
// Envoyé après approbation par admin

// app/Mail/WelcomeNewUser.php
// Envoyé lors de la conversion avec credentials
```

### Configuration Mail

Dans `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vintapp.com
MAIL_FROM_NAME="VintApp"
```

## 🎨 Interface utilisateur

### Page publique de pré-inscription
- Design moderne avec gradient violet
- Formulaire avec validation en temps réel
- Badges de statistiques en direct
- Liste des avantages (accès prioritaire, bonus, etc.)
- Responsive mobile

### Dashboard administrateur
- Statistiques en cartes (Total, En attente, Approuvés, Convertis)
- Stats temporelles (Aujourd'hui, Cette semaine, Ce mois)
- Filtres avancés (statut, date, recherche)
- Actions en masse (approuver/rejeter plusieurs)
- Export CSV
- Timeline d'historique

## 📊 Workflow complet

```
1. Utilisateur remplit le formulaire
   ↓
2. Email de confirmation envoyé
   ↓
3. Utilisateur clique sur le lien
   ↓ (status: pending → confirmed)
4. Admin examine la demande
   ↓
5. Admin approuve
   ↓ (status: confirmed → approved)
6. Email d'approbation envoyé
   ↓
7. Admin exécute la commande de conversion
   ↓
8. Compte créé automatiquement
   ↓ (status: approved → converted)
9. Email de bienvenue avec credentials
   ↓
10. Utilisateur se connecte et change son mot de passe
```

## 🔧 Configuration recommandée

### Pour la production

1. **Configurer le mail** (SMTP réel)
2. **Créer les templates d'email**
3. **Ajouter un middleware admin** :
   ```php
   // Dans WaitingUsersController
   $this->middleware('admin');
   ```
4. **Planifier la conversion automatique** (optionnel) :
   ```php
   // Dans app/Console/Kernel.php
   protected function schedule(Schedule $schedule)
   {
       $schedule->command('users:convert-waiting --all --limit=100 --notify')
                ->dailyAt('02:00');
   }
   ```

## 📝 Personnalisation

### Changer les pays disponibles

Dans `resources/views/preregistration/index.blade.php` :
```html
<select name="country">
    <option value="Congo (RDC)">🇨🇩 Congo (RDC)</option>
    <option value="Autre pays">🌍 Autre pays</option>
</select>
```

### Modifier le format de téléphone

Dans `PreRegistrationController.php` et `UserWaiting.php` :
```php
'phone' => ['nullable', 'string', 'regex:/^VOTRE_REGEX$/', 'max:15'],
```

### Personnaliser les statuts

Dans la migration `users_waiting` :
```php
$table->enum('status', ['pending', 'confirmed', 'approved', 'rejected', 'converted', 'NOUVEAU_STATUT']);
```

## 🐛 Dépannage

### Les emails ne sont pas envoyés
- Vérifiez `.env` (MAIL_* variables)
- Testez avec `php artisan tinker` :
  ```php
  Mail::raw('Test', function($msg) {
      $msg->to('test@example.com')->subject('Test');
  });
  ```

### La commande de conversion échoue
- Vérifiez les logs : `storage/logs/laravel.log`
- Assurez-vous que les utilisateurs ont le statut `approved`
- Vérifiez que les emails ne sont pas déjà dans la table `users`

### Page admin inaccessible
- Vérifiez que l'utilisateur est authentifié
- Ajoutez un middleware admin si nécessaire

## 📈 Métriques et analytics

### Requêtes SQL utiles

```sql
-- Taux de confirmation
SELECT 
    COUNT(*) as total,
    COUNT(email_confirmed_at) as confirmed,
    ROUND(COUNT(email_confirmed_at) * 100.0 / COUNT(*), 2) as taux_confirmation
FROM users_waiting;

-- Temps moyen d'attente
SELECT AVG(DATEDIFF(approved_at, created_at)) as jours_moyens
FROM users_waiting
WHERE approved_at IS NOT NULL;

-- Inscriptions par jour (7 derniers jours)
SELECT DATE(created_at) as date, COUNT(*) as inscriptions
FROM users_waiting
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

## ✅ Checklist de lancement

- [ ] Migration exécutée
- [ ] Routes testées
- [ ] Emails configurés et testés
- [ ] Templates d'emails créés
- [ ] Middleware admin en place
- [ ] Page de pré-inscription accessible
- [ ] Dashboard admin fonctionnel
- [ ] Commande de conversion testée
- [ ] Logs vérifiés
- [ ] Export CSV testé
- [ ] Responsive mobile vérifié

## 🆘 Support

Pour toute question ou problème :
- Consultez les logs : `storage/logs/laravel.log`
- Vérifiez la documentation Laravel
- Contactez l'équipe de développement

---

**Version**: 1.0.0  
**Dernière mise à jour**: {{ now()->format('d/m/Y') }}  
**Auteur**: Équipe VintApp
