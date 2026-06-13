#!/bin/bash

# Script rapide pour compiler les assets sur Hostinger
echo "🔨 Compilation des assets VintApp..."

# Injecter les couleurs
php artisan colors:inject

# Build
VITE_BUILD_PATH=build npm run build

# Créer lien symbolique si nécessaire
if [ ! -L "build" ] && [ -d "public/build" ]; then
    ln -sf public/build build
fi

echo "✅ Assets compilés dans public/build/"
ls -lh public/build/assets/*.css 2>/dev/null | tail -1
