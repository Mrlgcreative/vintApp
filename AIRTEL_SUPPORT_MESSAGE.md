# 📧 Message pour le Support Airtel Money

---

**Objet** : Validation de l'URL de Callback - VintApp

**Destinataire** : developer.support@airtel.com

---

Bonjour l'équipe Airtel Developer Support,

Je souhaite enregistrer une URL de callback pour mon application **VintApp** mais je reçois l'erreur suivante lors de la soumission :

```
"Not able to update callback data. Please try again"
```

## 📋 Détails de mon application

- **Nom de l'application** : VintApp
- **URL de Callback** : `https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money`
- **Environment** : Sandbox
- **Type d'intégration** : Mobile Money Payment

## ✅ Preuve que l'URL est fonctionnelle

Mon endpoint API est accessible et fonctionnel. Voici la preuve :

### Test 1 : Health Check
```bash
curl -H "ngrok-skip-browser-warning: true" \
  https://uncomely-uneffusing-averie.ngrok-free.dev/api/health
```

**Résultat** :
```json
{
  "status": "success",
  "message": "VintApp API is running",
  "version": "1.0.0"
}
```

### Test 2 : Callback Endpoint
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "ngrok-skip-browser-warning: true" \
  -d '{"transaction":{"id":"TEST123","status":"success"}}' \
  https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
```

**Résultat attendu** : Status 200 OK

## 🔍 Cause du problème

J'utilise **ngrok** (service de tunnel gratuit) pour exposer mon environnement de développement local. ngrok affiche une page d'avertissement pour les utilisateurs humains, mais l'API reste accessible via des requêtes HTTP avec l'en-tête approprié :

```
ngrok-skip-browser-warning: true
```

## 🙏 Ma demande

Pourriez-vous :

1. **Option A** : Configurer votre système de validation pour inclure l'en-tête `ngrok-skip-browser-warning: true` lors du test de l'URL

2. **Option B** : Valider manuellement mon URL de callback après avoir vérifié qu'elle est bien accessible

3. **Option C** : Me fournir les critères exacts de validation pour que je puisse adapter mon setup

## 📞 Mes coordonnées

- **Email** : gloirelumingu10@gmail.com
- **Application** : VintApp
- **Pays** : République Démocratique du Congo

## 📎 Informations techniques supplémentaires

- **Protocole** : HTTPS ✅
- **Port** : 443 (standard)
- **Method** : POST
- **Content-Type** : application/json
- **Response Format** : JSON

Je reste disponible pour tout test supplémentaire ou information complémentaire.

Merci d'avance pour votre aide !

Cordialement,
Gloire Lumingu
VintApp Developer

---

**Alternative URLs (si disponibles)** :

Si vous avez besoin d'une URL sans ngrok, je peux également fournir :
- [ ] URL avec localtunnel
- [ ] URL avec serveo.net  
- [ ] URL avec un domaine permanent (si nécessaire)

