## 🔥 Guide pour créer un utilisateur test Firebase

### Étapes dans la Console Firebase :

1. **Ouvrez votre Console Firebase** : https://console.firebase.google.com/project/vintapp-e6fa7

2. **Allez dans Authentication** :

    - Dans le menu de gauche : **Authentication**
    - Onglet **Users**

3. **Ajoutez un utilisateur** :

    - Cliquez sur **"Add user"**
    - Email : `test@example.com`
    - Mot de passe : `password123`
    - Cliquez **"Add user"**

4. **Récupérez votre VAPID Key** :
    - **Paramètres du projet** (icône engrenage)
    - Onglet **"Cloud Messaging"**
    - Section **"Configuration Web"**
    - Copiez la **"Clé de serveur Web (VAPID)"**
    - Remplacez `YOUR_VAPID_KEY_HERE` dans votre .env

### Ou utilisez notre script automatique :

```bash
php artisan firebase:create-test-user
```

### Test rapide après configuration :

1. Rechargez votre page de connexion
2. Testez avec : `test@example.com` / `password123`
3. Les messages d'erreur devraient être plus précis maintenant
