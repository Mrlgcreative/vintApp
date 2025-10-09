<x-mail::message>
# Bienvenue sur VintApp, {{ $name }} ! 🎉

Nous sommes ravis de vous annoncer que votre demande de pré-inscription a été **approuvée avec succès**.

Votre compte est maintenant prêt ! Pour commencer à utiliser VintApp et accéder à toutes nos fonctionnalités, il ne vous reste plus qu'à créer votre mot de passe personnel.

<x-mail::panel>
**Pourquoi définir un mot de passe ?**

Pour garantir la sécurité de votre compte et protéger vos informations personnelles, nous vous demandons de choisir un mot de passe fort et unique.
</x-mail::panel>

<x-mail::button :url="$setupUrl" color="success">
Créer mon mot de passe
</x-mail::button>

## Informations importantes

<x-mail::table>
| Détail | Information |
|:-------|:------------|
| **Validité du lien** | 7 jours |
| **Nombre d'utilisations** | Usage unique |
| **Expiration** | {{ $expiresAt->format('d/m/Y à H:i') }} |
| **Connexion automatique** | Oui, après la création |
</x-mail::table>

---

## Ce qui vous attend sur VintApp

✨ **Explorez** - Découvrez des articles vintage uniques  
💬 **Échangez** - Communiquez directement avec les vendeurs  
💰 **Achetez** - Transactions sécurisées et simplifiées  
� **Vendez** - Mettez en vente vos propres articles  

---

<x-mail::subcopy>
**Besoin d'aide ?**

Si vous rencontrez des difficultés ou si vous n'êtes pas à l'origine de cette demande, n'hésitez pas à nous contacter immédiatement.

**Problème avec le bouton ?** Copiez et collez ce lien dans votre navigateur :  
{{ $setupUrl }}
</x-mail::subcopy>

Merci de rejoindre la communauté VintApp ! 🚀

L'équipe {{ config('app.name') }}
</x-mail::message>

