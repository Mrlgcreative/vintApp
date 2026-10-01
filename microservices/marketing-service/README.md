# marketing-service

Microservice de **marketing / engagement / notifications** de VintApp.

> À extraire : `AffiliateService` (parrainage, points), `VintPassService`,
> `NotificationService`, `FirebasePushService`, `FirestoreService`,
> `PushNotificationService`, `AwardOrderPoints`.

## Périmètre

- Affiliation & parrainage (références, récompenses).
- Points & récompenses (`user_points`, `point_transactions`, `point_redemptions`).
- VintPass (abonnements et avantages).
- Notifications in-app + push (Firebase) + Firestore.

## Structure

```
src/
  App/
    Http/Controllers/
    Http/Middleware/
    Events/            # PointsAwarded, ReferralCompleted
    Listeners/         # écoute order.completed -> points/parrainage
    Jobs/              # envoi de push (async), traitement de masse
    Providers/
  Config/
  Database/Migrations/ # referrals, user_points, vintpass, notifications
  Routes/
  Docs/
```

## Événements consommés

- `order.completed` (attribution des points / complétion de parrainage)

## Frontière de données

Tables de propriété : `referrals`, `user_points`, `point_transactions`,
`point_redemptions`, `vintpass_*`, `notifications` (+ Firestore/Firebase).
