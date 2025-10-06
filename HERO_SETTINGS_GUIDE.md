# Guide de Configuration du Hero Banner

## 📋 Vue d'ensemble

Le banner principal (hero) de la page d'accueil est maintenant entièrement personnalisable via l'interface d'administration. Vous pouvez modifier les textes, l'image de fond et les boutons sans toucher au code.

## 🎯 Paramètres disponibles

### 1. **Titre du Hero** (`hero_title`)
- **Description**: Titre principal affiché en grand sur le banner
- **Valeur par défaut**: "Découvrez des articles uniques"
- **Type**: Texte simple
- **Localisation**: admin/settings, catégorie "hero"

### 2. **Sous-titre du Hero** (`hero_subtitle`)
- **Description**: Description détaillée sous le titre principal
- **Valeur par défaut**: "La marketplace moderne pour acheter et vendre en toute sécurité. Rejoignez notre communauté et trouvez des produits exceptionnels."
- **Type**: Texte long (textarea)
- **Localisation**: admin/settings, catégorie "hero"

### 3. **Image de fond** (`hero_image`)
- **Description**: URL de l'image de fond du banner
- **Valeur par défaut**: Image Unsplash (boutique)
- **Type**: URL
- **Localisation**: admin/settings, catégorie "hero"
- **Format accepté**: URL complète (http://... ou https://...)

### 4. **Texte du bouton principal** (`hero_button_primary_text`)
- **Description**: Texte du bouton principal (pour utilisateurs connectés)
- **Valeur par défaut**: "Vendre"
- **Type**: Texte simple
- **Localisation**: admin/settings, catégorie "hero"

### 5. **Texte du bouton secondaire** (`hero_button_secondary_text`)
- **Description**: Texte du bouton secondaire
- **Valeur par défaut**: "Parcourir"
- **Type**: Texte simple
- **Localisation**: admin/settings, catégorie "hero"

## 🔧 Comment modifier les paramètres

### Via l'interface d'administration

1. Connectez-vous en tant qu'administrateur
2. Accédez à **Admin** > **Paramètres** (`/admin/settings`)
3. Recherchez la section **Hero**
4. Modifiez les valeurs souhaitées
5. Cliquez sur **Enregistrer**
6. Rechargez la page d'accueil pour voir les changements

### Via la base de données

```sql
-- Modifier le titre
UPDATE settings SET value = 'Votre nouveau titre' WHERE key = 'hero_title';

-- Modifier l'image
UPDATE settings SET value = 'https://votre-image.com/banner.jpg' WHERE key = 'hero_image';

-- Modifier le sous-titre
UPDATE settings SET value = 'Votre nouvelle description' WHERE key = 'hero_subtitle';

-- Modifier le texte du bouton principal
UPDATE settings SET value = 'Vendre maintenant' WHERE key = 'hero_button_primary_text';

-- Modifier le texte du bouton secondaire
UPDATE settings SET value = 'Explorer' WHERE key = 'hero_button_secondary_text';
```

### Via le code (pour développeurs)

```php
use App\Models\Setting;

// Modifier un paramètre
Setting::set('hero_title', 'Nouveau titre');

// Récupérer un paramètre
$title = Setting::get('hero_title');

// Récupérer avec valeur par défaut
$title = Setting::get('hero_title', 'Titre par défaut');
```

## 🖼️ Recommandations pour les images

### Format et dimensions
- **Format recommandé**: JPG, PNG, WebP
- **Dimensions**: 1920x1080 px minimum (ratio 16:9)
- **Poids**: < 500 KB (optimisée pour le web)
- **Qualité**: 80-90%

### Sources d'images gratuites
- [Unsplash](https://unsplash.com) - Photos haute qualité
- [Pexels](https://pexels.com) - Images libres de droits
- [Pixabay](https://pixabay.com) - Banque d'images gratuite

### Utilisation d'Unsplash
Pour obtenir une URL d'image Unsplash :
1. Trouvez votre image sur unsplash.com
2. Cliquez droit > Copier l'adresse de l'image
3. Ajoutez les paramètres d'optimisation :
   ```
   ?auto=format&fit=crop&w=2070&q=80
   ```

Exemple complet :
```
https://images.unsplash.com/photo-1234567890?auto=format&fit=crop&w=2070&q=80
```

## 🎨 Styles et personnalisation avancée

### Modifier l'overlay (filtre sombre)
Dans `resources/views/home.blade.php`, ligne 31 :
```html
<div class="hero-banner" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('...')">
```

Changez `rgba(0,0,0,0.5)` :
- **Plus sombre**: `rgba(0,0,0,0.7)`
- **Plus clair**: `rgba(0,0,0,0.3)`
- **Coloré**: `rgba(79,0,206,0.4)` (violet transparent)

### Modifier la hauteur du banner
Dans le CSS de `home.blade.php` :
```css
.banner-overlay {
    min-height: 50vh; /* 50% de la hauteur de l'écran */
}
```

Options :
- `40vh` - Plus compact
- `60vh` - Plus spacieux
- `100vh` - Plein écran

### Modifier les boutons
Les boutons utilisent des classes Bootstrap personnalisées `.banner-action-btn`.
Styles définis dans `home.blade.php` (section `<style>`).

## 🔄 Cache et performance

### Vider le cache des paramètres
Les paramètres sont mis en cache pendant 1 heure (3600 secondes).

**Via l'interface admin** :
- Bouton "Vider Cache" dans Admin > Paramètres

**Via la ligne de commande** :
```bash
php artisan tinker
>>> App\Models\Setting::clearCache();
```

**Via le code** :
```php
use App\Models\Setting;
Setting::clearCache();
```

## 📱 Responsive Design

Le hero banner s'adapte automatiquement aux mobiles :
- **Desktop** : Image fixée (parallax)
- **Mobile** : Image scrollable (meilleure performance)
- **Hauteur ajustée** : 50vh desktop → 40vh mobile

## 🐛 Dépannage

### Les changements ne s'affichent pas
1. Videz le cache des paramètres
2. Videz le cache du navigateur (Ctrl+F5)
3. Vérifiez la console navigateur pour erreurs JS/CSS

### L'image ne s'affiche pas
1. Vérifiez que l'URL est accessible (ouvrez-la dans un nouvel onglet)
2. Vérifiez les CORS si image externe
3. Utilisez HTTPS pour les images sur un site HTTPS

### Les textes sont trop longs
Recommandations :
- **Titre** : 30-50 caractères
- **Sous-titre** : 80-150 caractères
- **Boutons** : 10-20 caractères

## 🔐 Sécurité

- Les paramètres hero sont publics (`is_public = true`)
- Ils sont automatiquement échappés dans les vues
- Les URLs sont validées côté serveur
- Pas de code HTML autorisé (protection XSS)

## 📊 Suivi des modifications

Toutes les modifications des paramètres sont automatiquement enregistrées avec :
- Date et heure du changement
- Utilisateur admin qui a effectué le changement (via Laravel logs)
- Ancienne et nouvelle valeur

Consultez les logs dans `storage/logs/laravel.log`.

## 💡 Astuces

1. **Test A/B** : Changez régulièrement le titre pour tester l'engagement
2. **Saisonnalité** : Adaptez l'image selon les saisons/événements
3. **Call-to-Action** : Utilisez des verbes d'action dans les boutons
4. **Contraste** : Assurez-vous que le texte reste lisible sur l'image

## 📝 Exemples de configurations

### Configuration E-commerce
```
Titre: "Trouvez vos articles préférés"
Sous-titre: "Des milliers de produits à découvrir, vendez et achetez en toute confiance"
Bouton 1: "Vendre"
Bouton 2: "Acheter"
```

### Configuration Marketplace
```
Titre: "La marketplace de confiance"
Sous-titre: "Acheteurs et vendeurs se rencontrent en toute sécurité depuis 2025"
Bouton 1: "Commencer"
Bouton 2: "Explorer"
```

### Configuration Promotion
```
Titre: "Soldes d'été jusqu'à -50%"
Sous-titre: "Profitez de réductions exceptionnelles sur des milliers d'articles"
Bouton 1: "Voir les offres"
Bouton 2: "S'inscrire"
```

## 🚀 Prochaines améliorations possibles

- [ ] Upload d'image directement dans l'admin (au lieu d'URL)
- [ ] Prévisualisation en temps réel des modifications
- [ ] Support de plusieurs images (carrousel)
- [ ] Choix de couleur pour l'overlay
- [ ] Position du texte (gauche, centre, droite)
- [ ] Animation d'entrée personnalisable

---

**Dernière mise à jour** : 6 octobre 2025
**Version** : 1.0
**Support** : Pour toute question, consultez la documentation Laravel ou contactez l'équipe de développement.
