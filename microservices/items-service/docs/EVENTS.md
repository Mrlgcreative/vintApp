# items-service — Contrat d'événements

## Transport : outbox transactionnelle → Redis Streams

```
requête vendeur/admin ─► ItemService
                         │
                         └─ transaction unique ─┬─ INSERT/UPDATE/DELETE items
                                                └─ INSERT outbox_messages   ← même transaction
                                                                           │
                              php artisan events:relay (ou superviseur) ────┘
                                                │
                                                └─ XADD vintapp.catalog {payload}
```

`items-service` n'écrit **jamais** directement sur le bus. Chaque mutation écrit
son événement dans `outbox_messages` dans la même transaction que la donnée
métier : l'article et l'événement réussissent ou échouent ensemble. Un
`item.updated` sans modification réelle en base est donc impossible.

C'est le point critique : un « publish after commit » classique perd
l'événement si le worker meurt entre le commit SQL et la publication. Ici la
ligne en base **est** la preuve de l'événement, et le relay la rejoue.

### Pourquoi Redis Streams plutôt que Pub/Sub

Avec Pub/Sub, un message émis pendant qu'un consommateur est arrêté est perdu
sans trace. Un stream conserve les entrées jusqu'à `XACK` du consommateur, ce
qui compte pour un changement de prix ou une rupture de stock.

### Ordre dans le relay

`OutboxRelay` publie **puis** marque `published_at`. Si le process meurt entre
les deux, le message sera republié : les consommateurs doivent donc dédupliquer
sur `event_id` (règle déjà présente dans `EVENTS.md` racine). L'inverse —
marquer puis publier — perdrait des événements.

### Commande

```bash
php artisan events:relay            # boucle jusqu'à vider l'outbox
php artisan events:relay --once     # une passe (cron, supervisord, k8s CronJob)
```

`EVENT_OUTBOX_BATCH` borne une passe, `EVENT_OUTBOX_BACKOFF` espace les
reprises après échec. Un message en échec est reprogrammé, pas supprimé, et
`attempts` + `last_error` restent consultables.

### Nom de clé et préfixe Redis

Le stream s'appelle littéralement **`vintapp.catalog`** dans Redis. Le service
utilise pour cela une connexion dédiée (`database.redis.events`) dont le
préfixe est vide.

Le préfixe Laravel par défaut (`{app}_database_`) s'applique aux clés de cache,
mais appliqué à un stream inter-services il produit
`vintapp_items_database_vintapp.catalog` : un consommateur qui lit le nom
documenté ne trouve rien. Le préfixe est conservé pour le cache, retiré pour le
bus. `tests/Unit/EventStreamContractTest.php` verrouille ce point.

### Configuration

| Variable | Défaut | Rôle |
|---|---|---|
| `EVENT_PUBLISHER` | `redis-stream` | `redis-stream`, `log`, `null` |
| `EVENT_REDIS_CONNECTION` | `events` | connexion Laravel Redis (sans préfixe) |
| `EVENT_STREAM_MAX_LENGTH` | `10000` | troncature approximative du stream |
| `EVENT_OUTBOX_BATCH` | `100` | messages par passe |
| `EVENT_OUTBOX_BACKOFF` | `30` | secondes avant reprise |

Un `EVENT_PUBLISHER` inconnu lève une exception au démarrage : une faute de
frappe ne doit pas laisser croire que les événements partent.

> Attention : les clés de `config/events.php` contiennent un point
> (`item.created`). Le relay lit le tableau puis indexe littéralement, car
> `config('events.streams.item.created')` serait interprété comme une notation
> imbriquée.

## Émis

Tous sur le stream **`vintapp.catalog`**. Consommateur enveloppe :

```json
{
  "event_id": "uuid",
  "type": "item.created",
  "occurred_at": "ISO-8601",
  "data": { }
}
```

### `item.created`

Émis après la création d'un article, dans la transaction.

```json
{
  "event_id": "uuid",
  "type": "item.created",
  "occurred_at": "ISO-8601",
  "data": {
    "item_id": "01J...ULID",
    "seller_id": 42,
    "category_id": 3,
    "brand_id": null,
    "name": "Montre",
    "price": 100.5,
    "currency": "USD",
    "quantity": 1,
    "condition": "good",
    "status": "active"
  }
}
```

### `item.updated`

Même payload, plus la liste `changed` des colonnes réellement modifiées. Un
`update` sans changement n'émet rien.

```json
{
  "event_id": "uuid",
  "type": "item.updated",
  "occurred_at": "ISO-8601",
  "data": {
    "item_id": "01J...ULID",
    "seller_id": 42,
    "category_id": 3,
    "brand_id": null,
    "name": "Montre",
    "price": 555.5,
    "currency": "USD",
    "quantity": 1,
    "condition": "good",
    "status": "active",
    "changed": ["price", "updated_at"]
  }
}
```

### `item.deleted`

Même payload que `item.created`. L'événement porte l'état de l'article *au
moment de la suppression* ; le consommateur doit traiter `item_id` comme
définitivement retiré.

## Consommés

Aucun. Le service est purement émetteur en v1. Les consommateurs pressentis :
`order-service` (disponibilité / stock), caches de recherche, `authenticity-service`.

## Reste à faire côté consommateurs

```
XREADGROUP  GROUP <service> <consumer> STREAMS vintapp.catalog >
XACK        vintapp.catalog <group> <id>
```

Chaque consommateur doit créer son groupe, dédupliquer sur `event_id` et
acquitter seulement après avoir écrit dans **sa** base.

## Idempotence

Une écriture résultant d'un événement doit être idempotente sur `event_id`.
Côté émission, un `update` qui ne modifie aucune colonne n'émet aucun
événement, ce qui évite les événements vides en rafale.
