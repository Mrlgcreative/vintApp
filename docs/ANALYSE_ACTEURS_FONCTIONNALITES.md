# Analyse du projet VintApp

## Aperçu général
VintApp est une **marketplace de luxe et d'occasion** (type Vinted/Vestiaire Collective) construite avec **Laravel 13 + Inertia.js + Vue 3**. Elle permet l'achat/vente d'articles de luxe, la vérification d'authenticité par des experts, les paiements multi-devises (USD/CDF), la livraison locale, la messagerie temps réel, et un programme d'affiliation.

---

## 1. ACTEURS DU SYSTÈME

| Acteur | Description | Rôle technique |
|--------|-------------|----------------|
| **Visiteur** | Utilisateur non connecté | Aucun rôle en base |
| **Utilisateur (Acheteur/Vendeur)** | Membre inscrit et connecté | Rôle `user` |
| **Expert** | Vérificateur d'authenticité certifié | Table `expert_profiles` |
| **Agent de Support** | Support client | Table `support_agents` |
| **Administrateur** | Gestion du backend | Rôle `admin` |
| **Système de Paiement** | Passerelles externes (CinetPay, AfribaPay, MaishaPay, etc.) | API externe |
| **Firebase/Auth Social** | Fournisseurs OAuth (Google, Apple) | API externe |
| **Pusher** | Service temps réel (messagerie, notifications) | API externe |

---

## 2. FONCTIONNALITÉS PAR ACTEUR

### 2.1 VISITEUR (Non connecté)

| Fonctionnalité | Détail |
|----------------|--------|
| **Consulter la page d'accueil** | Voir le splash screen, le hero carousel, les catégories, les articles en vedette |
| **Rechercher des articles** | Par mot-clé, catégorie, marque, prix |
| **Consulter un article** | Voir les détails, photos, prix, description |
| **Consulter les catégories/marques** | Naviguer par catégorie et marque |
| **Se pré-inscrire (waiting list)** | Laisse son email pour être notifié à l'ouverture |
| **S'inscrire / Créer un compte** | Inscription par email + vérification, ou via Google/Apple OAuth |
| **Se connecter** | Authentification par email/mot de passe ou OAuth |
| **Voir les pages légales** | Conditions générales, politique de confidentialité |
| **S'abonner à la newsletter** | Inscription à la newsletter |
| **Consulter le taux de change** | Voir les taux USD/CDF |
| **Vérifier un VintPass** | Scanner un code QR VintPass pour vérifier son authenticité |
| **Contacter le support** | Accéder à la page d'aide |

### 2.2 UTILISATEUR (Acheteur/Vendeur)

#### Gestion de profil

| Fonctionnalité | Détail |
|----------------|--------|
| **Gérer son profil** | Modifier nom, email, téléphone, bio, adresse, avatar |
| **Configurer le thème** | Changer le thème de couleur de l'interface |
| **Activer la 2FA** | Authentification à deux facteurs Google2FA |
| **Gérer les sessions actives** | Voir et révoquer les sessions connectées |
| **Voir les notifications** | Notifications en temps réel (Pusher) et push (FCM) |
| **S'inscrire à la newsletter** | S'abonner/se désabonner |

#### Module Marketplace (Achat)

| Fonctionnalité | Détail |
|----------------|--------|
| **Rechercher des articles** | Filtres par catégorie, marque, prix, état |
| **Ajouter aux favoris** | Sauvegarder des articles favoris |
| **Ajouter au panier** | Gérer un panier d'achats |
| **Passer commande** | Commander un ou plusieurs articles |
| **Payer une commande** | Paiement via CinetPay, AfribaPay, MaishaPay, Illicocash, Orange Money, Airtel Money, Mpesa, Africell |
| **Payer via wallet** | Utiliser le solde du wallet interne |
| **Négocier le prix** | Demander/Proposer une réduction sur un article |
| **Évaluer le vendeur** | Laisser un avis/note après achat |
| **Suivre la commande** | Voir le statut et le tracking de la commande |
| **Contacter le vendeur** | Messagerie temps réel |
| **Demander un remboursement** | Ouvrir une demande de remboursement |

#### Module Marketplace (Vente)

| Fonctionnalité | Détail |
|----------------|--------|
| **Publier un article** | Créer une annonce avec photos, description, prix |
| **Gérer ses articles** | Modifier, activer/désactiver, supprimer ses annonces |
| **Voir ses ventes** | Liste des commandes reçues |
| **Gérer les commandes** | Confirmer, préparer, expédier |
| **Proposer une réduction** | Faire une contre-offre à un acheteur |
| **Booster un article** | Promouvoir un article (système de boost payant) |
| **Voir les statistiques** | Nombre de vues, favoris, ventes |
| **Contacter l'acheteur** | Messagerie temps réel |

#### Module Wallet & Transactions

| Fonctionnalité | Détail |
|----------------|--------|
| **Voir son wallet** | Solde disponible et en attente (USD/CDF) |
| **Déposer des fonds** | Alimenter son wallet via paiement |
| **Retirer des fonds** | Demander un retrait vers un compte mobile/bancaire |
| **Voir l'historique** | Transactions, commissions, retraits |
| **Conversion de devises** | Convertir entre USD et CDF |

#### Module VintPass

| Fonctionnalité | Détail |
|----------------|--------|
| **Acheter un VintPass** | Pass numérique pour garantie d'authenticité |
| **Transférer un VintPass** | Transférer son pass à un autre utilisateur |
| **Scanner un VintPass** | Scanner un QR code pour vérifier |
| **Voir ses VintPass** | Historique et statut de ses passes |

#### Module Livraison Locale

| Fonctionnalité | Détail |
|----------------|--------|
| **Planifier une livraison locale** | Pour les articles avec acheteur/vendeur dans la même ville |
| **Suivre la livraison** | GPS tracking en temps réel |
| **Confirmer la réception** | Marquer comme reçu |

#### Module Affiliation

| Fonctionnalité | Détail |
|----------------|--------|
| **Générer un code de parrainage** | Créer son code personnel |
| **Parrainer des amis** | Partager son code, gagner des points |
| **Voir ses points** | Solde de points de fidélité |
| **Échanger des points** | Convertir ses points en réduction ou argent |
| **Voir le classement** | Leaderboard d'affiliation |
| **Voir ses récompenses** | Historique des gains d'affiliation |

#### Module Authenticité

| Fonctionnalité | Détail |
|----------------|--------|
| **Soumettre un article à vérification** | Payer pour faire vérifier un article par un expert |
| **Voir le résultat** | Certificat d'authenticité ou rejet |

### 2.3 EXPERT (Vérificateur)

| Fonctionnalité | Détail |
|----------------|--------|
| **Voir le tableau de bord expert** | Statistiques, notifications |
| **Recevoir des demandes de vérification** | Liste des articles à vérifier |
| **Examiner un article** | Voir les photos, description, soumettre son verdict |
| **Valider ou rejeter** | Approuver l'authenticité ou la rejeter avec motifs |
| **Gérer son profil** | Spécialités, commission, disponibilité |
| **Voir l'historique** | Vérifications effectuées, taux d'approbation |

### 2.4 AGENT DE SUPPORT

| Fonctionnalité | Détail |
|----------------|--------|
| **Voir les tickets de support** | Liste des conversations ouvertes |
| **Prendre un ticket** | Auto-assignment selon spécialité et charge |
| **Répondre aux utilisateurs** | Messagerie de support temps réel |
| **Fermer un ticket** | Résoudre et clôturer |
| **Gérer sa disponibilité** | Actif/inactif, nombre max de chats |
| **Voir ses statistiques** | Tickets résolus, temps de réponse |

### 2.5 ADMINISTRATEUR

#### Gestion des utilisateurs

| Fonctionnalité | Détail |
|----------------|--------|
| **Lister les utilisateurs** | Voir tous les comptes |
| **Modifier les utilisateurs** | Changer rôle, statut, informations |
| **Gérer les rôles** | Assigner les rôles admin/user/expert |
| **Gérer les inscriptions en attente** | Valider/rejeter les pré-inscriptions |

#### Gestion des articles

| Fonctionnalité | Détail |
|----------------|--------|
| **Lister tous les articles** | Modération des annonces |
| **Activer/Désactiver** | Approuver ou masquer un article |
| **Gérer les catégories** | CRUD catégories |
| **Gérer les marques** | CRUD marques |

#### Gestion des commandes

| Fonctionnalité | Détail |
|----------------|--------|
| **Voir toutes les commandes** | Suivi global |
| **Gérer les statuts** | Confirmer, annuler, rembourser |
| **Gérer les remboursements** | Approuver/rejeter les demandes |

#### Gestion financière

| Fonctionnalité | Détail |
|----------------|--------|
| **Voir les wallets** | Tous les wallets utilisateurs |
| **Voir les transactions** | Historique complet |
| **Voir les demandes de retrait** | Approuver/rejeter les retraits |
| **Voir les transactions en attente** | Valider les transactions wallet |
| **Gérer les commissions** | Configurer les taux de commission |
| **Voir les paiements** | Historique des paiements par fournisseur |

#### Gestion de l'authenticité

| Fonctionnalité | Détail |
|----------------|--------|
| **Gérer les experts** | Activer/désactiver, configurer commissions |
| **Voir les vérifications** | Audit des vérifications effectuées |
| **Voir les logs d'authenticité** | Historique complet |

#### Gestion du support

| Fonctionnalité | Détail |
|----------------|--------|
| **Gérer les agents de support** | Ajouter/supprimer des agents |
| **Voir tous les tickets** | Supervision du support |
| **Intervenir** | Répondre ou assigner |

#### Paramètres système

| Fonctionnalité | Détail |
|----------------|--------|
| **Gérer les paramètres** | Configuration générale de l'application |
| **Gérer le système de couleurs** | Personnaliser les couleurs du thème |
| **Gérer la newsletter** | Voir les abonnés, envoyer des campagnes |
| **Gérer les restrictions géographiques** | Configurer les villes/régions autorisées |
| **Monitoring** | Tableau de bord de performance, logs |
| **Voir les slides du hero** | Gérer le carousel de la page d'accueil |
| **Diffuser des notifications push** | Envoyer des notifications FCM à tous les utilisateurs |

---

## 3. RELATIONS ENTRE ACTEURS

```
                    ┌──────────────────────┐
                    │   VISITEUR (Guest)    │
                    └──────┬───────────────┘
                           │ s'inscrit
                           ▼
                    ┌──────────────────────┐
                    │   UTILISATEUR (User)  │◄────────── Parrainage (Affiliation)
                    │  ┌──────┬──────┐     │
                    └──┤Acheteur├─Vendeur├──┘
                       └──────┴──────┘
                           │        │
              ┌────────────┘        └────────────┐
              ▼                                    ▼
      ┌───────────────┐                   ┌──────────────┐
      │  EXPERT       │                   │   ADMIN      │
      │(Vérif.        │                   │(Superviseur) │
      │ authenticité) │                   └──────┬───────┘
      └───────┬───────┘                          │
              │                                  ▼
              │                          ┌──────────────────┐
              │                          │   AGENT SUPPORT  │
              │                          │(Support client)  │
              │                          └──────────────────┘
              │
              ▼
      ┌────────────────────────────┐
      │  SYSTÈME DE PAIEMENT       │
      │ (CinetPay, AfribaPay, etc) │
      └────────────────────────────┘
```

---

## 4. RÉSUMÉ DES MODULES FONCTIONNELS

| Module | Acteurs concernés |
|--------|-------------------|
| **Authentification & Inscription** | Visiteur → Utilisateur |
| **Marketplace (Articles)** | Visiteur, Utilisateur, Admin |
| **Commandes & Paiements** | Acheteur, Vendeur, Admin, Système Paiement |
| **Messagerie** | Acheteur, Vendeur |
| **Wallet & Transactions** | Utilisateur, Admin |
| **Authenticité** | Utilisateur, Expert, Admin |
| **VintPass** | Utilisateur, Visiteur |
| **Support** | Utilisateur, Agent Support, Admin |
| **Affiliation/Parrainage** | Utilisateur |
| **Livraison Locale** | Acheteur, Vendeur |
| **Newsletter** | Visiteur, Admin |
| **Notifications** | Utilisateur, Admin |
| **Pré-inscription (Waiting List)** | Visiteur, Admin |
| **Géolocalisation** | Visiteur, Utilisateur |
| **Configuration système** | Admin |
