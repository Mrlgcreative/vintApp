# 🎨 Mise à jour du Carrousel Hero - Documentation

## 📋 Résumé des changements

Le carrousel hero a été complètement revu pour offrir plus de flexibilité et de personnalisation. Les administrateurs peuvent désormais :

### ✨ Nouvelles fonctionnalités

1. **🎨 Couleur de fond personnalisable**
   - Sélecteur de couleur visuel
   - Format hexadécimal (#6A0DAD)
   - Aperçu en temps réel

2. **📍 Positionnement flexible du contenu**
   - **Texte** : gauche, centre, droite
   - **Image** : gauche ou droite
   - Layout adaptatif selon les choix

3. **🖼️ Images haute résolution PNG**
   - Format recommandé : 1920x1080px
   - Support PNG pour transparence
   - Affichage `object-fit: contain` (pas de zoom/déformation)

4. **🔘 Deux boutons d'action configurables**
   - Bouton principal (fond blanc)
   - Bouton secondaire (outline blanc)
   - URLs personnalisables

## 🗂️ Fichiers modifiés

### 1. **Base de données**
- `database/migrations/2025_10_21_191112_add_layout_fields_to_hero_slides_table.php`
  - Ajout de `background_color` (string, 7 caractères)
  - Ajout de `text_position` (left/center/right)
  - Ajout de `image_position` (left/right)

### 2. **Modèle**
- `app/Models/HeroSlide.php`
  - Ajout des nouveaux champs dans `$fillable`

### 3. **Contrôleur**
- `app/Http/Controllers/Admin/AdminController.php`
  - **storeHeroSlide()** : Validation et création avec nouveaux champs
  - **updateHeroSlide()** : Mise à jour avec nouveaux champs
  - Validation regex pour couleur hexadécimale
  - Validation des positions (left/center/right)

### 4. **Vues**
- `resources/views/admin/settings/hero-slides.blade.php`
  - Formulaire avec color picker
  - Sélecteurs de position (texte & image)
  - Aperçu du layout dans l'admin
  - Synchronisation color picker ↔ input texte

- `resources/views/home.blade.php`
  - Layout en deux colonnes (Bootstrap grid)
  - Texte à gauche/droite selon configuration
  - Image à gauche/droite selon configuration
  - Boutons stylisés avec effet hover
  - Background color personnalisée

## 🎯 Utilisation

### Créer une nouvelle slide

1. Aller dans **Admin → Paramètres → Carrousel Hero**
2. Cliquer sur **"Ajouter une Slide"**
3. Remplir :
   - ✅ Titre (obligatoire)
   - ✅ Sous-titre (optionnel)
   - ✅ Image PNG haute résolution (obligatoire)
   - ✅ Couleur de fond (obligatoire, défaut: #6A0DAD)
   - ✅ Position du texte (obligatoire, défaut: gauche)
   - ✅ Position de l'image (obligatoire, défaut: droite)
   - ✅ Bouton principal (optionnel)
   - ✅ Bouton secondaire (optionnel)
   - ✅ Activer la slide (checkbox)

### Exemple de configuration

**Configuration 1 : Texte à gauche, Image à droite**
```
Titre: "Bienvenue sur VintApp"
Sous-titre: "La plateforme de vente entre étudiants"
Couleur de fond: #6A0DAD (violet)
Position texte: left
Position image: right
Bouton principal: "Commencer" → /register
Bouton secondaire: "Explorer" → /items
```

**Configuration 2 : Image à gauche, Texte centré**
```
Titre: "Vendez facilement"
Sous-titre: "En quelques clics seulement"
Couleur de fond: #1E40AF (bleu)
Position texte: center
Position image: left
Bouton principal: "Publier" → /items/create
```

## 🎨 Structure du layout

```
┌─────────────────────────────────────────────┐
│  Background: [Couleur personnalisée]        │
│                                             │
│  ┌──────────────┐   ┌──────────────┐       │
│  │              │   │  📝 TITRE     │       │
│  │   🖼️ IMAGE   │   │  Sous-titre   │       │
│  │   (PNG HR)   │   │               │       │
│  │   contain    │   │  [Bouton 1]   │       │
│  │              │   │  [Bouton 2]   │       │
│  └──────────────┘   └──────────────┘       │
│                                             │
└─────────────────────────────────────────────┘
```

*Note : L'image et le texte peuvent être inversés*

## 📱 Responsive

- **Desktop (>768px)** : Layout 2 colonnes (6/6)
- **Mobile (<768px)** : Colonnes empilées verticalement
- Hauteur minimale : 400px
- Images : max-height 350px

## 🎨 Styles CSS

```css
/* Effet hover sur les boutons principaux */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2) !important;
}
```

## 🔧 Validation backend

```php
'background_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/|max:7'
'text_position' => 'required|in:left,right,center'
'image_position' => 'required|in:left,right'
```

## 📊 Champs de la table `hero_slides`

| Champ | Type | Description |
|-------|------|-------------|
| `title` | string(255) | Titre principal |
| `subtitle` | string(500) | Sous-titre optionnel |
| `image_path` | string(255) | Chemin de l'image (storage) |
| `background_color` | string(7) | Couleur hex (#RRGGBB) |
| `text_position` | string(20) | left / center / right |
| `image_position` | string(20) | left / right |
| `button_primary_text` | string(100) | Texte bouton 1 |
| `button_primary_url` | string(255) | URL bouton 1 |
| `button_secondary_text` | string(100) | Texte bouton 2 |
| `button_secondary_url` | string(255) | URL bouton 2 |
| `order` | integer | Ordre d'affichage |
| `is_active` | boolean | Slide activée |

## 🚀 Migration

```bash
php artisan migrate
```

La migration ajoute 3 nouveaux champs avec des valeurs par défaut :
- `background_color` : '#6A0DAD' (violet VintApp)
- `text_position` : 'left'
- `image_position` : 'right'

## ✅ Tests recommandés

1. ✅ Créer une slide avec texte à gauche + image à droite
2. ✅ Créer une slide avec texte à droite + image à gauche
3. ✅ Créer une slide avec texte centré
4. ✅ Tester différentes couleurs de fond
5. ✅ Tester avec PNG transparent
6. ✅ Vérifier le responsive sur mobile
7. ✅ Tester les boutons d'action
8. ✅ Vérifier le drag & drop pour réordonner

## 📝 Notes importantes

- **Images PNG recommandées** pour meilleure qualité
- **object-fit: contain** : pas de déformation d'image
- **Couleur de fond** : Utilisez des couleurs contrastées avec le blanc (texte blanc)
- **Accessibilité** : Les slides inactives n'apparaissent pas sur le site

## 🎉 Avantages

✅ Plus de flexibilité dans le design
✅ Cohérence visuelle avec la charte graphique
✅ Images haute qualité sans déformation
✅ Interface admin intuitive
✅ Aperçu en temps réel
✅ Drag & drop pour réorganiser
✅ Validation robuste côté serveur

---

**Créé le** : 21 octobre 2025  
**Version** : 2.0  
**Auteur** : GitHub Copilot
