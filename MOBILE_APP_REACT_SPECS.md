# VintApp Mobile - Spécifications & Architecture React Native/React

## 📋 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture Globale](#architecture-globale)
3. [Analyse Détaillée de l'Application Web](#analyse-détaillée-de-lapplication-web)
4. [Structure de l'App Mobile React](#structure-de-lapp-mobile-react)
5. [Interfaces & Designs Mobile](#interfaces--designs-mobile)
6. [Spécifications Techniques](#spécifications-techniques)
7. [Roadmap de Développement](#roadmap-de-développement)

---

## 🎯 Vue d'ensemble

**VintApp** est une plateforme e-commerce spécialisée en articles de luxe et vintage avec authentification des produits. L'application mobile React doit reproduire l'expérience web responsive avec les mêmes designs et fonctionnalités, optimisée pour mobile-first.

### Caractéristiques Principales

-   **Marketplace**: Achat/vente d'articles de luxe
-   **Authentification**: Vérification IA des produits
-   **Wallets**: Gestion multi-devises (USD/CDF)
-   **Experts**: Système de vérification d'authenticité
-   **Admin Panel**: Gestion complète des utilisateurs, commandes, wallets
-   **Support**: Système de messagerie en temps réel
-   **Affiliés**: Programme de referral

---

## 🏗️ Architecture Globale

### Stack Technologique

```
Frontend Web:
├── Laravel 11 (Backend)
├── Blade Templates
├── Tailwind CSS
├── Vue 3 (partiellement via Inertia)
└── Bootstrap 5

Backend:
├── PHP/Laravel 11
├── MySQL/PostgreSQL
├── Firebase (Auth, FCM, Storage)
├── Redis (Cache, Sessions)
└── APIs RESTful + Broadcasting

Mobile (À créer):
├── React Native / React
├── Expo / React Native CLI
├── Tailwind CSS / React Native Elements
├── Redux/Context API (State Management)
└── API REST (réutilisation backend)
```

---

## 📊 Analyse Détaillée de l'Application Web

### 1. **Modèles & Structures de Données**

#### Modèles Principaux

```
User
├── id, name, email, phone, address
├── avatar, avatar_url, bio, location
├── roles (Admin, Expert, User)
├── wallets (relation hasMany)
├── items (articles mis en vente)
├── ordersAsBuyer, ordersAsSeller
├── transactions
├── notifications, messages
├── points (système de points)
└── referral_code (programme affilié)

Item
├── id, user_id (vendeur)
├── name, description, price, currency
├── images (array), specifications (array)
├── category_id, brand_id
├── condition (nouveau/bon état/très bon)
├── quantity, views
├── status (active/pending/rejected/sold)
├── authenticity_verified, verification_score
├── color, size, item_number
└── timestamps

Order
├── id, order_number, buyer_id, seller_id, item_id
├── quantity, unit_price, total_amount, currency
├── status (pending/paid/shipped/delivered/confirmed/cancelled)
├── shipping_address, shipping_city, shipping_phone
├── tracking_number, scan_token
├── paid_at, shipped_at, delivered_at, confirmed_by_buyer_at
└── buyer_confirmation_note

Wallet
├── id, user_id
├── balance, currency (USD/CDF)
├── type (regular/pending/enterprise)
├── is_active, blocked_at
├── transaction_history
└── sub_wallets (commission, transport, boost)

Transaction
├── id, user_id, wallet_id
├── amount, currency, type (debit/credit)
├── status (pending/completed/failed)
├── description
├── related_order_id
└── timestamps

ProductAuthenticityCheck
├── id, item_id, expert_id
├── status (pending/expert_review/approved/rejected)
├── verification_fee, payment_completed
├── verification_images (array)
├── authenticity_score, report (détails)
└── timeline (assigned_at, completed_at)

ExpertProfile
├── user_id, specialties (array)
├── certification_level (junior/senior/master)
├── bio, is_active
├── verification_count, approval_rate
├── average_review_time
└── authenticity_badges earned

SupportChat & SupportMessage
├── chat: id, user_id, admin_id, status (open/in_progress/closed)
├── message: id, chat_id, sender_id, message, attachments
└── timestamps

Notification
├── id, user_id, type, data
├── read_at, created_at
└── polymorphic (relates to Order, Transaction, etc.)

Category, Brand
├── name, slug, description
├── image/icon, is_active
├── items_count, order_count
└── timestamps

Role
├── id, name (Admin/Expert/User), slug
├── description, permissions
└── users (relationship)

HeroSlide
├── id, title, subtitle, image
├── cta_text, cta_url, order
├── is_active, starts_at, ends_at
└── timestamps
```

### 2. **Fonctionnalités Web Détaillées**

#### 🏠 Dashboard

-   **Admin Dashboard**: Stats globales (users, transactions, revenus, wallets pending)
-   **Graphiques 30 jours**: Revenue, User Growth, Order Trends
-   **Quick Actions**: Accès rapide aux wallets en attente, chats support
-   **Real-time Updates**: Utilisateurs connectés, notifications actives

#### 👥 Gestion des Utilisateurs

-   **Liste avec filtres**: Recherche, rôle, statut, date
-   **Détails utilisateur**: Profil, wallets, transactions, commandes, items
-   **Actions**:
    -   Créer/Modifier/Supprimer
    -   Activer/Désactiver
    -   Révoquer/Assigner rôles
    -   Envoyer email (reset password, welcome, message)
    -   Export données
    -   Forcer logout (sessions)

#### 💰 Gestion des Wallets

-   **Dashboard Wallets**: Vue d'ensemble des wallets utilisateurs
-   **Wallets Pending**: Wallets en attente de confirmation acheteur
    -   Approuver/Rejeter individuellement
    -   Actions en masse (bulk approve/reject)
-   **Détails wallet**: Historique transactions, balance par devise
-   **Sous-wallets Entreprise**:
    -   Commission (revenus plateforme)
    -   Transport (frais livraison)
    -   Boost (revenus promotion articles)

#### 📝 Gestion des Transactions

-   **Liste transactions**: Filtres type, statut, devise, date range
-   **Détails transaction**: User, wallet, montant, statut, description
-   **États possibles**: Pending, Completed, Failed, Refunded

#### 📦 Gestion des Commandes

-   **Liste commandes**: Filtres statut, date
-   **Détails commande**: Buyer, Seller, Item, montant, adresse livraison
-   **Tracking GPS**: Suivi livraison en temps réel
-   **Facture**: Vue et téléchargement facture
-   **États commandes**: Pending → Paid → Shipped → Delivered → Confirmed

#### ✅ Vérification d'Authenticité

-   **Pendantes**: Listes items attente vérification
-   **Détails**: Images produit, spécifications, expert assigné
-   **Experts**: Assigner expert, timeline vérification, score authenticité
-   **Badges**: Affichage badge authenticité sur item (junior/senior/master)

#### 👨‍💼 Gestion des Experts

-   **Candidats**: Liste utilisateurs avec qualifications
-   **Désignation**: Assigner rôle expert + spécialités + level certification
-   **Profils Experts**: Spécialités, statistiques (verifications, approval rate, avg time)
-   **Actions**: Éditer, activer/désactiver, révoquer

#### 👤 Gestion des Administrateurs ⭐ NEW

-   **Candidats**: Liste utilisateurs non-admins
-   **Candidat Détails**: Stats compte (items, orders, transactions, account age)
-   **Désignation**: Assigner rôle admin + permissions spécifiques + raison
-   **Liste Admins**: Vue tous administrateurs
-   **Admin Détails**: Info admin, activity log
-   **Révocation**: Retirer droits admin + raison + notification utilisateur

#### 🏷️ Gestion Catégories & Marques

-   **CRUD complet**: Créer, lister, éditer, supprimer
-   **Statut**: Actif/Inactif
-   **Détails**: Nom, slug, description, image/icon
-   **Stats**: Nombre items, nombre commandes

#### 📰 Articles/Items

-   **Liste avec filtres**: Recherche, catégorie, marque, statut, condition
-   **Créer Item**: Nom, description, prix, images (multiple), spécifications
-   **Détails Item**: Infos complètes, images, authenticité status, boost active
-   **Éditer Item**: Modification données, images, prix
-   **Supprimer Item**: Soft/hard delete selon business logic
-   **Actions spéciales**: Approuver, Rejeter (avec raison), Boost (promotion), Bulk actions

#### 💬 Support Chat

-   **Liste chats**: Filtres statut (open, in progress, closed)
-   **Chat Détails**: Messages, attachments, sender info
-   **Actions**: Assigner admin, changer statut, envoyer message
-   **Real-time**: Websocket updates pour messages

#### 📧 Newsletter

-   **Souscripteurs**: Liste avec stats
-   **Créer/Envoyer**: Composer newsletter + aperçu + envoi ciblé
-   **Gestion**: Ajouter/Retirer souscripteurs, toggler statut
-   **Export**: CSV souscripteurs

#### 🎪 Hero Slides (Bannières Accueil)

-   **CRUD**: Créer, lister, éditer, supprimer
-   **Détails**: Titre, sous-titre, image, CTA button (texte + URL)
-   **Ordonnancement**: Réordonnement slides
-   **Activation**: Toggle actif/inactif

#### ⚙️ Paramètres Système

-   **Mode Maintenance**: Activer/désactiver avec message custom
-   **Settings globaux**: Configuration emails, commissions, frais, etc.
-   **Pre-registration**: Gérer statut pre-registration mode
-   **Restrictions géographiques**: Villes autorisées

#### 📊 Rapports & Analytics

-   **Périodes**: 7, 30, 90 jours
-   **Rapports**: Revenue, Users, Transactions, Popular Items
-   **Logs Système**: Fichier logs Laravel, stats erreurs

#### 👥 Utilisateurs Connectés Temps Réel

-   **Liste actifs**: Utilisateurs avec session active (< 5min)
-   **Détails**: Email, dernière page, Device type
-   **Actions**: Forcer logout utilisateur

---

### 3. **Designs & Interfaces Web**

#### Palette Couleurs

```
Primary: #3B82F6 (Bleu)
Secondary: #10B981 (Vert)
Success: #10B981
Warning: #F59E0B (Ambre)
Danger: #EF4444 (Rouge)
Light: #F3F4F6
Dark: #111827
Gray: #6B7280

Dark Mode:
BG: #1F2937
Text: #F3F4F6
Borders: #374151
```

#### Tipographie

```
Headings: Font-bold, dark text
Body: Regular, medium gray
Small: Text-xs, lighter gray
Code: Font-mono, gray background
```

#### Composants Récurrents

```
Cards:
- bg-white dark:bg-gray-800
- rounded-xl shadow-sm
- border border-gray-200 dark:border-gray-700
- p-6
- hover effects (shadow-lg, -translate-y-1)

Buttons:
- Primary: bg-primary-600 hover:bg-primary-700
- Secondary: border border-gray-300 hover:bg-gray-50
- Danger: bg-red-600 hover:bg-red-700
- Small: px-3 py-2 text-sm
- Medium: px-4 py-2 text-base
- Large: px-6 py-3 text-lg
- Icons: Fontawesome (fas fa-*)
- Transitions: transition-colors duration-200

Inputs:
- border border-gray-300 dark:border-gray-600
- rounded-lg
- focus:ring-2 focus:ring-primary-500
- focus:border-primary-500
- Responsive sizing

Tables:
- Responsive (hidden on mobile, card view)
- Striped rows
- Header bg-gray-50 dark:bg-gray-900
- Pagination bottom

Forms:
- Grid layout responsive
- Labels above inputs
- Validation messages
- Placeholder text

Modals/Overlays:
- Dark background (bg-black bg-opacity-50)
- Centered content
- Smooth animations
- Close button top-right

Navigation:
- Sidebar admin (collapsible)
- Top navbar (breadcrumbs, notifications, profile)
- Bottom mobile nav (mobile-only)
```

#### Layouts

```
Admin Layout (layouts.admin):
├── Top Navbar
│   ├── Logo/Branding
│   ├── Search global
│   ├── Notifications (bell icon)
│   └── Profile dropdown
├── Left Sidebar (collapsible)
│   ├── Navigation menu
│   ├── Active indicator
│   └── Dark mode toggle
└── Main Content Area
    ├── Breadcrumbs
    ├── Page title
    ├── Page actions (buttons)
    └── Content section

Public Layout (layouts.app):
├── Top Navbar
│   ├── Logo
│   ├── Navigation links
│   ├── Search bar
│   └── Auth buttons/Profile
├── Hero/Banner
├── Main Content
└── Footer
    ├── Links
    ├── Social
    └── Copyright
```

---

## 🚀 Structure de l'App Mobile React

### Architecture Recommandée

```
mobile-app/
├── src/
│   ├── components/
│   │   ├── common/
│   │   │   ├── Header.jsx
│   │   │   ├── BottomNavigation.jsx
│   │   │   ├── Card.jsx
│   │   │   ├── Button.jsx
│   │   │   ├── Input.jsx
│   │   │   ├── Modal.jsx
│   │   │   └── LoadingSpinner.jsx
│   │   ├── auth/
│   │   │   ├── LoginScreen.jsx
│   │   │   ├── SignupScreen.jsx
│   │   │   ├── OTPVerification.jsx
│   │   │   ├── PasswordReset.jsx
│   │   │   └── SocialLogin.jsx
│   │   ├── marketplace/
│   │   │   ├── HomeScreen.jsx
│   │   │   ├── SearchScreen.jsx
│   │   │   ├── ItemDetailScreen.jsx
│   │   │   ├── CreateItemScreen.jsx
│   │   │   ├── CartScreen.jsx
│   │   │   └── CheckoutScreen.jsx
│   │   ├── orders/
│   │   │   ├── OrdersListScreen.jsx
│   │   │   ├── OrderDetailScreen.jsx
│   │   │   ├── OrderTrackingScreen.jsx
│   │   │   └── InvoiceScreen.jsx
│   │   ├── wallet/
│   │   │   ├── WalletScreen.jsx
│   │   │   ├── TransactionsScreen.jsx
│   │   │   ├── AddFundsScreen.jsx
│   │   │   └── WithdrawScreen.jsx
│   │   ├── profile/
│   │   │   ├── ProfileScreen.jsx
│   │   │   ├── EditProfileScreen.jsx
│   │   │   ├── SettingsScreen.jsx
│   │   │   └── NotificationsScreen.jsx
│   │   ├── support/
│   │   │   ├── SupportListScreen.jsx
│   │   │   ├── ChatScreen.jsx
│   │   │   └── FAQScreen.jsx
│   │   ├── admin/
│   │   │   ├── AdminDashboard.jsx
│   │   │   ├── UsersManagement.jsx
│   │   │   ├── WalletsManagement.jsx
│   │   │   ├── OrdersManagement.jsx
│   │   │   ├── ItemsModeration.jsx
│   │   │   ├── ExpertsManagement.jsx
│   │   │   ├── AdminsManagement.jsx
│   │   │   └── SettingsManagement.jsx
│   │   └── expert/
│   │       ├── ExpertDashboard.jsx
│   │       ├── VerificationsScreen.jsx
│   │       └── ExpertProfileScreen.jsx
│   ├── screens/
│   │   ├── NotFoundScreen.jsx
│   │   ├── ServerErrorScreen.jsx
│   │   └── MaintenanceScreen.jsx
│   ├── layouts/
│   │   ├── AppLayout.jsx
│   │   ├── AdminLayout.jsx
│   │   └── AuthLayout.jsx
│   ├── services/
│   │   ├── api.js (Axios instance)
│   │   ├── authService.js
│   │   ├── itemService.js
│   │   ├── orderService.js
│   │   ├── walletService.js
│   │   ├── userService.js
│   │   ├── supportService.js
│   │   └── adminService.js
│   ├── hooks/
│   │   ├── useAuth.js
│   │   ├── useItems.js
│   │   ├── useOrders.js
│   │   ├── useWallet.js
│   │   ├── usePagination.js
│   │   └── useNotifications.js
│   ├── context/
│   │   ├── AuthContext.js
│   │   ├── CartContext.js
│   │   ├── NotificationContext.js
│   │   └── AdminContext.js
│   ├── store/
│   │   ├── slices/
│   │   │   ├── authSlice.js
│   │   │   ├── itemsSlice.js
│   │   │   ├── ordersSlice.js
│   │   │   ├── walletSlice.js
│   │   │   └── uiSlice.js
│   │   └── index.js (Redux store)
│   ├── utils/
│   │   ├── validators.js
│   │   ├── formatters.js
│   │   ├── constants.js
│   │   ├── helpers.js
│   │   └── localStorage.js
│   ├── styles/
│   │   ├── globals.css (Tailwind + customs)
│   │   ├── variables.css
│   │   └── animations.css
│   ├── App.jsx
│   ├── index.jsx
│   └── config.js
├── public/
│   ├── index.html
│   ├── favicon.ico
│   ├── icons/
│   │   ├── logo.svg
│   │   ├── icon-192.png
│   │   └── icon-512.png
│   └── manifest.json (PWA)
├── package.json
├── vite.config.js (ou craco.config.js pour CRA)
├── tailwind.config.js
├── .env.example
└── README.md
```

### Stack Technologies Recommandé

```json
{
    "dependencies": {
        "react": "^18.2.0",
        "react-dom": "^18.2.0",
        "react-router-dom": "^6.20.0",
        "axios": "^1.6.0",
        "redux": "^4.2.0",
        "react-redux": "^8.1.0",
        "@reduxjs/toolkit": "^1.9.0",
        "tailwindcss": "^3.3.0",
        "@heroicons/react": "^2.0.0",
        "date-fns": "^2.30.0",
        "react-hot-toast": "^2.4.0",
        "zustand": "^4.4.0",
        "react-query": "^3.39.0",
        "socket.io-client": "^4.7.0",
        "firebase": "^10.0.0",
        "react-lazy-load-image-component": "^1.10.0",
        "react-infinite-scroll-component": "^6.1.0",
        "react-dropzone": "^14.2.0",
        "js-cookie": "^3.0.5",
        "jwt-decode": "^3.1.2",
        "lodash": "^4.17.21"
    },
    "devDependencies": {
        "@vitejs/plugin-react": "^4.0.0",
        "vite": "^5.0.0",
        "postcss": "^8.4.31",
        "autoprefixer": "^10.4.16",
        "tailwindcss": "^3.3.0",
        "eslint": "^8.55.0",
        "eslint-plugin-react": "^7.33.0",
        "prettier": "^3.1.0"
    }
}
```

---

## 📱 Interfaces & Designs Mobile

### 1. **Authentication Screens**

#### Login Screen

```
┌─────────────────────────────┐
│         Logo VintApp         │
├─────────────────────────────┤
│                             │
│ Email Address               │
│ [________________]          │
│                             │
│ Password                    │
│ [________________]  [👁]    │
│                             │
│ [ Login Button ]            │
│                             │
│ Forgot Password?            │
│ Mot de passe oublié?        │
│                             │
├─────────────────────────────┤
│ Don't have account? Sign Up │
│ Google | Apple Sign In      │
└─────────────────────────────┘
```

#### Signup Screen

```
┌─────────────────────────────┐
│     Create Your Account      │
├─────────────────────────────┤
│ Full Name                   │
│ [________________]          │
│                             │
│ Email                       │
│ [________________]          │
│                             │
│ Phone (Optional)            │
│ [________________]          │
│                             │
│ Password                    │
│ [________________]          │
│                             │
│ Confirm Password            │
│ [________________]          │
│                             │
│ ☐ I agree to Terms         │
│                             │
│ [ Sign Up Button ]          │
│                             │
│ Already have account?       │
│ Log In                      │
└─────────────────────────────┘
```

#### OTP Verification

```
┌─────────────────────────────┐
│    Verify Your Email         │
├─────────────────────────────┤
│ We sent code to:            │
│ user@example.com            │
│                             │
│ Enter 6-digit code:         │
│ [__] [__] [__]              │
│ [__] [__] [__]              │
│                             │
│ [ Verify Button ]           │
│                             │
│ Didn't receive code?        │
│ [ Resend (60s) ]            │
│                             │
│ Wrong email? Change it      │
└─────────────────────────────┘
```

### 2. **Home/Marketplace Screens**

#### Home Screen

```
┌─────────────────────────────┐
│ ← | VintApp | 🔔 👤         │
├─────────────────────────────┤
│  [________Search____] 🔍    │
├─────────────────────────────┤
│ ≡ Filter |  ▼ Sort           │
├─────────────────────────────┤
│ ┌─────────────────────────┐  │
│ │                         │  │
│ │   [   Hero Slide 1   ]  │  │
│ │   Amazing Offers        │  │
│ │                         │  │
│ └─────────────────────────┘  │
├─────────────────────────────┤
│ Categories (horizontal)      │
│ [Luxury] [Vintage] [Shoes]  │
│ [Watches] [Bags] [...]      │
├─────────────────────────────┤
│ Featured Items              │
│                             │
│ ┌──────────┐ ┌──────────┐  │
│ │  Item 1  │ │  Item 2  │  │
│ │ [Image]  │ │ [Image]  │  │
│ │ $999     │ │ $1,299   │  │
│ │ ⭐⭐⭐   │ │ ⭐⭐⭐⭐  │  │
│ └──────────┘ └──────────┘  │
│                             │
│ ┌──────────┐ ┌──────────┐  │
│ │  Item 3  │ │  Item 4  │  │
│ │ [Image]  │ │ [Image]  │  │
│ │ $599     │ │ $799     │  │
│ │ ⭐⭐     │ │ ⭐⭐⭐   │  │
│ └──────────┘ └──────────┘  │
│                             │
│ [ Load More ]               │
└─────────────────────────────┘
│ 🏠 | 🔍 | ➕ | 💬 | 👤       │
└─────────────────────────────┘
```

#### Item Detail Screen

```
┌─────────────────────────────┐
│ ← |     Item Details    | ⋯  │
├─────────────────────────────┤
│      [  Main Image  ]       │
│       [◄ Images ►]          │
├─────────────────────────────┤
│ Luxury Louis Vuitton Bag    │
│ ⭐⭐⭐⭐⭐ (234 reviews)     │
│ $1,299.99                   │
│                             │
│ Condition: Very Good        │
│ Brand: Louis Vuitton        │
│ Color: Black                │
│ Size: Medium                │
│                             │
│ ✓ Authenticity Verified     │
│   Verified by Expert        │
│                             │
│ Description:                │
│ Perfect pre-owned LV bag... │
│ (read more)                 │
│                             │
│ Seller: @JohnDoe           │
│ ⭐⭐⭐⭐⭐ (98% positive)     │
│ Member since 2021           │
│                             │
│ ┌──────────────────────────┐ │
│ │ ❤️ Save | 💬 Message Seller│ │
│ └──────────────────────────┘ │
├─────────────────────────────┤
│        [ Add to Cart ]       │
│        [ Buy Now ]           │
└─────────────────────────────┘
```

#### Search/Filter Screen

```
┌─────────────────────────────┐
│ ← | Search Results         │
├─────────────────────────────┤
│ [________search term__] ✕  │
├─────────────────────────────┤
│ Show Filters ▼              │
├─────────────────────────────┤
│ Filter Panel (Collapsible):  │
│ ☐ Category                  │
│   ☑ Bags                    │
│   ☑ Watches                 │
│   ☐ Shoes                   │
│                             │
│ ☐ Price Range              │
│   $0  ────●────  $5000     │
│                             │
│ ☐ Condition                │
│   ☑ Like New               │
│   ☑ Very Good              │
│   ☐ Good                   │
│                             │
│ ☑ Authenticity Verified    │
│                             │
│ [ Apply Filters ]           │
│ [ Clear All ]               │
├─────────────────────────────┤
│ Results: 247 items          │
│ Sort: [ Newest ▼ ]          │
│                             │
│ [Item Card 1]               │
│ [Item Card 2]               │
│ [Item Card 3]               │
│ [ Load More ]               │
└─────────────────────────────┘
```

### 3. **Order & Checkout Screens**

#### Cart Screen

```
┌─────────────────────────────┐
│ ← | Shopping Cart      | (2) │
├─────────────────────────────┤
│ ┌──────────────────────────┐ │
│ │ [Item Image]             │ │
│ │ Item Name                │ │
│ │ $999                     │ │
│ │ Qty: [-] 1 [+]         │ │
│ │                 [×]      │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │ [Item Image]             │ │
│ │ Second Item              │ │
│ │ $599                     │ │
│ │ Qty: [-] 1 [+]         │ │
│ │                 [×]      │ │
│ └──────────────────────────┘ │
├─────────────────────────────┤
│ Subtotal:        $1,598.00  │
│ Shipping:          $25.00   │
│ Tax:               $47.94   │
│ ────────────────────────    │
│ Total:           $1,670.94  │
├─────────────────────────────┤
│      [ Proceed to Checkout ] │
│      [ Continue Shopping ]   │
└─────────────────────────────┘
```

#### Checkout Screen

```
┌─────────────────────────────┐
│ ← | Checkout             │
├─────────────────────────────┤
│ 1. Delivery Address         │
│ ┌──────────────────────────┐ │
│ │ Street: [_____________]  │ │
│ │ City:    [_____________] │ │
│ │ Phone:   [_____________] │ │
│ └──────────────────────────┘ │
│                             │
│ 2. Payment Method           │
│ ○ Credit/Debit Card        │
│ ○ Wallet (USD $1,200)      │
│ ○ Mobile Money             │
│                             │
│ 3. Order Summary            │
│ Item 1: $999 x 1            │
│ Shipping: $25               │
│ Total: $1,670.94            │
│                             │
│      [ Place Order ]         │
└─────────────────────────────┘
```

#### Order Tracking

```
┌─────────────────────────────┐
│ ← | Track Order       ORD... │
├─────────────────────────────┤
│ Status: Shipped             │
│ Last update: 2 hours ago    │
├─────────────────────────────┤
│ Timeline:                   │
│ ✓ Paid (Jan 15, 2:30 PM)   │
│ ✓ Packed (Jan 15, 3:45 PM) │
│ ✓ Shipped (Jan 16, 10 AM)  │
│ ◐ In Transit (now)          │
│ ○ Out for Delivery          │
│ ○ Delivered                 │
│                             │
│ Current Location:           │
│ Kinshasa Distribution Hub   │
│ Map preview (embedded)      │
│ [ Open Full Map ]           │
│                             │
│ Estimated Delivery:         │
│ Jan 18, 2024                │
│                             │
│ [ Message Seller ]          │
│ [ Report Issue ]            │
└─────────────────────────────┘
```

### 4. **Wallet & Payment Screens**

#### Wallet Screen

```
┌─────────────────────────────┐
│ ← | Wallet                  │
├─────────────────────────────┤
│ ┌──────────────────────────┐ │
│ │  USD Wallet              │ │
│ │  $2,450.50               │ │
│ │  Last updated: just now  │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │  CDF Wallet              │ │
│ │  500,000 FC              │ │
│ │  Last updated: 1h ago    │ │
│ └──────────────────────────┘ │
│                             │
│ Quick Actions:              │
│ [ Add Funds ] [ Withdraw ]  │
│ [ Send Money ]              │
├─────────────────────────────┤
│ Recent Transactions         │
│                             │
│ Transfer received: +$100    │
│ 2 hours ago                 │
│                             │
│ Item sold commission: -$50  │
│ 5 hours ago                 │
│                             │
│ [ View All ]                │
└─────────────────────────────┘
```

#### Add Funds

```
┌─────────────────────────────┐
│ ← | Add Funds to Wallet    │
├─────────────────────────────┤
│ Select Currency:            │
│ ○ USD  ● CDF                │
│                             │
│ Amount:                     │
│ [________] USD              │
│                             │
│ Payment Method:             │
│ ○ Credit/Debit Card         │
│ ○ Mobile Money (M-Pesa,etc)│
│ ○ Bank Transfer             │
│                             │
│ Fee: $0.00                  │
│ Total: $0.00                │
│                             │
│ ┌──────────────────────────┐ │
│ │ ☐ Save card for future   │ │
│ └──────────────────────────┘ │
│                             │
│      [ Continue Payment ]    │
│      [ Cancel ]              │
└─────────────────────────────┘
```

### 5. **Profile & Settings Screens**

#### Profile Screen

```
┌─────────────────────────────┐
│ ← | My Profile              │
├─────────────────────────────┤
│        [User Avatar]        │
│        John Doe             │
│        ⭐⭐⭐⭐⭐           │
│     john.doe@example.com    │
│        Member since 2021    │
│                             │
│ [ Edit Profile ]            │
├─────────────────────────────┤
│ Statistics:                 │
│ • Items Sold: 24            │
│ • Items Bought: 18          │
│ • Positive Rating: 98%      │
│ • Response Time: <1h        │
├─────────────────────────────┤
│ My Listings (4 items)       │
│ ┌──────────┐                │
│ │ [Image]  │ Item Name     │
│ │ $999     │ Active        │
│ └──────────┘                │
│                             │
│ [ View All Items ]          │
├─────────────────────────────┤
│ [ My Orders ] [ My Purchases]│
│ [ Help & Support ]          │
│ [ Settings ]                │
│ [ Logout ]                  │
└─────────────────────────────┘
```

#### Edit Profile

```
┌─────────────────────────────┐
│ ← | Edit Profile            │
├─────────────────────────────┤
│ Profile Picture:            │
│ [  Avatar + change  ]       │
│                             │
│ Full Name:                  │
│ [______________]            │
│                             │
│ Email:                      │
│ [______________] ✓ Verified │
│                             │
│ Phone:                      │
│ [______________] ✓ Verified │
│                             │
│ Address:                    │
│ [______________]            │
│                             │
│ Bio (max 500 chars):        │
│ [__________________        │
│  __________________        │
│  __________________]       │
│                             │
│ Location (City):            │
│ [______________]            │
│                             │
│      [ Save Changes ]        │
│      [ Cancel ]              │
└─────────────────────────────┘
```

#### Settings Screen

```
┌─────────────────────────────┐
│ ← | Settings                │
├─────────────────────────────┤
│ Account                     │
│ └─ Email Notifications  ✓  │
│ └─ Push Notifications   ✓  │
│ └─ SMS Notifications    ✗  │
│                             │
│ Privacy & Security          │
│ └─ Two-Factor Auth      ✗  │
│    [ Enable 2FA ]           │
│ └─ Privacy Level: Public    │
│    [ Manage ]               │
│ └─ Block List               │
│    [ 3 users blocked ]      │
│                             │
│ Preferences                 │
│ └─ Dark Mode            ✓  │
│ └─ Language: French         │
│    [ Change ]               │
│ └─ Currency: USD            │
│    [ Change ]               │
│                             │
│ About & Help                │
│ └─ About VintApp            │
│ └─ Terms of Service         │
│ └─ Privacy Policy           │
│ └─ FAQ                      │
│ └─ Contact Support          │
│                             │
│ [ Delete Account ]          │
└─────────────────────────────┘
```

### 6. **Admin Screens** (Mobile Admin Panel)

#### Admin Dashboard (Mobile)

```
┌─────────────────────────────┐
│ ← | Admin Dashboard         │
├─────────────────────────────┤
│ Quick Stats (Cards):        │
│ ┌──────────────────────────┐ │
│ │ Users: 2,345             │ │
│ │ +45 today                │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │ Revenue: $45,678         │ │
│ │ +2,345 USD today         │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │ Orders: 1,234            │ │
│ │ +12 pending              │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │ Wallets Pending: 8       │ │
│ │ $5,600 USD action needed │ │
│ └──────────────────────────┘ │
├─────────────────────────────┤
│ Quick Actions:              │
│ [ Approve Wallets ]         │
│ [ Review Items ]            │
│ [ View Reports ]            │
│ [ User Management ]         │
│                             │
│ Recent Activity             │
│ • New user registered       │
│ • Order shipped             │
│ • Support chat assigned     │
│ [ View All ]                │
└─────────────────────────────┘
```

#### Users Management

```
┌─────────────────────────────┐
│ ← | Users Management        │
├─────────────────────────────┤
│ [_______search___] 🔍       │
│ Filter ▼ | Sort ▼           │
├─────────────────────────────┤
│ User List:                  │
│                             │
│ ┌──────────────────────────┐ │
│ │ John Doe                 │ │
│ │ john@example.com         │ │
│ │ Role: User               │ │
│ │ Status: Active ● ▼       │ │
│ │ [ Details ] [ Actions ]  │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │ Jane Smith               │ │
│ │ jane@example.com         │ │
│ │ Role: Expert             │ │
│ │ Status: Active ● ▼       │ │
│ │ [ Details ] [ Actions ]  │ │
│ └──────────────────────────┘ │
│                             │
│ [ Load More ]               │
└─────────────────────────────┘
```

#### Admin Designation (New Feature)

```
┌─────────────────────────────┐
│ ← | Designate Admin         │
├─────────────────────────────┤
│ User: John Doe              │
│ john@example.com            │
│                             │
│ Account Stats:              │
│ • Joined: Jan 2021          │
│ • Items: 24                 │
│ • Orders: 18                │
│ • Positive Rating: 98%      │
│                             │
│ Permissions:                │
│ ☑ Manage Users              │
│ ☑ Manage Items              │
│ ☑ Manage Wallets            │
│ ☑ Manage Orders             │
│ ☑ View Analytics            │
│ ☐ Manage Admins             │
│                             │
│ Reason for Designation:     │
│ [__________________        │
│  __________________]       │
│                             │
│      [ Confirm ]             │
│      [ Cancel ]              │
└─────────────────────────────┘
```

#### Wallet Approvals

```
┌─────────────────────────────┐
│ ← | Pending Wallets (8)    │
├─────────────────────────────┤
│ ┌──────────────────────────┐ │
│ │ Jane Smith               │ │
│ │ $450.00 USD              │ │
│ │ Pending since: 2 hours   │ │
│ │                          │ │
│ │ [ Approve ] [ Reject ]   │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │ Alex Johnson             │ │
│ │ 50,000 CDF               │ │
│ │ Pending since: 5 hours   │ │
│ │                          │ │
│ │ [ Approve ] [ Reject ]   │ │
│ └──────────────────────────┘ │
│                             │
│ [ Bulk Approve All ]        │
│ [ Bulk Reject All ]         │
│                             │
│ [ Load More ]               │
└─────────────────────────────┘
```

### 7. **Support & Communication Screens**

#### Support Chat List

```
┌─────────────────────────────┐
│ ← | Support Chats       (2) │
├─────────────────────────────┤
│ Filter: [All ▼]             │
├─────────────────────────────┤
│ ┌──────────────────────────┐ │
│ │ Jane Smith               │ │
│ │ Issue with order #123    │ │
│ │ 🔴 Open - 2 hours ago   │ │
│ │ You: We'll help soon...  │ │
│ └──────────────────────────┘ │
│                             │
│ ┌──────────────────────────┐ │
│ │ John Doe                 │ │
│ │ Payment not received      │ │
│ │ 🟡 In Progress - 1 hour  │ │
│ │ Admin: Checking payment  │ │
│ └──────────────────────────┘ │
└─────────────────────────────┘
```

#### Chat Screen

```
┌─────────────────────────────┐
│ ← | Chat with Jane Smith    │
├─────────────────────────────┤
│ Status: Open since 2h       │
│                             │
│ Jane Smith: (2 hours ago)   │
│ Hello, my order didn't      │
│ arrive yet. Can you help?   │
│                             │
│          Admin: (1h ago)    │
│          Hi Jane! Let me    │
│          check the status   │
│          for you.           │
│                             │
│ Jane Smith: (45m ago)       │
│ Thank you! Order number     │
│ is ORD-2024-ABC123          │
│                             │
│          Admin: (now)       │
│          I see it was       │
│          shipped on Jan 16. │
│          Should arrive soon.│
│                             │
├─────────────────────────────┤
│ [________Message____]  [Send]│
│ [Attach] [Emoji]            │
└─────────────────────────────┘
```

---

## 🔧 Spécifications Techniques

### 1. **Intégration Backend API - Guide Complet** ⭐

#### Configuration de Base

**Fichier : `src/config.js`**

```javascript
const API_CONFIG = {
    // URL Backend - À adapter selon l'environnement
    BASE_URL: process.env.REACT_APP_API_URL || "http://localhost:8000/api",

    // Websocket pour real-time
    WEBSOCKET_URL: process.env.REACT_APP_WS_URL || "http://localhost:6001",

    // Timeouts
    REQUEST_TIMEOUT: 10000, // 10 secondes

    // Headers par défaut
    DEFAULT_HEADERS: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },

    // Version API
    API_VERSION: "v1",
};

export default API_CONFIG;
```

**Fichier : `.env.example`**

```env
# Backend Configuration
REACT_APP_API_URL=http://localhost:8000/api
REACT_APP_WS_URL=http://localhost:6001
REACT_APP_APP_NAME=VintApp Mobile
REACT_APP_VERSION=1.0.0

# Firebase Config (pour auth + notifications)
REACT_APP_FIREBASE_API_KEY=your_key
REACT_APP_FIREBASE_AUTH_DOMAIN=your_domain
REACT_APP_FIREBASE_PROJECT_ID=your_project
REACT_APP_FIREBASE_STORAGE_BUCKET=your_bucket
REACT_APP_FIREBASE_MESSAGING_SENDER_ID=your_id
REACT_APP_FIREBASE_APP_ID=your_app_id

# Environment
REACT_APP_ENV=development
```

#### Service Layer - Axios Instance

**Fichier : `src/services/api.js`**

```javascript
import axios from "axios";
import store from "../store";
import { logout, refreshToken } from "../store/slices/authSlice";
import API_CONFIG from "../config";

// Créer instance Axios
const api = axios.create({
    baseURL: API_CONFIG.BASE_URL,
    timeout: API_CONFIG.REQUEST_TIMEOUT,
    headers: API_CONFIG.DEFAULT_HEADERS,
});

// Interceptor Request - Ajouter Token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("access_token");

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        // Ajouter le device/user agent
        config.headers[
            "User-Agent"
        ] = `VintApp Mobile/${process.env.REACT_APP_VERSION}`;
        config.headers["X-Client-Type"] = "mobile-react";

        return config;
    },
    (error) => Promise.reject(error)
);

// Interceptor Response - Gérer erreurs & tokens expiré
api.interceptors.response.use(
    (response) => {
        // Logger les réponses en développement
        if (process.env.REACT_APP_ENV === "development") {
            console.log(
                `✅ ${response.config.method.toUpperCase()} ${
                    response.config.url
                }`,
                response.data
            );
        }
        return response;
    },
    async (error) => {
        const originalRequest = error.config;

        // Erreur 401 - Token expiré
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                // Appeler l'endpoint refresh-token
                const { data } = await axios.post(
                    `${API_CONFIG.BASE_URL}/auth/refresh-token`,
                    {},
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "access_token"
                            )}`,
                        },
                    }
                );

                // Sauvegarder nouveau token
                localStorage.setItem("access_token", data.access_token);

                // Réessayer requête originale
                originalRequest.headers.Authorization = `Bearer ${data.access_token}`;
                return api(originalRequest);
            } catch (refreshError) {
                // Refresh échoué - Logout utilisateur
                store.dispatch(logout());
                window.location.href = "/login";
                return Promise.reject(refreshError);
            }
        }

        // Erreur 403 - Pas de permission
        if (error.response?.status === 403) {
            console.error("❌ Access Denied:", error.response.data.message);
        }

        // Erreur 500 - Serveur
        if (error.response?.status === 500) {
            console.error("❌ Server Error:", error.response.data);
        }

        // Erreur réseau
        if (!error.response) {
            console.error("❌ Network Error - Backend indisponible");
        }

        return Promise.reject(error);
    }
);

export default api;
```

#### Service d'Authentification

**Fichier : `src/services/authService.js`**

```javascript
import api from "./api";
import store from "../store";
import { setUser, setToken } from "../store/slices/authSlice";

const authService = {
    // Login
    async login(email, password) {
        try {
            const { data } = await api.post("/auth/login", {
                email,
                password,
            });

            // Sauvegarder token
            localStorage.setItem("access_token", data.access_token);
            localStorage.setItem("user", JSON.stringify(data.user));

            // Dispatcher au Redux store
            store.dispatch(setUser(data.user));
            store.dispatch(setToken(data.access_token));

            return data;
        } catch (error) {
            throw error.response?.data || { message: "Login failed" };
        }
    },

    // Register
    async register(userData) {
        try {
            const { data } = await api.post("/auth/register", {
                name: userData.name,
                email: userData.email,
                password: userData.password,
                password_confirmation: userData.passwordConfirm,
                phone: userData.phone,
            });

            // Auto-login après registration
            localStorage.setItem("access_token", data.access_token);
            localStorage.setItem("user", JSON.stringify(data.user));

            store.dispatch(setUser(data.user));
            store.dispatch(setToken(data.access_token));

            return data;
        } catch (error) {
            throw error.response?.data || { message: "Registration failed" };
        }
    },

    // Logout
    async logout() {
        try {
            await api.post("/auth/logout");
        } catch (error) {
            console.error("Logout error:", error);
        } finally {
            // Nettoyer localStorage
            localStorage.removeItem("access_token");
            localStorage.removeItem("user");

            // Dispatcher logout au Redux
            store.dispatch({ type: "auth/logout" });

            // Rediriger vers login
            window.location.href = "/login";
        }
    },

    // Get Current User
    async getCurrentUser() {
        try {
            const { data } = await api.get("/auth/me");
            return data;
        } catch (error) {
            throw error.response?.data || { message: "Failed to fetch user" };
        }
    },

    // Verify 2FA
    async verify2FA(code) {
        try {
            const { data } = await api.post("/auth/2fa/verify", { code });
            localStorage.setItem("access_token", data.access_token);
            store.dispatch(setToken(data.access_token));
            return data;
        } catch (error) {
            throw (
                error.response?.data || { message: "2FA verification failed" }
            );
        }
    },

    // Password Reset Request
    async resetPasswordRequest(email) {
        try {
            const { data } = await api.post("/auth/password-reset-request", {
                email,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Password Reset Verify
    async resetPasswordVerify(email, code, password) {
        try {
            const { data } = await api.post("/auth/password-reset-verify", {
                email,
                code,
                password,
                password_confirmation: password,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },
};

export default authService;
```

#### Service Items

**Fichier : `src/services/itemService.js`**

```javascript
import api from "./api";

const itemService = {
    // Lister items avec filtres & pagination
    async getItems(params = {}) {
        try {
            const { data } = await api.get("/items", {
                params: {
                    page: params.page || 1,
                    per_page: params.per_page || 20,
                    search: params.search || null,
                    category_id: params.categoryId || null,
                    brand_id: params.brandId || null,
                    min_price: params.minPrice || null,
                    max_price: params.maxPrice || null,
                    condition: params.condition || null,
                    sort: params.sort || "latest",
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data || { message: "Failed to fetch items" };
        }
    },

    // Détails d'un item
    async getItemById(itemId) {
        try {
            const { data } = await api.get(`/items/${itemId}`);
            return data;
        } catch (error) {
            throw error.response?.data || { message: "Item not found" };
        }
    },

    // Créer nouvel item (avec images)
    async createItem(formData) {
        try {
            // formData doit être FormData object pour les fichiers
            const { data } = await api.post("/items", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data || { message: "Failed to create item" };
        }
    },

    // Modifier item
    async updateItem(itemId, updateData) {
        try {
            const { data } = await api.put(`/items/${itemId}`, updateData);
            return data;
        } catch (error) {
            throw error.response?.data || { message: "Failed to update item" };
        }
    },

    // Supprimer item
    async deleteItem(itemId) {
        try {
            const { data } = await api.delete(`/items/${itemId}`);
            return data;
        } catch (error) {
            throw error.response?.data || { message: "Failed to delete item" };
        }
    },

    // Recherche
    async searchItems(query) {
        try {
            const { data } = await api.get("/items/search", {
                params: { q: query },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Vérification authenticité
    async getAuthenticityInfo(itemId) {
        try {
            const { data } = await api.get(`/items/${itemId}/authenticity`);
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Demander vérification
    async requestAuthenticity(itemId) {
        try {
            const { data } = await api.post(
                `/items/${itemId}/request-authenticity`
            );
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },
};

export default itemService;
```

#### Service Commandes

**Fichier : `src/services/orderService.js`**

```javascript
import api from "./api";

const orderService = {
    // Lister mes commandes
    async getOrders(params = {}) {
        try {
            const { data } = await api.get("/orders", {
                params: {
                    page: params.page || 1,
                    per_page: params.per_page || 20,
                    status: params.status || null,
                    sort: params.sort || "latest",
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Détails commande
    async getOrderById(orderId) {
        try {
            const { data } = await api.get(`/orders/${orderId}`);
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Créer commande
    async createOrder(orderData) {
        try {
            const { data } = await api.post("/orders", {
                item_id: orderData.itemId,
                quantity: orderData.quantity,
                delivery_address_id: orderData.deliveryAddressId,
                notes: orderData.notes || null,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Tracking commande
    async getOrderTracking(orderId) {
        try {
            const { data } = await api.get(`/orders/${orderId}/tracking`);
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Confirmer réception
    async confirmDelivery(orderId, note = null) {
        try {
            const { data } = await api.post(`/orders/${orderId}/confirm`, {
                buyer_confirmation_note: note,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Annuler commande
    async cancelOrder(orderId, reason) {
        try {
            const { data } = await api.post(`/orders/${orderId}/cancel`, {
                reason,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Télécharger facture
    async downloadInvoice(orderId) {
        try {
            const { data } = await api.get(`/orders/${orderId}/invoice`, {
                responseType: "blob",
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },
};

export default orderService;
```

#### Service Wallet

**Fichier : `src/services/walletService.js`**

```javascript
import api from "./api";

const walletService = {
    // Infos wallet
    async getWallet() {
        try {
            const { data } = await api.get("/wallet");
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Balance
    async getBalance() {
        try {
            const { data } = await api.get("/wallet/balance");
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Transactions
    async getTransactions(params = {}) {
        try {
            const { data } = await api.get("/wallet/transactions", {
                params: {
                    page: params.page || 1,
                    per_page: params.per_page || 20,
                    type: params.type || null,
                    currency: params.currency || null,
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Ajouter fonds
    async addFunds(amount, currency = "USD") {
        try {
            const { data } = await api.post("/wallet/add-funds", {
                amount,
                currency,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Retirer fonds
    async withdraw(amount, currency = "USD", bankDetails) {
        try {
            const { data } = await api.post("/wallet/withdraw", {
                amount,
                currency,
                bank_account: bankDetails.accountNumber,
                bank_name: bankDetails.bankName,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Envoyer argent
    async sendMoney(recipientEmail, amount, currency = "USD") {
        try {
            const { data } = await api.post("/wallet/send-money", {
                recipient_email: recipientEmail,
                amount,
                currency,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },
};

export default walletService;
```

#### Service Support/Chat

**Fichier : `src/services/supportService.js`**

```javascript
import api from "./api";

const supportService = {
    // Lister mes chats
    async getChats(params = {}) {
        try {
            const { data } = await api.get("/support/chats", {
                params: {
                    page: params.page || 1,
                    status: params.status || null,
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Détails chat
    async getChatById(chatId) {
        try {
            const { data } = await api.get(`/support/chats/${chatId}`);
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Créer chat
    async createChat(subject, message) {
        try {
            const { data } = await api.post("/support/chats", {
                subject,
                initial_message: message,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Envoyer message
    async sendMessage(chatId, message, attachments = []) {
        try {
            const formData = new FormData();
            formData.append("message", message);

            // Ajouter attachments si présents
            attachments.forEach((file, index) => {
                formData.append(`attachments[${index}]`, file);
            });

            const { data } = await api.post(
                `/support/chats/${chatId}/messages`,
                formData,
                {
                    headers: { "Content-Type": "multipart/form-data" },
                }
            );
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Marquer comme lu
    async markAsRead(chatId) {
        try {
            const { data } = await api.post(
                `/support/chats/${chatId}/mark-read`
            );
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // Fermer chat
    async closeChat(chatId) {
        try {
            const { data } = await api.post(`/support/chats/${chatId}/close`);
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },
};

export default supportService;
```

#### Service Admin

**Fichier : `src/services/adminService.js`**

```javascript
import api from "./api";

const adminService = {
    // Dashboard stats
    async getDashboardStats() {
        try {
            const { data } = await api.get("/admin/dashboard");
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // GESTION UTILISATEURS
    async getUsers(params = {}) {
        try {
            const { data } = await api.get("/admin/users", {
                params: {
                    page: params.page || 1,
                    per_page: params.per_page || 20,
                    search: params.search || null,
                    role: params.role || null,
                    status: params.status || null,
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async getUserById(userId) {
        try {
            const { data } = await api.get(`/admin/users/${userId}`);
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // ⭐ GESTION DES ADMINS (NEW)
    async getAdminCandidates(params = {}) {
        try {
            const { data } = await api.get("/admin/admins/candidates", {
                params: {
                    page: params.page || 1,
                    search: params.search || null,
                    status: params.status || null,
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async getAdmins(params = {}) {
        try {
            const { data } = await api.get("/admin/admins", {
                params: {
                    page: params.page || 1,
                    search: params.search || null,
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async designateAdmin(userId, permissions = [], reason = null) {
        try {
            const { data } = await api.post(
                `/admin/admins/designate/${userId}`,
                {
                    permissions,
                    reason,
                }
            );
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async revokeAdmin(userId, reason) {
        try {
            const { data } = await api.post(`/admin/admins/${userId}/revoke`, {
                reason,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // GESTION WALLETS
    async getPendingWallets(params = {}) {
        try {
            const { data } = await api.get("/admin/wallets/pending", {
                params: {
                    page: params.page || 1,
                    per_page: params.per_page || 20,
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async approveWallet(walletId) {
        try {
            const { data } = await api.post(
                `/admin/wallets/${walletId}/approve`
            );
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async rejectWallet(walletId, reason) {
        try {
            const { data } = await api.post(
                `/admin/wallets/${walletId}/reject`,
                {
                    reason,
                }
            );
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    // GESTION ITEMS
    async getPendingItems(params = {}) {
        try {
            const { data } = await api.get("/admin/items/pending", {
                params: {
                    page: params.page || 1,
                    per_page: params.per_page || 20,
                },
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async approveItem(itemId) {
        try {
            const { data } = await api.post(`/admin/items/${itemId}/approve`);
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },

    async rejectItem(itemId, reason) {
        try {
            const { data } = await api.post(`/admin/items/${itemId}/reject`, {
                reason,
            });
            return data;
        } catch (error) {
            throw error.response?.data;
        }
    },
};

export default adminService;
```

#### Real-Time avec WebSocket (Socket.io)

**Fichier : `src/services/socketService.js`**

```javascript
import io from "socket.io-client";
import API_CONFIG from "../config";

class SocketService {
    constructor() {
        this.socket = null;
    }

    // Connecter au serveur
    connect(userId) {
        this.socket = io(API_CONFIG.WEBSOCKET_URL, {
            auth: {
                token: localStorage.getItem("access_token"),
                userId: userId,
            },
            reconnection: true,
            reconnectionDelay: 1000,
            reconnectionDelayMax: 5000,
            reconnectionAttempts: 5,
        });

        // Événements de connexion
        this.socket.on("connect", () => {
            console.log("✅ WebSocket Connected");
        });

        this.socket.on("disconnect", () => {
            console.log("❌ WebSocket Disconnected");
        });

        this.socket.on("connect_error", (error) => {
            console.error("WebSocket Error:", error);
        });
    }

    // Notifications en temps réel
    onNotification(callback) {
        this.socket.on("notification", callback);
    }

    // Mise à jour commande en temps réel
    onOrderUpdate(orderId, callback) {
        this.socket.on(`order-${orderId}-updated`, callback);
    }

    // Messages chat
    onChatMessage(chatId, callback) {
        this.socket.on(`chat-${chatId}-message`, callback);
    }

    // Admin: mise à jour wallet
    onWalletUpdate(callback) {
        this.socket.on("wallet-updated", callback);
    }

    // Typiquement disponible
    onTyping(chatId, callback) {
        this.socket.on(`chat-${chatId}-typing`, callback);
    }

    // Émettre événement
    emit(event, data) {
        if (this.socket) {
            this.socket.emit(event, data);
        }
    }

    // Déconnecter
    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
}

export default new SocketService();
```

#### Hook Personnalisé - useApi

**Fichier : `src/hooks/useApi.js`**

```javascript
import { useState, useCallback } from "react";

// Hook réutilisable pour les appels API
export const useApi = (apiFunction) => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const execute = useCallback(
        async (...args) => {
            try {
                setLoading(true);
                setError(null);
                const result = await apiFunction(...args);
                setData(result);
                return result;
            } catch (err) {
                setError(err);
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [apiFunction]
    );

    return { data, loading, error, execute };
};
```

#### Exemple d'Utilisation dans un Composant

**Fichier : `src/components/marketplace/HomeScreen.jsx`**

```javascript
import React, { useEffect } from "react";
import { useApi } from "../../hooks/useApi";
import itemService from "../../services/itemService";
import LoadingSpinner from "../common/LoadingSpinner";
import ItemCard from "./ItemCard";

export default function HomeScreen() {
    const {
        data: items,
        loading,
        error,
        execute,
    } = useApi(itemService.getItems);

    useEffect(() => {
        // Charger les items au montage
        execute({ page: 1, per_page: 20 });
    }, [execute]);

    if (loading) return <LoadingSpinner />;
    if (error)
        return <div className="text-red-500">Erreur: {error.message}</div>;

    return (
        <div className="grid grid-cols-2 gap-4">
            {items?.data?.map((item) => (
                <ItemCard key={item.id} item={item} />
            ))}
        </div>
    );
}
```

---

### 2. **API Endpoints à Créer/Adapter**

Les endpoints Laravel existants seront réutilisés. Les principaux endpoints:

```
Authentication:
POST   /api/auth/login
POST   /api/auth/register
POST   /api/auth/logout
POST   /api/auth/refresh-token
POST   /api/auth/2fa/verify
GET    /api/auth/me

Items:
GET    /api/items (avec filtres, pagination)
GET    /api/items/{id}
POST   /api/items (créer)
PUT    /api/items/{id} (modifier)
DELETE /api/items/{id}
GET    /api/items/search
GET    /api/items/{id}/authenticity

Orders:
GET    /api/orders
GET    /api/orders/{id}
POST   /api/orders
GET    /api/orders/{id}/tracking
POST   /api/orders/{id}/confirm

Wallet:
GET    /api/wallet
GET    /api/wallet/transactions
POST   /api/wallet/add-funds
POST   /api/wallet/withdraw
GET    /api/wallet/balance

Users:
GET    /api/users/{id}
PUT    /api/users/{id}
GET    /api/users/{id}/reviews

Admin:
GET    /api/admin/dashboard
GET    /api/admin/users (avec filtres)
GET    /api/admin/users/{id}
POST   /api/admin/users/{id}/designate-admin
DELETE /api/admin/users/{id}/revoke-admin
GET    /api/admin/wallets/pending
POST   /api/admin/wallets/{id}/approve
POST   /api/admin/wallets/{id}/reject
[... plus 50+ endpoints admin ...]

WebSocket (Socket.io):
- real-time notifications
- live chat messaging
- order status updates
- online users tracking
```

### 2. **State Management (Redux/Context)**

```javascript
// Redux Slices Structure
store/
├── slices/
│   ├── authSlice.js
│   │   ├── state: { user, token, isAuthenticated, loading, error }
│   │   ├── actions: login, logout, register, refreshToken, setUser
│   │   └── selectors: selectUser, selectIsAuthenticated
│   │
│   ├── itemsSlice.js
│   │   ├── state: { items, totalItems, currentPage, filters, loading }
│   │   ├── actions: fetchItems, setFilters, search, setSort
│   │   └── selectors: selectFilteredItems, selectTotalCount
│   │
│   ├── cartSlice.js
│   │   ├── state: { items, total, itemCount }
│   │   ├── actions: addItem, removeItem, updateQuantity, clearCart
│   │   └── selectors: selectCartTotal, selectItemCount
│   │
│   ├── ordersSlice.js
│   │   ├── state: { orders, currentOrder, filters, pagination }
│   │   ├── actions: fetchOrders, fetchOrderDetail, createOrder
│   │   └── selectors: selectUserOrders, selectOrderById
│   │
│   ├── walletSlice.js
│   │   ├── state: { balanceUSD, balanceCDF, transactions, history }
│   │   ├── actions: fetchBalance, addFunds, withdraw, fetchTransactions
│   │   └── selectors: selectTotalBalance, selectTransactions
│   │
│   ├── adminSlice.js
│   │   ├── state: { users, items, orders, wallets, filters }
│   │   ├── actions: fetchUsers, fetchPendingWallets, approveWallet
│   │   └── selectors: selectAdminStats, selectPendingWallets
│   │
│   ├── notificationSlice.js
│   │   ├── state: { notifications, unreadCount }
│   │   ├── actions: addNotification, markAsRead, clearNotifications
│   │   └── selectors: selectUnreadCount
│   │
│   └── uiSlice.js
│       ├── state: { isDarkMode, sidebarOpen, activeTab }
│       ├── actions: toggleDarkMode, setSidebarOpen
│       └── selectors: selectDarkMode
```

### 3. **Authentication & Security**

```javascript
// Token Management
- Store JWT token in secure storage (localStorage ou sessionStorage)
- Auto-refresh token before expiry
- Handle 401 responses globally
- Firebase Auth integration for social login
- 2FA verification flow

// API Security
- CORS configured
- CSRF protection
- Rate limiting
- Input validation & sanitization
- Encrypted sensitive data
```

### 4. **Offline Capabilities (PWA)**

```javascript
// Service Worker
- Cache API responses
- Background sync for failed requests
- Offline page fallback
- Push notifications
- App install prompt

// Storage
- LocalStorage: User preferences, cached data
- IndexedDB: Large data sets (items, orders history)
- SessionStorage: Temporary session data
```

### 5. **Performance Optimizations**

```javascript
// Image Optimization
- Lazy loading (Intersection Observer)
- Image compression
- Responsive images (srcset)
- WebP format with fallbacks
- CDN integration

// Code Splitting
- Route-based lazy loading
- Component lazy loading
- Separate bundles for admin

// Caching Strategies
- Cache-first for static assets
- Network-first for API calls
- Stale-while-revalidate for UI data

// Bundle Size
- Tree-shaking unused code
- Minification & compression
- Removal of unused dependencies
```

### 6. **Mobile-Specific Considerations**

```javascript
// Touch & Gestures
- Swipe navigation between tabs
- Long-press context menus
- Double-tap to zoom
- Pull-to-refresh

// Navigation
- Bottom tab navigation (mobile convention)
- Back button handling
- Navigation history management
- Deep linking support

// Device Features
- Camera access (item photos, verification)
- Location services (delivery tracking)
- Notifications (push, in-app)
- Biometric auth (fingerprint, face)

// Network Handling
- Detect offline/online status
- Retry failed requests
- Data sync when back online
- Bandwidth optimization
```

---

## 📐 Roadmap de Développement

### Phase 1: Setup & Foundation (Semaine 1-2)

-   [ ] Project setup (React, Vite, Tailwind)
-   [ ] Environment configuration
-   [ ] Redux store architecture
-   [ ] API service layer
-   [ ] Authentication flow
-   [ ] Base layouts & components

### Phase 2: Core Features (Semaine 3-6)

-   [ ] Marketplace (browse, search, filter)
-   [ ] Item details & images
-   [ ] Shopping cart
-   [ ] Checkout flow
-   [ ] Payment integration
-   [ ] Order management
-   [ ] Wallet management

### Phase 3: User Features (Semaine 7-9)

-   [ ] User profile & settings
-   [ ] My items listing
-   [ ] My orders history
-   [ ] Notifications system
-   [ ] Support chat
-   [ ] Reviews & ratings
-   [ ] Favorites/Saved items

### Phase 4: Seller Features (Semaine 10-12)

-   [ ] Sell item flow
-   [ ] Item management
-   [ ] Sales dashboard
-   [ ] Seller ratings & reviews
-   [ ] Messaging with buyers

### Phase 5: Admin Panel (Semaine 13-15)

-   [ ] Admin dashboard
-   [ ] User management
-   [ ] Wallet management
-   [ ] Order management
-   [ ] Item moderation
-   [ ] Reports & analytics
-   [ ] Settings management
-   [ ] Admin designation system (NEW)

### Phase 6: Advanced Features (Semaine 16-18)

-   [ ] Expert verification system
-   [ ] Authenticity checking
-   [ ] Expert management
-   [ ] Real-time notifications
-   [ ] Live chat (WebSocket)
-   [ ] Push notifications (FCM)
-   [ ] Affiliate system

### Phase 7: Testing & Polish (Semaine 19-21)

-   [ ] Unit tests
-   [ ] Integration tests
-   [ ] E2E tests
-   [ ] Performance testing
-   [ ] Security audit
-   [ ] Bug fixes
-   [ ] UI/UX polishing

### Phase 8: Deployment & Launch (Semaine 22)

-   [ ] Build optimization
-   [ ] App store submission (iOS/Android)
-   [ ] Beta testing
-   [ ] Production deployment
-   [ ] Monitoring setup
-   [ ] User feedback collection

---

## 📚 Documentation à Créer

```
documentation/
├── API.md (endpoints détails)
├── AUTHENTICATION.md (auth flows)
├── STATE_MANAGEMENT.md (Redux guide)
├── COMPONENTS.md (component library)
├── STYLES.md (design system)
├── MOBILE_PATTERNS.md (patterns mobiles)
├── PERFORMANCE.md (optimizations)
├── DEPLOYMENT.md (deployment guide)
├── TROUBLESHOOTING.md (common issues)
└── CONTRIBUTING.md (contribution guide)
```

---

## 🎨 Design Tokens

```css
/* Colors */
--primary: #3b82f6;
--primary-light: #60a5fa;
--primary-dark: #1e40af;
--secondary: #10b981;
--success: #10b981;
--warning: #f59e0b;
--danger: #ef4444;
--info: #0ea5e9;

/* Spacing */
--xs: 4px;
--sm: 8px;
--md: 16px;
--lg: 24px;
--xl: 32px;
--2xl: 48px;

/* Typography */
--font-sans: "Inter", system-ui;
--text-xs: 12px;
--text-sm: 14px;
--text-base: 16px;
--text-lg: 18px;
--text-xl: 20px;
--text-2xl: 24px;

/* Shadows */
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);

/* Border Radius */
--radius-sm: 4px;
--radius-md: 8px;
--radius-lg: 12px;
--radius-xl: 16px;

/* Transitions */
--duration-fast: 150ms;
--duration-normal: 300ms;
--duration-slow: 500ms;
```

---

## ⚡ Guide de Démarrage Rapide

### Initialiser le Projet

```bash
# Créer projet Vite React
npm create vite@latest vintapp-mobile -- --template react
cd vintapp-mobile

# Installer dépendances
npm install

# Installer dépendances essentielles
npm install axios redux react-redux @reduxjs/toolkit
npm install react-router-dom tailwindcss postcss autoprefixer
npm install socket.io-client @heroicons/react
npm install date-fns react-hot-toast zustand

# Configurer Tailwind
npx tailwindcss init -p

# Lancer en développement
npm run dev
```

### Structure Initiale à Créer

```bash
mkdir -p src/{components/{common,auth,marketplace,admin},services,store/slices,hooks,context,utils,styles}
touch src/config.js
touch src/services/api.js
touch src/store/index.js
```

### Configuration Environment

Créer `.env.local` :

```env
REACT_APP_API_URL=http://localhost:8000/api
REACT_APP_WS_URL=http://localhost:6001
REACT_APP_ENV=development
```

---

## 🔐 Sécurité & Bonnes Pratiques

### Protection des Données Sensibles

```javascript
// ❌ À ÉVITER
const token = localStorage.getItem("access_token"); // Vulnérable XSS
const password = "user_password"; // Jamais stocker le mot de passe

// ✅ À FAIRE
const token = sessionStorage.getItem("access_token"); // Plus sécurisé
// Utiliser Token Bearer dans Authorization header
// Chiffrer les données sensibles avant stockage
```

### CSRF Protection

```javascript
// Le backend Laravel envoie token CSRF
api.interceptors.request.use((config) => {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;
    if (csrfToken) {
        config.headers["X-CSRF-TOKEN"] = csrfToken;
    }
    return config;
});
```

### Rate Limiting

```javascript
// Implémenter throttle pour éviter abus API
import { throttle } from "lodash";

const handleSearch = throttle((query) => {
    itemService.searchItems(query);
}, 1000); // Appeler max 1x/seconde
```

### Validation & Sanitization

```javascript
// Valider TOUJOURS côté client ET serveur
const errors = validateForm(formData, {
    email: { required: true, email: true },
    password: { required: true, minLength: 8 },
    phone: { required: true, pattern: PHONE_REGEX },
});

// Sanitizer HTML
import DOMPurify from "dompurify";
const cleanHTML = DOMPurify.sanitize(userInput);
```

---

## 📊 Monitoring & Debugging

### Sentry Setup

```javascript
// src/main.jsx
import * as Sentry from "@sentry/react";

Sentry.init({
    dsn: import.meta.env.VITE_SENTRY_DSN,
    environment: import.meta.env.VITE_ENV,
    tracesSampleRate: 1.0,
    release: import.meta.env.VITE_APP_VERSION,
});

const SentryRoutes = Sentry.withSentryRouting(Routes);
```

### Console Logging Strategy

```javascript
// Development
console.log("Request:", method, url); // Debug infos
console.group("API Response"); // Grouper logs
console.table(data); // Afficher données
console.groupEnd();

// Production
logger.error("User action failed", { userId, action, error });
Sentry.captureException(error); // Envoyer à monitoring
```

---

## 🚀 Déploiement

### Build Optimization

```bash
# Build production
npm run build

# Analyser bundle size
npm install -D rollup-plugin-visualizer
# Voir rapport dans dist/stats.html
```

### Vercel Deployment

```bash
# Installer Vercel CLI
npm i -g vercel

# Déployer
vercel

# Environnements
vercel env add REACT_APP_API_URL
vercel env add REACT_APP_WS_URL
```

### Docker Setup

**Fichier : `Dockerfile`**

```dockerfile
FROM node:18-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

---

## 💡 Tips & Tricks

### Performance Tips

```javascript
// 1. Lazy Load Components
const AdminPanel = lazy(() => import("./AdminPanel"));

// 2. Memoize Components
const ItemCard = memo(({ item }) => <div>{item.name}</div>);

// 3. Use React Query for Caching
const { data } = useQuery("items", () => itemService.getItems(), {
    staleTime: 5 * 60 * 1000, // Cache 5 minutes
});

// 4. Virtualize Long Lists
import { FixedSizeList } from "react-window";

// 5. Code Split Routes
const AdminRoutes = lazy(() => import("./routes/admin"));
```

### Mobile Best Practices

```javascript
// Touch-friendly buttons (min 44x44px)
<button className="px-4 py-3 min-h-11 min-w-11">
  Touch me!
</button>

// Safe area for notches
@supports(padding: max(0px)) {
  body {
    padding-left: max(12px, env(safe-area-inset-left));
    padding-right: max(12px, env(safe-area-inset-right));
  }
}

// Meta viewport tag
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
```

---

## 🔗 Liaison Complète Avec Backend

### Flux Complet - Authentication Example

```
1. User inputs credentials
   ↓
2. Frontend validates locally
   ↓
3. POST /api/auth/login with email + password
   ↓
4. Backend validates credentials
   ↓
5. Backend generates JWT token
   ↓
6. Frontend stores token (sessionStorage)
   ↓
7. Token added to Authorization header for all requests
   ↓
8. API interceptor checks response for 401
   ↓
9. If 401: POST /api/auth/refresh-token
   ↓
10. Backend returns new token
    ↓
11. Retry original request with new token
```

### Flux Complet - Order Creation

```
1. User selects item, adds to cart
   ↓
2. Frontend stores in Redux cart slice
   ↓
3. User proceeds to checkout
   ↓
4. Validate address & payment method locally
   ↓
5. POST /api/orders with item_id, quantity, delivery_address_id
   ↓
6. Backend validates inventory, user wallet
   ↓
7. Backend creates order, deducts wallet balance
   ↓
8. Frontend receives order confirmation
   ↓
9. Frontend subscribes to order updates via WebSocket
    socket.on('order-{id}-updated', handleOrderUpdate)
   ↓
10. User can track in real-time
    - Paid → Packed → Shipped → Delivered → Confirmed
```

---

## 📱 Checklist de Lancement

-   [ ] Backend Laravel rodé et API endpoints testés
-   [ ] Configurations CORS correctes
-   [ ] Authentication (JWT + Refresh token) working
-   [ ] WebSocket real-time events setup
-   [ ] Firebase/Social login configured
-   [ ] Images stockées sur CDN
-   [ ] Environment variables configurées (.env.production)
-   [ ] Error handling & logging en place
-   [ ] SSL/HTTPS activé
-   [ ] Rate limiting en place
-   [ ] Tests unitaires & intégration >80%
-   [ ] E2E tests pour critical paths
-   [ ] Performance budget respecté (<3MB bundle)
-   [ ] Accessibility WCAG 2.1 AA
-   [ ] Mobile responsive testé sur devices réels
-   [ ] PWA working (manifest.json, service worker)
-   [ ] Sentry monitoring actif
-   [ ] Analytics (Google Analytics, Mixpanel)
-   [ ] Backup & recovery plan
-   [ ] Security audit complété

---

## 🆘 Troubleshooting Courants

### CORS Error

```
❌ Access to XMLHttpRequest has been blocked by CORS policy

✅ Solution:
1. Vérifier config/cors.php (origins, allowed_methods)
2. Vérifier les headers (Authorization, Content-Type)
3. Test avec curl: curl -H "Origin: http://localhost:3000" http://localhost:8000/api/items
```

### Token Expired

```
❌ 401 Unauthorized

✅ Solution:
1. Vérifier token expiré (jwt.io pour décoder)
2. Vérifier refresh token endpoint working
3. Vérifier localStorage/sessionStorage
4. Force logout si token refresh échoue
```

### Images Not Loading

```
❌ 404 Not Found ou CORS error

✅ Solution:
1. Vérifier URL complète (BASE_URL + image path)
2. Vérifier fichier existe sur serveur
3. Vérifier permissions fichier
4. Utiliser CDN pour images
```

### WebSocket Connection Failed

```
❌ Cannot connect to WebSocket

✅ Solution:
1. Vérifier WebSocket URL correct
2. Vérifier port 6001 ouvert
3. Vérifier auth token valide
4. Check firewall rules
```

---

**Version**: 2.0  
**Dernière mise à jour**: 12 Décembre 2024  
**Auteur**: Architecture VintApp Mobile - Full Backend Integration
