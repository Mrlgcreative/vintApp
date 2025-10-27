## 🔍 Diagnostic Google OAuth - Solutions Communes

### ❌ **Erreurs Courantes et Solutions**

#### 1. **"Domaine non autorisé" (unauthorized-domain)**

**Cause**: Votre domaine localhost n'est pas autorisé dans Firebase
**Solution**:

1. Allez à **Firebase Console** > **Authentication** > **Settings** > **Authorized domains**
2. Ajoutez: `localhost`, `127.0.0.1`, `localhost:8000`

#### 2. **"Connexion Google désactivée" (operation-not-allowed)**

**Cause**: Google Auth n'est pas activé dans Firebase
**Solution**:

1. **Firebase Console** > **Authentication** > **Sign-in method**
2. Activez **Google** comme provider
3. Configurez l'email de support

#### 3. **"Pop-up bloqué" (popup-blocked)**

**Cause**: Le navigateur bloque les pop-ups
**Solution**:

-   Autorisez les pop-ups pour `localhost:8000`
-   Ou utilisez `signInWithRedirect()` au lieu de `signInWithPopup()`

#### 4. **"Configuration OAuth manquante"**

**Cause**: Client ID OAuth non configuré
**Solution**:

1. **Google Cloud Console**: https://console.cloud.google.com/
2. **APIs & Services** > **Credentials**
3. Vérifiez que le **Client ID** correspond à celui dans Firebase

### 🧪 **Tests de Diagnostic**

#### Test 1: Vérifiez les domaines autorisés

```bash
# Dans la console du navigateur (F12):
console.log('Domaine actuel:', window.location.origin);
```

#### Test 2: Vérifiez la configuration Firebase

```javascript
// Dans la console du navigateur:
console.log("Auth domain:", auth.config.authDomain);
console.log("API Key:", auth.config.apiKey);
```

#### Test 3: Test manuel du provider Google

```javascript
// Dans la console du navigateur:
console.log("Provider Google configuré:", googleProvider);
```

### 🎯 **Action Immédiate**

1. **Ouvrez la console du navigateur** (F12)
2. **Essayez la connexion Google**
3. **Regardez les logs détaillés** que nous avons ajoutés
4. **Copiez l'erreur exacte** pour un diagnostic précis
