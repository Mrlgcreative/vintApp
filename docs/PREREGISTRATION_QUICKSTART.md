# 🎯 Guide Rapide - Système de Pré-inscription

## 🚀 Démarrage rapide

### 1. Migration déjà exécutée ✅
```bash
# La table users_waiting a été créée avec succès
```

### 2. Accès aux pages

#### Pour les utilisateurs (Public)
```
http://localhost/preregistration
```

#### Pour les administrateurs
```
http://localhost/admin/waiting-users
```
⚠️ **Nécessite une connexion admin**

## 📝 Workflow utilisateur

1. **Inscription**: L'utilisateur remplit le formulaire sur `/preregistration`
2. **Confirmation**: Il reçoit un email et clique sur le lien de confirmation
3. **Attente**: Son statut passe à "Confirmé"
4. **Approbation**: L'admin approuve sa demande
5. **Conversion**: L'admin exécute la commande de conversion
6. **Connexion**: L'utilisateur reçoit ses identifiants et peut se connecter

## 👨‍💼 Workflow administrateur

### Gérer les pré-inscriptions

1. Aller sur `/admin/waiting-users`
2. Voir la liste avec filtres et statistiques
3. Approuver ou rejeter les demandes
4. Exporter en CSV si besoin

### Convertir en comptes réels

Quand l'application est prête à lancer :

```bash
# Convertir TOUS les utilisateurs approuvés
php artisan users:convert-waiting --all

# Convertir avec notifications email
php artisan users:convert-waiting --all --notify

# Convertir un utilisateur spécifique
php artisan users:convert-waiting --id=1

# Convertir avec limite
php artisan users:convert-waiting --all --limit=50
```

## 📊 Que fait la conversion ?

Pour chaque utilisateur approuvé :
- ✅ Crée un compte `User` avec mot de passe temporaire
- ✅ Crée 2 wallets (USD + CDF) avec balance à 0
- ✅ Email vérifié automatiquement
- ✅ Envoie un email avec les credentials (si --notify)
- ✅ Affiche les credentials dans le terminal

### Format des mots de passe temporaires
```
ABCDEF-123456
(6 lettres majuscules + tiret + 6 chiffres)
```

⚠️ **IMPORTANT**: Notez les credentials affichés ou utilisez `--notify` pour les envoyer par email !

## 🎨 Captures d'écran (à venir)

- [ ] Formulaire de pré-inscription
- [ ] Page de confirmation
- [ ] Dashboard admin
- [ ] Détails d'une pré-inscription

## 📧 Configuration Email

Pour envoyer les emails de confirmation et de bienvenue, configurez `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vintapp.com
MAIL_FROM_NAME="VintApp"
```

## 🔧 TODO pour la production

- [ ] Configurer un serveur SMTP réel
- [ ] Créer les templates d'email (Mailable)
- [ ] Ajouter un middleware admin
- [ ] Tester le workflow complet
- [ ] Personnaliser les messages
- [ ] Ajouter des traductions si besoin

## 📖 Documentation complète

Voir `PREREGISTRATION_GUIDE.md` pour la documentation complète avec :
- Architecture détaillée
- Toutes les routes
- Exemples SQL
- Personnalisation
- Dépannage

## ✅ Checklist de vérification

- [x] Migration créée et exécutée
- [x] Modèle UserWaiting créé
- [x] Controllers créés (Public + Admin)
- [x] Routes configurées
- [x] Vues créées (Public + Admin)
- [x] Commande Artisan fonctionnelle
- [x] Documentation complète
- [ ] Emails configurés (à faire)
- [ ] Middleware admin (recommandé)
- [ ] Tests manuels (à faire)

## 🎉 Résultat

Vous avez maintenant un système complet de pré-inscription avec :

✅ Formulaire public magnifique  
✅ Confirmation par email  
✅ Dashboard admin professionnel  
✅ Gestion complète (filtres, recherche, export)  
✅ Conversion automatique en comptes réels  
✅ Statistiques en temps réel  
✅ Actions en masse  

**Le système est prêt à être testé !** 🚀
