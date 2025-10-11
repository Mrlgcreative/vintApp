# 📱 URLs de Callback - VintApp

## 🎯 URLs à fournir aux opérateurs de paiement

### Airtel Money
```
Callback URL: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/airtel_money
Success URL: https://uncomely-uneffusing-averie.ngrok-free.dev/payment/success
Cancel URL:  https://uncomely-uneffusing-averie.ngrok-free.dev/payment/cancel
```

### M-Pesa
```
Callback URL: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/mpesa
Success URL: https://uncomely-uneffusing-averie.ngrok-free.dev/payment/success
Cancel URL:  https://uncomely-uneffusing-averie.ngrok-free.dev/payment/cancel
```

### Orange Money
```
Callback URL: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/orange_money
Success URL: https://uncomely-uneffusing-averie.ngrok-free.dev/payment/success
Cancel URL:  https://uncomely-uneffusing-averie.ngrok-free.dev/payment/cancel
```

### Africell Money
```
Callback URL: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/africell
Success URL: https://uncomely-uneffusing-averie.ngrok-free.dev/payment/success
Cancel URL:  https://uncomely-uneffusing-averie.ngrok-free.dev/payment/cancel
```

### Illicocash
```
Callback URL: https://uncomely-uneffusing-averie.ngrok-free.dev/api/payment-callbacks/illicocash
Success URL: https://uncomely-uneffusing-averie.ngrok-free.dev/payment/success
Cancel URL:  https://uncomely-uneffusing-averie.ngrok-free.dev/payment/cancel
```

---

## ⚠️ Important

- ✅ Toutes les URLs sont en **HTTPS** (requis par les opérateurs)
- ✅ Les routes sont déjà configurées dans Laravel
- ✅ ngrok est actif et fonctionnel
- 🔄 Cette URL ngrok change à chaque redémarrage (version gratuite)

---

## 🔄 Quand ngrok redémarre

Si vous redémarrez ngrok, vous obtiendrez une nouvelle URL. Vous devrez alors :

1. Mettre à jour `APP_URL` dans `.env`
2. Mettre à jour les URLs dans les portails des opérateurs
3. Exécuter `php artisan config:clear`

---

## 📞 Support

Pour plus d'informations, consultez : `AIRTEL_MONEY_SETUP.md`
