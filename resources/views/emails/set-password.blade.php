<x-mail::message>
# 🎉 Félicitations {{ $name }} !

Votre pré-inscription sur **VintApp** a été **approuvée** ! 

Votre compte utilisateur a été créé avec succès. Pour y accéder, vous devez d'abord définir votre mot de passe personnel.

<x-mail::button :url="$setupUrl" color="success">
✨ Définir mon mot de passe
</x-mail::button>

## 🔐 Informations importantes

- Ce lien est **valide pendant 7 jours**
- Il est à **usage unique** (une seule utilisation)
- Après avoir défini votre mot de passe, vous serez automatiquement connecté à votre compte

## ⏰ Date d'expiration

Votre lien expire le : **{{ $expiresAt->format('d/m/Y à H:i') }}**

---

Si vous n'avez pas demandé la création de ce compte ou si vous rencontrez des problèmes, contactez-nous immédiatement.

Merci de faire partie de la communauté VintApp ! 🚀

Cordialement,<br>
{{ config('app.name') }}

<x-mail::subcopy>
Si vous avez des difficultés à cliquer sur le bouton, copiez et collez l'URL suivante dans votre navigateur :
[{{ $setupUrl }}]({{ $setupUrl }})
</x-mail::subcopy>
</x-mail::message>

