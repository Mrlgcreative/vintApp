# auth-service

Microservice d'**identité, authentification et gestion des rôles** de VintApp.

> Extraire de : `User`, `Role`, `AuthService`, `Api/Auth/AuthController`,
> `Api/Auth/TwoFactorAuthController`, flux Sanctum, 2FA.

## Périmètre

- Inscription / connexion / déconnexion (tokens Sanctum, TTL 60 jours).
- 2FA Google Authenticator + codes de récupération à usage unique.
- Réinitialisation de mot de passe (invalide les sessions ouvertes).
- Rôles et permissions (`admin`, `user`, `vendeur`, `expert`).
- **Introspection de token** pour les autres microservices.

## API

Toutes les routes sont préfixées `/v1`. Format de réponse unique :

```json
{ "success": true, "message": "OK", "data": {}, "meta": {} }
```

| Méthode | Route | Auth | Rôle |
|---|---|---|---|
| POST | `/v1/register` | — | inscription, rend un token |
| POST | `/v1/login` | — | token, ou `pending_token` si 2FA active |
| POST | `/v1/logout` | Bearer | révoque le token + la session |
| GET | `/v1/me` | Bearer | identité courante |
| POST | `/v1/forgot-password` | — | envoi du lien de reset |
| POST | `/v1/reset-password` | — | nouveau mot de passe |
| POST | `/v1/two-factor/verify` | Bearer `2fa:pending` | échange contre token complet |
| POST | `/v1/two-factor/enable` | Bearer | secret + QR + codes de recovery |
| POST | `/v1/two-factor/confirm` | Bearer | active la 2FA |
| POST | `/v1/two-factor/disable` | Bearer | mot de passe requis |
| POST | `/v1/two-factor/regenerate-codes` | Bearer | mot de passe requis |
| POST | `/v1/token/introspect` | Bearer + `X-Vintapp-Key` | **réservé aux services** |

## Introspection : comment un autre service valide un token

```http
POST /v1/token/introspect
Authorization: Bearer <token-utilisateur>
X-Vintapp-Key: <SERVICE_SHARED_SECRET>
```

Réponse :

```json
{
  "success": true,
  "data": {
    "active": true,
    "identity": {
      "user_id": 1,
      "public_id": "01M3VZ0Q6C6VP4ZF99GRSNG6D4",
      "email": "gloire@example.cd",
      "roles": ["user"],
      "two_factor_enabled": false,
      "token_id": 4,
      "abilities": ["*"]
    }
  }
}
```

C'est le **seul** canal d'accès à l'identité : aucun autre service ne lit la
table `users`.

## Règles de sécurité

1. **Fail-closed sur `X-Vintapp-Key`** : secret absent → `503`, secret faux →
   `401`. Aucune requête n'est traitée sans secret valide.
2. Un token `2fa:pending` n'est **jamais** introspectable ni accepté sur les
   routes protégées (`/v1/me`, endpoints 2FA). Il ne sert qu'à
   `/v1/two-factor/verify`.
3. Un token dont la `user_sessions` liée est `is_active = false` est refusé,
   même si le token existe encore en base.
4. Un reset de mot de passe révoque toutes les sessions actives.
5. Login : message d'erreur identique pour email inconnu et mot de passe faux
   (ne révèle pas les comptes existants).
6. Un code de récupération est consommé au premier usage.

## Frontière de données

Tables possédées : `users`, `roles`, `role_user`, `personal_access_tokens`,
`user_sessions`, `password_reset_tokens`.

Base **propre** (`vintapp_auth`). Les autres services stockent `user_id` comme
clé métier, jamais comme FK.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed          # crée les 4 rôles
php artisan serve --port=8101
```

MySQL en production : renseigner `DB_HOST` / `DB_PORT` / `DB_USERNAME` /
`DB_PASSWORD` / `DB_DATABASE` dans `.env`. Le compte MySQL doit avoir les droits
sur la base du service, pas seulement sur celle du monolithe.

`SERVICE_SHARED_SECRET` est **obligatoire** pour que les autres services
puissent introspecter. Sans lui, `/v1/token/introspect` répond `503`.

## Tests

```bash
vendor/bin/phpunit
```

22 tests, 91 assertions. Les tests tournent sur SQLite `:memory:` (configuré
dans `phpunit.xml`), indépendamment de la base de développement.
