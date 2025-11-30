# 🔄 Navigation Skeleton Manager - Guide d'utilisation

## 📖 Vue d'ensemble

Le **Navigation Skeleton Manager** affiche automatiquement un skeleton lors de la navigation entre les pages de votre application, créant une expérience utilisateur fluide et professionnelle.

## ✨ Caractéristiques

-   ✅ **Interception automatique** des clics sur tous les liens
-   ✅ **Détection intelligente** du type de page destination
-   ✅ **6 types de skeleton** : Product Grid, Product Detail, Dashboard, List, Profile, Generic
-   ✅ **Transition fluide** avec temps minimum d'affichage
-   ✅ **Configuration flexible** avec patterns d'exclusion
-   ✅ **Scroll to top** automatique lors de la navigation
-   ✅ **Support dark mode** natif

## 🚀 Installation

Le système est **automatiquement activé** sur toutes les pages qui incluent `app.blade.php`.

### Scripts chargés (dans l'ordre)

```html
<!-- 1. Content Visibility Manager -->
<script src="{{ asset('js/content-visibility.js') }}"></script>

<!-- 2. Page Skeleton Loader -->
<script src="{{ asset('js/page-skeleton.js') }}"></script>

<!-- 3. Navigation Skeleton Manager -->
<script src="{{ asset('js/navigation-skeleton.js') }}"></script>
```

## 🎯 Utilisation automatique

### Comportement par défaut

Tous les liens **internes** déclenchent automatiquement un skeleton :

```blade
<!-- ✅ Affiche un skeleton automatiquement -->
<a href="/items">Voir les produits</a>
<a href="/items/123">Détail produit</a>
<a href="/dashboard">Tableau de bord</a>
```

### Détection du type de skeleton

Le système détecte automatiquement le type de skeleton basé sur l'URL :

| URL                                      | Skeleton affiché                          |
| ---------------------------------------- | ----------------------------------------- |
| `/items`                                 | Product Grid (12 cards)                   |
| `/items/{id}`                            | Product Detail (galerie + info)           |
| `/dashboard`                             | Dashboard (stats + chart)                 |
| `/orders`, `/messages`, `/notifications` | List (10 items)                           |
| `/profile`, `/settings`                  | Profile (avatar + tabs + cards)           |
| Autres                                   | Generic (header + content grid + sidebar) |

## 🎨 Personnalisation du skeleton

### Méthode 1 : Attribut data-skeleton-type

Forcez un type de skeleton spécifique :

```blade
<a href="/custom-page" data-skeleton-type="product-grid">
    Lien avec skeleton grid
</a>

<a href="/search" data-skeleton-type="list">
    Lien avec skeleton liste
</a>

<a href="/settings" data-skeleton-type="profile">
    Lien avec skeleton profil
</a>
```

### Méthode 2 : Classes CSS

Le système détecte aussi les classes :

```blade
<a href="/product/123" class="product-link">
    <!-- Affichera product-detail -->
</a>

<a href="/category/electronics" class="category-link">
    <!-- Affichera product-grid -->
</a>
```

### Types de skeleton disponibles

1. **product-grid** : Grille de 12 cards produits
2. **product-detail** : Galerie images + informations
3. **dashboard** : 4 stats + graphique + tableau
4. **list** : Liste de 10 items avec avatars
5. **profile** : Avatar + tabs + 6 cards
6. **generic** : Layout standard avec sidebar

## 🚫 Désactiver le skeleton

### Pour un lien spécifique

```blade
<!-- Ne déclenche PAS de skeleton -->
<a href="/about" data-no-skeleton>À propos</a>

<!-- Lien externe (automatiquement exclu) -->
<a href="https://external.com" target="_blank">Site externe</a>

<!-- Téléchargement (automatiquement exclu) -->
<a href="/file.pdf" download>Télécharger</a>
```

### Patterns exclus par défaut

```javascript
excludePatterns: [
    "/logout",
    "/login",
    "/register",
    "#", // Liens d'ancrage
];
```

### Ajouter des patterns d'exclusion

```javascript
// Exclure dynamiquement
window.navigationSkeletonManager.addExcludePattern("/api/");
window.navigationSkeletonManager.addExcludePattern(/^\/admin\//);
```

### Désactiver temporairement

```javascript
// Désactiver
window.navigationSkeletonManager.disable();

// Effectuer des actions...

// Réactiver
window.navigationSkeletonManager.enable();
```

## ⚙️ Configuration avancée

### Options par défaut

```javascript
{
    enabledSelectors: 'a[href]:not([target="_blank"]):not([data-no-skeleton])',
    excludePatterns: ['/logout', '/login', '/register', '#'],
    minDisplayTime: 300,    // Affichage minimum 300ms
    maxWaitTime: 5000,      // Timeout maximum 5s
    detectPageType: true    // Détection auto du type
}
```

### Personnalisation globale

```javascript
// Dans votre JavaScript personnalisé
document.addEventListener("DOMContentLoaded", () => {
    if (window.navigationSkeletonManager) {
        // Modifier le temps minimum
        window.navigationSkeletonManager.options.minDisplayTime = 500;

        // Ajouter des exclusions
        window.navigationSkeletonManager.addExcludePattern("/search");
    }
});
```

## 📱 Exemples d'intégration

### Navigation principale

```blade
<nav class="navbar">
    <!-- ✅ Tous ces liens déclenchent le skeleton approprié -->
    <a href="/">Accueil</a>
    <a href="/items">Produits</a>
    <a href="/dashboard">Dashboard</a>
    <a href="/orders">Commandes</a>
    <a href="/profile">Profil</a>

    <!-- ❌ Celui-ci est exclu -->
    <a href="/logout" data-no-skeleton>Déconnexion</a>
</nav>
```

### Cards produits

```blade
@foreach($items as $item)
    <div class="product-card">
        <!-- ✅ Affiche product-detail skeleton -->
        <a href="/items/{{ $item->id }}" class="product-link">
            <img src="{{ $item->image }}" alt="{{ $item->name }}">
            <h3>{{ $item->name }}</h3>
            <p>{{ $item->price }}</p>
        </a>
    </div>
@endforeach
```

### Boutons d'action

```blade
<!-- Skeleton dashboard -->
<a href="/dashboard"
   class="btn btn-primary"
   data-skeleton-type="dashboard">
    Voir mes statistiques
</a>

<!-- Skeleton product-grid -->
<a href="/search?q=laptop"
   class="btn btn-secondary"
   data-skeleton-type="product-grid">
    Rechercher
</a>

<!-- Pas de skeleton -->
<form action="/logout" method="POST">
    <button type="submit" data-no-skeleton>
        Déconnexion
    </button>
</form>
```

## 🎭 Templates de skeleton

### Product Grid

```
┌─────────────────────────────────────┐
│  [Titre]              [Bouton]      │
│  ┌───────────────────────────────┐  │
│  │  Barre de recherche           │  │
│  └───────────────────────────────┘  │
│  ┌───┐ ┌───┐ ┌───┐ ┌───┐          │
│  │ 1 │ │ 2 │ │ 3 │ │ 4 │          │
│  └───┘ └───┘ └───┘ └───┘          │
│  ┌───┐ ┌───┐ ┌───┐ ┌───┐          │
│  │ 5 │ │ 6 │ │ 7 │ │ 8 │          │
│  └───┘ └───┘ └───┘ └───┘          │
│  ┌───┐ ┌───┐ ┌───┐ ┌───┐          │
│  │ 9 │ │10 │ │11 │ │12 │          │
│  └───┘ └───┘ └───┘ └───┘          │
└─────────────────────────────────────┘
```

### Product Detail

```
┌─────────────────────────────────────┐
│  ┌──────────┐  ┌─────────────────┐  │
│  │          │  │ Titre           │  │
│  │  Image   │  │ Description     │  │
│  │  Grande  │  │ Prix           │  │
│  │          │  │ Tags           │  │
│  └──────────┘  │ Boutons        │  │
│  [1][2][3][4]  └─────────────────┘  │
└─────────────────────────────────────┘
```

### Dashboard

```
┌─────────────────────────────────────┐
│  ┌────┐ ┌────┐ ┌────┐ ┌────┐       │
│  │Stat│ │Stat│ │Stat│ │Stat│       │
│  └────┘ └────┘ └────┘ └────┘       │
│  ┌───────────────────────────────┐  │
│  │         Graphique             │  │
│  └───────────────────────────────┘  │
│  ┌───────────────────────────────┐  │
│  │         Tableau               │  │
│  └───────────────────────────────┘  │
└─────────────────────────────────────┘
```

### Profile

```
┌─────────────────────────────────────┐
│  ┌─┐ Nom utilisateur                │
│  │█│ Email                          │
│  └─┘                                │
│  [Tab1][Tab2][Tab3][Tab4]           │
│  ┌─────────┐ ┌─────────┐            │
│  │ Card 1  │ │ Card 2  │            │
│  └─────────┘ └─────────┘            │
│  ┌─────────┐ ┌─────────┐            │
│  │ Card 3  │ │ Card 4  │            │
│  └─────────┘ └─────────┘            │
└─────────────────────────────────────┘
```

## 🔧 API JavaScript

### Propriétés globales

```javascript
// Instance globale
window.navigationSkeletonManager;

// Classes
window.NavigationSkeletonManager;
window.PageSkeletonLoader;
```

### Méthodes

```javascript
// Désactiver/Activer
navigationSkeletonManager.disable();
navigationSkeletonManager.enable();

// Ajouter exclusion
navigationSkeletonManager.addExcludePattern("/api/");

// Naviguer manuellement avec skeleton
navigationSkeletonManager.navigateWithSkeleton("/items", linkElement);
```

### Événements

```javascript
// Quand le skeleton se cache
document.addEventListener("skeletonHidden", () => {
    console.log("Skeleton caché, contenu visible");
});
```

## 🐛 Dépannage

### Le skeleton ne s'affiche pas

1. Vérifiez que les scripts sont chargés :

```javascript
console.log(window.navigationSkeletonManager);
// Devrait afficher l'instance
```

2. Vérifiez que le lien n'est pas exclu :

```blade
<!-- ❌ Pas de skeleton -->
<a href="/logout">Logout</a>

<!-- ✅ Avec skeleton -->
<a href="/logout" data-skeleton-type="generic">Logout</a>
```

3. Vérifiez la console pour les erreurs

### Le skeleton reste affiché

Le système a un timeout de 5 secondes maximum. Si le skeleton reste :

-   Vérifiez que la navigation fonctionne
-   Ouvrez la console pour voir les erreurs

### Flash de contenu

Si vous voyez le contenu avant le skeleton :

```javascript
// Augmentez le temps minimum
navigationSkeletonManager.options.minDisplayTime = 500;
```

## ⚡ Performance

### Impact sur les métriques

**Sans skeleton de navigation :**

-   Impression de lenteur ❌
-   Page blanche pendant le chargement
-   Expérience utilisateur pauvre

**Avec skeleton de navigation :**

-   Feedback immédiat ✅
-   Perception de rapidité
-   Expérience fluide comme une SPA

### Optimisations

1. **Scripts chargés sans defer** : Skeleton disponible immédiatement
2. **Temps minimum optimisé** : 300ms évite les flashs
3. **Scroll to top** : Navigation plus claire
4. **Détection intelligente** : Bon skeleton pour chaque page

## 📊 Statistiques

```javascript
// Temps d'affichage du skeleton
const startTime = Date.now();
// ... navigation ...
const displayTime = Date.now() - startTime;
console.log(`Skeleton affiché pendant ${displayTime}ms`);
```

## 🎨 Exemples CSS personnalisés

### Skeleton personnalisé pour une page spécifique

```javascript
// Dans votre page
document.addEventListener("DOMContentLoaded", () => {
    const link = document.querySelector("#special-link");
    link.addEventListener("click", (e) => {
        e.preventDefault();

        const skeleton = new PageSkeletonLoader();
        skeleton.showCustom(`
            <div class="custom-skeleton">
                <!-- Votre HTML -->
            </div>
        `);

        setTimeout(() => {
            window.location.href = link.href;
        }, 300);
    });
});
```

## 🌟 Bonnes pratiques

### ✅ À faire

1. **Utilisez data-skeleton-type** pour les pages importantes

```blade
<a href="/checkout" data-skeleton-type="generic">Passer commande</a>
```

2. **Excluez les actions critiques**

```blade
<a href="/delete" data-no-skeleton>Supprimer</a>
```

3. **Testez sur connexion lente**

```
Chrome DevTools > Network > Slow 3G
```

### ❌ À éviter

1. Ne pas mettre de skeleton sur TOUS les liens

```blade
<!-- ❌ Inutile pour les modales -->
<a href="#modal" data-skeleton-type="generic">Ouvrir</a>

<!-- ✅ Mieux -->
<a href="#modal" data-no-skeleton>Ouvrir</a>
```

2. Ne pas avoir un skeleton trop différent du contenu réel

3. Ne pas surcharger avec trop d'éléments dans le skeleton

## 📚 Ressources

-   **Fichier** : `public/js/navigation-skeleton.js`
-   **Dépendances** : `page-skeleton.js`, `content-visibility.js`
-   **Documentation** : `docs/PAGE_SKELETON_GUIDE.md`

---

**VintApp Team** - Navigation fluide et professionnelle 🚀
