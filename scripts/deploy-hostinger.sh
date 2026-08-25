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
php artisan cache:clear 2>/dev/null || true
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

# 9. Synchroniser le storage public
echo "📁 Synchronisation du storage..."
php artisan storage:link 2>/dev/null || {
    # Si storage:link échoue (exec désactivé), créer le lien manuellement
    if [ ! -L "public/storage" ] && [ ! -d "public/storage" ]; then
        ln -s ../storage/app/public public/storage
        echo "✅ Lien symbolique créé: public/storage -> ../storage/app/public"
    elif [ -d "public/storage" ] && [ ! -L "public/storage" ]; then
        # Si public/storage est un répertoire (pas un lien), le remplacer par un lien
        rm -rf public/storage
        ln -s ../storage/app/public public/storage
        echo "✅ Répertoire remplacé par lien symbolique"
    else
        echo "✅ Lien symbolique déjà en place"
    fi
}

# 10. Optimisation Laravel
echo "⚡ Optimisation Laravel..."
php artisan optimize

# 10. Migrations
echo "📦 Migrations..."
php artisan migrate --force

echo ""
echo "✅ Déploiement terminé !"
echo ""
echo "📋 Vérifications:"
echo "  1. Palette active: $(php artisan tinker --execute='echo app(\App\Services\ColorPaletteService::class)->getActivePaletteName();')"
echo "  2. Assets compilés: $(ls -lh public/build/assets/*.css 2>/dev/null | wc -l) fichiers CSS"
echo ""
echo "🌐 Votre application est prête !"
