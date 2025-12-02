# Comment obtenir une nouvelle clé API Firebase

## Méthode 1 : Via la Console Firebase (Recommandé)

1. Allez sur https://console.firebase.google.com
2. Sélectionnez le projet **vintapp-e6fa7**
3. Cliquez sur l'icône ⚙️ puis **Paramètres du projet**
4. Dans l'onglet **Général**, cherchez **Vos applications** (SDK setup and configuration)
5. Si vous avez déjà une app web, cliquez sur l'icône de configuration `</>`
6. Copiez la nouvelle `apiKey` depuis le code affiché

## Méthode 2 : Via Google Cloud Console

1. Allez sur https://console.cloud.google.com
2. Sélectionnez le projet **vintapp-e6fa7**
3. Dans le menu, allez dans **APIs & Services** > **Credentials**
4. Cherchez la clé API pour **Browser key (auto created by Firebase)**
5. Si elle est désactivée, créez-en une nouvelle :
    - Cliquez sur **+ CREATE CREDENTIALS** > **API key**
    - Notez la nouvelle clé
    - Configurez les restrictions (optionnel mais recommandé) :
        - **Application restrictions** : HTTP referrers
        - Ajoutez vos domaines autorisés :
            - `http://localhost:8000/*`
            - `http://localhost/*`
            - `https://vitapp.mykenyastudentprocess.com/*`
        - **API restrictions** : Restrict key
            - Firebase Authentication API
            - Identity Toolkit API

## Étapes suivantes

Une fois que vous avez la nouvelle clé API :

1. **Mettez à jour le fichier `.env`** :

    ```env
    FIREBASE_API_KEY="VOTRE_NOUVELLE_CLE_API_ICI"
    ```

2. **Videz les caches** :

    ```powershell
    php artisan config:clear
    php artisan cache:clear
    ```

3. **Rechargez la page** dans votre navigateur

## Note importante

⚠️ **Ne partagez jamais votre clé API publiquement** (par exemple dans un commit Git public)

Bien que cette clé soit destinée au frontend (donc visible), il est recommandé de :

-   Configurer des restrictions de domaine
-   Activer App Check dans Firebase pour plus de sécurité
