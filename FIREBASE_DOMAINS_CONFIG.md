# 🔧 Configuration Domaines Autorisés Firebase

## ❌ Erreur: "Domaine non autorisé pour Google Auth"

### 🎯 Solution Immédiate

1. **Ouvrir Firebase Console**: https://console.firebase.google.com/project/vintapp-e6fa7

2. **Navigation**:

    - Menu gauche → **Authentication**
    - Onglet **Settings** (en haut)
    - Descendre à **"Authorized domains"**

3. **Ajouter les domaines** (cliquer "Add domain" pour chacun):

    ```
    localhost
    127.0.0.1
    localhost:8000
    127.0.0.1:8000
    ```

4. **Sauvegarder** et **tester** immédiatement

### ✅ Vérification Post-Configuration

Après avoir ajouté les domaines, testez:

1. **Rechargez** votre page de connexion
2. **Essayez Google Auth** → Devrait maintenant fonctionner
3. **Si ça ne marche pas**, regardez la console (F12) pour de nouvelles erreurs

### 🔄 Méthode Alternative (Déjà Implémentée)

Si le popup continue à échouer, le code utilisera automatiquement `signInWithRedirect()` comme méthode de fallback.

### 📋 Checklist de Configuration

-   [ ] Domaines ajoutés dans Firebase Console
-   [ ] Google OAuth activé dans Sign-in methods
-   [ ] Page rechargée après modifications
-   [ ] Test de connexion Google effectué

### 🆘 Si Le Problème Persiste

1. **Vérifiez Google Cloud Console**: https://console.cloud.google.com/
2. **APIs & Services** → **Credentials**
3. Assurez-vous que les **Authorized JavaScript origins** incluent `http://localhost:8000`

---

**⏱️ Configuration estimée: 2-3 minutes**
