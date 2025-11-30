# Guide d'utilisation du Lazy Loading - VintApp PWA

## 📚 Table des matières

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Utilisation de base](#utilisation-de-base)
4. [Composants Blade](#composants-blade)
5. [Options avancées](#options-avancées)
6. [Performances](#performances)
7. [Dépannage](#dépannage)

## Introduction

Le système de lazy loading de VintApp optimise automatiquement le chargement des images et du contenu pour améliorer les performances de votre PWA.

### Avantages

✅ **Chargement plus rapide** : Les images ne se chargent que lorsqu'elles sont visibles  
✅ **Économie de données** : Réduit la consommation de bande passante  
✅ **Meilleure expérience utilisateur** : Pages plus réactives  
✅ **SEO amélioré** : Temps de chargement optimisé  
✅ **Support automatique** : Fonctionne avec les images dynamiques

## Installation

Le système est déjà intégré dans votre application. Les fichiers suivants ont été ajoutés :

```
public/
├── js/
│   └── lazy-loading.js      # Gestionnaire principal
└── css/
    └── lazy-loading.css     # Styles pour le loading

resources/views/
└── components/
    └── lazy-image.blade.php # Composant Blade
```

### Intégration dans vos vues

Le lazy loading est automatiquement activé dans `app.blade.php` :

```html
<!-- CSS -->
<link rel="stylesheet" href="{{ asset('css/lazy-loading.css') }}" />

<!-- JavaScript -->
<script src="{{ asset('js/lazy-loading.js') }}" defer></script>
```

## Utilisation de base

### 1. Images simples avec `data-src`

Remplacez `src` par `data-src` pour activer le lazy loading :

```html
<!-- ❌ Ancien (chargement immédiat) -->
<img src="{{ Storage::url($item->images[0]) }}" alt="Mon article" />

<!-- ✅ Nouveau (lazy loading) -->
<img
    data-src="{{ Storage::url($item->images[0]) }}"
    src="data:image/svg+xml,..."
    loading="lazy"
    alt="Mon article"
/>
```

### 2. Images de fond avec `data-bg`

Pour les images de fond CSS :

```html
<div
    data-bg="{{ asset('images/banner.jpg') }}"
    class="h-64 bg-cover bg-center lazy-container"
></div>
```

### 3. Iframes

```html
<iframe
    data-src="https://www.youtube.com/embed/VIDEO_ID"
    class="w-full h-64"
    loading="lazy"
>
</iframe>
```

## Composants Blade

### Composant `<x-lazy-image>`

Utilisez le composant Blade pour simplifier l'intégration :

```blade
<x-lazy-image
    src="{{ Storage::url($item->images[0]) }}"
    alt="{{ $item->name }}"
    class="w-full h-48 object-cover rounded-lg"
    width="300"
    height="200"
/>
```

#### Paramètres disponibles

| Paramètre     | Type   | Défaut     | Description                 |
| ------------- | ------ | ---------- | --------------------------- |
| `src`         | string | **requis** | URL de l'image              |
| `alt`         | string | ''         | Texte alternatif            |
| `class`       | string | ''         | Classes CSS supplémentaires |
| `width`       | int    | null       | Largeur de l'image          |
| `height`      | int    | null       | Hauteur de l'image          |
| `aspectRatio` | string | null       | Ratio (16-9, 4-3, 1-1)      |
| `placeholder` | bool   | true       | Afficher un placeholder     |

#### Exemples avec aspect ratio

```blade
<!-- Ratio 16:9 (vidéo) -->
<x-lazy-image
    src="{{ $image }}"
    alt="Vidéo"
    aspectRatio="16-9"
/>

<!-- Ratio carré (avatar) -->
<x-lazy-image
    src="{{ $avatar }}"
    alt="Avatar"
    aspectRatio="1-1"
    class="rounded-full"
/>
```

## Options avancées

### Configuration personnalisée

Vous pouvez personnaliser le comportement du lazy loader :

```javascript
// Dans votre fichier JavaScript
window.lazyLoader = new LazyLoadingManager({
    rootMargin: "200px", // Charger avant 200px du viewport
    threshold: 0.01, // Seuil de visibilité
    loadingClass: "is-loading", // Classe personnalisée
    loadedClass: "is-loaded", // Classe après chargement
    errorClass: "has-error", // Classe en cas d'erreur
});
```

### Préchargement d'images

Pour précharger des images importantes :

```javascript
window.lazyLoader.preloadImages([
    "/images/logo.png",
    "/images/hero-banner.jpg",
]);
```

### Forcer le chargement

Pour charger toutes les images immédiatement :

```javascript
window.lazyLoader.loadAll();
```

### Événements personnalisés

Écoutez les événements de chargement :

```javascript
document.addEventListener("lazyloaded", function (e) {
    console.log("Image chargée:", e.detail.src);
    // Votre code ici
});
```

## Skeleton Loaders

Utilisez les skeleton loaders pour améliorer l'expérience pendant le chargement :

```html
<!-- Liste de produits -->
<div class="grid-skeleton">
    <div class="skeleton-card">
        <div class="skeleton-loader skeleton-image mb-4"></div>
        <div class="skeleton-loader skeleton-title mb-2"></div>
        <div class="skeleton-loader skeleton-text"></div>
        <div class="skeleton-loader skeleton-text w-3/4"></div>
    </div>
</div>
```

### Classes disponibles

-   `.skeleton-loader` - Base
-   `.skeleton-image` - Image (200px height)
-   `.skeleton-title` - Titre (1.5rem height)
-   `.skeleton-text` - Texte (1rem height)
-   `.skeleton-avatar` - Avatar (3rem circle)
-   `.skeleton-button` - Bouton (2.5rem height)

## Performances

### Métriques améliorées

Avec le lazy loading activé, vous devriez observer :

-   **Temps de chargement initial** : -40% à -60%
-   **First Contentful Paint (FCP)** : -30% à -50%
-   **Largest Contentful Paint (LCP)** : -20% à -40%
-   **Données transférées** : -50% à -70% (selon le scroll)

### Bonnes pratiques

#### ✅ À faire

```blade
<!-- 1. Toujours fournir un placeholder -->
<img data-src="{{ $image }}"
     src="data:image/svg+xml,..."
     loading="lazy">

<!-- 2. Spécifier les dimensions -->
<img data-src="{{ $image }}"
     width="300"
     height="200"
     loading="lazy">

<!-- 3. Utiliser loading="eager" pour l'image principale -->
<img data-src="{{ $heroImage }}"
     loading="eager">

<!-- 4. Grouper dans un container -->
<div class="lazy-container">
    <img data-src="{{ $image }}">
</div>
```

#### ❌ À éviter

```blade
<!-- 1. Pas de placeholder -->
<img data-src="{{ $image }}"> <!-- ❌ -->

<!-- 2. Lazy loading sur l'image hero -->
<img data-src="{{ $heroImage }}" loading="lazy"> <!-- ❌ -->

<!-- 3. Trop d'images sans lazy loading -->
@foreach($items as $item)
    <img src="{{ $item->image }}"> <!-- ❌ -->
@endforeach
```

### Optimisation des images

Combinez le lazy loading avec l'optimisation d'images :

```php
// Dans votre controller
use Intervention\Image\Facades\Image;

$image = Image::make($file)
    ->resize(800, null, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    })
    ->encode('jpg', 85);
```

## Progressive Image Loading

Pour une expérience encore plus fluide, utilisez le chargement progressif :

```html
<div class="progressive-image">
    <!-- Petite image floue (< 5KB) -->
    <img src="{{ $thumbnail }}" class="progressive-image__placeholder" />

    <!-- Image complète (lazy loaded) -->
    <img data-src="{{ $fullImage }}" class="progressive-image__full" />
</div>
```

## Dépannage

### Les images ne se chargent pas

1. Vérifiez que `lazy-loading.js` est bien chargé :

```javascript
console.log(window.lazyLoader); // Doit afficher l'instance
```

2. Vérifiez la console pour les erreurs :

```javascript
// Activez le mode debug
localStorage.setItem("lazyDebug", "true");
```

3. Vérifiez que les URLs sont correctes :

```blade
{{ Storage::url($item->images[0]) }}
```

### Les images clignotent au chargement

Ajoutez une transition CSS :

```css
img[data-src] {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

img.lazy-loaded {
    opacity: 1;
}
```

### Performance toujours lente

1. Vérifiez la taille des images (< 200KB recommandé)
2. Utilisez le format WebP si possible
3. Activez la compression serveur (gzip/brotli)
4. Vérifiez le cache du navigateur

### IntersectionObserver non supporté

Le script fournit un fallback automatique, mais pour une meilleure compatibilité :

```html
<!-- Ajoutez ce polyfill pour les anciens navigateurs -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver"></script>
```

## Support et compatibilité

### Navigateurs supportés

| Navigateur | Version | Support     |
| ---------- | ------- | ----------- |
| Chrome     | 58+     | ✅ Complet  |
| Firefox    | 55+     | ✅ Complet  |
| Safari     | 12.1+   | ✅ Complet  |
| Edge       | 16+     | ✅ Complet  |
| IE 11      | -       | ⚠️ Fallback |

### Fallback pour anciens navigateurs

Pour les navigateurs sans IntersectionObserver, toutes les images se chargent immédiatement.

## Exemples d'utilisation

### Liste de produits

```blade
@foreach($items as $item)
    <div class="product-card">
        <div class="lazy-container aspect-ratio-1-1">
            <img data-src="{{ Storage::url($item->images[0]) }}"
                 src="data:image/svg+xml,..."
                 loading="lazy"
                 alt="{{ $item->name }}"
                 class="w-full h-full object-cover">
        </div>
        <h3>{{ $item->name }}</h3>
        <p>{{ $item->price }}</p>
    </div>
@endforeach
```

### Galerie d'images

```blade
<div class="grid grid-cols-3 gap-4">
    @foreach($item->images as $index => $image)
        <img data-src="{{ Storage::url($image) }}"
             src="data:image/svg+xml,..."
             loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
             alt="Image {{ $index + 1 }}"
             class="w-full h-48 object-cover rounded-lg cursor-pointer"
             onclick="openLightbox({{ $index }})">
    @endforeach
</div>
```

### Bannière hero

```blade
<!-- Ne pas lazy load l'image principale -->
<div class="hero-section"
     style="background-image: url('{{ $heroBanner }}')">
    <h1>Bienvenue sur VintApp</h1>
</div>

<!-- Lazy load les autres sections -->
<div data-bg="{{ $section2Banner }}"
     class="lazy-container h-64">
</div>
```

## Ressources

-   [MDN - Lazy Loading](https://developer.mozilla.org/en-US/docs/Web/Performance/Lazy_loading)
-   [web.dev - Native Lazy Loading](https://web.dev/browser-level-image-lazy-loading/)
-   [Can I Use - Loading attribute](https://caniuse.com/loading-lazy-attr)

---

**VintApp Team** - Optimisé pour la performance 🚀
