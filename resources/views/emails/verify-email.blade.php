@component('mail::message')
# 🎉 Bienvenue sur VintApp !

Bonjour **{{ $user->name }}**,

Merci de vous être inscrit sur **VintApp**, votre marketplace de confiance pour acheter et vendre des articles vintage et uniques !

Pour activer votre compte et commencer à explorer des milliers d'articles, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
✅ Vérifier mon email
@endcomponent

**Ce lien expirera dans 60 minutes.**

---

## 🚀 Prochaines étapes

Une fois votre email vérifié, vous pourrez :

- 🛍️ **Parcourir** des milliers d'articles vintage
- 💰 **Vendre** vos propres articles
- ⭐ **Ajouter** des favoris
- 💬 **Échanger** avec d'autres utilisateurs
- 🔔 **Recevoir** des notifications personnalisées

---

## 🔒 Sécurité

Si vous n'avez pas créé de compte sur VintApp, ignorez simplement cet email. Aucune action supplémentaire n'est requise.

**Lien de vérification manuel :**  
Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :
{{ $verificationUrl }}

---

Merci de faire partie de la communauté VintApp ! 🙌

**L'équipe VintApp**

@component('mail::subcopy')
**Vous avez des questions ?**  
Contactez-nous à [{{ config('mail.from.address') }}](mailto:{{ config('mail.from.address') }}) ou visitez notre [centre d'aide](#).
@endcomponent
@endcomponent
