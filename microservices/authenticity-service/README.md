# authenticity-service

Microservice de **vérification d'authenticité** de VintApp.

> À extraire : `ProductAuthenticityCheck`, `ExpertProfile`, `VerificationImage`,
> `AuthenticityVerificationService`, `VerificationPaymentService`,
> `AuthenticityController`, `ExpertNotificationService`.

## Périmètre

- Demandes de vérification d'un produit.
- Catalogues & comptes d'**experts** certifiés.
- Tâches d'authenticité & certification (approbation/rejet expert).
- Facturation des frais de vérification (débit wallet via wallet-service).
- Scan/QR et suivi de la certification.

## Structure

```
src/
  App/
    Http/Controllers/
    Http/Middleware/      # expert (équivalent IsExpert monolithique)
    Events/               # VerificationApproved, VerificationRejected
    Listeners/
    Jobs/
    Providers/
  Config/
  Database/Migrations/    # product_authenticity_checks, expert_profiles, ...
  Routes/
  Docs/
```

## Événements émis

- `verification.approved`
- `verification.rejected`

## Événements consommés

- `payment.completed` (paiement des frais de vérification)

## Frontière de données

Tables de propriété : `product_authenticity_checks`, `expert_profiles`,
`verification_images`, `audit_logs`.
