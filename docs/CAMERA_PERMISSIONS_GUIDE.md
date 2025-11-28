# 📷 Guide d'autorisation caméra localhost

## Chrome / Edge

### Méthode 1 : Via l'icône dans la barre d'adresse
1. Ouvrez `http://localhost:8000/orders`
2. Cliquez sur l'icône **🔒** ou **ⓘ** à gauche de l'URL
3. Trouvez **"Caméra"** dans la liste
4. Changez de "Bloquer" ou "Demander" à **"Autoriser"**
5. Rechargez la page (F5)
6. Cliquez sur "Scanner QR Code"

### Méthode 2 : Via les paramètres Chrome
1. Ouvrez un nouvel onglet
2. Allez sur : `chrome://settings/content/camera`
3. Dans la section **"Autorisés à utiliser votre caméra"**
4. Cliquez sur **"Ajouter"**
5. Entrez : `http://localhost:8000`
6. Cliquez sur **"Ajouter"**
7. Retournez sur votre page et rechargez (F5)

### Méthode 3 : Supprimer le blocage
1. Allez sur : `chrome://settings/content/camera`
2. Dans **"Non autorisés à utiliser votre caméra"**
3. Si vous voyez `localhost` → Cliquez sur les 3 points → **"Supprimer"**
4. Rechargez votre page

## Firefox

### Méthode 1 : Via l'icône dans la barre d'adresse
1. Ouvrez `http://localhost:8000/orders`
2. Cliquez sur l'icône **🔒** à gauche de l'URL
3. Cliquez sur **">"** à côté de "Connexion non sécurisée"
4. Cliquez sur **"Plus d'informations"**
5. Allez dans l'onglet **"Permissions"**
6. Trouvez **"Utiliser la caméra"**
7. Décochez **"Utiliser les permissions par défaut"**
8. Cochez **"Autoriser"**
9. Fermez la fenêtre et rechargez (F5)

### Méthode 2 : Via les paramètres Firefox
1. Allez sur : `about:preferences#privacy`
2. Descendez jusqu'à **"Permissions"**
3. À côté de **"Caméra"**, cliquez sur **"Paramètres..."**
4. Trouvez `http://localhost:8000` dans la liste
5. Changez le statut en **"Autoriser"**
6. Cliquez sur **"Enregistrer les modifications"**

## Test rapide

Après avoir autorisé la caméra :

1. Ouvrez : `http://localhost:8000/orders`
2. Ouvrez la console développeur (F12)
3. Tapez : 
   ```javascript
   navigator.mediaDevices.getUserMedia({video: true})
     .then(() => console.log('✅ Caméra accessible'))
     .catch(err => console.error('❌ Erreur:', err.name, err.message))
   ```
4. Appuyez sur Entrée
5. Si vous voyez "✅ Caméra accessible" → C'est bon !
6. Si vous voyez "❌ Erreur" → Réessayez les étapes ci-dessus

## Solution ultime : Navigation privée

Si rien ne fonctionne, essayez en mode navigation privée :

1. **Chrome** : `Ctrl+Shift+N`
2. **Firefox** : `Ctrl+Shift+P`
3. Allez sur : `http://localhost:8000/orders`
4. Cliquez sur "Scanner QR Code"
5. **Autorisez** quand la popup apparaît

Le mode privé n'a aucune permission mémorisée, donc le navigateur demandera à nouveau.

---

## ⚠️ Important

- N'utilisez **JAMAIS** ngrok pour le scanner caméra (il bloque les permissions)
- Utilisez **TOUJOURS** `http://localhost:8000` pour le scanner
- Pour partager avec d'autres : ils doivent scanner le QR avec leur téléphone
