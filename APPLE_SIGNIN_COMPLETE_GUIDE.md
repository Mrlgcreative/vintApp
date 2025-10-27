# 🍎 Configuration Apple Sign-In pour Firebase

## 📋 **Prérequis Obligatoires**

-   **Apple Developer Account** (99$/an) - INDISPENSABLE
-   Accès à Apple Developer Console
-   Projet Firebase configuré

## 🔧 **Étapes de Configuration Complète**

### **1. Apple Developer Console**

🔗 **URL** : https://developer.apple.com/account/

#### **A. Créer un App ID**

1. **Certificates, Identifiers & Profiles** → **Identifiers** → **"+"**
2. **Sélectionner** : App IDs → **Continue**
3. **Configurer** :
    - **Description** : `VintApp iOS`
    - **Bundle ID** : `com.mrlgcreative.vintapp` (remplacez par votre nom)
    - **Capabilities** : ✅ **Sign In with Apple**
4. **Continue** → **Register**

#### **B. Créer un Service ID (Web)**

1. **Identifiers** → **"+"** → **Services IDs**
2. **Configurer** :
    - **Description** : `VintApp Web Auth`
    - **Identifier** : `com.mrlgcreative.vintapp.web`
    - **✅ Sign In with Apple** → **Configure**
3. **Configuration détaillée** :
    - **Primary App ID** : Sélectionnez l'App ID créé en A
    - **Domains** : `vintapp-e6fa7.firebaseapp.com`
    - **Return URLs** : `https://vintapp-e6fa7.firebaseapp.com/__/auth/handler`
4. **Save** → **Continue** → **Register**

#### **C. Créer une Private Key**

1. **Keys** → **"+"**
2. **Configurer** :
    - **Key Name** : `VintApp Apple Auth Key`
    - **✅ Sign In with Apple** → **Configure**
    - **Primary App ID** : Votre App ID
3. **Continue** → **Register**
4. **⚠️ CRITIQUE** : **Téléchargez le fichier .p8** immédiatement
5. **📝 Notez** : Le **Key ID** (ex: `ABC123DEF4`)

#### **D. Récupérer le Team ID**

1. **Membership** (menu de gauche)
2. **📝 Notez** : **Team ID** (ex: `XYZ789ABC1`)

### **2. Configuration Firebase Console**

🔗 **URL** : https://console.firebase.google.com/project/vintapp-e6fa7

1. **Authentication** → **Sign-in method**
2. **Apple** → **Enable**
3. **Remplir les champs** :
    ```
    Service ID: com.mrlgcreative.vintapp.web
    Apple Team ID: XYZ789ABC1 (de l'étape D)
    Key ID: ABC123DEF4 (de l'étape C)
    Private Key: -----BEGIN PRIVATE KEY-----
    (Contenu complet du fichier .p8)
    -----END PRIVATE KEY-----
    ```
4. **Save**

### **3. Configuration des Domaines**

Dans **Firebase Console** → **Authentication** → **Settings** → **Authorized domains** :

```
localhost
127.0.0.1
localhost:8000
127.0.0.1:8000
vintapp-e6fa7.firebaseapp.com (déjà présent)
```

## ✅ **Vérification de Configuration**

### **Test Rapide**

1. **Rechargez** votre page de connexion
2. **Bouton Apple** doit être visible
3. **Test de connexion** → Devrait ouvrir l'interface Apple

### **Debug en cas de problème**

Ouvrez la console (F12) et regardez :

```javascript
console.log("Apple Provider:", appleProvider);
console.log("Firebase Config:", auth.config);
```

## 🚫 **Limitations Sans Apple Developer Account**

**❌ Impossible d'utiliser Apple Sign-In sans :**

-   Apple Developer Account payant (99$/an)
-   App ID et Service ID configurés
-   Private Key générée

**🔄 Alternatives Gratuites :**

-   Google OAuth (gratuit)
-   Facebook Login (gratuit)
-   Email/Mot de passe (gratuit)

## 📝 **Résumé des Identifiants Requis**

| Élément         | Exemple                        | Où le trouver                 |
| --------------- | ------------------------------ | ----------------------------- |
| **Service ID**  | `com.mrlgcreative.vintapp.web` | Apple Developer → Service IDs |
| **Team ID**     | `XYZ789ABC1`                   | Apple Developer → Membership  |
| **Key ID**      | `ABC123DEF4`                   | Apple Developer → Keys        |
| **Private Key** | Contenu fichier .p8            | Téléchargé lors création Key  |

## ⏱️ **Temps de Configuration**

-   **Avec Apple Dev Account** : 15-20 minutes
-   **Sans Apple Dev Account** : Impossible

---

**🎯 Une fois configuré, Apple Sign-In fonctionnera sur votre application !**
