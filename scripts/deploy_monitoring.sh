#!/bin/bash
# ============================================================
#  Installation du monitoring_security.py sur l'hébergement
#  mutualisé Hostinger (u557260640@fr-int-web2123)
#
#  Ce script :
#   1. installe mysql-connector-python (si absent)
#   2. teste la connexion DB + les 3 tâches
#   3. configure la crontab
#
#  Usage : bash scripts/deploy_monitoring.sh
# ============================================================
set -e

echo "=== 1. Vérification Python ==="
python3 --version

echo ""
echo "=== 2. Installation de mysql-connector-python ==="
if python3 -c "import mysql.connector" 2>/dev/null; then
    echo "  mysql-connector déjà installé."
else
    pip3 install --user mysql-connector-python --quiet || pip install --user mysql-connector-python --quiet
    echo "  installé."
fi

echo ""
echo "=== 3. Placement du script ==="
# Le script doit être dans ~/scripts (hors public_html, non accessible au web)
mkdir -p ~/scripts
if [ ! -f ~/scripts/monitoring_security.py ]; then
    cp scripts/monitoring_security.py ~/scripts/monitoring_security.py
    chmod +x ~/scripts/monitoring_security.py
    echo "  script installé dans ~/scripts"
else
    cp scripts/monitoring_security.py ~/scripts/monitoring_security.py
    chmod +x ~/scripts/monitoring_security.py
    echo "  script mis à jour."
fi

echo ""
echo "=== 4. Test de chaque tâche ==="
echo "--- purge ---"
python3 ~/scripts/monitoring_security.py purge
echo "--- alert ---"
python3 ~/scripts/monitoring_security.py alert
echo "--- report ---"
python3 ~/scripts/monitoring_security.py report

echo ""
echo "=== 5. Configuration de la crontab ==="
# Purge quotidienne à 03h00, alerte toutes les 10 min, rapport toutes les heures
CRON="@daily cd /home/u557260640/scripts && python3 /home/u557260640/scripts/monitoring_security.py purge
*/10 * * * * cd /home/u557260640/scripts && python3 /home/u557260640/scripts/monitoring_security.py alert
0 * * * * cd /home/u557260640/scripts && python3 /home/u557260640/scripts/monitoring_security.py report"

# Ajouter seulement les lignes qui n'existent pas déjà
( crontab -l 2>/dev/null | grep -v "monitoring_security.py" ; echo "$CRON" ) | crontab -
echo "  crontab mise à jour :"
crontab -l | grep monitoring_security

echo ""
echo "=== Terminé. Logs : ~/scripts/logs/security_monitor.log et security_report.log ==="
