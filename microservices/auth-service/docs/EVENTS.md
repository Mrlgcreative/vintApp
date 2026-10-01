# auth-service — Contrat d'événements

## Émis

| Type | Déclencheur | Données |
|---|---|---|
| `user.registered` | `UserRegistered` après création de compte | `user_id`, `public_id`, `email` |
| `user.authenticated` | `UserAuthenticated` après login réussi (2FA validée ou non requise) | `user_id`, `public_id`, `email` |

Ces événements sont émis sur la file locale. Le branchement Redis / la
publication vers `microservices/EVENTS.md` se fait quand le premier consommateur
(order-service, marketing-service) existe.

## Consommés

Aucun pour l'instant. `withdrawal.requested` et `payment.*` concernent
payment-service et wallet-service, qui lynquent sur `user_id` sans avoir besoin
d'un callback ici.

## Note sur les tokens 2FA

Un `UserAuthenticated` n'est **pas** émis tant que la 2FA n'est pas validée.
L'événement ne doit donc jamais être traité comme preuve qu'un utilisateur est
authentifié, mais comme trace d'une connexion réussie.
