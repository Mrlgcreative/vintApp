# 🔐 Activation Google OAuth dans Firebase

## ❌ Erreur: "Connexion Google désactivée. Vérifiez la configuration Firebase"

### 🎯 Solution Immédiate

1. **Ouvrir Firebase Console**: https://console.firebase.google.com/project/vintapp-e6fa7

2. **Navigation vers Sign-in Method**:

    - Menu gauche → **Authentication**
    - Onglet **Sign-in method** (en haut)

3. **Activer Google Provider**:
    - Dans la liste des providers, trouvez **"Google"**
    - Cliquez sur la ligne **Google**
    - **Activez** le toggle "Enable" (passer à ON)
    - **Email de support** : Entrez votre email (obligatoire)
    - Cliquez **"Save"**

### ✅ Configuration Complète Recommandée

Pendant que vous y êtes, configurez aussi :

4. **Domaines Autorisés** (si pas encore fait) :

    - Onglet **Settings**
    - Section **"Authorized domains"**
    - Ajouter : `localhost`, `127.0.0.1`, `localhost:8000`

5. **Vérification Email/Mot de passe** :
    - Retour à **Sign-in method**
    - Vérifiez que **"Email/Password"** est aussi activé

### 🧪 Test Après Configuration

1. **Sauvegardez** dans Firebase Console
2. **Rechargez** votre page de connexion
3. **Testez Google Auth** → Devrait maintenant fonctionner !

### 📋 Checklist Complète

-   [ ] Google Provider activé dans Sign-in method
-   [ ] Email de support renseigné
-   [ ] Domaines autorisés configurés
-   [ ] Email/Password aussi activé
-   [ ] Configuration sauvegardée
-   [ ] Page de test rechargée

### 🔍 Debug Supplémentaire

Si le problème persiste, regardez la console du navigateur (F12) pour :

-   Configuration Firebase Auth
-   État des providers
-   Messages d'erreur détaillés

---

**⏱️ Configuration estimée: 2-3 minutes**
**🎯 Résultat attendu: Google Auth fonctionnel**
