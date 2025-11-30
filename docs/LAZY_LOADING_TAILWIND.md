# 🎨 Lazy Loading avec Tailwind CSS - VintApp

## 📖 Vue d'ensemble

Le système de **Lazy Loading** utilise maintenant **Tailwind CSS** pour des styles modulaires, maintenables et cohérents avec le reste de l'application.

## ✨ Avantages Tailwind

-   ✅ **Cohérence** : Même système de design que l'app
-   ✅ **Purge automatique** : CSS optimisé en production
-   ✅ **Dark mode** : Support natif avec `dark:`
-   ✅ **Responsive** : Media queries simplifiées
-   ✅ **Personnalisable** : Extend dans `tailwind.config.js`

## 🎨 Classes disponibles

### Images Lazy Loading

```html
<!-- Image en cours de chargement -->
<img class="lazy-loading" data-src="image.jpg" />
<!-- Classes appliquées : opacity-60 blur-[5px] transition-all -->

<!-- Image chargée -->
<img class="lazy-loaded" src="image.jpg" />
<!-- Classes appliquées : opacity-100 blur-0 -->

<!-- Image avec erreur -->
<img class="lazy-error" src="fallback.jpg" />
<!-- Classes appliquées : opacity-50 border-2 border-dashed border-red-500 -->

<!-- Placeholder -->
<img class="lazy-placeholder" src="placeholder.jpg" />
<!-- Gradient animé avec shimmer -->
```

### Containers Lazy

```html
<!-- Container avec spinner -->
<div class="lazy-container">
    <img data-src="image.jpg" loading="lazy" />
</div>
<!-- Spinner centré automatiquement -->

<!-- Container chargé (spinner caché) -->
<div class="lazy-container lazy-loaded">
    <img src="image.jpg" />
</div>
```

### Skeleton Loaders

```html
<!-- Skeleton de base -->
<div class="skeleton-loader"></div>

<!-- Variantes -->
<div class="skeleton-loader skeleton-text"></div>
<!-- Ligne de texte -->
<div class="skeleton-loader skeleton-title"></div>
<!-- Titre -->
<div class="skeleton-loader skeleton-image"></div>
<!-- Image -->
<div class="skeleton-loader skeleton-avatar"></div>
<!-- Avatar rond -->
<div class="skeleton-loader skeleton-button"></div>
<!-- Bouton -->
```

### Progressive Image Loading

```html
<div class="progressive-image">
    <!-- Placeholder flou -->
    <img
        class="progressive-image__placeholder"
        src="thumbnail-10x10.jpg"
        alt="Placeholder"
    />

    <!-- Image complète -->
    <img
        class="progressive-image__full"
        data-src="full-image.jpg"
        alt="Image complète"
    />
</div>
```

### Aspect Ratios

```html
<!-- 16:9 (vidéo) -->
<div class="aspect-ratio-16-9">
    <img data-src="video-thumbnail.jpg" loading="lazy" />
</div>

<!-- 4:3 (photo classique) -->
<div class="aspect-ratio-4-3">
    <img data-src="photo.jpg" loading="lazy" />
</div>

<!-- 1:1 (carré) -->
<div class="aspect-ratio-1-1">
    <img data-src="avatar.jpg" loading="lazy" />
</div>
```

### Effets d'animation

```html
<!-- Blur up effect -->
<img class="blur-up" data-src="image.jpg" />
<img class="blur-up loaded" src="image.jpg" />

<!-- Fade in -->
<div class="fade-in">Contenu qui apparaît</div>

<!-- Pulse loading -->
<div class="pulse-loading">Chargement...</div>
```

### Grid Skeleton

```html
<div class="grid-skeleton">
    <div class="skeleton-card">
        <div class="skeleton-loader skeleton-image mb-4"></div>
        <div class="skeleton-loader skeleton-title"></div>
        <div class="skeleton-loader skeleton-text"></div>
    </div>
    <!-- Répéter pour 12 cards -->
</div>
```

## 🎨 Personnalisation Tailwind

### tailwind.config.js

```javascript
export default {
    theme: {
        extend: {
            animation: {
                shimmer: "shimmer 1.5s infinite",
                "shimmer-overlay": "shimmer-overlay 2s infinite",
                fadeIn: "fadeIn 0.2s ease-in",
            },
            keyframes: {
                shimmer: {
                    "0%": { backgroundPosition: "-200% 0" },
                    "100%": { backgroundPosition: "200% 0" },
                },
                "shimmer-overlay": {
                    "100%": { transform: "translateX(100%)" },
                },
                fadeIn: {
                    "0%": { opacity: "0", transform: "translateY(10px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
            },
            transitionDuration: {
                400: "400ms",
            },
        },
    },
};
```

## 💡 Exemples pratiques

### Card produit avec lazy loading

```blade
<div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg">
    <!-- Image avec aspect ratio -->
    <div class="aspect-ratio-4-3 lazy-container">
        <img data-src="{{ Storage::url($item->image) }}"
             loading="lazy"
             alt="{{ $item->name }}"
             class="lazy-loading">
    </div>

    <!-- Contenu -->
    <div class="p-4">
        <h3 class="font-semibold text-gray-900 dark:text-white">
            {{ $item->name }}
        </h3>
        <p class="text-primary-600">{{ $item->price }}</p>
    </div>
</div>
```

### Grid de produits avec skeleton

```blade
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($items as $item)
        <div class="fade-in">
            <div class="lazy-container aspect-ratio-1-1">
                <img data-src="{{ Storage::url($item->image) }}"
                     loading="lazy"
                     class="lazy-loading rounded-lg">
            </div>
        </div>
    @empty
        <!-- Skeleton -->
        @for($i = 0; $i < 12; $i++)
            <div class="skeleton-card">
                <div class="skeleton-loader skeleton-image mb-4"></div>
                <div class="skeleton-loader skeleton-title mb-2"></div>
                <div class="skeleton-loader skeleton-text w-20"></div>
            </div>
        @endfor
    @endforelse
</div>
```

### Hero image progressive

```blade
<div class="progressive-image h-96 rounded-2xl overflow-hidden">
    <!-- Tiny placeholder (quelques Ko) -->
    <img class="progressive-image__placeholder"
         src="{{ Storage::url($item->thumbnail_tiny) }}"
         alt="Loading...">

    <!-- Full quality (lazy loaded) -->
    <img class="progressive-image__full"
         data-src="{{ Storage::url($item->hero_image) }}"
         loading="eager"
         alt="{{ $item->name }}">
</div>
```

### Liste avec avatars

```blade
<ul class="space-y-4">
    @foreach($users as $user)
        <li class="flex items-center gap-4 fade-in">
            <!-- Avatar lazy -->
            <div class="lazy-container">
                <img data-src="{{ $user->avatar }}"
                     loading="lazy"
                     class="skeleton-avatar lazy-loading">
            </div>

            <div class="flex-1">
                <h4 class="font-medium">{{ $user->name }}</h4>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </li>
    @endforeach
</ul>
```

## 🌙 Dark Mode

Toutes les classes supportent le dark mode automatiquement :

```html
<!-- Skeleton en mode sombre -->
<div class="skeleton-loader">
    <!-- Light: gray-100 via gray-200 -->
    <!-- Dark: gray-700 via gray-600 -->
</div>

<!-- Card en mode sombre -->
<div class="skeleton-card">
    <!-- Light: bg-white -->
    <!-- Dark: bg-gray-800 -->
</div>
```

## 📱 Responsive

```html
<!-- Spinner responsive -->
<div class="lazy-container">
    <!-- Mobile: 32x32, 3px border -->
    <!-- Desktop: 40x40, 4px border -->
</div>

<!-- Grid responsive -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <!-- 2 colonnes mobile, 3 tablet, 4 desktop -->
</div>
```

## 🔧 Utilities Tailwind

### Combiner avec Tailwind

```html
<!-- Lazy loading + Tailwind utilities -->
<img
    data-src="image.jpg"
    loading="lazy"
    class="lazy-loading rounded-xl shadow-lg hover:scale-105 transition-transform"
/>

<!-- Skeleton + Tailwind spacing -->
<div class="skeleton-loader skeleton-image mb-6 rounded-2xl"></div>

<!-- Container + Tailwind flex -->
<div class="lazy-container flex items-center justify-center min-h-[400px]">
    <img data-src="image.jpg" loading="lazy" />
</div>
```

### Classes personnalisées

```html
<!-- Ajouter vos propres classes -->
<div
    class="skeleton-loader skeleton-image 
            rounded-2xl shadow-2xl 
            ring-4 ring-primary-100 
            transform hover:scale-105 
            transition-all duration-300"
></div>
```

## ⚡ Performance

### Purge CSS en production

```javascript
// tailwind.config.js
export default {
    content: [
        "./resources/views/**/*.blade.php",
        "./public/js/**/*.js",
        // Lazy loading CSS sera purgé automatiquement
    ],
};
```

### Build optimisé

```bash
# Development
npm run dev

# Production (CSS purgé)
npm run build
```

## 🎯 Best Practices

### ✅ À faire

```html
<!-- Utiliser les classes Tailwind existantes -->
<div class="skeleton-loader rounded-lg shadow-md"></div>

<!-- Combiner lazy loading + transitions -->
<img class="lazy-loading transition-all duration-300 hover:scale-110" />

<!-- Dark mode explicite -->
<div class="bg-white dark:bg-gray-800 skeleton-loader"></div>
```

### ❌ À éviter

```html
<!-- Ne pas créer de styles inline -->
<div style="background: linear-gradient(...)"></div>

<!-- Ne pas dupliquer les classes Tailwind -->
<div class="skeleton-loader bg-gray-200"></div>
<!-- skeleton-loader a déjà le background -->

<!-- Ne pas oublier le dark mode -->
<div class="bg-white skeleton-loader"></div>
<!-- Ajouter dark:bg-gray-800 -->
```

## 🧪 Debug

### Voir les classes appliquées

```javascript
// Console navigateur
const img = document.querySelector("img.lazy-loading");
console.log(img.className);
// "lazy-loading opacity-60 blur-[5px] transition-all duration-300"
```

### Vérifier le build Tailwind

```bash
# Voir les classes générées
npm run dev

# Vérifier le fichier compilé
cat public/build/assets/app-*.css | grep "lazy-loading"
```

## 📊 Comparaison

### Avant (CSS vanilla)

```css
.lazy-loading {
    opacity: 0.6;
    filter: blur(5px);
    transition: opacity 0.3s ease-in-out, filter 0.3s ease-in-out;
}
```

### Après (Tailwind)

```css
.lazy-loading {
    @apply opacity-60 blur-[5px] transition-all duration-300 ease-in-out;
}
```

**Avantages :**

-   ✅ Cohérence avec le reste de l'app
-   ✅ Purge automatique (fichier CSS plus petit)
-   ✅ Dark mode intégré
-   ✅ Responsive avec `sm:`, `md:`, `lg:`
-   ✅ Facile à étendre

## 🔄 Migration

### Anciennes classes → Nouvelles classes

| Ancienne classe         | Nouvelle classe Tailwind |
| ----------------------- | ------------------------ |
| `opacity: 0.6`          | `opacity-60`             |
| `filter: blur(5px)`     | `blur-[5px]`             |
| `border-radius: 0.5rem` | `rounded-lg`             |
| `margin-bottom: 0.5rem` | `mb-2`                   |
| `width: 3rem`           | `w-12`                   |
| `height: 200px`         | `h-48`                   |

## 📚 Ressources

-   **Config Tailwind** : `tailwind.config.js`
-   **CSS Source** : `public/css/lazy-loading.css`
-   **Build** : `npm run dev` ou `npm run build`
-   **Docs Tailwind** : https://tailwindcss.com

---

**VintApp Team** - Design system unifié 🎨
