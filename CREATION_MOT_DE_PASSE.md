# 🔑 Système de Création de Mot de Passe - Documentation

## Vue d'ensemble

Ce système permet aux utilisateurs pré-inscrits de **créer leur propre mot de passe** après approbation par l'admin. C'est plus sécurisé que de générer un mot de passe aléatoire.

---

## 🔄 Workflow Complet

```
1. Utilisateur se pré-inscrit
   └─> Statut: "pending"
   └─> Email de confirmation envoyé

2. Utilisateur confirme son email
   └─> Statut: "confirmed"

3. Admin approuve la pré-inscription
   └─> Statut: "approved"
   └─> Compte User créé automatiquement
   └─> Token de mot de passe généré (valide 7 jours)
   └─> Email avec lien envoyé

4. Utilisateur clique sur le lien
   └─> Redirigé vers /set-password?token=xxx&email=xxx
   └─> Formulaire de création de mot de passe

5. Utilisateur définit son mot de passe
   └─> Mot de passe hashé et enregistré
   └─> Token supprimé (usage unique)
   └─> Connexion automatique
   └─> Redirection vers le dashboard
   └─> Statut final: "converted"
```

---

## 🗄️ Base de données

### Table `users_waiting` - Nouveaux champs

```sql
password_setup_token VARCHAR(64) NULL
password_setup_token_expires_at TIMESTAMP NULL
```

**Migration** : `2025_10_06_211348_add_password_setup_token_to_users_waiting_table.php`

---

## 📧 Email envoyé à l'utilisateur

**Sujet** : `✅ Votre compte VintApp est prêt ! Définissez votre mot de passe`

**Contenu** :
- Message de félicitations
- Bouton "Définir mon mot de passe"
- Date d'expiration du lien (7 jours)
- URL de secours si le bouton ne fonctionne pas

**Template** : `resources/views/emails/set-password.blade.php`

**Mailable** : `app/Mail/SetPasswordMail.php`

---

## 🔐 Sécurité

### Token de mot de passe

- **Génération** : `Str::random(60)` (60 caractères aléatoires)
- **Stockage** : Hash SHA-256 dans la BDD
- **Expiration** : 7 jours après création
- **Usage** : Une seule utilisation (supprimé après définition du mot de passe)

### Validation du mot de passe

**Exigences** :
- Minimum 8 caractères
- Confirmation obligatoire (password_confirmation)
- Validation côté serveur ET côté client

**Indicateur de force** :
- 🔴 Faible (< 2 critères)
- 🟠 Moyen (2-3 critères)
- 🟢 Fort (4+ critères)

Critères évalués :
- Longueur ≥ 8 caractères
- Longueur ≥ 12 caractères
- Majuscules + minuscules
- Chiffres
- Caractères spéciaux

---

## 🎯 Code Important

### 1. Modèle `UserWaiting`

#### Méthode `createUserAccount()`

```php
public function createUserAccount()
{
    // Générer token
    $token = Str::random(60);
    $expiresAt = now()->addDays(7);
    
    // Stocker le hash du token
    $this->update([
        'password_setup_token' => hash('sha256', $token),
        'password_setup_token_expires_at' => $expiresAt,
    ]);
    
    // Créer le User
    $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,
        'country' => $this->country ?? 'RDC',
        'password' => bcrypt(Str::random(32)), // Temporaire
        'email_verified_at' => $this->email_confirmed_at,
    ]);
    
    // Marquer comme converti
    $this->markAsConverted($user);
    
    return ['user' => $user, 'token' => $token];
}
```

#### Méthode `sendPasswordSetupEmail()`

```php
public function sendPasswordSetupEmail($token)
{
    $setupUrl = route('password.setup', [
        'token' => $token, 
        'email' => $this->email
    ]);
    
    Mail::to($this->email)->send(new SetPasswordMail($this, $setupUrl));
}
```

#### Méthode `approve()` modifiée

```php
public function approve($adminNotes = null)
{
    $this->update([
        'status' => 'approved',
        'approved_at' => now(),
        'admin_notes' => $adminNotes,
    ]);
    
    // Créer le compte + générer token
    $result = $this->createUserAccount();
    
    // Envoyer l'email
    $this->sendPasswordSetupEmail($result['token']);
    
    return $this;
}
```

---

### 2. AdminController

#### `showSetPasswordForm()` - Afficher le formulaire

```php
public function showSetPasswordForm(Request $request)
{
    $token = $request->query('token');
    $email = $request->query('email');
    
    // Vérifications...
    
    $userWaiting = UserWaiting::where('email', $email)
        ->where('password_setup_token', hash('sha256', $token))
        ->first();
    
    // Vérifier expiration...
    
    return view('auth.set-password', [
        'token' => $token,
        'email' => $email,
        'name' => $user->name,
    ]);
}
```

#### `setPassword()` - Enregistrer le mot de passe

```php
public function setPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    
    // Vérifier token + email...
    
    // Mettre à jour le mot de passe
    $user->update([
        'password' => bcrypt($request->password),
    ]);
    
    // Supprimer le token
    $userWaiting->update([
        'password_setup_token' => null,
        'password_setup_token_expires_at' => null,
    ]);
    
    // Connexion auto
    auth()->login($user);
    
    return redirect()->route('dashboard')
        ->with('success', '🎉 Bienvenue sur VintApp !');
}
```

---

### 3. Routes

```php
// Routes publiques (accessibles même en mode pré-inscription)
Route::get('/set-password', [AdminController::class, 'showSetPasswordForm'])
    ->name('password.setup');

Route::post('/set-password', [AdminController::class, 'setPassword'])
    ->name('password.setup.store');
```

---

## 🧪 Tests

### Test du workflow complet

1. **Créer une pré-inscription de test**
   ```bash
   php artisan tinker
   ```
   ```php
   $waiting = App\Models\UserWaiting::create([
       'name' => 'Test User',
       'email' => 'test@example.com',
       'phone' => '0812345678',
       'country' => 'RDC',
       'confirmation_token' => Str::random(32),
       'status' => 'confirmed',
       'email_confirmed_at' => now(),
   ]);
   ```

2. **Approuver depuis l'interface admin**
   - Aller sur `/admin/waiting-users`
   - Cliquer sur "Approuver" pour le test user
   - Vérifier les logs : `storage/logs/laravel.log`

3. **Vérifier l'email**
   - Si SMTP configuré : Vérifier la boîte mail
   - Sinon : Vérifier les logs Laravel

4. **Extraire le lien depuis les logs**
   ```
   Chercher : "Email de configuration de mot de passe envoyé à: test@example.com"
   ```

5. **Tester le lien**
   - Copier l'URL du lien
   - Ouvrir dans un navigateur
   - Remplir le formulaire
   - Soumettre

6. **Vérifications**
   ```php
   // Dans Tinker
   $user = User::where('email', 'test@example.com')->first();
   $user->password; // Doit être un hash bcrypt
   
   $waiting = UserWaiting::where('email', 'test@example.com')->first();
   $waiting->status; // Doit être "converted"
   $waiting->password_setup_token; // Doit être NULL
   ```

---

## 🛠️ Configuration SMTP (pour production)

### Gmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vintapp.com
MAIL_FROM_NAME="VintApp"
```

### Mailtrap (pour développement)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

---

## 🐛 Dépannage

### Problème : "Lien invalide ou expiré"

**Causes possibles** :
1. Le token a expiré (> 7 jours)
2. Le token a déjà été utilisé
3. L'email ne correspond pas
4. Le hash du token ne correspond pas

**Solution** :
```php
// Vérifier dans Tinker
$waiting = UserWaiting::where('email', 'test@example.com')->first();
$waiting->password_setup_token; // Doit être un hash SHA-256
$waiting->password_setup_token_expires_at; // Doit être dans le futur
```

**Réinitialiser le token manuellement** :
```php
$token = Str::random(60);
$waiting->update([
    'password_setup_token' => hash('sha256', $token),
    'password_setup_token_expires_at' => now()->addDays(7),
]);

$url = route('password.setup', ['token' => $token, 'email' => $waiting->email]);
echo $url; // Copier ce lien
```

---

### Problème : "Email non envoyé"

**Vérifications** :
1. SMTP configuré dans `.env`
2. Vérifier les logs : `storage/logs/laravel.log`
3. Tester l'envoi d'email :
   ```bash
   php artisan tinker
   ```
   ```php
   Mail::raw('Test email', function($message) {
       $message->to('test@example.com')->subject('Test');
   });
   ```

---

### Problème : "Compte non créé"

**Vérifier** :
```php
$waiting = UserWaiting::where('email', 'test@example.com')->first();
$waiting->status; // Doit être "approved" ou "converted"
$waiting->converted_user_id; // Doit contenir un ID

$user = User::find($waiting->converted_user_id);
$user; // Doit exister
```

---

## 📊 Statuts de UserWaiting

| Statut | Description |
|--------|-------------|
| `pending` | Pré-inscription en attente de confirmation email |
| `confirmed` | Email confirmé, en attente d'approbation admin |
| `approved` | Approuvé par admin, compte User créé, email de mot de passe envoyé |
| `converted` | Mot de passe défini, utilisateur actif |
| `rejected` | Rejeté par admin |

---

## ✅ Checklist

- [x] Migration exécutée
- [x] Modèle `UserWaiting` mis à jour
- [x] Méthodes `createUserAccount()` et `sendPasswordSetupEmail()` créées
- [x] Méthode `approve()` modifiée
- [x] Mailable `SetPasswordMail` créé
- [x] Template email finalisé
- [x] Routes publiques ajoutées
- [x] Méthodes dans `AdminController`
- [x] Vue `set-password.blade.php` créée
- [x] Middleware `CheckPreregistrationMode` mis à jour
- [ ] SMTP configuré (pour production)
- [ ] Tests complets effectués

---

## 🎨 Interface Utilisateur

### Page `/set-password`

**Design** :
- Dégradé violet (branding VintApp)
- Formulaire centré avec carte blanche
- Icône de clé dans un cercle
- Message de bienvenue personnalisé

**Fonctionnalités** :
- Toggle visibilité du mot de passe (icône œil)
- Barre de force du mot de passe (faible/moyen/fort)
- Validation en temps réel
- Messages d'erreur clairs
- Désactivation du bouton après soumission

---

**Dernière mise à jour** : 6 octobre 2025  
**Version** : 1.0
