# Guide Complet - Système de Décaissement via Agents Mobile Money

## 🎯 Aperçu

Le système de décaissement via agents mobile money permet aux utilisateurs de VintApp de retirer leurs fonds directement chez des agents agréés plutôt que sur leur propre numéro mobile. Cette fonctionnalité est particulièrement utile pour :

-   Les utilisateurs sans compte mobile money
-   Les retraits de gros montants nécessitant un agent
-   La gestion des liquidités via des partenaires agréés
-   Les zones où les agents ont de meilleures commissions

## 🏗️ Architecture

### Composants Principaux

1. **WalletController** - Gestion des retraits avec validation des agents
2. **MobileMoneyService** - Service de décaissement avec méthode `cashOutAgent()`
3. **Configuration Agents** - Fichier `config/agent_services.php`
4. **Webhooks** - Callbacks pour les confirmations de décaissement

### Flux de Décaissement Agent

```mermaid
graph TD
    A[Utilisateur demande retrait] --> B[Choisit method: agent]
    B --> C[Saisit agent_phone + agent_id]
    C --> D[WalletController validation]
    D --> E[Détection opérateur via préfixe]
    E --> F[Appel cashOutAgent()]
    F --> G[API Provider spécifique]
    G --> H[Transaction pending]
    H --> I[Webhook callback]
    I --> J[Transaction completed/failed]
    J --> K[Notification utilisateur]
```

## 🔧 Configuration

### 1. Variables d'Environnement

Ajouter à votre `.env` (voir `AGENT_ENV_CONFIG.md`) :

```env
# Orange Money Agents
ORANGE_MONEY_AGENT_ENABLED=true
ORANGE_MONEY_AGENT_API_URL=https://api.orange.com/orange-money-agents/cd/v1
ORANGE_MONEY_AGENT_AGENT_KEY=your_orange_agent_key
ORANGE_MONEY_AGENT_API_KEY=your_orange_api_key

# Airtel Money Agents
AIRTEL_MONEY_AGENT_ENABLED=true
AIRTEL_MONEY_AGENT_CLIENT_ID=your_airtel_client_id
AIRTEL_MONEY_AGENT_CLIENT_SECRET=your_airtel_client_secret

# M-Pesa Agents
MPESA_AGENT_ENABLED=true
MPESA_AGENT_API_KEY=your_mpesa_agent_key
MPESA_AGENT_AGENT_CODE=your_mpesa_agent_code

# Africell Agents
AFRICELL_AGENT_ENABLED=true
AFRICELL_AGENT_AGENT_ID=your_africell_agent_id
AFRICELL_AGENT_API_SECRET=your_africell_secret

# Illicocash Agents
ILLICOCASH_AGENT_ENABLED=true
ILLICOCASH_AGENT_AGENT_CODE=your_illico_agent_code
ILLICOCASH_AGENT_API_TOKEN=your_illico_token
```

### 2. Cache des Configurations

```bash
php artisan config:clear
php artisan cache:clear
```

## 📡 Utilisation API

### Décaissement via Agent

**Endpoint:** `POST /wallet/{wallet}/withdraw-funds`

**Payload pour agent:**

```json
{
    "amount": 100.0,
    "phone_number": "+243841234567",
    "payment_method": "agent",
    "agent_id": 123,
    "agent_phone": "+243841234567",
    "description": "Retrait chez agent Orange Money"
}
```

**Payload décaissement direct (classique):**

```json
{
    "amount": 50.0,
    "phone_number": "+243841234567",
    "payment_method": "orange_money",
    "description": "Retrait direct"
}
```

### Réponse Success

```json
{
    "success": true,
    "message": "Demande de retrait en cours de traitement ! Les fonds seront envoyés vers +243841234567 sous quelques minutes.",
    "transaction_id": "WTH-1698765432-5678",
    "status": "processing"
}
```

### Réponse Error

```json
{
    "success": false,
    "message": "Configuration agent Orange Money manquante",
    "error": "AGENT_CONFIG_MISSING"
}
```

## 🔍 Validation et Règles

### Validation des Champs

```php
[
    'amount' => 'required|numeric|min:0.01|max:' . $wallet->balance,
    'phone_number' => ['required', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/'],
    'payment_method' => 'required|string|in:orange_money,airtel_money,mpesa,africell,illicocash,agent',

    // Champs spécifiques aux agents
    'agent_id' => 'nullable|integer',
    'agent_phone' => ['nullable', 'string', 'regex:/^(\+?243|0)?[0-9]{9}$/', 'required_if:payment_method,agent'],
]
```

### Détection Automatique d'Opérateur

Le système détecte automatiquement l'opérateur selon le préfixe :

| Préfixe        | Opérateur    |
| -------------- | ------------ |
| 84, 85, 89     | Orange Money |
| 81, 82, 83     | M-Pesa       |
| 97, 98, 99     | Airtel Money |
| 90, 91, 92, 93 | Africell     |

## 📊 Métadonnées de Transaction

Les transactions d'agents contiennent des métadonnées enrichies :

```json
{
    "phone_number": "+243841234567",
    "payment_method": "agent",
    "agent_id": 123,
    "agent_phone": "+243841234567",
    "withdrawal_date": "2025-10-27 14:30:00"
}
```

## 🔄 Webhooks et Callbacks

### URL de Callback

```
POST /wallet/withdrawals/webhook/{provider}
```

### Payload Webhook Exemple

```json
{
    "status": "completed",
    "agent_transaction_id": "AGT-OM-789123456",
    "reference": "WTH-1698765432-5678",
    "amount": 100.0,
    "currency": "USD",
    "agent_phone": "+243841234567",
    "timestamp": "2025-10-27T14:35:00Z"
}
```

### Gestion des Statuts

-   **processing** → Transaction en cours
-   **completed** → Décaissement réussi
-   **failed** → Échec (remboursement automatique)

## 🧪 Tests et Débogage

### Script de Test

```bash
php test-agent-cashout.php
```

Ce script teste :

-   ✅ Détection d'opérateur par numéro
-   ✅ Configuration des agents
-   ✅ Validation des données
-   ✅ Génération des métadonnées

### Logs de Débogage

```bash
# Suivre les logs en temps réel
tail -f storage/logs/laravel.log | grep -E "(agent|cash.*out)"

# Filtrer par transaction
tail -f storage/logs/laravel.log | grep "WTH-1698765432-5678"
```

### Commandes Utiles

```bash
# Vider le cache après modifications config
php artisan config:clear && php artisan cache:clear

# Vérifier les routes
php artisan route:list | findstr wallet

# Tester la syntaxe PHP
php -l app/Services/MobileMoneyService.php
php -l app/Http/Controllers/WalletController.php
```

## 🚀 Interface Utilisateur

### Formulaire de Retrait (Exemple Blade)

```html
<!-- Mode sélection -->
<select name="payment_method" id="paymentMethod">
    <option value="orange_money">Orange Money Direct</option>
    <option value="agent">Via Agent Mobile Money</option>
</select>

<!-- Champs agent (affichés si method=agent) -->
<div id="agentFields" style="display: none;">
    <input type="text" name="agent_phone" placeholder="+243841234567" />
    <input type="number" name="agent_id" placeholder="ID Agent (optionnel)" />
</div>

<script>
    document
        .getElementById("paymentMethod")
        .addEventListener("change", function () {
            const isAgent = this.value === "agent";
            document.getElementById("agentFields").style.display = isAgent
                ? "block"
                : "none";
        });
</script>
```

## ⚠️ Points d'Attention

### Sécurité

1. **Validation des agents** - Vérifier que les agents sont agréés
2. **Limites de montants** - Respecter les plafonds des opérateurs
3. **Webhooks sécurisés** - Vérifier les signatures des callbacks
4. **Logs sensibles** - Ne pas logger les credentials

### Performance

1. **Timeout API** - Configurer des timeouts appropriés (30s)
2. **Retry Logic** - Implémenter la logique de retry (3 tentatives)
3. **Cache** - Mettre en cache les configurations agents
4. **Queue Jobs** - Traiter les webhooks de manière asynchrone

### Monitoring

1. **Taux de succès** par opérateur
2. **Temps de réponse** des APIs agents
3. **Volume de transactions** par agent
4. **Alertes** sur les échecs répétés

## 🔗 API Documentation

### Endpoints Agents

| Opérateur    | Endpoint Agent               | Documentation                                |
| ------------ | ---------------------------- | -------------------------------------------- |
| Orange Money | `/orange-money-agents/cd/v1` | [Lien API](https://developer.orange.com)     |
| Airtel Money | `/merchant/v1/agents`        | [Lien API](https://developers.airtel.africa) |
| M-Pesa       | `/mpesa/agent/v1`            | [Lien API](https://developer.vodacom.cd)     |
| Africell     | `/agent/v1`                  | [Lien API](https://developer.africell.cd)    |
| Illicocash   | `/agent/v1`                  | [Lien API](https://developer.illicocash.com) |

## 🎯 Prochaines Étapes

1. **Obtenir les credentials** des opérateurs pour les APIs agents
2. **Configurer les webhooks** avec les URLs publiques
3. **Tester en sandbox** avec de vrais agents partenaires
4. **Implémenter l'UI** pour la sélection d'agents
5. **Ajouter la gestion** des commissions agents
6. **Monitorer** les performances en production

---

## ✅ Résumé de l'Implémentation

**Ce qui a été fait :**

-   ✅ Méthode `cashOutAgent()` dans `MobileMoneyService`
-   ✅ Validation étendue dans `WalletController`
-   ✅ Configuration agents `config/agent_services.php`
-   ✅ Détection automatique d'opérateur par numéro
-   ✅ Métadonnées enrichies pour le tracking
-   ✅ Support des 5 opérateurs majeurs en RDC
-   ✅ Script de test complet
-   ✅ Documentation détaillée

**Prêt pour :** Configuration des credentials et tests avec de vraies APIs ! 🚀
