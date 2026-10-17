# marketing-service — Contrat d'événements

## Émis
- `points.awarded` : points attribués (user_id, points, reason).
- `referral.completed` : parrainage abouti (referrer_id, referee_id).

## Consommés
- `order.completed` (order-service) → attribution points + complétion parrainage.
