#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Monitoring de sécurité VintApp — cron Python sur hébergement mutualisé Hostinger.

Fonctions :
  purge  : supprime les security_login_attempts plus vieux que RETENTION_DAYS jours
  alert  : détecte un pic de tentatives échouées et écrit une alerte dans un log
  report : écrit un sommaire périodique (top IP, top emails, compteurs)

Usage :
  python3 monitoring_security.py purge
  python3 monitoring_security.py alert
  python3 monitoring_security.py report
  python3 monitoring_security.py all          # purge + alert + report d'un coup

Prérequis : pip install mysql-connector-python
Connexion : lit les credentials depuis le fichier .env du projet Laravel,
            ou depuis des variables d'environnement si présentes.
"""

import os
import sys
import re
import datetime
import logging

# ---------------------------------------------------------------------------
# Configuration
# ---------------------------------------------------------------------------
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Recherche du .env Laravel dans plusieurs emplacements courants sur Hostinger :
#  - ~/public_html/.env
#  - ~/.env
#  - ~/app/.env (si le projet est dans un sous-dossier)
CANDIDATE_ENV = [
    os.path.join(os.path.expanduser("~"), "public_html", ".env"),
    os.path.join(os.path.expanduser("~"), ".env"),
    os.path.join(BASE_DIR, "public_html", ".env"),
    os.path.join(BASE_DIR, ".env"),
]

ENV_PATH = next((p for p in CANDIDATE_ENV if os.path.exists(p)), CANDIDATE_ENV[0])

LOG_DIR = os.path.join(BASE_DIR, "logs")
os.makedirs(LOG_DIR, exist_ok=True)

# Rétention des tentatives de connexion (jours)
RETENTION_DAYS = int(os.environ.get("SEC_RETENTION_DAYS", "30"))

# Seuil d'alerte : nombre d'échecs dans la fenêtre ALERT_WINDOW_MINUTES
ALERT_MAX_FAILURES = int(os.environ.get("SEC_ALERT_FAILURES", "15"))
ALERT_WINDOW_MINUTES = int(os.environ.get("SEC_ALERT_WINDOW", "15"))

# Fenêtre du rapport (minutes)
REPORT_WINDOW_MINUTES = int(os.environ.get("SEC_REPORT_WINDOW", "60"))

LOG_FILE = os.path.join(LOG_DIR, "security_monitor.log")
REPORT_FILE = os.path.join(LOG_DIR, "security_report.log")

logging.basicConfig(
    filename=LOG_FILE,
    level=logging.INFO,
    format="%(asctime)s %(levelname)s %(message)s",
)


# ---------------------------------------------------------------------------
# Lecture des credentials depuis .env
# ---------------------------------------------------------------------------
def parse_env(path):
    values = {}
    try:
        with open(path, "r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if not line or line.startswith("#") or "=" not in line:
                    continue
                key, _, val = line.partition("=")
                values[key.strip()] = val.strip().strip('"').strip("'")
    except Exception as e:
        logging.error("Erreur lecture .env %s : %s", path, e)
    return values


def load_db_config():
    env = parse_env(ENV_PATH)
    return {
        "host": env.get("DB_HOST", "localhost"),
        "port": int(env.get("DB_PORT", "3306") or 3306),
        "database": env.get("DB_DATABASE", ""),
        "user": env.get("DB_USERNAME", ""),
        "password": env.get("DB_PASSWORD", ""),
    }


# ---------------------------------------------------------------------------
# MySQL
# ---------------------------------------------------------------------------
def mysql_connect(cfg):
    import mysql.connector
    return mysql.connector.connect(
        host=cfg["host"],
        port=cfg["port"],
        database=cfg["database"],
        user=cfg["user"],
        password=cfg["password"],
        charset="utf8mb4",
    )


# ---------------------------------------------------------------------------
# Purge
# ---------------------------------------------------------------------------
def purge(cfg):
    cutoff = datetime.datetime.now() - datetime.timedelta(days=RETENTION_DAYS)
    cutoff_str = cutoff.strftime("%Y-%m-%d %H:%M:%S")
    try:
        conn = mysql_connect(cfg)
        cur = conn.cursor()
        cur.execute(
            "DELETE FROM security_login_attempts WHERE created_at < %s",
            (cutoff_str,),
        )
        conn.commit()
        deleted = cur.rowcount
        cur.close()
        conn.close()
        logging.info("PURGE terminée : %d tentatives supprimées (< %s)", deleted, cutoff_str)
        print(f"PURGE OK : {deleted} tentatives supprimées")
    except Exception as e:
        logging.error("PURGE échouée : %s", e)
        print(f"PURGE ERREUR : {e}")
        return 1
    return 0


# ---------------------------------------------------------------------------
# Alerte
# ---------------------------------------------------------------------------
def alert(cfg):
    window = datetime.datetime.now() - datetime.timedelta(minutes=ALERT_WINDOW_MINUTES)
    window_str = window.strftime("%Y-%m-%d %H:%M:%S")
    try:
        conn = mysql_connect(cfg)
        cur = conn.cursor(dictionary=True)
        # Échecs par IP
        cur.execute(
            """
            SELECT ip_address, SUM(attempts) AS n
            FROM security_login_attempts
            WHERE success = 0 AND created_at >= %s
            GROUP BY ip_address
            HAVING n >= %s
            ORDER BY n DESC
            """,
            (window_str, ALERT_MAX_FAILURES),
        )
        ips = cur.fetchall()
        # Échecs par email (+IP)
        cur.execute(
            """
            SELECT email, ip_address, SUM(attempts) AS n
            FROM security_login_attempts
            WHERE success = 0 AND created_at >= %s AND email IS NOT NULL
            GROUP BY email, ip_address
            HAVING n >= %s
            ORDER BY n DESC
            """,
            (window_str, ALERT_MAX_FAILURES),
        )
        emails = cur.fetchall()
        cur.close()
        conn.close()

        if ips or emails:
            msg = (
                f"[ALERTE FORCE BRUTE] fenêtre {ALERT_WINDOW_MINUTES}min "
                f"| IP: {len(ips)} | emails: {len(emails)}"
            )
            for row in ips:
                msg += f" | IP {row['ip_address']}: {row['n']} échecs"
            for row in emails:
                msg += f" | email {row['email']} ({row['ip_address']}): {row['n']}"
            logging.warning(msg)
            print(msg)
        else:
            logging.info("ALERTE : aucune anomalie.")
            print("ALERTE OK : aucune anomalie détectée")
    except Exception as e:
        logging.error("ALERTE échouée : %s", e)
        print(f"ALERTE ERREUR : {e}")
        return 1
    return 0


# ---------------------------------------------------------------------------
# Rapport
# ---------------------------------------------------------------------------
def report(cfg):
    window = datetime.datetime.now() - datetime.timedelta(minutes=REPORT_WINDOW_MINUTES)
    window_str = window.strftime("%Y-%m-%d %H:%M:%S")
    try:
        conn = mysql_connect(cfg)
        cur = conn.cursor(dictionary=True)

        cur.execute(
            "SELECT COUNT(*) AS n FROM security_login_attempts WHERE created_at >= %s",
            (window_str,),
        )
        total = cur.fetchone()["n"]

        cur.execute(
            "SELECT COUNT(DISTINCT ip_address) AS n FROM security_login_attempts WHERE created_at >= %s",
            (window_str,),
        )
        distinct_ips = cur.fetchone()["n"]

        cur.execute(
            """
            SELECT ip_address, SUM(attempts) AS n
            FROM security_login_attempts
            WHERE created_at >= %s
            GROUP BY ip_address ORDER BY n DESC LIMIT 5
            """,
            (window_str,),
        )
        top_ips = cur.fetchall()

        cur.execute(
            """
            SELECT email, SUM(attempts) AS n
            FROM security_login_attempts
            WHERE created_at >= %s AND email IS NOT NULL
            GROUP BY email ORDER BY n DESC LIMIT 5
            """,
            (window_str,),
        )
        top_emails = cur.fetchall()

        cur.close()
        conn.close()

        with open(REPORT_FILE, "a", encoding="utf-8") as f:
            f.write(f"\n=== Rapport {datetime.datetime.now().isoformat(sep=' ')} ===\n")
            f.write(f"Tentatives ({REPORT_WINDOW_MINUTES}min) : {total}\n")
            f.write(f"IP distinctes : {distinct_ips}\n")
            f.write("Top IP :\n")
            for r in top_ips:
                f.write(f"  {r['ip_address']} : {r['n']}\n")
            f.write("Top emails :\n")
            for r in top_emails:
                f.write(f"  {r['email']} : {r['n']}\n")

        logging.info("RAPPORT écrit (%d tentatives, %d IP)", total, distinct_ips)
        print(f"REPORT OK : {total} tentatives, {distinct_ips} IP distinctes")
    except Exception as e:
        logging.error("RAPPORT échoué : %s", e)
        print(f"REPORT ERREUR : {e}")
        return 1
    return 0


# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------
def main():
    task = sys.argv[1] if len(sys.argv) > 1 else "all"
    cfg = load_db_config()

    if not cfg["database"] or not cfg["user"]:
        print("ERREUR : credentials DB introuvables (vérifier .env / DB_USERNAME)")
        return 2

    print(f"Connexion DB : {cfg['host']}/{cfg['database']}")

    exit_code = 0
    if task in ("purge", "all"):
        exit_code += purge(cfg)
    if task in ("alert", "all"):
        exit_code += alert(cfg)
    if task in ("report", "all"):
        exit_code += report(cfg)

    return 1 if exit_code else 0


if __name__ == "__main__":
    sys.exit(main())
