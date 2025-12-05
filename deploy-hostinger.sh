#!/bin/bash

# Script de déploiement pour Hostinger
# Sur Hostinger, le dossier public/ est à la racine (public_html)

echo "🚀 Déploiement VintApp sur Hostinger..."

# 1. Pull des dernières modifications
echo "📥 Pull des modifications..."
git pull origin sky

# 2. Installer les dépendances
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader

# 3. Installation des dépendances npm
echo "📦 Installation npm..."
npm install

# 4. Vider les caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Activer la palette Tons Naturels
echo "🎨 Activation palette Tons Naturels..."
php artisan tinker --execute="App\Models\Setting::updateOrCreate(['key'=>'active_color_palette'],['value'=>'earth','label'=>'Active Color Palette','type'=>'string']); echo 'OK';"

# 6. Injecter les couleurs
echo "🎨 Injection des couleurs..."
php artisan colors:inject

# 7. Build des assets
echo "🔨 Compilation des assets..."
VITE_BUILD_PATH=build npm run build

# 8. Créer le lien symbolique si nécessaire
echo "🔗 Création du lien symbolique..."
if [ ! -L "build" ] && [ -d "public/build" ]; then
    ln -sf public/build build
    echo "✅ Lien symbolique créé: build -> public/build"
fi

# 9. Optimisation Laravel
echo "⚡ Optimisation Laravel..."
php artisan optimize

# 10. Migrations (optionnel - décommenter si nécessaire)
# php artisan migrate --force

echo ""
echo "✅ Déploiement terminé !"
echo ""
echo "📋 Vérifications:"
echo "  1. Palette active: $(php artisan tinker --execute='echo app(\App\Services\ColorPaletteService::class)->getActivePaletteName();')"
echo "  2. Assets compilés: $(ls -lh public/build/assets/*.css 2>/dev/null | wc -l) fichiers CSS"
echo ""
echo "🌐 Votre application est prête !"
