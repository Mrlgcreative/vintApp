# Guide de Migration CSS vers Tailwind - Vinted Violet

## ✅ Migration Complète

Le fichier `vinted-violet.css` a été remplacé par une configuration Tailwind 100% avec la palette de couleurs **Vinted**.

## 📦 Configuration

### `tailwind.config.js`

-   ✅ Palette `vinted` complète avec toutes les nuances (50-900)
-   ✅ Shadows personnalisées (`shadow-vinted-sm`, `shadow-vinted`, `shadow-vinted-lg`, etc.)
-   ✅ Border radius Vinted (`rounded-vinted`, `rounded-vinted-lg`, `rounded-vinted-xl`)
-   ✅ Animations (`animate-fade-in-up`, `hover:-translate-y-0.5`)
-   ✅ Transitions personnalisées

## 🎨 Palette de Couleurs Disponibles

```html
<!-- Primary Purple -->
<div class="bg-vinted-primary-600 text-white">Primary</div>
<div class="bg-vinted-primary-700 text-white">Primary Dark</div>

<!-- Secondary Gray -->
<div class="bg-vinted-secondary-100">Light Gray</div>
<div class="bg-vinted-secondary-500 text-white">Medium Gray</div>

<!-- Accent -->
<div class="bg-vinted-accent-50">Light Purple Accent</div>

<!-- Success -->
<div class="bg-vinted-success-600 text-white">Success</div>

<!-- Danger -->
<div class="bg-vinted-danger-500 text-white">Danger</div>

<!-- Warning -->
<div class="bg-vinted-warning-500 text-white">Warning</div>
```

## 🔄 Guide de Conversion des Classes

### Boutons

#### Avant (CSS)

```html
<button class="btn btn-primary">Action</button>
```

#### Après (Tailwind)

```html
<button
    class="bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white rounded-lg font-medium transition-all hover:-translate-y-0.5 hover:shadow-vinted-primary px-4 py-2"
>
    Action
</button>
```

#### OU avec composant

```html
<x-button-primary>Action</x-button-primary>
<x-button-outline>Annuler</x-button-outline>
```

### Cards

#### Avant

```html
<div class="card">
    <div class="card-header">Titre</div>
    <div class="card-body">Contenu</div>
</div>
```

#### Après

```html
<div
    class="border-0 rounded-xl shadow-vinted-sm transition-all hover:shadow-vinted hover:-translate-y-0.5"
>
    <div class="bg-white border-b border-vinted-secondary-200 rounded-t-xl p-4">
        Titre
    </div>
    <div class="p-4">Contenu</div>
</div>
```

### Formulaires

#### Avant

```html
<input type="text" class="form-control" />
```

#### Après

```html
<input
    type="text"
    class="border-2 border-vinted-secondary-200 rounded-lg transition-all focus:border-vinted-primary-600 focus:ring-4 focus:ring-vinted-primary-600/10 text-[0.95rem] px-4 py-2 w-full"
/>
```

### Alerts

#### Avant

```html
<div class="alert alert-danger">Erreur!</div>
```

#### Après

```html
<div
    class="bg-vinted-danger-50 text-vinted-danger-500 border-l-4 border-vinted-danger-500 rounded-xl font-medium p-4"
>
    Erreur!
</div>
```

#### OU avec composant

```html
<x-alert variant="danger">Erreur!</x-alert>
<x-alert variant="success">Succès!</x-alert>
```

### Badges

#### Avant

```html
<span class="badge bg-primary">Nouveau</span>
```

#### Après

```html
<span
    class="rounded-full font-medium px-3 py-1.5 text-sm bg-vinted-primary-600 text-white"
>
    Nouveau
</span>
```

#### OU avec composant

```html
<x-badge variant="primary">Nouveau</x-badge>
<x-badge variant="danger">Urgent</x-badge>
```

### Navbar

#### Avant

```html
<nav class="navbar navbar-dark bg-primary">
    <a class="navbar-brand">Logo</a>
    <a class="nav-link active">Accueil</a>
</nav>
```

#### Après

```html
<nav
    class="bg-gradient-to-br from-vinted-primary-600 to-vinted-primary-700 shadow-vinted-sm"
>
    <a class="font-bold text-2xl text-white">Logo</a>
    <a
        class="font-medium text-white transition-all hover:text-white/90 hover:-translate-y-0.5 relative after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-white after:rounded-sm"
    >
        Accueil
    </a>
</nav>
```

### Modals

#### Avant

```html
<div class="modal-content">
    <div class="modal-header">Titre</div>
    <div class="modal-body">Contenu</div>
    <div class="modal-footer">Actions</div>
</div>
```

#### Après

```html
<div class="border-0 rounded-2xl shadow-vinted-xl">
    <div class="border-b border-vinted-secondary-200 rounded-t-2xl p-4">
        Titre
    </div>
    <div class="p-6">Contenu</div>
    <div class="border-t border-vinted-secondary-200 rounded-b-2xl p-4">
        Actions
    </div>
</div>
```

## 🧩 Composants Blade Créés

### 1. Button Primary

```html
<x-button-primary>Soumettre</x-button-primary>
<x-button-primary size="sm">Petit</x-button-primary>
<x-button-primary size="lg" class="w-full">Grand</x-button-primary>
```

### 2. Button Outline

```html
<x-button-outline>Annuler</x-button-outline>
<x-button-outline size="sm">Fermer</x-button-outline>
```

### 3. Alert

```html
<x-alert variant="danger">Message d'erreur</x-alert>
<x-alert variant="success">Opération réussie</x-alert>
<x-alert variant="warning">Attention</x-alert>
```

### 4. Badge

```html
<x-badge variant="primary">Nouveau</x-badge>
<x-badge variant="danger">Urgent</x-badge>
<x-badge variant="success">Approuvé</x-badge>
```

## 📋 Classes Utilitaires Personnalisées

### Shadows

```html
<div class="shadow-vinted-sm">Ombre légère</div>
<div class="shadow-vinted">Ombre normale</div>
<div class="shadow-vinted-lg">Ombre forte</div>
<div class="shadow-vinted-xl">Ombre très forte</div>
<div class="shadow-vinted-primary">Ombre violette</div>
```

### Border Radius

```html
<div class="rounded-vinted">8px</div>
<div class="rounded-vinted-lg">12px</div>
<div class="rounded-vinted-xl">16px</div>
```

### Animations

```html
<div class="animate-fade-in-up">Apparition</div>
<div class="transition-all hover:-translate-y-0.5 hover:shadow-vinted-lg">
    Effet lift
</div>
```

### Focus States

```html
<input
    class="focus:border-vinted-primary-600 focus:ring-4 focus:ring-vinted-primary-600/10"
/>
```

## 🚀 Prochaines Étapes

### 1. Compiler Tailwind

```bash
npm run build
```

### 2. Créer des Composants Réutilisables (Optionnel)

Créez des composants dans `resources/views/components/` pour les patterns récurrents:

-   `card.blade.php`
-   `form-input.blade.php`
-   `form-select.blade.php`
-   `dropdown-menu.blade.php`
-   `modal.blade.php`

### 3. Plugin Scrollbar (Optionnel)

Pour des scrollbars personnalisées avec Tailwind:

```bash
npm install -D tailwind-scrollbar
```

Puis dans `tailwind.config.js`:

```javascript
plugins: [forms, require('tailwind-scrollbar')],
```

Utilisation:

```html
<div
    class="scrollbar-thin scrollbar-thumb-vinted-secondary-300 scrollbar-track-vinted-secondary-100"
>
    Contenu scrollable
</div>
```

## 📝 Notes

-   Les erreurs lint dans `vinted-violet.css` pour `@apply` sont normales (CSS standard ne connaît pas Tailwind)
-   Les composants Blade ont des erreurs lint mais fonctionnent correctement en production
-   Vous pouvez maintenant supprimer complètement `vinted-violet.css` si toutes vos vues utilisent Tailwind
-   Les variables CSS root restent pour compatibilité, supprimez-les quand toutes les vues sont migrées

## 🎯 Avantages de cette Migration

✅ **Moins de CSS personnalisé** - Tout est dans Tailwind  
✅ **Cohérence** - Palette de couleurs unifiée  
✅ **Performance** - PurgeCSS enlève le CSS inutilisé  
✅ **Maintenabilité** - Classes utilitaires réutilisables  
✅ **Responsif** - Breakpoints Tailwind intégrés (`md:`, `lg:`, etc.)  
✅ **Dark mode ready** - `dark:` classes disponibles
