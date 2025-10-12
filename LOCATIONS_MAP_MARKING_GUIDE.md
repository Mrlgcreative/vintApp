# 🗺️ Guide : Marquage de Villes sur la Carte

## 📋 Vue d'ensemble

La fonctionnalité de **marquage interactif sur carte** permet aux administrateurs d'ajouter rapidement de nouvelles villes en cliquant directement sur la carte OpenStreetMap, au lieu de rechercher manuellement les coordonnées GPS.

---

## ✨ Fonctionnalités ajoutées

### 1. **Mode Marquage Interactif**
- ✅ Activation/désactivation du mode marquage par bouton
- ✅ Curseur en forme de croix (`crosshair`) pendant le mode actif
- ✅ Indicateur visuel en haut de la carte (bandeau bleu)
- ✅ Raccourcis clavier : touche `M` pour activer/désactiver, `Escape` pour annuler

### 2. **Placement de Marqueur**
- ✅ Clic sur la carte place un **marqueur bleu pulsant**
- ✅ Popup interactive avec coordonnées GPS affichées
- ✅ Boutons : "Ajouter ville" ou "Annuler"
- ✅ Un seul marqueur temporaire à la fois (l'ancien est supprimé)

### 3. **Géocodage Inversé Automatique**
- ✅ Appel API OpenStreetMap Nominatim
- ✅ Détection automatique du nom de la ville
- ✅ Remplissage automatique : nom, région, pays, code pays
- ✅ Message toast de confirmation

### 4. **Intégration avec le Formulaire**
- ✅ Les coordonnées sont pré-remplies dans le modal "Ajouter une ville"
- ✅ Le nom de la ville est automatiquement recherché et renseigné
- ✅ L'aperçu de la ville s'affiche avec drapeau et coordonnées
- ✅ Bouton "Voir sur la carte" pour visualiser la position

### 5. **Système de Notifications (Toasts)**
- ✅ Messages colorés en bas à droite de l'écran
- ✅ Types : succès (vert), erreur (rouge), avertissement (orange), info (bleu)
- ✅ Animations d'entrée/sortie fluides
- ✅ Fermeture automatique après 3 secondes

### 6. **Guide d'aide intégré**
- ✅ Bouton "?" dans la barre d'outils de la carte
- ✅ Popup explicatif avec instructions détaillées
- ✅ Liste des raccourcis clavier
- ✅ Astuces d'utilisation

---

## 🎯 Comment utiliser

### Méthode 1 : Bouton d'activation

1. **Cliquez** sur le bouton **"Ajouter ville par clic"** (ou **"Marquer"** sur mobile)
2. Le mode marquage s'active (bandeau bleu + curseur croix)
3. **Cliquez** n'importe où sur la carte
4. Un **marqueur bleu** apparaît avec un popup
5. Cliquez sur **"Ajouter ville"** dans le popup
6. Le formulaire s'ouvre avec les données pré-remplies
7. Vérifiez et **validez**

### Méthode 2 : Raccourci clavier

1. Appuyez sur la touche **`M`** (pas dans un champ texte)
2. Le mode marquage s'active automatiquement
3. Suivez les étapes 3-7 de la Méthode 1
4. Appuyez sur **`Escape`** pour annuler à tout moment

### Méthode 3 : Depuis le modal

1. Ouvrez le modal "Ajouter une ville"
2. Lisez l'astuce bleue : *"Vous pouvez aussi cliquer sur la carte"*
3. Fermez le modal
4. Utilisez la Méthode 1 ou 2

---

## 🎨 Éléments visuels

### Couleurs des marqueurs
- 🟢 **Vert** : Ville active (existante dans la base)
- 🔴 **Rouge** : Ville inactive (existante, désactivée)
- 🔵 **Bleu pulsant** : Nouveau marqueur temporaire (avant enregistrement)

### Indicateur de mode
```
┌─────────────────────────────────────────────────┐
│ 🖱️ Mode Marquage Activé                         │
│ Cliquez sur la carte pour placer un marqueur   │
│                              [Désactiver]       │
└─────────────────────────────────────────────────┘
```

### Popup de marqueur temporaire
```
┌──────────────────────────────┐
│ 🗺️ Nouvelle ville            │
│ ─────────────────────────    │
│ 📍 Coordonnées :             │
│ Latitude: -4.325678°         │
│ Longitude: 15.307812°        │
│                              │
│ [Ajouter ville]    [❌]      │
│                              │
│ 💡 Le système va rechercher  │
│    le nom via géocodage      │
└──────────────────────────────┘
```

---

## 🔧 Détails techniques

### JavaScript ajouté

#### Variables globales
```javascript
let mapMarkerMode = false;  // État du mode marquage
let tempMarker = null;      // Marqueur temporaire bleu
```

#### Fonctions principales

1. **`enableMapMarkerMode()`** - Active le mode marquage
2. **`disableMapMarkerMode()`** - Désactive le mode marquage
3. **`onMapClick(e)`** - Gère le clic sur la carte
4. **`openAddCityWithCoords(lat, lng)`** - Ouvre le modal avec coordonnées
5. **`cancelTempMarker()`** - Supprime le marqueur temporaire
6. **`reverseGeocode(lat, lng)`** - Appel API OpenStreetMap Nominatim
7. **`showToast(message, type, duration)`** - Affiche une notification
8. **`viewOnMap()`** - Visualise la ville sur la carte depuis le modal
9. **`showMapHelp()`** - Affiche le guide d'aide

### API utilisée

**OpenStreetMap Nominatim - Reverse Geocoding**
```
GET https://nominatim.openstreetmap.org/reverse
    ?format=json
    &lat={latitude}
    &lon={longitude}
    &zoom=10
    &addressdetails=1
```

**Réponse** : Objet JSON contenant :
- `address.city` / `address.town` / `address.village` → Nom de la ville
- `address.state` / `address.region` → Province/Région
- `address.country` → Nom du pays
- `address.country_code` → Code ISO (ex: "cd", "fr")

### CSS ajouté

```css
/* Animation pulse pour le marqueur bleu */
@keyframes pulse {
    0%   { box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 0 rgba(59,130,246,0.7); }
    50%  { box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 8px rgba(59,130,246,0); }
    100% { box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 0 rgba(59,130,246,0); }
}

/* Animations des toasts */
@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
}

@keyframes slideDown {
    from { transform: translateY(0); opacity: 1; }
    to   { transform: translateY(100%); opacity: 0; }
}
```

---

## 📱 Responsive Design

### Desktop (> 1024px)
- ✅ Tous les boutons avec texte complet
- ✅ Indicateur de mode en pleine largeur
- ✅ Popup avec toutes les informations

### Tablet (640px - 1024px)
- ✅ Textes légèrement raccourcis
- ✅ Boutons avec icônes + texte court
- ✅ Même fonctionnalité

### Mobile (< 640px)
- ✅ Boutons avec icônes uniquement
- ✅ Texte "Marquer" au lieu de "Ajouter ville par clic"
- ✅ Popup adapté à la taille de l'écran
- ✅ Toasts en pleine largeur

---

## 🚀 Améliorations futures possibles

### Court terme
- [ ] Validation des coordonnées (vérifier si dans les limites du pays)
- [ ] Historique des derniers marquages
- [ ] Confirmation avant remplacement de marqueur

### Moyen terme
- [ ] Mode "multi-marquage" (placer plusieurs villes d'un coup)
- [ ] Import en masse depuis fichier GPX/KML
- [ ] Dessin de zones géographiques (polygones)
- [ ] Calcul automatique de distance entre villes

### Long terme
- [ ] Mode "offline" avec tiles en cache
- [ ] Intégration Google Maps en alternative
- [ ] Statistiques de densité de villes par région
- [ ] Heatmap des zones couvertes

---

## ⚠️ Limitations connues

1. **Géocodage inversé** : Dépend de l'API Nominatim (peut être lent ou indisponible)
2. **Précision GPS** : Dépend du zoom de la carte au moment du clic
3. **Noms de villes** : Parfois retourne des noms en anglais ou incomplets
4. **Rate limiting** : Nominatim limite à 1 requête/seconde (usage respectueux)

---

## 🎓 Bonnes pratiques

### Pour l'administrateur
1. ✅ **Zoomez** sur la carte avant de cliquer (niveau 10-15 recommandé)
2. ✅ **Vérifiez** toujours le nom de la ville après géocodage
3. ✅ **Comparez** avec les villes existantes pour éviter les doublons
4. ✅ **Désactivez** le mode marquage après usage (touche `Escape`)

### Pour les développeurs
1. ✅ Toujours gérer les erreurs d'API (try/catch)
2. ✅ Respecter les limites de rate limiting de Nominatim
3. ✅ Nettoyer les marqueurs temporaires à la fermeture du modal
4. ✅ Tester sur différentes résolutions d'écran

---

## 📞 Support

En cas de problème :
1. Vérifiez la console JavaScript (F12) pour les erreurs
2. Testez votre connexion internet
3. Essayez de recharger la page (F5)
4. Vérifiez que l'API Nominatim est accessible : https://nominatim.openstreetmap.org/

---

## 📝 Changelog

### Version 1.0 (12 octobre 2025)
- ✨ Ajout du mode marquage interactif
- ✨ Géocodage inversé automatique
- ✨ Système de toasts
- ✨ Raccourcis clavier (M, Escape)
- ✨ Guide d'aide intégré
- ✨ Animations CSS (pulse, slide)
- ✨ Support responsive complet

---

**Développé avec 💙 pour VintApp**
