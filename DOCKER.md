# VintApp - Configuration Docker pour développement local

# ========================================================

## Prérequis

-   Docker Desktop installé
-   Docker Compose installé

## Structure

```
docker/
├── nginx/
│   └── conf.d/
│       └── app.conf      # Configuration Nginx
└── php/
    └── local.ini         # Configuration PHP
```

## Démarrage rapide

### 1. Copier le fichier d'environnement

```bash
cp .env.example .env
```

### 2. Configurer les variables Docker dans .env

```env
DB_CONNECTION=mysql
DB_HOST=mysql           # ← Nom du service Docker (pas 127.0.0.1)
DB_PORT=3306
DB_DATABASE=vintapp
DB_USERNAME=vintapp
DB_PASSWORD=secret

REDIS_HOST=redis        # ← Nom du service Docker
```

### 3. Construire et démarrer les containers

```bash
# Première fois (build + démarrage)
docker-compose up -d --build

# Fois suivantes
docker-compose up -d
```

### 4. Installer les dépendances

```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### 5. Configurer Laravel

```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan storage:link
```

### 6. Compiler les assets

```bash
docker-compose exec app npm run build
# ou pour le dev avec hot reload
docker-compose exec app npm run dev
```

## Accès aux services

| Service     | URL                   | Description     |
| ----------- | --------------------- | --------------- |
| Application | http://localhost:8000 | VintApp         |
| PHPMyAdmin  | http://localhost:8080 | Gestion MySQL   |
| MySQL       | localhost:3306        | Base de données |
| Redis       | localhost:6379        | Cache/Sessions  |

## Commandes utiles

```bash
# Voir les logs
docker-compose logs -f

# Logs d'un service spécifique
docker-compose logs -f app

# Accéder au container PHP
docker-compose exec app bash

# Exécuter des commandes Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Arrêter les containers
docker-compose down

# Arrêter et supprimer les volumes (reset complet)
docker-compose down -v
```

## Résolution de problèmes

### Erreur de connexion à la base de données

Assurez-vous que `DB_HOST=mysql` (nom du service) et non `127.0.0.1`

### Permissions des fichiers

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Reconstruire après modification du Dockerfile

```bash
docker-compose up -d --build
```
