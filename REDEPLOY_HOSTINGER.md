# 🚀 Redéploiement Complet VintApp sur Hostinger

## Étape 1 : Connexion SSH

```bash
ssh u282969929@uk-fast-web1350.uk.cloudlogin.co -p 65002
```

## Étape 2 : Nettoyage et Clone

```bash
cd ~
rm -rf public_html
git clone -b sky https://github.com/Mrlgcreative/vintApp.git public_html
cd public_html
```

## Étape 3 : Configuration .env

```bash
cp .env.example .env
nano .env
```

**Configurer** :

-   `APP_ENV=production`
-   `APP_DEBUG=false`
-   `APP_URL=https://votre-domaine.com`
-   Database credentials
-   API keys (Google, Firebase, etc.)

## Étape 4 : Installation Composer

```bash
composer install --no-dev --optimize-autoloader
```

## Étape 5 : Génération clé application

```bash
php artisan key:generate
php artisan storage:link
```

## Étape 6 : Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage
```

## Étape 7 : Installation NPM et Build

```bash
npm install
npm run build
```

## Étape 8 : Migration et Configuration

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

## Étape 9 : Activation Palette "Tons Naturels"

```bash
php artisan colors:activate earth
php artisan colors:inject
```

## Étape 10 : Vérification

```bash
php artisan tinker
```

Puis dans tinker :

```php
\App\Models\Setting::where('key', 'active_color_palette')->first();
exit
```

## Étape 11 : Lien symbolique build

```bash
ln -sf public/build build
```

## ✅ Déploiement terminé !

Votre application est maintenant prête avec la palette "Tons Naturels" (marron) activée.

---

## 🔄 Déploiements futurs (script automatique)

```bash
cd ~/public_html
./deploy-hostinger.sh
```
