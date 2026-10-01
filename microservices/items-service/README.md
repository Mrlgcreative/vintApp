# items-service

Microservice du **catalogue** de VintApp : articles, catégories, marques, stock.

> Extraire de : `Item`, `Category`, `Brand`, `ItemService`, `Api\Items\ItemController`,
> `Api\Catalog\CategoryController`, `Api\Catalog\BrandController`.

## Périmètre (v1 — catalogue cœur)

- Lecture publique des articles actifs (liste filtrée, détail, articles d'une
  catégorie / d'une marque).
- CRUD vendeur des articles (création, mise à jour, suppression, stock, prix).
- CRUD admin des catégories et marques.
- Émission des événements `item.created` / `item.updated` / `item.deleted` sur
  Redis Streams, via outbox transactionnelle (`docs/EVENTS.md`).

## Hors périmètre (v1)

- Boost, authenticité, vérification, modération, avis, favoris → périmètre
  ultérieur (l'`Item` du monolithe porte déjà ces colonnes : elles ne sont pas
  reprises ici).
- Upload d'images : l'API stocke des **chemins** (`images`, `logo`, `image`),
  pas de binaire.
- Identité des utilisateurs → `auth-service` (introspection).
- Paiement / commande → `payment-service`, `order-service`.

## API

Format de réponse : `{success, message, data, meta}`.

| Méthode | Route | Auth | Rôle |
|---|---|---|---|
| GET | `/v1/items` | public | articles actifs, filtres + pagination |
| GET | `/v1/items/{item}` | public | détail d'un article actif |
| GET | `/v1/me/items` | Bearer | articles du vendeur (tous statuts) |
| POST | `/v1/items` | Bearer | créer un article (vendeur = identité) |
| PUT/PATCH | `/v1/items/{item}` | Bearer | modifier (vendeur ou admin) |
| DELETE | `/v1/items/{item}` | Bearer | supprimer (vendeur ou admin) |
| GET | `/v1/categories` | public | catégories actives (+ `items_count`) |
| GET | `/v1/categories/{category}` | public | détail (+ `parent`, `children`) |
| GET | `/v1/categories/{category}/items` | public | articles de la catégorie |
| POST / PUT / DELETE | `/v1/categories[/{category}]` | Bearer admin | CRUD catégories |
| GET | `/v1/brands` | public | marques actives (+ `items_count`) |
| GET | `/v1/brands/{brand}` | public | détail |
| GET | `/v1/brands/{brand}/items` | public | articles de la marque |
| POST / PUT / DELETE | `/v1/brands[/{brand}]` | Bearer admin | CRUD marques |

### Filtres de `GET /v1/items`

`category_id`, `brand_id`, `category` (slug), `brand` (slug), `condition`,
`currency`, `min_price`, `max_price`, `search` (nom ou description),
`sort` (`recent`, `oldest`, `price_asc`, `price_desc`, `views`, `name`),
`per_page` (plafonné par `ITEMS_MAX_PER_PAGE`).

Tri et pagination sont résolus via une table blanche et un plafond : ni
`orderBy()` arbitraire ni `per_page` non borné.

### Règles d'écriture

- Le `user_id` (vendeur) provient **uniquement** de l'identité introspectée ; un
  `user_id` fourni par le client est ignoré.
- Un article n'est public que `status = active` ; le vendeur voit les autres
  statuts via `/v1/me/items`.
- Suppression d'une catégorie bloquée si elle contient des articles ou des
  sous-catégories ; suppression d'une marque bloquée si elle est utilisée.

## Règles de sécurité (fail-closed)

1. **Identité fail-closed.** Les routes d'écriture passent par `auth-service` ;
   si le service est injoignable ou non configuré ⇒ `503`, jamais de mode
   dégradé. Token absent/invalide ⇒ `401`.
2. **Pas de FK inter-services.** `user_id` est une clé métier. Les catégories et
   marques sont possédées par ce service, donc réellement contraintes.
3. **Autorisation par ressource.** Modifier/supprimer un article exige d'être
   son vendeur ou admin ; catégories/marques sont réservées aux admins.

## Frontière de données

Tables possédées : `categories`, `brands`, `items`, `outbox_messages`.

Base **propre** (`vintapp_items`). `user_id` est une clé métier, jamais une FK
vers `auth-service`.

## Configuration

`.env` :

```
APP_URL=http://127.0.0.1:8103

# Identité (obligatoire, sinon /v1/* en écriture => 503)
ITEMS_AUTH_SERVICE_URL=http://127.0.0.1:8101
ITEMS_AUTH_SERVICE_SHARED_SECRET=...

# Catalogue
ITEMS_PER_PAGE=15
ITEMS_MAX_PER_PAGE=100
ITEMS_DEFAULT_CURRENCY=USD

# Bus d'événements
EVENT_PUBLISHER=redis-stream     # redis-stream | log | null
EVENT_REDIS_CONNECTION=events    # connexion sans préfixe
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

Sans Redis, mettre `EVENT_PUBLISHER=log` : les événements restent dans
`outbox_messages` et seront publiés au branchement d'un vrai transport.

## Installation

### 1. Redis (bus d'événements)

```bash
docker compose -f microservices/docker-compose.yml up -d redis
docker exec vintapp-redis redis-cli ping   # PONG
```

Le service utilise une connexion Redis dédiée (`database.redis.events`),
volontairement **sans préfixe**. Le préfixe Laravel par défaut transformerait
`vintapp.catalog` en `vintapp_items_database_vintapp.catalog`, nom qu'aucun
consommateur ne connaît. Le préfixe reste actif pour le cache du service.

### 2. Service

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --port=8103

# Relay des événements (à laisser tourner : cron, supervisord ou CronJob)
php artisan events:relay
```

## Tests

```bash
vendor/bin/phpunit
```

50 tests, 133 assertions (SQLite `:memory:`). `IdentityTest` couvre le
fail-closed et l'isolation entre vendeurs ; `ItemApiTest` les filtres, la
création scopée à l'identité et les mutations ; `CategoryApiTest` /
`BrandApiTest` les CRUD admin et les garde-fous de suppression ;
`EventTransportTest` l'outbox (atomicité, backoff) ; `EventStreamContractTest`
le nom de stream littéral `vintapp.catalog`.

## Événements

Contrat détaillé et payloads : [`docs/EVENTS.md`](docs/EVENTS.md).

Aucun consommateur n'est branché dans ce dépôt : `order-service` et les caches
devront lire le stream `vintapp.catalog` et acquitter.
