# Guide Complet du Système de Remboursement - VintApp

## Vue d'ensemble

Le système de remboursement VintApp permet aux acheteurs de demander des remboursements et aux administrateurs de les gérer efficacement avec un système de notifications automatiques.

## Fonctionnalités principales

### 1. Demande de remboursement (Acheteur)

-   **Éligibilité automatique** : Vérification des critères de remboursement
-   **Formulaire sécurisé** : Protection CSRF et validation des données
-   **Types de remboursement** : Complet, partiel, ou retour produit
-   **Suivi en temps réel** : États visible dans l'interface utilisateur

### 2. Gestion administrative

-   **Interface dédiée** : Menu "Remboursements" dans l'administration
-   **Actions disponibles** : Approuver, rejeter, négocier
-   **Contrôle d'accès** : Restrictions par rôles administrateurs
-   **Historique complet** : Suivi de toutes les actions

### 3. Notifications automatiques

-   **Multi-canal** : Notifications en app et par email
-   **Événements couverts** : Approbation, rejet, négociation
-   **Destinataires** : Acheteurs et vendeurs concernés
-   **Journalisation** : Logs détaillés pour le débogage

## Architecture technique

### Modèles de données

```php
// Relation Order -> Refunds
public function refunds() {
    return $this->hasMany(Refund::class);
}

// Relation User -> Orders
public function orders() {
    return $this->hasMany(Order::class, 'buyer_id');
}
```

### Contrôleurs principaux

-   **PaymentController** : Logique métier des remboursements
-   **Admin\RefundController** : Interface d'administration
-   **NotificationService** : Gestion centralisée des notifications

### Méthodes de notification

```php
// Service de notifications pour remboursements
createRefundApprovedNotification($refund)
createRefundRejectedNotification($refund)
createRefundNegotiationNotification($refund)
```

## Routes disponibles

### Interface publique

-   `GET /refunds` : Liste des remboursements utilisateur
-   `POST /refunds` : Créer une demande de remboursement
-   `GET /refunds/{id}` : Détails d'un remboursement

### Interface administrative

-   `GET /admin/refunds` : Liste de tous les remboursements
-   `GET /admin/refunds/{id}` : Détails administrateur
-   `POST /admin/refunds/{id}/approve` : Approuver un remboursement
-   `POST /admin/refunds/{id}/reject` : Rejeter un remboursement
-   `POST /admin/refunds/{id}/negotiate` : Proposer une contre-offre

## États des remboursements

1. **pending** : En attente de traitement
2. **approved** : Approuvé par l'administrateur
3. **rejected** : Rejeté par l'administrateur
4. **negotiating** : En cours de négociation
5. **completed** : Remboursement effectué

## Critères d'éligibilité

### Remboursement complet

-   Commande livrée depuis moins de 30 jours
-   Produit non retournable = false
-   Aucun remboursement précédent sur cette commande

### Remboursement partiel

-   Commande livrée depuis moins de 15 jours
-   Montant demandé ≤ 50% du total
-   Justification fournie

### Retour produit

-   Commande livrée depuis moins de 7 jours
-   Produit en parfait état
-   Frais de retour à la charge de l'acheteur

## Sécurité et validations

### Protection CSRF

-   Tous les formulaires incluent le token CSRF
-   Validation côté serveur obligatoire

### Contrôle d'accès

-   Interface admin : rôle administrateur requis
-   Interface utilisateur : propriétaire de la commande uniquement

### Validation des données

```php
'reason' => 'required|string|max:1000',
'amount' => 'required|numeric|min:0.01',
'type' => 'required|in:full,partial,return'
```

## Notifications détaillées

### Approbation de remboursement

-   **Destinataires** : Acheteur et vendeur
-   **Message acheteur** : "Votre demande de remboursement a été approuvée"
-   **Message vendeur** : "Un remboursement a été approuvé pour votre vente"
-   **Actions** : Traitement du paiement automatique

### Rejet de remboursement

-   **Destinataire** : Acheteur uniquement
-   **Message** : "Votre demande de remboursement a été rejetée"
-   **Détails** : Raison du rejet incluse

### Négociation

-   **Destinataire** : Acheteur uniquement
-   **Message** : "Nouvelle proposition pour votre remboursement"
-   **Détails** : Montant et conditions de la contre-offre

## Journalisation et monitoring

### Logs automatiques

```php
Log::info('Remboursement approuvé', [
    'refund_id' => $refund->id,
    'admin_id' => auth()->id(),
    'amount' => $refund->amount
]);
```

### Métriques recommandées

-   Nombre de demandes par jour
-   Taux d'approbation/rejet
-   Délais de traitement moyen
-   Montants remboursés par période

## Maintenance et troubleshooting

### Vérifications de santé

Utilisez le script `test_refund_system.php` pour :

-   Vérifier les relations entre modèles
-   Tester l'accès aux tables
-   Valider les méthodes de notification

### Erreurs communes

1. **"Call to undefined method refunds()"**

    - Vérifiez la présence de la relation dans le modèle Order

2. **"CSRF token mismatch"**

    - Assurez-vous que le token CSRF est inclus dans les formulaires

3. **"Method [orders] does not exist"**
    - Vérifiez la relation orders() dans le modèle User

### Logs de débogage

```bash
# Surveiller les logs en temps réel
tail -f storage/logs/laravel.log | grep -i refund
```

## Évolutions futures possibles

1. **Remboursements automatiques** : Critères prédéfinis
2. **API REST** : Intégration avec applications mobiles
3. **Webhooks** : Notifications vers systèmes externes
4. **Analytics** : Tableau de bord avec statistiques détaillées
5. **Multi-devises** : Support des devises différentes

---

## Support technique

En cas de problème, vérifiez :

1. Les logs Laravel dans `storage/logs/`
2. La configuration de la base de données
3. Les permissions des rôles utilisateurs
4. Le statut des services de notification
