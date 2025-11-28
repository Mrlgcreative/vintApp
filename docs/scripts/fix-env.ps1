# ⚠️ INSTRUCTIONS IMPORTANTES ⚠️
# 
# Le fichier .env est actuellement ouvert dans VS Code et verrouillé.
# Pour que les notifications fonctionnent, vous devez :
#
# 1. FERMER le fichier .env dans VS Code (cliquez sur la croix de l'onglet)
# 2. Ou FERMER VS Code complètement
# 3. Puis exécuter cette commande dans PowerShell :
#

$content = Get-Content .env -Raw
$content = $content -replace 'BROADCAST_CONNECTION=log', 'BROADCAST_CONNECTION=pusher'

# Ajouter les lignes PUSHER si elles n'existent pas
if ($content -notmatch 'PUSHER_APP_ID=') {
    $pusherConfig = @"


# Pusher Configuration (Notifications Temps Réel)
PUSHER_APP_ID=2062646
PUSHER_APP_KEY=2da990522500b234fb20
PUSHER_APP_SECRET=aaa1b3153b3d8c00132f
PUSHER_APP_CLUSTER=eu
PUSHER_SCHEME=https
PUSHER_PORT=443
"@
    # Insérer avant la ligne Google OAuth
    $content = $content -replace '(# Google OAuth Configuration)', "$pusherConfig`r`n`$1"
}

# Sauvegarder
$content | Out-File -FilePath .env -Encoding utf8 -NoNewline

Write-Host "✅ Fichier .env mis à jour avec succès !"
Write-Host "Maintenant exécutez : php artisan config:clear"
