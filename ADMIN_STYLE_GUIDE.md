# Guide de Style Admin - Tailwind CSS

## Vue d'ensemble

Ce guide présente les standards de design et les composants utilisés dans l'interface d'administration après la conversion complète de Bootstrap vers Tailwind CSS.

## Architecture

### Fichiers principaux
- `layouts/admin.blade.php` - Layout principal avec sidebar et navigation
- `public/css/admin-components.css` - Styles personnalisés et composants
- `public/js/admin-utils.js` - Utilitaires JavaScript pour l'interaction

### Dossiers convertis
- ✅ `admin/categories/` - Gestion des catégories
- ✅ `admin/brands/` - Gestion des marques  
- ✅ `admin/transactions/` - Transactions financières
- ✅ `admin/wallets/` - Gestion des portefeuilles
- ✅ `admin/orders/` - Gestion des commandes
- ✅ `admin/logs/` - Journaux système

## Palette de couleurs

### Couleurs principales
```css
Primaire: bg-blue-600, text-blue-600
Secondaire: bg-gray-600, text-gray-600
Succès: bg-green-600, text-green-600
Attention: bg-yellow-600, text-yellow-600
Danger: bg-red-600, text-red-600
```

### Couleurs de fond
```css
Fond principal: bg-gray-50
Cartes: bg-white
Sidebar: bg-gray-900
Navigation: bg-white avec shadow
```

## Composants standardisés

### 1. Cartes (Cards)
```html
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Titre</h3>
    <div class="text-gray-600">Contenu</div>
</div>
```

### 2. Boutons
```html
<!-- Bouton primaire -->
<button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
    <i class="fas fa-plus mr-2"></i>Ajouter
</button>

<!-- Bouton secondaire -->
<button class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
    Annuler
</button>

<!-- Bouton outline -->
<button class="border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 font-medium py-2 px-4 rounded-lg transition-all duration-200">
    Voir plus
</button>
```

### 3. Formulaires
```html
<div class="form-group">
    <label class="form-label">Nom de la catégorie</label>
    <input type="text" class="form-input" placeholder="Entrez le nom">
    <div class="error-message">Message d'erreur</div>
</div>
```

### 4. Tables
```html
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nom
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    Contenu
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### 5. Badges de statut
```html
<!-- Actif -->
<span class="badge badge-success">Actif</span>

<!-- Inactif -->
<span class="badge badge-secondary">Inactif</span>

<!-- En attente -->
<span class="badge badge-warning">En attente</span>

<!-- Échoué -->
<span class="badge badge-danger">Échoué</span>
```

### 6. Modals
```html
<div id="myModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('myModal')"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Titre du modal</h3>
                <p class="text-gray-600">Contenu du modal</p>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="btn-primary" onclick="closeModal('myModal')">
                    Confirmer
                </button>
                <button type="button" class="btn-secondary mr-3" onclick="closeModal('myModal')">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
```

### 7. Dropdowns
```html
<div class="relative inline-block text-left">
    <button onclick="toggleDropdown('actionDropdown')" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
        Actions
        <i class="fas fa-chevron-down ml-2"></i>
    </button>
    
    <div id="actionDropdown" class="dropdown-menu hidden">
        <a href="#" class="dropdown-item">
            <i class="fas fa-edit mr-2"></i>Modifier
        </a>
        <a href="#" class="dropdown-item">
            <i class="fas fa-trash mr-2"></i>Supprimer
        </a>
    </div>
</div>
```

## JavaScript Utils

### Fonctions principales disponibles

#### 1. Gestion des modals
```javascript
openModal('modalId')    // Ouvrir un modal
closeModal('modalId')   // Fermer un modal
toggleModal('modalId')  // Basculer un modal
```

#### 2. Gestion des dropdowns
```javascript
toggleDropdown('dropdownId')  // Basculer un dropdown
```

#### 3. Notifications toast
```javascript
showToast('Message de succès', 'success', 3000)
showToast('Message d\'erreur', 'error', 5000)
showToast('Attention', 'warning', 4000)
```

#### 4. États de chargement
```javascript
const button = document.getElementById('submitBtn');
setLoading(button, true);   // Activer le chargement
setLoading(button, false);  // Désactiver le chargement
```

#### 5. Formatage
```javascript
formatNumber(1234.56)                    // "1 234,56"
formatCurrency(1234.56, 'XOF', 'fr-FR') // "1 234,56 CFA"
```

#### 6. Validation
```javascript
isValidEmail('test@example.com')  // true/false
```

#### 7. Auto-save
```javascript
enableAutoSave('formId', '/api/save', 30000)
```

#### 8. Recherche en temps réel
```javascript
enableLiveSearch('searchInput', 'resultsContainer', '/api/search')
```

## Responsive Design

### Breakpoints Tailwind
- `sm:` - 640px et plus
- `md:` - 768px et plus  
- `lg:` - 1024px et plus
- `xl:` - 1280px et plus
- `2xl:` - 1536px et plus

### Classes responsives courantes
```html
<!-- Grille responsive -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Texte responsive -->
<h1 class="text-2xl md:text-3xl lg:text-4xl font-bold">

<!-- Padding responsive -->
<div class="p-4 md:p-6 lg:p-8">

<!-- Masquer sur mobile -->
<div class="hidden md:block">

<!-- Largeur responsive -->
<div class="w-full md:w-1/2 lg:w-1/3">
```

## Animations et Transitions

### Classes d'animation personnalisées
```css
.animate-fade-in      /* Apparition en fondu */
.animate-slide-in     /* Glissement latéral */
.animate-pulse-slow   /* Pulsation lente */
.animate-bounce-slow  /* Rebond lent */
```

### Effets hover
```css
.card-hover          /* Élévation au survol */
.hover-scale         /* Agrandissement au survol */
.hover-rotate        /* Rotation au survol */
.hover-shadow        /* Ombre au survol */
```

## Bonnes pratiques

### 1. Cohérence des espacements
- Utilisez l'échelle Tailwind : `2, 4, 6, 8, 12, 16, 20, 24`
- Préférez `gap-6` à `space-x-6` pour les grilles
- Utilisez `mb-4` pour l'espacement vertical standard

### 2. Couleurs cohérentes
- Respectez la palette définie
- Utilisez les variantes `-50` à `-900` pour les nuances
- Préférez `text-gray-600` à `text-gray-500` pour le texte secondaire

### 3. Accessibilité
- Ajoutez toujours `focus:ring-2 focus:ring-blue-500` sur les éléments interactifs
- Utilisez `sr-only` pour le texte destiné aux lecteurs d'écran
- Respectez les contrastes de couleur

### 4. Performance
- Préférez les utilitaires Tailwind aux styles CSS personnalisés
- Groupez les classes similaires
- Utilisez les composants personnalisés pour éviter la répétition

## Maintenance

### Ajouter un nouveau composant
1. Créer le composant dans `admin-components.css`
2. Documenter l'utilisation dans ce guide
3. Ajouter des utilitaires JavaScript si nécessaire dans `admin-utils.js`

### Modifier des styles existants
1. Vérifier l'impact sur toutes les vues converties
2. Tester la responsivité sur différentes tailles d'écran
3. Valider l'accessibilité

### Tests à effectuer
- [ ] Responsivité mobile/desktop
- [ ] Fonctionnalité des dropdowns et modals
- [ ] Cohérence visuelle entre les vues
- [ ] Performance de chargement
- [ ] Accessibilité au clavier

## Ressources

- [Documentation Tailwind CSS](https://tailwindcss.com/docs)
- [FontAwesome Icons](https://fontawesome.com/icons)
- [Heroicons](https://heroicons.com/) (alternative aux icônes)
- [Headless UI](https://headlessui.com/) (composants accessibles)

---

*Dernière mise à jour : Conversion complète Bootstrap → Tailwind CSS*