# ⏰ CONFIGURATION CRON - VintApp

_Date : 28 novembre 2025_

---

## 📅 TÂCHES PLANIFIÉES CONFIGURÉES

### 1. **Backup Automatique Base de Données** 💾

**Fréquence** : Tous les jours à 2h00 du matin

```bash
php artisan backup:run --only-db
```

**Objectif** : Sauvegarder quotidiennement la base de données MySQL

**Stockage** : `storage/app/Laravel/`

**Logs** :

-   Succès → `storage/logs/business.log`
-   Échec → `storage/logs/errors.log`

---

### 2. **Nettoyage Anciens Backups** 🧹

**Fréquence** : Tous les dimanches à 3h00 du matin

```bash
php artisan backup:clean
```

**Objectif** : Supprimer les backups de plus de 7 jours (configurable dans `config/backup.php`)

**Rétention** : Par défaut, 7 jours

---

### 3. **Réinitialisation Métriques Monitoring** 🔄

**Fréquence** : Tous les lundis à 00:00

```php
app(\App\Services\MonitoringService::class)->resetMetrics();
```

**Objectif** : Nettoyer les métriques de cache pour démarrer la semaine avec des statistiques fraîches

**Cache keys nettoyés** :

-   `monitoring:performance`
-   `monitoring:business`
-   `monitoring:errors`
-   `monitoring:cache_hits`
-   `monitoring:cache_misses`
-   `monitoring:db_stats`

---

### 4. **Nettoyage Telescope** 🔭

**Fréquence** : Tous les jours à 1h00 du matin

```bash
php artisan telescope:prune --hours=168
```

**Objectif** : Supprimer les entrées Telescope de plus de 7 jours (168 heures)

**Impact** : Libère de l'espace disque et améliore les performances

---

### 5. **Nettoyage Cache Expiré** 🗑️

**Fréquence** : Toutes les heures

```bash
php artisan cache:prune-stale-tags
```

**Objectif** : Nettoyer les tags de cache périmés

---

## 🖥️ CONFIGURATION SERVEUR

### **Windows (Serveur Local)**

Utiliser le **Planificateur de tâches Windows** :

1. Ouvrir "Planificateur de tâches"
2. Créer une nouvelle tâche de base
3. Déclencheur : Quotidien à 00:00
4. Action : Démarrer un programme
    ```
    Programme : C:\php\php.exe
    Arguments : C:\Users\gloir\Desktop\vintApp\artisan schedule:run
    ```

**OU** utiliser une tâche unique qui exécute toutes les minutes :

```cmd
php C:\Users\gloir\Desktop\vintApp\artisan schedule:run >> NUL 2>&1
```

---

### **Linux/Ubuntu (Serveur Production)**

Ajouter au crontab :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne
* * * * * cd /var/www/vintapp && php artisan schedule:run >> /dev/null 2>&1
```

**Explication** :

-   `* * * * *` → Toutes les minutes
-   Laravel Schedule se charge d'exécuter les tâches au bon moment
-   `>> /dev/null 2>&1` → Supprime les sorties

---

### **Vérifier le Cron**

```bash
# Lister les tâches planifiées
php artisan schedule:list

# Tester manuellement (à tout moment)
php artisan schedule:run

# Tester avec sortie détaillée
php artisan schedule:run --verbose
```

---

## 📊 MONITORING DES BACKUPS

### **Vérifier les Backups Existants**

```bash
# Lister tous les backups
php artisan backup:list

# Exemple de sortie :
# +---------+-------+-----------+---------+--------------+--------------------+--------------+
# | Name    | Disk  | Reachable | Healthy | # of backups | Newest backup      | Used storage |
# +---------+-------+-----------+---------+--------------+--------------------+--------------+
# | Laravel | local | ✅        | ✅      |            7 | 2025-11-28 02:00   |      13.51 MB|
# +---------+-------+-----------+---------+--------------+--------------------+--------------+
```

### **Backup Manuel**

```bash
# Backup complet (BDD + fichiers)
php artisan backup:run

# Backup BDD uniquement (plus rapide)
php artisan backup:run --only-db

# Backup fichiers uniquement
php artisan backup:run --only-files

# Backup avec notification
php artisan backup:run --only-db --mail=admin@vintapp.com
```

---

## 🚨 NOTIFICATIONS EN CAS D'ÉCHEC

### **Configuration dans `.env`**

```env
BACKUP_MAIL_FROM_ADDRESS=noreply@vintapp.com
BACKUP_MAIL_FROM_NAME="VintApp Backups"
BACKUP_MAIL_TO_ADDRESS=admin@vintapp.com
BACKUP_MAIL_TO_NAME="Admin VintApp"
```

### **Notifications Slack** (Optionnel)

Dans `config/backup.php` :

```php
'notifications' => [
    'mail' => [
        'to' => env('BACKUP_MAIL_TO_ADDRESS', 'admin@vintapp.com'),
    ],
    'slack' => [
        'webhook_url' => env('BACKUP_SLACK_WEBHOOK_URL'),
    ],
],
```

---

## 📁 STRUCTURE DES BACKUPS

```
storage/app/Laravel/
├── 2025-11-28-02-00-00.zip    ← Backup du 28 nov 2h00
├── 2025-11-27-02-00-00.zip    ← Backup du 27 nov 2h00
├── 2025-11-26-02-00-00.zip    ← Backup du 26 nov 2h00
└── ...
```

**Contenu d'un backup** :

```
backup.zip
├── db-dumps/
│   └── mysql-vintapp.sql      ← Dump SQL complet
└── manifest.json              ← Métadonnées
```

---

## 🔐 SÉCURITÉ & BONNES PRATIQUES

### 1. **Stockage Cloud** (Production)

Configurer S3/Azure/Google Cloud dans `.env` :

```env
BACKUP_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=vintapp-backups
```

### 2. **Encryption des Backups**

Dans `config/backup.php` :

```php
'backup' => [
    'password' => env('BACKUP_PASSWORD'),
    'encryption' => 'default',
],
```

### 3. **Rotation Automatique**

```php
'cleanup' => [
    'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
    'defaultStrategy' => [
        'keepAllBackupsForDays' => 7,
        'keepDailyBackupsForDays' => 30,
        'keepWeeklyBackupsForWeeks' => 12,
        'keepMonthlyBackupsForMonths' => 6,
        'keepYearlyBackupsForYears' => 2,
        'deleteOldestBackupsWhenUsingMoreMegabytesThan' => 5000,
    ],
],
```

---

## 🧪 TESTS

### **Tester les Tâches Planifiées**

```bash
# Exécuter immédiatement toutes les tâches planifiées
php artisan schedule:run

# Voir les tâches sans les exécuter
php artisan schedule:list

# Tester le backup
php artisan backup:run --only-db

# Vérifier la santé des backups
php artisan backup:list
```

---

## 📝 LOGS

### **Où trouver les logs ?**

```
storage/logs/
├── laravel.log          ← Logs généraux
├── business.log         ← Succès backups
├── errors.log           ← Échecs backups
└── performance.log      ← Performances
```

### **Surveiller les Logs**

```bash
# Logs généraux
tail -f storage/logs/laravel.log

# Logs business (backups)
tail -f storage/logs/business.log

# Logs erreurs
tail -f storage/logs/errors.log
```

---

## ✅ CHECKLIST DÉPLOIEMENT PRODUCTION

-   [ ] Configurer le cron sur le serveur
-   [ ] Tester `php artisan schedule:run`
-   [ ] Vérifier le premier backup automatique
-   [ ] Configurer le stockage cloud (S3/Azure)
-   [ ] Activer les notifications par email
-   [ ] Tester la restauration d'un backup
-   [ ] Documenter la procédure de restauration
-   [ ] Configurer l'encryption des backups
-   [ ] Surveiller l'espace disque

---

## 🔄 RESTAURATION D'UN BACKUP

### **1. Localiser le Backup**

```bash
ls -lh storage/app/Laravel/
```

### **2. Extraire le ZIP**

```bash
unzip storage/app/Laravel/2025-11-28-02-00-00.zip -d /tmp/restore
```

### **3. Restaurer la BDD**

```bash
mysql -u root -p vintapp < /tmp/restore/db-dumps/mysql-vintapp.sql
```

### **4. Vérifier**

```bash
php artisan migrate:status
php artisan tinker
>>> \App\Models\User::count();
```

---

## 📚 RESSOURCES

-   **Documentation Spatie Backup** : https://spatie.be/docs/laravel-backup
-   **Laravel Task Scheduling** : https://laravel.com/docs/11.x/scheduling
-   **Crontab Generator** : https://crontab.guru/

---

## 🎯 RÉSUMÉ

**Tâches Configurées** :

1. ✅ Backup BDD quotidien (2h00)
2. ✅ Nettoyage backups hebdomadaire (dimanche 3h00)
3. ✅ Reset monitoring hebdomadaire (lundi 00h00)
4. ✅ Prune Telescope quotidien (1h00)
5. ✅ Prune cache horaire

**Prochaines Étapes** :

-   Configurer le cron sur le serveur
-   Tester la restauration
-   Configurer stockage cloud

**Fichier de Configuration** : `routes/console.php`
