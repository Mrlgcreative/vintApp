#!/bin/bash

# Script de déploiement pour Hostinger VintApp
# Usage: ./deploy.sh

set -e  # Arrêter en cas d'erreur

echo "🚀 Démarrage du déploiement VintApp Production..."
echo ""

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo "❌ Erreur: artisan non trouvé. Êtes-vous dans le bon répertoire ?"
    exit 1
fi

# Afficher la branche actuelle
echo "📍 Branche Git actuelle:"
git branch --show-current
echo ""

# Récupérer les derniers changements
echo "📥 Récupération des changements depuis GitHub (branche sky)..."
git pull origin sky
echo "✅ Code mis à jour"
echo ""

# Vérifier que .env existe
if [ ! -f ".env" ]; then
    echo "⚠️  ATTENTION: .env n'existe pas !"
    echo "   Copiez .env.production.example vers .env et configurez-le"
    exit 1
fi

# Vider les caches Laravel
echo "🧹 Nettoyage des caches Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "✅ Caches nettoyés"
echo ""

# Vérifier les permissions storage
echo "🔐 Vérification des permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || echo "⚠️  Impossible de modifier les permissions (normal sur certains hébergeurs)"
echo ""

# Optimiser pour la production (optionnel - décommentez si nécessaire)
# echo "⚡ Optimisation pour la production..."
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache
# echo "✅ Optimisations appliquées"
# echo ""

# Vérifier la base de données
echo "🗄️  Test de connexion à la base de données..."
php artisan db:show 2>/dev/null || echo "⚠️  Commande db:show non disponible (Laravel < 9)"
echo ""

echo "✅ ========================================="
echo "✅ DÉPLOIEMENT TERMINÉ AVEC SUCCÈS !"
echo "✅ ========================================="
echo ""
echo "📝 Actions à vérifier manuellement :"
echo "   1. ✓ Code à jour depuis GitHub"
echo "   2. ✓ Caches vidés"
echo "   3. ⚠️  Vérifier que .env est bien configuré"
echo "   4. ⚠️  Tester la connexion DB"
echo "   5. ⚠️  Vérifier que Firebase serviceAccountKey.json existe"
echo ""
echo "🌐 Testez votre site: https://vitapp.mykenyastudentprocess.com"
echo ""
