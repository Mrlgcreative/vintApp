# Guide du système de support VintApp

## 🎯 Vue d'ensemble

Le système de support client VintApp offre une assistance complète avec :
- Widget flottant d'assistance accessible sur toutes les pages
- Chat rapide pour les questions simples
- Système de tickets complet avec suivi
- Interface d'administration pour gérer les demandes

## ✅ Composants installés

### 1. Widget d'assistance (pour utilisateurs)
**Emplacement** : En bas à droite de toutes les pages (après connexion)

**Fonctionnalités** :
- ✅ Bouton flottant avec badge de notifications
- ✅ Chat rapide intégré
- ✅ Affichage des conversations en cours
- ✅ Animation pulsante pour les nouveaux messages
- ✅ Actualisation automatique toutes les 30 secondes
- ✅ Compteur de caractères
- ✅ Gestion des erreurs

**Accès** : Cliquer sur le bouton flottant violet en bas à droite

### 2. Interface utilisateur complète
**Routes disponibles** :
- `/support` - Liste des conversations
- `/support/create` - Créer une demande détaillée
- `/support/{id}` - Voir une conversation
- `/support/quick-chat` - API chat rapide (POST)

**Fonctionnalités** :
- ✅ Créer une demande de support (avec catégorie, priorité, pièces jointes)
- ✅ Voir l'historique des conversations
- ✅ Répondre aux messages
- ✅ Fermer une conversation
- ✅ Pièces jointes (images, PDF, documents)

### 3. Interface d'administration
**Routes disponibles** :
- `/admin/support` - Liste et gestion des tickets
- `/admin/support/{id}` - Détails d'une conversation
- `/admin/support/{id}/assign` - Assigner un admin
- `/admin/support/{id}/status` - Changer le statut
- `/admin/support/{id}/priority` - Changer la priorité

**Fonctionnalités** :
- ✅ Tableau de bord avec statistiques
- ✅ Filtrage (statut, priorité, catégorie, admin)
- ✅ Assignation des tickets
- ✅ Changement de statut et priorité
- ✅ Réponses avec pièces jointes
- ✅ Fermeture/réouverture de conversations

## 🚀 Comment tester

### Test 1 : Chat rapide (Widget)
1. Connectez-vous à votre compte
2. Sur n'importe quelle page, cliquez sur le bouton violet en bas à droite
3. Le widget s'ouvre - vous devriez voir "Besoin d'aide ?"
4. Tapez un message court (ex: "J'ai besoin d'aide")
5. Cliquez sur "Envoyer"
6. ✅ Un message de succès devrait apparaître avec une référence
7. Le widget se met à jour pour afficher la nouvelle conversation

### Test 2 : Demande détaillée
1. Cliquez sur "Nouvelle demande détaillée" dans le widget
   OU accédez à `/support/create`
2. Remplissez le formulaire :
   - Catégorie : Problème technique
   - Sujet : Test de la demande
   - Priorité : Normal
   - Message : Description détaillée du problème
   - Pièces jointes : (optionnel) Ajoutez une image
3. Cliquez sur "Envoyer la demande"
4. ✅ Redirection vers la page de la conversation
5. ✅ Votre message initial devrait être affiché

### Test 3 : Répondre à une conversation
1. Accédez à `/support`
2. Cliquez sur une conversation existante
3. Dans le formulaire de réponse, écrivez un message
4. (Optionnel) Ajoutez des pièces jointes
5. Cliquez sur "Envoyer"
6. ✅ Votre réponse devrait apparaître dans la conversation
7. ✅ Le statut peut changer automatiquement

### Test 4 : Interface admin
1. Connectez-vous avec un compte admin
2. Accédez à `/admin/support`
3. ✅ Vous devriez voir toutes les demandes avec statistiques
4. Testez les filtres (statut, priorité, catégorie)
5. Cliquez sur une conversation
6. Testez :
   - Assignation à un admin
   - Changement de statut
   - Changement de priorité
   - Ajout d'une réponse
   - Fermeture/réouverture

### Test 5 : Actualisation en temps réel
1. Ouvrez deux navigateurs :
   - Navigateur 1 : Compte utilisateur
   - Navigateur 2 : Compte admin
2. Dans le navigateur 1 (utilisateur) :
   - Ouvrez le widget
   - Envoyez un message rapide
3. Dans le navigateur 2 (admin) :
   - Attendez max 30 secondes
   - ✅ Le compteur devrait se mettre à jour
   - Répondez à la conversation
4. Dans le navigateur 1 (utilisateur) :
   - Attendez max 30 secondes
   - ✅ Le badge de notification devrait apparaître

## 🎨 Styles Bootstrap

Tous les composants utilisent **Bootstrap 5** :
- Cards, badges, buttons : Classes Bootstrap natives
- Grid system : `row`, `col-*` pour la responsivité
- Modals : Composant modal Bootstrap natif
- Forms : Classes `form-control`, `form-select`
- Alerts : Classes `alert-success`, `alert-danger`

## 📱 Responsive

Le widget est entièrement responsive :
- **Desktop** : Widget flottant en bas à droite
- **Mobile** : Widget flottant adapté avec taille réduite
- **Tablette** : Taille intermédiaire

## 🔄 Actualisation automatique

**Widget** :
- Vérifie les nouveaux messages toutes les 30 secondes
- Met à jour le badge de notification
- Rafraîchit le contenu si le popup est fermé

**Interface admin** :
- Recharge manuelle via le bouton refresh
- Les statistiques se mettent à jour à chaque rechargement

## 🐛 Dépannage

### Le widget n'apparaît pas
- ✅ Vérifiez que vous êtes connecté (`@auth`)
- ✅ Vérifiez que le widget est inclus dans `app.blade.php`
- ✅ Vérifiez la console pour les erreurs JavaScript

### Le chat rapide ne fonctionne pas
- ✅ Vérifiez que la route `support.quick-chat` existe
- ✅ Vérifiez le token CSRF dans la meta
- ✅ Vérifiez la console réseau (Network tab)

### Les pièces jointes ne s'affichent pas
- ✅ Vérifiez que le lien symbolique storage existe : `php artisan storage:link`
- ✅ Vérifiez les permissions du dossier `storage/app/public`

### Les statistiques admin sont nulles
- ✅ Vérifiez que les modèles `SupportChat` existent
- ✅ Vérifiez que la méthode `getSupportStats()` est dans `DashboardController`

## 📊 Base de données

Tables créées :
- `support_chats` : Conversations principales
- `support_messages` : Messages individuels

Relations :
- Un `SupportChat` appartient à un `User` (créateur)
- Un `SupportChat` peut être assigné à un `User` (admin)
- Un `SupportChat` a plusieurs `SupportMessage`
- Un `SupportMessage` appartient à un `User` (auteur)

## 🎯 Prochaines étapes

Pour améliorer le système :
1. **Notifications en temps réel** : WebSockets (Laravel Echo + Pusher)
2. **Recherche** : Recherche dans les conversations
3. **Export** : Export des conversations en PDF
4. **Analytics** : Temps de réponse moyen, satisfaction client
5. **Chatbot** : Réponses automatiques pour questions fréquentes
6. **Notes internes** : Notes privées entre admins
7. **Tags** : Étiquettes pour catégoriser les conversations
8. **SLA** : Alertes pour les délais de réponse

## 📝 Notes importantes

- Le widget utilise Bootstrap Modal API (pas de classes Tailwind)
- Les routes utilisateur et admin sont séparées
- Le système gère automatiquement les conversations existantes dans le chat rapide
- Les fichiers sont limités à 5 MB par défaut
- Le système est entièrement traduit en français

## 🔐 Sécurité

- ✅ Protection CSRF sur toutes les requêtes
- ✅ Validation des données (server-side)
- ✅ Middleware `auth` pour toutes les routes
- ✅ Vérification des permissions (utilisateur peut voir uniquement ses conversations)
- ✅ Validation des types de fichiers
- ✅ Limite de taille des fichiers

---

**Version** : 1.0.0
**Date** : {{ date('d/m/Y') }}
**Framework** : Laravel avec Bootstrap 5
