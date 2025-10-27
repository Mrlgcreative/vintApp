# Configuration des Agents Mobile Money pour Décaissement

# Ajouter ces variables à votre fichier .env

# ==========================================

# ORANGE MONEY AGENTS

# ==========================================

ORANGE_MONEY_AGENT_ENABLED=true
ORANGE_MONEY_AGENT_API_URL=https://api.orange.com/orange-money-agents/cd/v1
ORANGE_MONEY_AGENT_AGENT_KEY=your_orange_agent_key_here
ORANGE_MONEY_AGENT_API_KEY=your_orange_api_key_here

# ==========================================

# AIRTEL MONEY AGENTS

# ==========================================

AIRTEL_MONEY_AGENT_ENABLED=true
AIRTEL_MONEY_AGENT_API_URL=https://openapiuat.airtel.africa/merchant/v1/agents
AIRTEL_MONEY_AGENT_CLIENT_ID=your_airtel_agent_client_id_here
AIRTEL_MONEY_AGENT_CLIENT_SECRET=your_airtel_agent_client_secret_here

# ==========================================

# M-PESA AGENTS (VODACOM)

# ==========================================

MPESA_AGENT_ENABLED=true
MPESA_AGENT_API_URL=https://api.vodacom.cd/mpesa/agent/v1
MPESA_AGENT_API_KEY=your_mpesa_agent_api_key_here
MPESA_AGENT_AGENT_CODE=your_mpesa_agent_code_here
MPESA_AGENT_SERVICE_CODE=your_mpesa_service_code_here

# ==========================================

# AFRICELL AGENTS

# ==========================================

AFRICELL_AGENT_ENABLED=true
AFRICELL_AGENT_API_URL=https://api.africell.cd/agent/v1
AFRICELL_AGENT_AGENT_ID=your_africell_agent_id_here
AFRICELL_AGENT_API_SECRET=your_africell_agent_api_secret_here

# ==========================================

# ILLICOCASH AGENTS

# ==========================================

ILLICOCASH_AGENT_ENABLED=true
ILLICOCASH_AGENT_API_URL=https://api.illicocash.com/agent/v1
ILLICOCASH_AGENT_AGENT_CODE=your_illicocash_agent_code_here
ILLICOCASH_AGENT_API_TOKEN=your_illicocash_agent_token_here

# ==========================================

# WEBHOOKS ET CALLBACKS

# ==========================================

# Ces URLs seront appelées par les providers pour notifier du statut

# Assurez-vous que votre APP_URL est accessible publiquement

AGENT_WEBHOOK_BASE_URL=${APP_URL}/wallet/withdrawals/webhook

# ==========================================

# CONFIGURATION DE SÉCURITÉ

# ==========================================

# Secrets pour vérifier les signatures des webhooks

ORANGE_AGENT_WEBHOOK_SECRET=your_orange_webhook_secret_here
AIRTEL_AGENT_WEBHOOK_TOKEN=your_airtel_webhook_token_here
MPESA_AGENT_WEBHOOK_SECRET=your_mpesa_webhook_secret_here
AFRICELL_AGENT_WEBHOOK_SECRET=your_africell_webhook_secret_here
ILLICOCASH_AGENT_WEBHOOK_SECRET=your_illicocash_webhook_secret_here
