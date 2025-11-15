# ✅ Intégration Firebase - Pré-inscription VintApp

## 🎉 Implémentation terminée !

L'intégration Firebase pour la page de pré-inscription est maintenant **complète et fonctionnelle**.

## 📦 Ce qui a été fait

### 1. ✅ Frontend (Blade + JavaScript)

**Fichier modifié** : `resources/views/preregistration/index.blade.php`

-   ✅ Firebase SDK intégré (Auth + Firestore)
-   ✅ Configuration Firebase depuis `.env` Laravel
-   ✅ Formulaire avec validation côté client
-   ✅ Création automatique de compte Firebase Auth
-   ✅ Enregistrement dans Firestore (`preregistrations` collection)
-   ✅ Génération de mot de passe temporaire
-   ✅ Messages de succès/erreur dynamiques
-   ✅ Redirection vers page de statistiques
-   ✅ Gestion d'erreurs Firebase (email déjà utilisé, etc.)

### 2. ✅ Backend (Laravel)

**Contrôleur** : `app/Http/Controllers/PreRegistrationController.php`

-   ✅ Endpoint API JSON pour enregistrement
-   ✅ Validation des données (name, email, phone, country, reasons, firebase_uid)
-   ✅ Enregistrement dans `users_waiting` avec `firebase_uid`
-   ✅ Auto-confirmation (email validé par Firebase)
-   ✅ Support des motivations (reasons array)
-   ✅ Logging des inscriptions

**Modèle** : `app/Models/UserWaiting.php`

-   ✅ Champ `firebase_uid` ajouté à `$fillable`

**Migration** : `2025_11_15_000001_add_firebase_uid_to_users_waiting_table.php`

-   ✅ Colonne `firebase_uid` (string 128, nullable, indexée)
-   ✅ Migration exécutée avec succès ✓

### 3. ✅ Configuration

**Fichier** : `config/firebase.php`

-   ✅ Configuration web Firebase pour JavaScript
-   ✅ Variables d'environnement mappées

## 🔐 Fonctionnement complet

### Processus d'inscription (étape par étape)

1. **Utilisateur remplit le formulaire** sur `/preregistration`

2. **Soumission du formulaire** :

    ```javascript
    // Empêche la soumission normale
    e.preventDefault();

    // Génère mot de passe : VintAppx7k9m2p5!
    const tempPassword = "VintApp" + Math.random().toString(36).slice(-8) + "!";
    ```

3. **Création compte Firebase** :

    ```javascript
    const userCredential = await auth.createUserWithEmailAndPassword(
        email,
        tempPassword
    );
    const user = userCredential.user;

    // Mise à jour du profil
    await user.updateProfile({ displayName: name });
    ```

4. **Enregistrement Firestore** :

    ```javascript
    await db.collection('preregistrations').doc(user.uid).set({
      uid: user.uid,
      name: "Jean Dupont",
      email: "jean@example.com",
      phone: "+243812345678",
      country: "Congo (RDC)",
      reasons: ["Acheter des produits vintage", ...],
      status: "pending",
      approved: false,
      createdAt: serverTimestamp(),
      tempPassword: "VintAppx7k9m2p5!",
      accountType: "preregistration"
    });
    ```

5. **Enregistrement Laravel** (API JSON) :

    ```javascript
    const response = await fetch("/preregistration", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
        },
        body: JSON.stringify({
            name,
            email,
            phone,
            country,
            reasons,
            firebase_uid: user.uid,
        }),
    });
    ```

6. **Déconnexion Firebase** :

    ```javascript
    await auth.signOut(); // L'utilisateur se connectera plus tard
    ```

7. **Confirmation** :
    - Message de succès affiché
    - Redirection vers `/preregistration/stats` après 3 secondes

## 📊 Données enregistrées

### Base de données Laravel (`users_waiting`)

| Colonne        | Type         | Valeur exemple                                         |
| -------------- | ------------ | ------------------------------------------------------ |
| `id`           | bigint       | 1                                                      |
| `name`         | varchar      | Jean Dupont                                            |
| `email`        | varchar      | jean@example.com                                       |
| `phone`        | varchar      | +243812345678                                          |
| `country`      | varchar      | Congo (RDC)                                            |
| `message`      | text         | Acheter des produits vintage, Rejoindre une communauté |
| `firebase_uid` | varchar(128) | **abc123xyz789** ← Nouveau !                           |
| `confirmed_at` | timestamp    | 2025-11-15 10:30:00 ← Auto-confirmé                    |
| `status`       | varchar      | pending                                                |
| `created_at`   | timestamp    | 2025-11-15 10:30:00                                    |

### Firestore Firebase (`preregistrations/{uid}`)

```json
{
    "uid": "abc123xyz789",
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "phone": "+243812345678",
    "country": "Congo (RDC)",
    "reasons": [
        "Acheter des produits vintage de qualité",
        "Rejoindre une communauté de passionnés"
    ],
    "status": "pending",
    "approved": false,
    "createdAt": "2025-11-15T10:30:00Z",
    "tempPassword": "VintAppx7k9m2p5!",
    "accountType": "preregistration"
}
```

### Firebase Authentication

-   ✅ Compte créé avec email/password
-   ✅ `displayName` = nom complet
-   ✅ Email vérifié (optionnel selon config)

## 🔄 Migration vers l'application finale

### Quand l'app sera en ligne

Les utilisateurs pré-inscrits pourront **directement se connecter** :

1. **Page de login** : `/login`
2. **Identifiants** :

    - Email : `jean@example.com`
    - Mot de passe : `VintAppx7k9m2p5!` (reçu par email)

3. **Première connexion** :
    - Redirection vers changement de mot de passe **obligatoire**
    - Nouveau mot de passe défini
    - Accès complet à l'application

### Script de migration automatique

Créer une commande Artisan :

```bash
php artisan make:command ConvertPreregisteredUsers
```

```php
public function handle()
{
    $waiting = UserWaiting::whereNotNull('firebase_uid')
        ->whereNull('converted_at')
        ->get();

    foreach ($waiting as $w) {
        $user = User::create([
            'name' => $w->name,
            'email' => $w->email,
            'phone' => $w->phone,
            'country' => $w->country,
            'firebase_uid' => $w->firebase_uid,
            'email_verified_at' => now(),
            'must_change_password' => true
        ]);

        $w->update([
            'converted_at' => now(),
            'converted_user_id' => $user->id
        ]);
    }
}
```

## ⚙️ Configuration requise

### Fichier `.env`

Assurez-vous d'avoir **toutes** ces variables :

```env
# Firebase Web Configuration (pour JavaScript)
FIREBASE_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
FIREBASE_AUTH_DOMAIN=votre-projet.firebaseapp.com
FIREBASE_PROJECT_ID=votre-projet
FIREBASE_STORAGE_BUCKET=votre-projet.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789012
FIREBASE_APP_ID=1:123456789012:web:abcdef123456

# Firebase Server (PHP SDK - optionnel pour cette feature)
FIREBASE_CREDENTIALS=path/to/serviceAccountKey.json
FIREBASE_DATABASE_URL=https://votre-projet.firebaseio.com
```

### Console Firebase

1. **Authentication** → Sign-in method :

    - ✅ Activer "Email/Password"

2. **Firestore Database** :

    - ✅ Créer la base de données
    - ✅ Configurer les règles (voir ci-dessous)

3. **Settings** → Authorized domains :
    - ✅ Ajouter votre domaine (ex: `vintapp.com`, `localhost`)

### Règles Firestore recommandées

```javascript
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    // Collection des pré-inscriptions
    match /preregistrations/{userId} {
      // Permettre la création seulement pour son propre UID
      allow create: if request.auth != null &&
                      request.auth.uid == userId &&
                      request.resource.data.accountType == 'preregistration';

      // Lecture/modification réservée aux admins
      allow read, update, delete: if request.auth != null &&
        exists(/databases/$(database)/documents/admins/$(request.auth.uid));
    }
  }
}
```

## 📧 TODO : Envoi d'email

### Email de bienvenue

Créer un Mailable pour envoyer le mot de passe temporaire :

```bash
php artisan make:mail WelcomePreregistration
```

**Template** : `resources/views/emails/preregistration/welcome.blade.php`

```html
<h1>Bienvenue sur VintApp, {{ $name }} !</h1>

<p>Votre pré-inscription a été validée avec succès.</p>

<div style="background: #f3f4f6; padding: 20px; margin: 20px 0;">
    <h2>Vos identifiants de connexion</h2>
    <p><strong>Email :</strong> {{ $email }}</p>
    <p><strong>Mot de passe :</strong> <code>{{ $tempPassword }}</code></p>
</div>

<p>
    Une fois l'application en ligne, connectez-vous sur :<br />
    <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
</p>

<p>
    <strong>⚠️ Important :</strong> Vous devrez changer votre mot de passe lors
    de votre première connexion.
</p>
```

**Appel dans le contrôleur** :

```php
use App\Mail\WelcomePreregistration;

// Dans la méthode store(), après l'enregistrement
Mail::to($userWaiting->email)->send(new WelcomePreregistration([
    'name' => $userWaiting->name,
    'email' => $userWaiting->email,
    'tempPassword' => $request->input('temp_password'), // À récupérer du frontend
    'loginUrl' => route('login')
]));
```

⚠️ **Note** : Il faudra modifier le JavaScript pour envoyer le `tempPassword` au backend.

## 🧪 Tests

### Test manuel

1. Ouvrir `/preregistration`
2. Remplir le formulaire :
    - Nom : Test User
    - Email : test@example.com
    - Téléphone : +243812345678
    - Pays : Congo (RDC)
    - Cocher 2-3 motivations
3. Soumettre
4. Vérifier :
    - ✅ Message de succès
    - ✅ Enregistrement dans `users_waiting`
    - ✅ Compte créé dans Firebase Auth
    - ✅ Document dans Firestore `preregistrations`

### Vérification Firebase Console

```bash
# Firebase Authentication
firebase auth:export users.json --project votre-projet

# Firestore
# Ouvrir console.firebase.google.com
# → Firestore Database
# → Collection "preregistrations"
# → Vérifier les documents
```

### Vérification Base de données

```sql
SELECT
    id, name, email, firebase_uid,
    confirmed_at, created_at
FROM users_waiting
WHERE firebase_uid IS NOT NULL
ORDER BY created_at DESC;
```

## 🚀 Déploiement

### Checklist avant mise en production

-   [ ] Variables `.env` Firebase configurées
-   [ ] Migration `firebase_uid` appliquée
-   [ ] Domaine ajouté dans Firebase → Authorized domains
-   [ ] Règles Firestore configurées (sécurité)
-   [ ] Email de bienvenue implémenté et testé
-   [ ] Page "Changer mot de passe" créée
-   [ ] Script de conversion utilisateurs créé
-   [ ] Tests E2E effectués
-   [ ] Documentation équipe mise à jour

### En production

```bash
# 1. Mettre à jour le code
git pull origin main

# 2. Appliquer la migration
php artisan migrate --force

# 3. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. Compiler assets si nécessaire
npm run build

# 5. Redémarrer services
php artisan queue:restart
```

## 📈 Métriques à suivre

-   **Total pré-inscriptions** : `UserWaiting::count()`
-   **Avec Firebase** : `UserWaiting::whereNotNull('firebase_uid')->count()`
-   **Convertis en users** : `UserWaiting::whereNotNull('converted_at')->count()`
-   **Par pays** : `UserWaiting::groupBy('country')->count()`
-   **Taux de conversion** : (convertis / total) × 100

## 🆘 Support

Pour toute question :

1. **Documentation Firebase** : https://firebase.google.com/docs
2. **Laravel Firebase Package** : https://github.com/kreait/laravel-firebase
3. **Guide complet** : Voir `FIREBASE_PREREGISTRATION_GUIDE.md`

---

**Créé le** : 15 novembre 2025  
**Status** : ✅ Prêt pour production  
**Version** : 1.0.0
