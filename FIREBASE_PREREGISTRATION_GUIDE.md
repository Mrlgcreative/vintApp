# Guide d'intégration Firebase pour la Pré-inscription VintApp

## 📋 Vue d'ensemble

Ce système permet aux utilisateurs de s'inscrire via Firebase Auth pendant la phase de pré-inscription. Une fois l'application en ligne, ils pourront simplement se connecter avec leurs identifiants existants.

## 🔧 Configuration requise

### 1. Variables d'environnement Firebase

Assurez-vous que votre fichier `.env` contient toutes les variables Firebase :

```env
# Firebase Server-side (PHP SDK)
FIREBASE_CREDENTIALS=path/to/serviceAccountKey.json
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_DATABASE_URL=https://your-project.firebaseio.com
FIREBASE_STORAGE_BUCKET=your-project.appspot.com

# Firebase Client-side (Web)
FIREBASE_API_KEY=your-api-key
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:abcdef123456

# Firebase Cloud Messaging (optionnel)
FIREBASE_VAPID_KEY=your-vapid-key
FIREBASE_SERVER_KEY=your-server-key
```

### 2. Migration de la base de données

Exécutez la migration pour ajouter le champ `firebase_uid` :

```bash
php artisan migrate
```

Cela ajoutera la colonne `firebase_uid` à la table `users_waiting`.

## 🚀 Fonctionnement

### Processus d'inscription

1. **Utilisateur remplit le formulaire** de pré-inscription
2. **JavaScript Firebase** :
    - Crée un compte Firebase Auth avec un mot de passe temporaire
    - Enregistre les données dans Firestore (`preregistrations` collection)
    - Envoie les données au backend Laravel
3. **Backend Laravel** :
    - Enregistre dans la table `users_waiting` avec le `firebase_uid`
    - Marque automatiquement comme confirmé
4. **Déconnexion automatique** de Firebase (l'utilisateur se connectera plus tard)

### Structure Firestore

**Collection : `preregistrations`**

```javascript
{
  uid: "firebase-user-uid",
  name: "Jean Dupont",
  email: "jean@example.com",
  phone: "+243812345678",
  country: "Congo (RDC)",
  reasons: ["Acheter des produits vintage", "Rejoindre une communauté"],
  status: "pending",
  approved: false,
  createdAt: Timestamp,
  tempPassword: "VintApp8xyz!",
  accountType: "preregistration"
}
```

## 🔐 Sécurité

### Mot de passe temporaire

Le mot de passe est généré automatiquement avec le format :

```
VintApp + 8 caractères aléatoires + !
Exemple : VintAppx7k9m2p5!
```

⚠️ **Important** : Ce mot de passe doit être envoyé à l'utilisateur par email pour qu'il puisse se connecter plus tard.

### Règles Firestore recommandées

```javascript
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /preregistrations/{userId} {
      // Permettre la création uniquement pour son propre UID
      allow create: if request.auth != null && request.auth.uid == userId;

      // Seuls les admins peuvent lire/modifier
      allow read, update, delete: if request.auth != null &&
        get(/databases/$(database)/documents/users/$(request.auth.uid)).data.role == 'admin';
    }
  }
}
```

### Règles Firebase Auth recommandées

Dans la console Firebase, activez :

-   ✅ Email/Password authentication
-   ❌ Désactiver "Email enumeration protection" pour la phase de pré-inscription
-   ✅ Activer l'envoi d'email de vérification (optionnel)

## 📧 Envoi d'emails

### TODO : Implémentation nécessaire

Créez une méthode dans `UserWaiting.php` pour envoyer l'email de bienvenue :

```php
public function sendWelcomeEmail($tempPassword)
{
    Mail::to($this->email)->send(new WelcomePreregistrationMail([
        'name' => $this->name,
        'email' => $this->email,
        'password' => $tempPassword,
        'loginUrl' => route('login')
    ]));
}
```

Créez le Mailable :

```bash
php artisan make:mail WelcomePreregistrationMail
```

Template email :

```
Bonjour {{ $name }},

Bienvenue sur VintApp ! Votre pré-inscription a été enregistrée avec succès.

VOS IDENTIFIANTS DE CONNEXION :
Email : {{ $email }}
Mot de passe temporaire : {{ $password }}

Une fois l'application en ligne, vous pourrez vous connecter sur :
{{ $loginUrl }}

⚠️ Nous vous recommandons de changer votre mot de passe lors de votre première connexion.

À bientôt sur VintApp !
L'équipe VintApp
```

## 🔄 Migration vers l'application principale

### Quand l'application sera en ligne

1. **Les utilisateurs pré-inscrits pourront** :

    - Se connecter avec email + mot de passe temporaire
    - Être redirigés vers une page de changement de mot de passe obligatoire
    - Accéder à l'application normalement

2. **Script de migration** (à créer) :

```php
// app/Console/Commands/MigratePreregisteredUsers.php
public function handle()
{
    $waitingUsers = UserWaiting::whereNotNull('firebase_uid')
        ->whereNull('converted_at')
        ->get();

    foreach ($waitingUsers as $waiting) {
        // Vérifier si le compte Firebase existe toujours
        // Créer l'utilisateur dans la table users
        // Marquer comme converti
        $user = User::create([
            'name' => $waiting->name,
            'email' => $waiting->email,
            'phone' => $waiting->phone,
            'country' => $waiting->country,
            'firebase_uid' => $waiting->firebase_uid,
            'email_verified_at' => now(),
            'must_change_password' => true
        ]);

        $waiting->update([
            'converted_at' => now(),
            'converted_user_id' => $user->id
        ]);
    }
}
```

## 📊 Monitoring

### Vérifier les inscriptions

```php
// Total pré-inscriptions
UserWaiting::count();

// Avec Firebase UID
UserWaiting::whereNotNull('firebase_uid')->count();

// Convertis en utilisateurs
UserWaiting::whereNotNull('converted_at')->count();
```

### Console Firebase

1. **Authentication** → Users : Voir tous les comptes créés
2. **Firestore** → preregistrations : Voir les documents
3. **Analytics** : Suivre les inscriptions

## 🐛 Dépannage

### Erreur "Email already in use"

L'utilisateur existe déjà dans Firebase. Options :

-   Lui proposer de se connecter
-   Récupérer le mot de passe via "Forgot password"

### Erreur de CORS

Ajoutez votre domaine dans Firebase Console → Authentication → Settings → Authorized domains

### Firestore permission denied

Vérifiez que les règles Firestore autorisent la création pour l'utilisateur authentifié

### Email pas reçu

1. Vérifiez les logs Laravel : `storage/logs/laravel.log`
2. Configurez le SMTP dans `.env`
3. Testez avec `php artisan tinker` puis `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

## ✅ Checklist de déploiement

-   [ ] Variables Firebase configurées dans `.env`
-   [ ] Migration `firebase_uid` exécutée
-   [ ] Règles Firestore configurées
-   [ ] Email/Password activé dans Firebase Auth
-   [ ] Domaine ajouté dans "Authorized domains"
-   [ ] Email de bienvenue implémenté et testé
-   [ ] Script de migration créé
-   [ ] Page de changement de mot de passe obligatoire créée
-   [ ] Tests E2E effectués

## 🎯 Prochaines étapes

1. **Implémenter l'envoi d'email** avec le mot de passe temporaire
2. **Créer la page de premier login** avec changement de mot de passe obligatoire
3. **Développer le script de migration** pour convertir les pré-inscrits en utilisateurs
4. **Ajouter un dashboard admin** pour gérer les pré-inscriptions Firebase
5. **Configurer Firebase Functions** pour des actions automatiques (email, notifications, etc.)

## 📞 Support

Pour toute question sur l'intégration Firebase :

-   Documentation Firebase : https://firebase.google.com/docs
-   Documentation Laravel Firebase : https://github.com/kreait/laravel-firebase
-   VintApp Dev Team : dev@vintapp.com
