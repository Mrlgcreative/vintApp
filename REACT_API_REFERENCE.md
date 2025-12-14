# VintApp - Référence API pour Application React

> Documentation des endpoints API pour connecter votre application React Native/React

## Configuration de Base

```javascript
// config/api.js
const API_CONFIG = {
    BASE_URL: "https://vitapp.mykenyastudentprocess.com/api",
    VERSION: "v1",
    TIMEOUT: 30000, // 30 secondes
};

export const API_URL = `${API_CONFIG.BASE_URL}/${API_CONFIG.VERSION}`;
export default API_CONFIG;
```

## Headers par Défaut

```javascript
const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    Authorization: `Bearer ${token}`, // Token Sanctum
};
```

---

## 🔐 Authentification

### Authentification Standard

| Méthode | Endpoint                      | Description                    | Auth |
| ------- | ----------------------------- | ------------------------------ | ---- |
| `POST`  | `/api/register`               | Inscription utilisateur        | ❌   |
| `POST`  | `/api/login`                  | Connexion utilisateur          | ❌   |
| `POST`  | `/api/logout`                 | Déconnexion utilisateur        | ✅   |
| `GET`   | `/api/user`                   | Obtenir l'utilisateur connecté | ✅   |
| `POST`  | `/api/validate-referral-code` | Valider un code de parrainage  | ❌   |
| `POST`  | `/api/fcm-token`              | Enregistrer token FCM          | ✅   |

#### POST /api/register - Inscription

**Request:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201 Created):**

```json
{
    "success": true,
    "message": "Inscription réussie",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar": null
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
}
```

#### POST /api/login - Connexion

**Request:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200 OK):**

```json
{
    "success": true,
    "message": "Connexion réussie",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar": "https://example.com/avatar.jpg",
        "email_verified_at": "2025-01-01T00:00:00.000000Z",
        "role": "user"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
}
```

**Response (401 Unauthorized):**

```json
{
    "success": false,
    "message": "Les informations de connexion fournies sont incorrectes."
}
```

#### POST /api/logout - Déconnexion

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**

```json
{
    "success": true,
    "message": "Déconnexion réussie"
}
```

#### GET /api/user - Utilisateur connecté

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**

```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar": "https://example.com/avatar.jpg",
        "email_verified_at": "2025-01-01T00:00:00.000000Z",
        "role": "user"
    }
}
```

### Exemple d'utilisation React Native

```javascript
// services/authService.js
const API_BASE = "https://vitapp.mykenyastudentprocess.com/api";

export const authService = {
    // Inscription
    register: async (name, email, password) => {
        const response = await fetch(`${API_BASE}/register`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                name,
                email,
                password,
                password_confirmation: password,
            }),
        });
        return response.json();
    },

    // Connexion
    login: async (email, password) => {
        const response = await fetch(`${API_BASE}/login`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ email, password }),
        });
        return response.json();
    },

    // Déconnexion
    logout: async (token) => {
        const response = await fetch(`${API_BASE}/logout`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
        });
        return response.json();
    },

    // Récupérer l'utilisateur connecté
    getUser: async (token) => {
        const response = await fetch(`${API_BASE}/user`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
        });
        return response.json();
    },
};
```

### Authentification OAuth (Social Login)

| Méthode | Endpoint                | Description                  | Auth |
| ------- | ----------------------- | ---------------------------- | ---- |
| `GET`   | `/auth/google`          | Rediriger vers Google OAuth  | ❌   |
| `GET`   | `/auth/google/callback` | Callback après auth Google   | ❌   |
| `GET`   | `/auth/apple`           | Rediriger vers Apple Sign In | ❌   |
| `POST`  | `/auth/apple/callback`  | Callback après auth Apple    | ❌   |

### Mot de passe oublié

| Méthode | Endpoint                  | Description                       | Auth |
| ------- | ------------------------- | --------------------------------- | ---- |
| `POST`  | `/forgot-password`        | Envoyer email de réinitialisation | ❌   |
| `GET`   | `/reset-password/{token}` | Page de réinitialisation          | ❌   |
| `POST`  | `/reset-password`         | Réinitialiser le mot de passe     | ❌   |

```javascript
// Exemple: Connexion avec Google (React Native avec WebView ou Linking)
import * as Linking from "expo-linking";
import * as WebBrowser from "expo-web-browser";

const API_BASE = "https://vitapp.mykenyastudentprocess.com";

// Option 1: Ouvrir dans le navigateur système
const loginWithGoogle = async () => {
    const result = await WebBrowser.openAuthSessionAsync(
        `${API_BASE}/auth/google`,
        Linking.createURL("/auth-callback")
    );

    if (result.type === "success") {
        // Extraire le token de l'URL de callback
        const url = result.url;
        // Traiter le token retourné
    }
};

// Option 2: Utiliser @react-native-google-signin/google-signin
import { GoogleSignin } from "@react-native-google-signin/google-signin";

GoogleSignin.configure({
    webClientId: "VOTRE_WEB_CLIENT_ID.apps.googleusercontent.com",
});

const signInWithGoogle = async () => {
    try {
        await GoogleSignin.hasPlayServices();
        const userInfo = await GoogleSignin.signIn();

        // Envoyer le token au backend pour validation
        const response = await fetch(`${API_BASE}/api/auth/google/token`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id_token: userInfo.idToken,
            }),
        });

        return response.json();
    } catch (error) {
        console.error("Google Sign-In Error:", error);
    }
};
```

```javascript
// Exemple: Connexion avec Apple (iOS uniquement)
import * as AppleAuthentication from "expo-apple-authentication";

const signInWithApple = async () => {
    try {
        const credential = await AppleAuthentication.signInAsync({
            requestedScopes: [
                AppleAuthentication.AppleAuthenticationScope.FULL_NAME,
                AppleAuthentication.AppleAuthenticationScope.EMAIL,
            ],
        });

        // Envoyer les credentials au backend
        const response = await fetch(`${API_BASE}/auth/apple/callback`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                identityToken: credential.identityToken,
                authorizationCode: credential.authorizationCode,
                fullName: credential.fullName,
                email: credential.email,
            }),
        });

        return response.json();
    } catch (error) {
        console.error("Apple Sign-In Error:", error);
    }
};
```

---

## � Firebase Cloud Messaging (FCM) - Notifications Push

### Configuration Firebase

```javascript
// config/firebase.js
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
    apiKey: "VOTRE_FIREBASE_API_KEY",
    authDomain: "vintapp-xxxxx.firebaseapp.com",
    projectId: "vintapp-xxxxx",
    storageBucket: "vintapp-xxxxx.appspot.com",
    messagingSenderId: "123456789012",
    appId: "1:123456789012:web:abcdef123456",
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

export { app, messaging };
```

### Endpoints FCM

| Méthode | Endpoint                         | Description                    | Auth |
| ------- | -------------------------------- | ------------------------------ | ---- |
| `POST`  | `/api/fcm-token`                 | Enregistrer le token FCM       | ✅   |
| `POST`  | `/api/test-fcm-notification`     | Tester l'envoi de notification | ✅   |
| `POST`  | `/api/notifications/subscribe`   | S'abonner aux notifications    | ✅   |
| `POST`  | `/api/notifications/unsubscribe` | Se désabonner                  | ✅   |

### Enregistrement du Token FCM

```javascript
// services/firebase-messaging.js
import { getToken, onMessage } from "firebase/messaging";
import { messaging } from "../config/firebase";

const VAPID_KEY = "VOTRE_VAPID_KEY"; // Depuis Firebase Console

// Demander permission et obtenir le token
export const requestNotificationPermission = async () => {
    try {
        const permission = await Notification.requestPermission();

        if (permission === "granted") {
            const token = await getToken(messaging, { vapidKey: VAPID_KEY });
            console.log("Token FCM:", token);
            return token;
        } else {
            console.log("Permission notifications refusée");
            return null;
        }
    } catch (error) {
        console.error("Erreur permission notifications:", error);
        return null;
    }
};

// Enregistrer le token sur le serveur
export const registerFCMToken = async (authToken) => {
    const fcmToken = await requestNotificationPermission();

    if (!fcmToken) return null;

    try {
        const response = await fetch(
            "https://vitapp.mykenyastudentprocess.com/api/fcm-token",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${authToken}`,
                },
                body: JSON.stringify({
                    token: fcmToken,
                    device_type: "web", // ou 'android', 'ios'
                }),
            }
        );

        return response.json();
    } catch (error) {
        console.error("Erreur enregistrement token FCM:", error);
        return null;
    }
};

// Écouter les messages en foreground
export const onForegroundMessage = (callback) => {
    return onMessage(messaging, (payload) => {
        console.log("Message reçu en foreground:", payload);
        callback(payload);
    });
};
```

### React Native avec Firebase

```javascript
// Pour React Native, utilisez @react-native-firebase/messaging
import messaging from "@react-native-firebase/messaging";

// Demander permission (iOS)
async function requestUserPermission() {
    const authStatus = await messaging().requestPermission();
    const enabled =
        authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
        authStatus === messaging.AuthorizationStatus.PROVISIONAL;

    if (enabled) {
        console.log("Authorization status:", authStatus);
        return true;
    }
    return false;
}

// Obtenir le token FCM
async function getFCMToken() {
    try {
        const token = await messaging().getToken();
        console.log("Token FCM:", token);
        return token;
    } catch (error) {
        console.error("Erreur obtention token FCM:", error);
        return null;
    }
}

// Enregistrer le token sur le serveur
async function registerToken(authToken) {
    const fcmToken = await getFCMToken();

    if (!fcmToken) return;

    await fetch("https://vitapp.mykenyastudentprocess.com/api/fcm-token", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${authToken}`,
        },
        body: JSON.stringify({
            token: fcmToken,
            device_type: Platform.OS, // 'android' ou 'ios'
        }),
    });
}

// Écouter les notifications en background
messaging().setBackgroundMessageHandler(async (remoteMessage) => {
    console.log("Message en background:", remoteMessage);
});

// Écouter les notifications en foreground
messaging().onMessage(async (remoteMessage) => {
    console.log("Message en foreground:", remoteMessage);
    // Afficher une notification locale
});

// Écouter quand l'app est ouverte via une notification
messaging().onNotificationOpenedApp((remoteMessage) => {
    console.log("App ouverte via notification:", remoteMessage);
    // Naviguer vers l'écran approprié
});
```

### Types de Notifications Reçues

```javascript
// Structure d'une notification FCM
{
    notification: {
        title: "Article approuvé !",
        body: "Votre article 'iPhone 13' est maintenant en ligne",
        image: "https://vitapp.../storage/items/image.jpg"
    },
    data: {
        type: "item_approved", // ou "item_rejected", "new_message", "order_update", etc.
        item_id: "123",
        url: "/items/123",
        timestamp: "2025-12-13T10:30:00Z"
    }
}

// Types de notifications supportés
const NOTIFICATION_TYPES = {
    ITEM_APPROVED: 'item_approved',      // Article approuvé
    ITEM_REJECTED: 'item_rejected',      // Article rejeté
    NEW_MESSAGE: 'new_message',          // Nouveau message
    ORDER_CREATED: 'order_created',      // Nouvelle commande
    ORDER_SHIPPED: 'order_shipped',      // Commande expédiée
    ORDER_DELIVERED: 'order_delivered',  // Commande livrée
    PAYMENT_RECEIVED: 'payment_received',// Paiement reçu
    WALLET_CREDITED: 'wallet_credited',  // Crédit portefeuille
    ADMIN_BROADCAST: 'admin_broadcast',  // Message admin
};
```

---

## 📱 API V1 - Routes Principales

### 🏠 Page d'Accueil (Home)

| Méthode | Endpoint       | Description                            | Auth |
| ------- | -------------- | -------------------------------------- | ---- |
| `GET`   | `/api/v1/home` | Données complètes de la page d'accueil | ❌   |

#### GET /api/v1/home - Page d'accueil

Retourne toutes les données nécessaires pour afficher la page d'accueil de l'application.

**Response (200 OK):**

```json
{
    "success": true,
    "data": {
        "categories": [
            {
                "id": 1,
                "name": "Vêtements",
                "slug": "vetements",
                "icon": "👕",
                "is_active": true,
                "sort_order": 1,
                "items_count": 150
            }
        ],
        "spotlight_items": [
            {
                "id": 1,
                "title": "iPhone 14 Pro",
                "price": 450000,
                "images": ["https://..."],
                "category": { "id": 1, "name": "Électronique" },
                "brand": { "id": 1, "name": "Apple" },
                "user": { "id": 1, "name": "John Doe" },
                "active_boosts": [{ "boost_type": { "name": "spotlight" } }]
            }
        ],
        "boosted_items": [
            { "id": 2, "title": "Samsung Galaxy S23", "..." }
        ],
        "latest_items": [
            { "id": 3, "title": "Nike Air Max", "..." }
        ],
        "stats": {
            "users": 1500,
            "items": 3200,
            "categories": 12,
            "boosted_items": 45
        },
        "hero_slides": [
            {
                "id": 1,
                "title": "Bienvenue sur VintApp",
                "subtitle": "Achetez et vendez facilement",
                "image": "https://...",
                "button_text": "Découvrir",
                "button_url": "/items",
                "is_active": true,
                "sort_order": 1
            }
        ],
        "hero_settings": {
            "title": "Découvrez des articles uniques",
            "subtitle": "La marketplace moderne pour acheter et vendre en toute sécurité.",
            "image": "https://images.unsplash.com/...",
            "button_primary": "Vendre",
            "button_secondary": "Parcourir"
        }
    }
}
```

```javascript
// Exemple: Récupérer les données de la page d'accueil
const getHomeData = async () => {
    const response = await fetch(`${API_URL}/home`);
    return response.json();
};

// Exemple complet avec gestion d'état React
import { useState, useEffect } from "react";

const useHomeData = () => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchHomeData = async () => {
            try {
                const response = await fetch(`${API_URL}/home`);
                const result = await response.json();
                if (result.success) {
                    setData(result.data);
                }
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };
        fetchHomeData();
    }, []);

    return { data, loading, error };
};
```

---

### 🛍️ Items (Articles)

| Méthode  | Endpoint             | Description          | Auth |
| -------- | -------------------- | -------------------- | ---- |
| `GET`    | `/api/v1/items`      | Liste des articles   | ❌   |
| `GET`    | `/api/v1/items/{id}` | Détails d'un article | ❌   |
| `POST`   | `/api/v1/items`      | Créer un article     | ✅   |
| `PUT`    | `/api/v1/items/{id}` | Modifier un article  | ✅   |
| `DELETE` | `/api/v1/items/{id}` | Supprimer un article | ✅   |

```javascript
// Exemple: Récupérer tous les articles
const getItems = async () => {
    const response = await fetch(`${API_URL}/items`);
    return response.json();
};

// Exemple: Créer un article
const createItem = async (itemData, token) => {
    const response = await fetch(`${API_URL}/items`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(itemData),
    });
    return response.json();
};
```

---

### 📁 Catégories

| Méthode  | Endpoint                        | Description              | Auth |
| -------- | ------------------------------- | ------------------------ | ---- |
| `GET`    | `/api/v1/categories`            | Liste des catégories     | ❌   |
| `GET`    | `/api/v1/categories/{id}`       | Détails d'une catégorie  | ✅   |
| `GET`    | `/api/v1/categories/{id}/items` | Articles d'une catégorie | ✅   |
| `POST`   | `/api/v1/categories`            | Créer une catégorie      | ✅   |
| `PUT`    | `/api/v1/categories/{id}`       | Modifier une catégorie   | ✅   |
| `DELETE` | `/api/v1/categories/{id}`       | Supprimer une catégorie  | ✅   |

---

### 🏷️ Marques (Brands)

| Méthode  | Endpoint                    | Description           | Auth |
| -------- | --------------------------- | --------------------- | ---- |
| `GET`    | `/api/v1/brands`            | Liste des marques     | ❌   |
| `GET`    | `/api/v1/brands/{id}`       | Détails d'une marque  | ✅   |
| `GET`    | `/api/v1/brands/{id}/items` | Articles d'une marque | ✅   |
| `POST`   | `/api/v1/brands`            | Créer une marque      | ✅   |
| `PUT`    | `/api/v1/brands/{id}`       | Modifier une marque   | ✅   |
| `DELETE` | `/api/v1/brands/{id}`       | Supprimer une marque  | ✅   |

---

### 👤 Utilisateur (User)

| Méthode  | Endpoint                | Description              | Auth |
| -------- | ----------------------- | ------------------------ | ---- |
| `GET`    | `/api/v1/user/profile`  | Profil utilisateur       | ✅   |
| `PUT`    | `/api/v1/user/profile`  | Modifier le profil       | ✅   |
| `PUT`    | `/api/v1/user/password` | Modifier le mot de passe | ✅   |
| `POST`   | `/api/v1/user/avatar`   | Uploader un avatar       | ✅   |
| `GET`    | `/api/v1/user/stats`    | Statistiques utilisateur | ✅   |
| `GET`    | `/api/v1/user/items`    | Mes articles             | ✅   |
| `GET`    | `/api/v1/user/orders`   | Mes commandes            | ✅   |
| `GET`    | `/api/v1/user/sales`    | Mes ventes               | ✅   |
| `GET`    | `/api/v1/user/reviews`  | Mes avis                 | ✅   |
| `DELETE` | `/api/v1/user/account`  | Supprimer mon compte     | ✅   |

```javascript
// Exemple: Obtenir le profil utilisateur
const getUserProfile = async (token) => {
    const response = await fetch(`${API_URL}/user/profile`, {
        headers: { Authorization: `Bearer ${token}` },
    });
    return response.json();
};

// Exemple: Mettre à jour le profil
const updateProfile = async (profileData, token) => {
    const response = await fetch(`${API_URL}/user/profile`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(profileData),
    });
    return response.json();
};
```

---

### 📦 Commandes (Orders)

| Méthode | Endpoint                               | Description            | Auth |
| ------- | -------------------------------------- | ---------------------- | ---- |
| `GET`   | `/api/v1/orders`                       | Liste des commandes    | ✅   |
| `POST`  | `/api/v1/orders`                       | Créer une commande     | ✅   |
| `GET`   | `/api/v1/orders/sales`                 | Mes ventes             | ✅   |
| `GET`   | `/api/v1/orders/{id}`                  | Détails d'une commande | ✅   |
| `POST`  | `/api/v1/orders/{id}/confirm-payment`  | Confirmer le paiement  | ✅   |
| `POST`  | `/api/v1/orders/{id}/mark-shipped`     | Marquer comme expédié  | ✅   |
| `POST`  | `/api/v1/orders/{id}/mark-delivered`   | Marquer comme livré    | ✅   |
| `POST`  | `/api/v1/orders/{id}/confirm-delivery` | Confirmer la livraison | ✅   |

```javascript
// Exemple: Créer une commande
const createOrder = async (orderData, token) => {
    const response = await fetch(`${API_URL}/orders`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(orderData),
    });
    return response.json();
};

// Exemple: Confirmer la livraison
const confirmDelivery = async (orderId, token) => {
    const response = await fetch(
        `${API_URL}/orders/${orderId}/confirm-delivery`,
        {
            method: "POST",
            headers: { Authorization: `Bearer ${token}` },
        }
    );
    return response.json();
};
```

---

### 💬 Messages

| Méthode | Endpoint                                 | Description                  | Auth |
| ------- | ---------------------------------------- | ---------------------------- | ---- |
| `GET`   | `/api/v1/messages`                       | Liste des conversations      | ✅   |
| `POST`  | `/api/v1/messages`                       | Envoyer un message           | ✅   |
| `GET`   | `/api/v1/messages/{userId}`              | Messages avec un utilisateur | ✅   |
| `PUT`   | `/api/v1/messages/{messageId}/mark-read` | Marquer comme lu             | ✅   |
| `GET`   | `/api/v1/messages/unread/count`          | Nombre de non-lus            | ✅   |
| `POST`  | `/api/v1/messages/discount/apply`        | Appliquer une réduction      | ✅   |
| `GET`   | `/api/v1/messages/discounts/{itemId}`    | Réductions disponibles       | ✅   |

```javascript
// Exemple: Envoyer un message
const sendMessage = async (messageData, token) => {
    const response = await fetch(`${API_URL}/messages`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
            receiver_id: messageData.receiverId,
            content: messageData.content,
            item_id: messageData.itemId, // optionnel
        }),
    });
    return response.json();
};
```

---

### ⭐ Avis (Reviews)

| Méthode  | Endpoint                            | Description       | Auth |
| -------- | ----------------------------------- | ----------------- | ---- |
| `GET`    | `/api/v1/reviews`                   | Liste des avis    | ✅   |
| `GET`    | `/api/v1/reviews/item/{itemId}`     | Avis d'un article | ✅   |
| `GET`    | `/api/v1/reviews/seller/{sellerId}` | Avis d'un vendeur | ✅   |
| `POST`   | `/api/v1/reviews`                   | Créer un avis     | ✅   |
| `PUT`    | `/api/v1/reviews/{reviewId}`        | Modifier un avis  | ✅   |
| `DELETE` | `/api/v1/reviews/{reviewId}`        | Supprimer un avis | ✅   |

---

### 💰 Portefeuille (Wallet)

| Méthode | Endpoint                      | Description             | Auth |
| ------- | ----------------------------- | ----------------------- | ---- |
| `GET`   | `/api/v1/wallet`              | Voir mon portefeuille   | ✅   |
| `GET`   | `/api/v1/wallet/transactions` | Historique transactions | ✅   |
| `POST`  | `/api/v1/wallet/add-funds`    | Ajouter des fonds       | ✅   |
| `POST`  | `/api/v1/wallet/withdraw`     | Retirer des fonds       | ✅   |
| `POST`  | `/api/v1/wallet/convert`      | Convertir (points/cash) | ✅   |

```javascript
// Exemple: Obtenir le solde du portefeuille
const getWallet = async (token) => {
    const response = await fetch(`${API_URL}/wallet`, {
        headers: { Authorization: `Bearer ${token}` },
    });
    return response.json();
};

// Exemple: Retirer des fonds
const withdrawFunds = async (amount, paymentMethod, token) => {
    const response = await fetch(`${API_URL}/wallet/withdraw`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
            amount,
            payment_method: paymentMethod,
        }),
    });
    return response.json();
};
```

---

### 🔔 Notifications

| Méthode  | Endpoint                               | Description                | Auth |
| -------- | -------------------------------------- | -------------------------- | ---- |
| `GET`    | `/api/v1/notifications`                | Liste des notifications    | ✅   |
| `GET`    | `/api/v1/notifications/unread`         | Notifications non lues     | ✅   |
| `GET`    | `/api/v1/notifications/unread/count`   | Nombre non lues            | ✅   |
| `POST`   | `/api/v1/notifications/mark-all-read`  | Tout marquer comme lu      | ✅   |
| `POST`   | `/api/v1/notifications/{id}/mark-read` | Marquer comme lu           | ✅   |
| `DELETE` | `/api/v1/notifications/{id}`           | Supprimer une notification | ✅   |
| `DELETE` | `/api/v1/notifications/read/all`       | Supprimer les lues         | ✅   |

```javascript
// Exemple: Obtenir les notifications
const getNotifications = async (token) => {
    const response = await fetch(`${API_URL}/notifications`, {
        headers: { Authorization: `Bearer ${token}` },
    });
    return response.json();
};

// Exemple: Marquer tout comme lu
const markAllAsRead = async (token) => {
    const response = await fetch(`${API_URL}/notifications/mark-all-read`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
    });
    return response.json();
};
```

---

### 🎫 Support

| Méthode | Endpoint                     | Description          | Auth |
| ------- | ---------------------------- | -------------------- | ---- |
| `GET`   | `/api/v1/support`            | Liste des tickets    | ✅   |
| `POST`  | `/api/v1/support`            | Créer un ticket      | ✅   |
| `GET`   | `/api/v1/support/stats`      | Statistiques support | ✅   |
| `GET`   | `/api/v1/support/{id}`       | Détails d'un ticket  | ✅   |
| `POST`  | `/api/v1/support/{id}/reply` | Répondre à un ticket | ✅   |
| `POST`  | `/api/v1/support/{id}/close` | Fermer un ticket     | ✅   |

```javascript
// Exemple: Créer un ticket support
const createSupportTicket = async (ticketData, token) => {
    const response = await fetch(`${API_URL}/support`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
            subject: ticketData.subject,
            message: ticketData.message,
            category: ticketData.category,
        }),
    });
    return response.json();
};
```

---

### 💳 Paiements (Payments)

| Méthode | Endpoint                                    | Description               | Auth |
| ------- | ------------------------------------------- | ------------------------- | ---- |
| `GET`   | `/api/v1/payments`                          | Liste des paiements       | ✅   |
| `GET`   | `/api/v1/payments/stats`                    | Statistiques paiements    | ✅   |
| `GET`   | `/api/v1/payments/{transactionId}`          | Détails d'un paiement     | ✅   |
| `POST`  | `/api/v1/payments/initiate`                 | Initier un paiement       | ✅   |
| `POST`  | `/api/v1/payments/refund/{orderId}`         | Demander un remboursement | ✅   |
| `GET`   | `/api/v1/payments/refund/{refundId}/status` | Statut remboursement      | ✅   |

```javascript
// Exemple: Initier un paiement
const initiatePayment = async (paymentData, token) => {
    const response = await fetch(`${API_URL}/payments/initiate`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
            amount: paymentData.amount,
            provider: paymentData.provider, // 'mpesa', 'orange_money', etc.
            phone_number: paymentData.phoneNumber,
            order_id: paymentData.orderId,
        }),
    });
    return response.json();
};
```

---

### ✅ Vérification d'Authenticité

| Méthode | Endpoint                                       | Description               | Auth |
| ------- | ---------------------------------------------- | ------------------------- | ---- |
| `GET`   | `/api/v1/authenticity/dashboard`               | Dashboard authenticité    | ✅   |
| `POST`  | `/api/v1/authenticity/{check}/confirm-payment` | Confirmer paiement vérif. | ✅   |
| `PUT`   | `/api/v1/authenticity/{check}/update-status`   | Mettre à jour statut      | ✅   |
| `GET`   | `/api/v1/items/{item}/authenticity/can-verify` | Peut vérifier ?           | ✅   |
| `GET`   | `/api/v1/items/{item}/authenticity/status`     | Statut vérification       | ✅   |
| `POST`  | `/api/v1/items/{item}/authenticity/submit`     | Soumettre vérification    | ✅   |

---

### 👥 Affiliation (Parrainage)

| Méthode | Endpoint                               | Description               | Auth |
| ------- | -------------------------------------- | ------------------------- | ---- |
| `GET`   | `/api/affiliate/dashboard`             | Dashboard affiliation     | ✅   |
| `GET`   | `/api/affiliate/referral-codes`        | Mes codes parrainage      | ✅   |
| `POST`  | `/api/affiliate/referral-codes`        | Créer un code             | ✅   |
| `POST`  | `/api/affiliate/referral-codes/custom` | Créer code personnalisé   | ✅   |
| `GET`   | `/api/affiliate/codes/stats`           | Stats des codes           | ✅   |
| `GET`   | `/api/affiliate/referrals`             | Mes filleuls              | ✅   |
| `GET`   | `/api/affiliate/points-history`        | Historique des points     | ✅   |
| `POST`  | `/api/affiliate/convert-points`        | Convertir points en cash  | ✅   |
| `POST`  | `/api/affiliate/calculate-conversion`  | Calculer conversion       | ✅   |
| `GET`   | `/api/affiliate/redemptions`           | Historique conversions    | ✅   |
| `POST`  | `/api/affiliate/apply-referral-code`   | Appliquer code parrainage | ✅   |
| `GET`   | `/api/affiliate/generate-link`         | Générer lien parrainage   | ✅   |

---

## 🔧 Endpoints Publics (Sans Auth)

| Méthode | Endpoint                            | Description                     |
| ------- | ----------------------------------- | ------------------------------- |
| `GET`   | `/api/health`                       | Vérifier l'état de l'API        |
| `POST`  | `/api/validate-location`            | Valider la géolocalisation      |
| `POST`  | `/api/payment-callbacks/{provider}` | Callbacks paiement (opérateurs) |
| `GET`   | `/api/payment-callbacks/status`     | Statut callback                 |

---

## 👨‍💼 API Admin (Nécessite rôle Admin)

### Dashboard & Stats

| Méthode | Endpoint                      | Description           |
| ------- | ----------------------------- | --------------------- |
| `GET`   | `/api/v1/admin/dashboard`     | Dashboard admin       |
| `GET`   | `/api/v1/admin/stats/summary` | Résumé statistiques   |
| `GET`   | `/api/v1/admin/notifications` | Notifications admin   |
| `GET`   | `/api/v1/admin/reports`       | Rapports              |
| `GET`   | `/api/v1/admin/online-users`  | Utilisateurs en ligne |

### Gestion Utilisateurs

| Méthode | Endpoint                              | Description                |
| ------- | ------------------------------------- | -------------------------- |
| `GET`   | `/api/v1/admin/users`                 | Liste utilisateurs         |
| `GET`   | `/api/v1/admin/users/{userId}`        | Détails utilisateur        |
| `POST`  | `/api/v1/admin/users/{userId}/status` | Changer statut utilisateur |

### Gestion Portefeuilles

| Méthode | Endpoint                                   | Description              |
| ------- | ------------------------------------------ | ------------------------ |
| `GET`   | `/api/v1/admin/wallets`                    | Liste portefeuilles      |
| `GET`   | `/api/v1/admin/wallets/pending`            | Portefeuilles en attente |
| `POST`  | `/api/v1/admin/wallets/{walletId}/approve` | Approuver portefeuille   |
| `POST`  | `/api/v1/admin/wallets/{walletId}/reject`  | Rejeter portefeuille     |
| `POST`  | `/api/v1/admin/wallets/bulk-approve`       | Approbation en masse     |

### Gestion Transactions & Commandes

| Méthode | Endpoint                     | Description        |
| ------- | ---------------------------- | ------------------ |
| `GET`   | `/api/v1/admin/transactions` | Liste transactions |
| `GET`   | `/api/v1/admin/orders`       | Liste commandes    |

### Gestion Articles

| Méthode | Endpoint                              | Description            |
| ------- | ------------------------------------- | ---------------------- |
| `GET`   | `/api/v1/admin/items`                 | Liste articles         |
| `POST`  | `/api/v1/admin/items/{itemId}/status` | Changer statut article |
| `GET`   | `/api/v1/admin/brands`                | Liste marques          |
| `GET`   | `/api/v1/admin/categories`            | Liste catégories       |

### Support & Vérification

| Méthode | Endpoint                              | Description                |
| ------- | ------------------------------------- | -------------------------- |
| `GET`   | `/api/v1/admin/support-chats`         | Chats support              |
| `GET`   | `/api/v1/admin/support`               | Tickets support            |
| `GET`   | `/api/v1/admin/support/stats`         | Stats support              |
| `GET`   | `/api/v1/admin/support/{supportChat}` | Détails chat               |
| `GET`   | `/api/v1/admin/verification-checks`   | Vérifications authenticité |

### Affiliation Admin

| Méthode | Endpoint                                 | Description       |
| ------- | ---------------------------------------- | ----------------- |
| `GET`   | `/api/v1/admin/affiliate/stats`          | Stats affiliation |
| `GET`   | `/api/v1/admin/affiliate/top-performers` | Top affiliés      |
| `GET`   | `/api/v1/admin/affiliate/referrers`      | Liste parrains    |
| `GET`   | `/api/v1/admin/affiliate/activity`       | Activité récente  |

### Remboursements

| Méthode | Endpoint                         | Description           |
| ------- | -------------------------------- | --------------------- |
| `GET`   | `/api/v1/admin/refunds`          | Liste remboursements  |
| `GET`   | `/api/v1/admin/refunds/{refund}` | Détails remboursement |

### Utilisateurs en Attente

| Méthode | Endpoint                                            | Description      |
| ------- | --------------------------------------------------- | ---------------- |
| `GET`   | `/api/v1/admin/waiting-users`                       | Liste en attente |
| `GET`   | `/api/v1/admin/waiting-users/stats`                 | Stats attente    |
| `POST`  | `/api/v1/admin/waiting-users/{waitingUser}/approve` | Approuver        |

### Monitoring

| Méthode | Endpoint                          | Description      |
| ------- | --------------------------------- | ---------------- |
| `GET`   | `/api/v1/admin/monitoring/stats`  | Stats monitoring |
| `GET`   | `/api/v1/admin/monitoring/health` | Santé système    |

### Paramètres

| Méthode | Endpoint                       | Description        |
| ------- | ------------------------------ | ------------------ |
| `GET`   | `/api/v1/admin/settings`       | Paramètres         |
| `PUT`   | `/api/v1/admin/settings/{key}` | Modifier paramètre |

---

## 📱 Service API React (Exemple Complet)

```javascript
// services/api.js
import AsyncStorage from "@react-native-async-storage/async-storage";

const API_BASE_URL = "https://votre-domaine.com/api/v1";

class ApiService {
    constructor() {
        this.token = null;
    }

    async init() {
        this.token = await AsyncStorage.getItem("auth_token");
    }

    async setToken(token) {
        this.token = token;
        await AsyncStorage.setItem("auth_token", token);
    }

    async clearToken() {
        this.token = null;
        await AsyncStorage.removeItem("auth_token");
    }

    getHeaders() {
        const headers = {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
        if (this.token) {
            headers["Authorization"] = `Bearer ${this.token}`;
        }
        return headers;
    }

    async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const config = {
            ...options,
            headers: {
                ...this.getHeaders(),
                ...options.headers,
            },
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw { status: response.status, ...data };
            }

            return data;
        } catch (error) {
            console.error("API Error:", error);
            throw error;
        }
    }

    // Auth
    async login(email, password) {
        const data = await this.request("/login", {
            method: "POST",
            body: JSON.stringify({ email, password }),
        });
        if (data.token) {
            await this.setToken(data.token);
        }
        return data;
    }

    async register(userData) {
        return this.request("/register", {
            method: "POST",
            body: JSON.stringify(userData),
        });
    }

    async logout() {
        await this.clearToken();
    }

    // User
    async getProfile() {
        return this.request("/user/profile");
    }

    async updateProfile(profileData) {
        return this.request("/user/profile", {
            method: "PUT",
            body: JSON.stringify(profileData),
        });
    }

    // Items
    async getItems(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/items${query ? `?${query}` : ""}`);
    }

    async getItem(id) {
        return this.request(`/items/${id}`);
    }

    async createItem(itemData) {
        return this.request("/items", {
            method: "POST",
            body: JSON.stringify(itemData),
        });
    }

    // Orders
    async getOrders() {
        return this.request("/orders");
    }

    async createOrder(orderData) {
        return this.request("/orders", {
            method: "POST",
            body: JSON.stringify(orderData),
        });
    }

    async confirmDelivery(orderId) {
        return this.request(`/orders/${orderId}/confirm-delivery`, {
            method: "POST",
        });
    }

    // Wallet
    async getWallet() {
        return this.request("/wallet");
    }

    async getTransactions() {
        return this.request("/wallet/transactions");
    }

    // Notifications
    async getNotifications() {
        return this.request("/notifications");
    }

    async getUnreadCount() {
        return this.request("/notifications/unread/count");
    }

    async markAllAsRead() {
        return this.request("/notifications/mark-all-read", {
            method: "POST",
        });
    }

    // Messages
    async getMessages() {
        return this.request("/messages");
    }

    async sendMessage(receiverId, content, itemId = null) {
        return this.request("/messages", {
            method: "POST",
            body: JSON.stringify({
                receiver_id: receiverId,
                content,
                item_id: itemId,
            }),
        });
    }

    // Categories & Brands
    async getCategories() {
        return this.request("/categories");
    }

    async getBrands() {
        return this.request("/brands");
    }

    // Support
    async getSupportTickets() {
        return this.request("/support");
    }

    async createSupportTicket(subject, message, category) {
        return this.request("/support", {
            method: "POST",
            body: JSON.stringify({ subject, message, category }),
        });
    }

    // Payments
    async initiatePayment(paymentData) {
        return this.request("/payments/initiate", {
            method: "POST",
            body: JSON.stringify(paymentData),
        });
    }
}

export default new ApiService();
```

---

## 🔐 Providers de Paiement Supportés

| Provider     | Code           | Description                      |
| ------------ | -------------- | -------------------------------- |
| M-Pesa       | `mpesa`        | Mobile Money (Vodacom/Safaricom) |
| Orange Money | `orange_money` | Orange Money                     |
| Airtel Money | `airtel_money` | Airtel Money                     |
| Africell     | `africell`     | Africell Money                   |
| IllicoCash   | `illicocash`   | IllicoCash                       |

---

## 📝 Notes Importantes

1. **Rate Limiting**: Les routes ont des limites de requêtes (ex: 60/min pour les routes utilisateur)
2. **Cache**: Certaines routes publiques sont cachées (categories, brands: 1h)
3. **Authentification**: Utilisez Laravel Sanctum tokens
4. **FCM**: Pour les notifications push, enregistrez d'abord le token FCM

---

## 🔗 Liens Utiles

-   Documentation Laravel Sanctum: https://laravel.com/docs/sanctum
-   React Native Fetch API: https://reactnative.dev/docs/network

---

_Généré le 13 décembre 2025_
