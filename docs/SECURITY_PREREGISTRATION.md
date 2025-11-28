# 🔒 Sécurité du système de pré-inscription

## ⚠️ FAILLE DE SÉCURITÉ CORRIGÉE

### Problème identifié
Les routes admin de gestion des pré-inscriptions étaient accessibles à **tous les utilisateurs authentifiés**, pas seulement aux administrateurs.

### Ancien code (VULNÉRABLE) ❌
```php
// routes/web.php
Route::middleware(['auth'])->prefix('admin/waiting-users')->name('admin.waiting-users.')->group(function () {
    // Routes admin...
});
```

**Impact** : N'importe quel utilisateur connecté pouvait :
- ✅ Voir la liste de toutes les pré-inscriptions
- ✅ Accéder aux détails des utilisateurs (email, téléphone, IP, etc.)
- ✅ Approuver ou rejeter des demandes
- ✅ Supprimer des pré-inscriptions
- ✅ Exporter les données en CSV
- ✅ Effectuer des actions en masse

### Nouveau code (SÉCURISÉ) ✅
```php
// routes/web.php
Route::middleware(['auth', 'admin'])->prefix('admin/waiting-users')->name('admin.waiting-users.')->group(function () {
    // Routes admin...
});
```

**Protection** : Maintenant seuls les administrateurs peuvent accéder aux pages.

---

## 🛡️ Système de sécurité mis en place

### 1. **Middleware AdminMiddleware**

**Fichier** : `app/Http/Middleware/AdminMiddleware.php`

```php
public function handle(Request $request, Closure $next)
{ 
    // Vérifier si l'utilisateur est connecté
    if (!Auth::check()) {
        abort(403, 'Accès refusé. Vous devez être connecté.');
    }

    $user = Auth::user();
    
    // Vérifier si l'utilisateur a le rôle admin
    if (!$user->isAdmin()) {
        abort(403, 'Accès refusé. Vous devez être administrateur pour accéder à cette page.');
    }

    return $next($request);
}
```

### 2. **Méthode isAdmin() dans le modèle User**

**Fichier** : `app/Models/User.php`

```php
/**
 * Vérifie si l'utilisateur est un admin.
 */
public function isAdmin(): bool
{
    return $this->hasRole('admin');
}

/**
 * Vérifie si l'utilisateur a un rôle spécifique.
 */
public function hasRole(string $role): bool
{
    return $this->roles()->where('slug', $role)->exists();
}
```

### 3. **Relation Many-to-Many avec la table roles**

**Fichier** : `app/Models/User.php`

```php
/**
 * Les rôles de l'utilisateur.
 */
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}
```

### 4. **Enregistrement du middleware**

**Fichier** : `bootstrap/app.php`

```php
$middleware->alias([
    'auth' => \App\Http\Middleware\Authenticate::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    // ... autres middlewares
]);
```

---

## 🧪 Comment tester la sécurité

### Test 1 : Utilisateur non connecté
```bash
# URL : http://127.0.0.1:8000/admin/waiting-users
# Résultat attendu : Redirection vers /login
```

### Test 2 : Utilisateur connecté SANS rôle admin
```bash
# 1. Se connecter avec un compte utilisateur normal
# 2. Essayer d'accéder à : http://127.0.0.1:8000/admin/waiting-users
# Résultat attendu : Erreur 403 "Accès refusé. Vous devez être administrateur."
```

### Test 3 : Utilisateur connecté AVEC rôle admin
```bash
# 1. Se connecter avec un compte admin
# 2. Accéder à : http://127.0.0.1:8000/admin/waiting-users
# Résultat attendu : Page s'affiche correctement
```

---

## 🔑 Comment assigner le rôle admin à un utilisateur

### Méthode 1 : Via Tinker (Rapide)
```bash
php artisan tinker

# Trouver l'utilisateur
$user = User::find(1);

# Trouver le rôle admin
$adminRole = Role::where('slug', 'admin')->first();

# Assigner le rôle
$user->roles()->attach($adminRole);

# Vérifier
$user->isAdmin(); // Doit retourner true
```

### Méthode 2 : Via SQL direct
```sql
-- 1. Trouver l'ID du rôle admin
SELECT id FROM roles WHERE slug = 'admin';

-- 2. Assigner le rôle à l'utilisateur (ID utilisateur = 1, ID rôle admin = 1)
INSERT INTO role_user (user_id, role_id) VALUES (1, 1);

-- 3. Vérifier
SELECT u.name, r.name as role 
FROM users u 
JOIN role_user ru ON u.id = ru.user_id 
JOIN roles r ON ru.role_id = r.id 
WHERE u.id = 1;
```

### Méthode 3 : Via Seeder (pour développement)
```php
// database/seeders/AdminSeeder.php
public function run()
{
    $adminRole = Role::where('slug', 'admin')->first();
    
    $admin = User::create([
        'name' => 'Administrateur',
        'email' => 'admin@vintapp.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);
    
    $admin->roles()->attach($adminRole);
    
    // Créer aussi les wallets
    Wallet::create([
        'user_id' => $admin->id,
        'currency' => 'USD',
        'balance' => 0,
    ]);
    
    Wallet::create([
        'user_id' => $admin->id,
        'currency' => 'CDF',
        'balance' => 0,
    ]);
}
```

---

## 📋 Checklist de sécurité

- ✅ **Middleware admin appliqué** aux routes `/admin/waiting-users/*`
- ✅ **Vérification du rôle** dans `AdminMiddleware`
- ✅ **Méthode isAdmin()** implémentée dans le modèle User
- ✅ **Relation roles** configurée (Many-to-Many)
- ✅ **Messages d'erreur clairs** en cas d'accès refusé
- ✅ **Code 403** retourné (pas de redirection qui pourrait révéler l'existence de la page)

---

## 🚨 Autres recommandations de sécurité

### 1. Logs des actions admin
Ajoutez des logs pour tracer les actions sensibles :

```php
// Dans WaitingUsersController::approve()
Log::info('Admin approved waiting user', [
    'admin_id' => auth()->id(),
    'admin_name' => auth()->user()->name,
    'user_id' => $waitingUser->id,
    'user_email' => $waitingUser->email,
    'ip' => request()->ip(),
]);
```

### 2. Rate limiting sur les routes admin
```php
// routes/web.php
Route::middleware(['auth', 'admin', 'throttle:60,1'])->prefix('admin/waiting-users')...
```

### 3. Validation des permissions par action
Pour un contrôle plus fin, créez des permissions spécifiques :
- `waiting-users.view`
- `waiting-users.approve`
- `waiting-users.reject`
- `waiting-users.delete`

### 4. Audit trail
Créez une table `admin_actions` pour tracer toutes les actions :

```php
Schema::create('admin_actions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('admin_id')->constrained('users');
    $table->string('action'); // approve, reject, delete, etc.
    $table->string('model_type'); // UserWaiting
    $table->unsignedBigInteger('model_id');
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->ipAddress('ip_address');
    $table->text('user_agent')->nullable();
    $table->timestamps();
});
```

### 5. Protection CSRF
Toutes les routes POST/DELETE/PUT sont déjà protégées par le token CSRF de Laravel :
```blade
<form method="POST">
    @csrf
    <!-- Formulaire -->
</form>
```

---

## 📚 Ressources

- [Laravel Authentication](https://laravel.com/docs/11.x/authentication)
- [Laravel Authorization](https://laravel.com/docs/11.x/authorization)
- [Laravel Middleware](https://laravel.com/docs/11.x/middleware)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)

---

## ✅ Conclusion

La faille de sécurité a été **corrigée avec succès**. Le système de pré-inscription est maintenant **sécurisé** et accessible uniquement aux administrateurs.

**Date de correction** : 6 octobre 2025  
**Auteur** : GitHub Copilot  
**Statut** : ✅ SÉCURISÉ
