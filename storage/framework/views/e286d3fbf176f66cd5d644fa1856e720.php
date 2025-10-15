<?php $__env->startComponent('mail::message'); ?>
# 🎉 Bienvenue sur VintApp !

Bonjour **<?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>**,

Merci de vous être inscrit sur **VintApp**, votre marketplace de confiance pour acheter et vendre des articles vintage et uniques !

Pour activer votre compte et commencer à explorer des milliers d'articles, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :

<?php $__env->startComponent('mail::button', ['url' => $verificationUrl, 'color' => 'primary']); ?>
✅ Vérifier mon email
<?php echo $__env->renderComponent(); ?>

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
<?php echo new \Illuminate\Support\EncodedHtmlString($verificationUrl); ?>


---

Merci de faire partie de la communauté VintApp ! 🙌

**L'équipe VintApp**

<?php $__env->startComponent('mail::subcopy'); ?>
**Vous avez des questions ?**  
Contactez-nous à [<?php echo new \Illuminate\Support\EncodedHtmlString(config('mail.from.address')); ?>](mailto:<?php echo new \Illuminate\Support\EncodedHtmlString(config('mail.from.address')); ?>) ou visitez notre [centre d'aide](#).
<?php echo $__env->renderComponent(); ?>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/emails/verify-email.blade.php ENDPATH**/ ?>