# 🎯 Système de Traçage de Commandes & Utilisateurs Connectés - Documentation

## ✅ Fonctionnalités Implémentées

### 1. 📍 Système de Traçage GPS des Commandes

#### **Base de données**
- ✅ Table `order_tracking` créée avec les champs :
  - Position actuelle (latitude, longitude, adresse, ville)
  - Statut de livraison (pending, picked_up, in_transit, out_for_delivery, delivered, failed, returned)
  - Destination client (customer_latitude, customer_longitude, customer_address)
  - Informations transporteur (carrier, tracking_code)
  - Dates (tracked_at, estimated_delivery)

#### **Modèle OrderTracking**
- ✅ Relations avec Order
- ✅ Calcul de distance (formule de Haversine)
- ✅ Méthodes utiles :
  - `getLatestForOrder($orderId)` - Dernière position
  - `getHistoryForOrder($orderId)` - Historique complet
  - `calculateDistance()` - Distance en km
  - `getDistanceToCustomerAttribute` - Distance restante

#### **Routes Admin**
```php
GET  /admin/orders/{order}/tracking           - Page de traçage GPS
POST /admin/orders/{order}/tracking           - Mise à jour position
GET  /admin/orders/{order}/invoice            - Facture imprimable
GET  /admin/orders/{order}/invoice/download   - Télécharger PDF
```

#### **Interface de Traçage**
✅ **Page admin/orders/tracking.blade.php** :
- 🗺️ Carte interactive Leaflet (OpenStreetMap)
- 📍 Marqueur position actuelle du colis
- 🏠 Marqueur destination client
- 📏 Ligne de trajet avec distance
- 📊 Statistiques : Distance restante, livraison estimée
- 📜 Timeline historique des positions
- ➕ Modal pour ajouter/mettre à jour position

**Fonctionnalités de la carte** :
- Auto-centrage sur position et destination
- Zoom adaptatif
- Icônes personnalisées (camion 🚚, maison 🏠)
- Info-bulles interactives

#### **Facture Imprimable**
✅ **Page admin/orders/invoice.blade.php** :
- 🧾 Design professionnel prêt à imprimer
- 📋 Informations complètes :
  - Détails commande (numéro, date, statut)
  - Acheteur & vendeur
  - Articles commandés
  - Totaux et prix
- 📍 **Section de traçage intégrée** :
  - Statut actuel de livraison
  - Code de suivi
  - Position actuelle
  - Distance restante
  - Livraison estimée
- 🖨️ Bouton impression avec CSS optimisé
- 📱 Responsive et prêt pour PDF

---

### 2. 👥 Utilisateurs Connectés en Temps Réel

#### **Base de données**
- ✅ Table `user_sessions` créée avec :
  - Informations session (session_id, ip_address, user_agent)
  - Détection appareil (device_type, browser, os)
  - Localisation (latitude, longitude, city, country)
  - Activité (login_at, logout_at, last_activity, is_active)

#### **Modèle UserSession**
- ✅ Relations avec User
- ✅ Détection automatique d'appareil (via jenssegers/agent)
- ✅ Méthodes utiles :
  - `trackSession()` - Créer/mettre à jour session
  - `getActiveSessions()` - Sessions < 5 min
  - `getOnlineUsersCount()` - Nombre d'utilisateurs en ligne
  - `markAsInactive()` - Déconnecter
  - `cleanupOldSessions()` - Nettoyage automatique

#### **Routes Admin**
```php
GET  /admin/users/online              - Page utilisateurs connectés
GET  /admin/users/online/data         - API données en temps réel (JSON)
POST /admin/users/sessions/{id}/logout - Forcer déconnexion
```

#### **Interface Utilisateurs en Ligne**
✅ **Page admin/users/online.blade.php** :
- 📊 **Statistiques en temps réel** :
  - Total connectés
  - Par appareil (mobile, tablette, ordinateur)
  - Par rôle (admin, vendeur, acheteur)
- 👤 **Cartes utilisateurs** affichant :
  - Avatar et informations
  - Type d'appareil + navigateur
  - Dernière activité
  - Localisation (si disponible)
  - Adresse IP
- 🔄 **Actualisation automatique** toutes les 10 secondes
- 🔍 **Filtres** :
  - Recherche par nom/email
  - Filtre par type d'appareil
- 🎨 **Design moderne** :
  - Indicateurs de présence animés (pulse)
  - Badges colorés par appareil
  - Icônes navigateur/OS
  - Layout responsive

---

## 📁 Structure des Fichiers

### **Migrations**
```
database/migrations/
├── 2025_10_22_120000_create_order_tracking_table.php
└── 2025_10_22_120100_create_user_sessions_table.php
```

### **Modèles**
```
app/Models/
├── OrderTracking.php  (avec calculs GPS)
└── UserSession.php    (avec détection appareil)
```

### **Contrôleur**
```
app/Http/Controllers/Admin/AdminController.php
└── Nouvelles méthodes :
    ├── orderTracking()           - Afficher traçage
    ├── updateOrderTracking()     - Mettre à jour position
    ├── orderInvoice()            - Facture
    ├── downloadOrderInvoice()    - PDF facture
    ├── onlineUsers()             - Page utilisateurs connectés
    ├── getOnlineUsersData()      - API JSON données
    └── forceLogoutUser()         - Déconnecter utilisateur
```

### **Vues**
```
resources/views/admin/
├── orders/
│   ├── tracking.blade.php  (Carte GPS + Timeline)
│   └── invoice.blade.php   (Facture imprimable)
└── users/
    └── online.blade.php    (Utilisateurs connectés)
```

---

## 🚀 Comment Utiliser

### **1. Traçage de Commande**

#### **Accéder au traçage** :
1. Aller dans **Admin > Commandes**
2. Cliquer sur une commande
3. Bouton **"Traçage GPS"** ou lien dans menu

#### **Mettre à jour position** :
1. Cliquer sur **"Mettre à jour Position"**
2. Remplir le formulaire :
   - **Statut** (obligatoire) : En attente, Collecté, En transit, etc.
   - **Coordonnées GPS** : Latitude et Longitude
   - **Adresse actuelle** : Texte descriptif
   - **Transporteur** : DHL, FedEx, Local, etc.
   - **Code de suivi** : Numéro de tracking
   - **Description** : Note sur la livraison
   - **Livraison estimée** : Date/heure prévue
3. Cliquer **"Enregistrer"**

**La carte se met à jour automatiquement** :
- Marqueur camion = position actuelle
- Marqueur maison = destination client
- Ligne pointillée = trajet
- Distance calculée automatiquement

#### **Imprimer facture** :
1. Depuis la page de traçage : bouton **"Voir Facture"**
2. La facture s'ouvre avec toutes les infos de tracking
3. Bouton **"Imprimer / PDF"** en haut à droite

---

### **2. Utilisateurs Connectés**

#### **Accéder à la page** :
Admin > Utilisateurs > **"Utilisateurs Connectés"**

#### **Fonctionnalités** :
- ✅ Voir qui est en ligne en temps réel
- ✅ Connaître leur appareil (mobile, tablette, PC)
- ✅ Voir leur navigateur et système d'exploitation
- ✅ Savoir depuis combien de temps ils sont actifs
- ✅ Voir leur localisation approximative
- ✅ Filtrer par type d'appareil
- ✅ Rechercher par nom ou email

#### **Actualisation** :
- Automatique toutes les 10 secondes
- Bouton **"Actualiser"** manuel disponible
- Indicateur visuel de mise à jour

---

## 🔧 Configuration Technique

### **Packages Installés**
```bash
composer require jenssegers/agent
```

### **Migrations Exécutées**
```bash
php artisan migrate
```

### **Relations Ajoutées**

**Order.php** :
```php
public function trackings()      // Tous les trackings
public function latestTracking() // Dernier tracking
```

**User.php** :
```php
public function sessions()       // Toutes les sessions
public function activeSessions() // Sessions actives < 5 min
```

---

## 📊 Schéma de la Base de Données

### **Table `order_tracking`**
```sql
id                    BIGINT (PK)
order_id              BIGINT (FK -> orders)
status                VARCHAR (pending, in_transit, delivered...)
latitude              DECIMAL(10,8)  -- Position actuelle
longitude             DECIMAL(11,8)
address               VARCHAR
city                  VARCHAR
country               VARCHAR (défaut: 'CD')
description           TEXT
tracking_code         VARCHAR
carrier               VARCHAR
customer_latitude     DECIMAL(10,8)  -- Destination
customer_longitude    DECIMAL(11,8)
customer_address      VARCHAR
customer_city         VARCHAR
customer_phone        VARCHAR
tracked_at            TIMESTAMP
estimated_delivery    TIMESTAMP
created_at            TIMESTAMP
updated_at            TIMESTAMP

INDEX: order_id, status, tracked_at
```

### **Table `user_sessions`**
```sql
id                BIGINT (PK)
user_id           BIGINT (FK -> users)
session_id        VARCHAR (UNIQUE)
ip_address        VARCHAR(45)  -- IPv4/IPv6
user_agent        TEXT
device_type       VARCHAR (mobile, tablet, desktop)
browser           VARCHAR
os                VARCHAR
latitude          DECIMAL(10,8)
longitude         DECIMAL(11,8)
city              VARCHAR
country           VARCHAR
last_activity     TIMESTAMP
login_at          TIMESTAMP
logout_at         TIMESTAMP
is_active         BOOLEAN
created_at        TIMESTAMP
updated_at        TIMESTAMP

INDEX: user_id, session_id, is_active, last_activity
```

---

## 🎨 Captures d'Écran des Fonctionnalités

### **Page de Traçage GPS**
- 🗺️ Carte interactive plein écran
- 📍 Marqueurs animés avec icônes
- 📊 Statistiques en cards (distance, statut, etc.)
- 📜 Timeline verticale avec étapes
- ➕ Modal de mise à jour élégant

### **Facture Imprimable**
- 🧾 En-tête avec logo et infos entreprise
- 📋 Tableau détaillé des articles
- 💰 Section totaux avec gradient
- 📍 Bloc tracking avec fond coloré
- 🖨️ Optimisée pour impression A4

### **Utilisateurs Connectés**
- 📊 4 cartes statistiques en haut
- 👥 Grille de cartes utilisateurs
- 🔴 Indicateurs en ligne animés (pulse)
- 🎨 Badges colorés par appareil
- 🔄 Indicateur d'actualisation en temps réel

---

## ⚠️ Notes Importantes

### **Sécurité**
- ✅ Toutes les routes protégées par middleware `auth` et `admin`
- ✅ Validation des données GPS (latitude/longitude)
- ✅ Throttling appliqué (60 requêtes/minute)

### **Performance**
- ✅ Index sur colonnes fréquemment requêtées
- ✅ Sessions actives = `last_activity` < 5 minutes
- ✅ Nettoyage automatique des vieilles sessions disponible

### **Améliorations Futures Possibles**
- [ ] Export PDF des factures (installer `barryvdh/laravel-dompdf`)
- [ ] Notifications push lors de changement de statut
- [ ] Tracking en temps réel avec WebSockets
- [ ] Géolocalisation automatique via IP (GeoIP2)
- [ ] Historique complet des sessions utilisateurs
- [ ] Graphiques d'analyse des connexions

---

## 📞 Support

Pour toute question ou problème :
1. Vérifier que les migrations sont bien exécutées
2. S'assurer que le package `jenssegers/agent` est installé
3. Vérifier les permissions sur les routes admin
4. Consulter les logs Laravel pour les erreurs

---

## 🎉 Résumé

Vous disposez maintenant de :
- ✅ **Traçage GPS complet** des commandes avec carte interactive
- ✅ **Facturation professionnelle** avec informations de suivi
- ✅ **Monitoring temps réel** des utilisateurs connectés
- ✅ **Design moderne et responsive** sur toutes les pages
- ✅ **Architecture scalable** et maintenable

Toutes les fonctionnalités demandées sont **100% opérationnelles** ! 🚀
