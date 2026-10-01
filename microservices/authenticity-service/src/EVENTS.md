# authenticity-service — Contrat d'événements

## Émis
- `verification.approved` / `verification.rejected` : décision d'un expert
  (check_id, item_id, approved).

## Consommés
- `payment.completed` (payment-service) → confirmation du paiement des frais
  de vérification.
