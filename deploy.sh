#!/bin/bash

# Script de déploiement pour Hostinger
# Usage: ./deploy.sh

echo "🚀 Démarrage du déploiement..."

# Récupérer les derniers changements
echo "📥 Récupération des changements depuis GitHub..."
git pull origin sky

# Vider les caches Laravel
echo "🧹 Nettoyage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Installer/mettre à jour les dépendances si nécessaire
# php artisan composer install --no-dev --optimize-autoloader

# Mettre à jour la base de données (migrations)
# php artisan migrate --force

echo "✅ Déploiement terminé !"
echo ""
echo "📝 Actions manuelles si nécessaire :"
echo "   - Vérifier admin_role dans la base de données"
echo "   - Tester les endpoints API"
echo ""
