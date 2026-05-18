# 🗄️ Accès à la Base de Données MySQL - FrejSmithWeb

## 🔗 Informations de Connexion

```
Serveur : localhost
Port    : 3306
Utilisateur : root
Mot de passe : Gbotounousmith1992@@@
Base de données : frejsmithweb
```

---

## 📊 Accès à la Base de Données

### Option 1️⃣ : Ligne de Commande (Terminal)

#### Connexion Interactive
```bash
mysql -u root -p
# Puis entre le mot de passe : Gbotounousmith1992@@@
```

Ou directement :
```bash
mysql -u root -p"Gbotounousmith1992@@@"
```

#### Sélectionner la base de données
```sql
USE frejsmithweb;
```

#### Voir toutes les tables
```sql
SHOW TABLES;
```

**Résultat :**
```
admin_attempts
contacts
js_errors
reservations
reviews
security_logs
visitors
```

---

### Option 2️⃣ : Commande Directe (Sans Interaction)

#### Voir toutes les tables
```bash
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "SHOW TABLES;"
```

#### Voir la structure d'une table
```bash
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "DESCRIBE security_logs;"
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "DESCRIBE js_errors;"
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "DESCRIBE admin_attempts;"
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "DESCRIBE visitors;"
```

---

## 📋 Structure des Tables de Sécurité

### 1️⃣ Table : `security_logs`
Enregistre tous les événements de sécurité détectés.

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | INT | Identifiant unique |
| `event_type` | VARCHAR(100) | Type d'événement (injection, automation, etc.) |
| `severity` | ENUM | Sévérité (low, medium, high, critical) |
| `ip_address` | VARCHAR(45) | Adresse IP du visiteur |
| `user_agent` | TEXT | User-Agent du navigateur |
| `session_id` | VARCHAR(100) | ID de session unique |
| `fingerprint` | TEXT | Empreinte digitale du navigateur |
| `page_url` | VARCHAR(500) | URL visitée |
| `event_data` | JSON | Données détaillées en JSON |
| `threat_score` | INT | Score de menace (0-100) |
| `blocked` | BOOLEAN | Si l'événement a été bloqué |
| `created_at` | TIMESTAMP | Date/heure de l'événement |

**Requête pour voir les événements :**
```sql
SELECT * FROM security_logs ORDER BY created_at DESC LIMIT 10;
```

### 2️⃣ Table : `js_errors`
Enregistre toutes les erreurs JavaScript détectées.

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | INT | Identifiant unique |
| `message` | TEXT | Message d'erreur |
| `filename` | VARCHAR(500) | Fichier affecté |
| `lineno` | INT | Numéro de ligne |
| `colno` | INT | Numéro de colonne |
| `user_agent` | TEXT | User-Agent du navigateur |
| `url` | VARCHAR(500) | URL complète |
| `session_id` | VARCHAR(100) | ID de session |
| `fingerprint` | TEXT | Empreinte du navigateur |
| `created_at` | TIMESTAMP | Date/heure de l'erreur |

**Requête pour voir les erreurs :**
```sql
SELECT message, filename, lineno, COUNT(*) as count 
FROM js_errors 
GROUP BY message, filename, lineno 
ORDER BY created_at DESC;
```

### 3️⃣ Table : `admin_attempts`
Enregistre toutes les tentatives de connexion admin.

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | INT | Identifiant unique |
| `ip_address` | VARCHAR(45) | IP d'où provient la tentative |
| `username` | VARCHAR(100) | Nom d'utilisateur utilisé |
| `success` | BOOLEAN | Si la connexion a réussi |
| `user_agent` | TEXT | User-Agent du navigateur |
| `created_at` | TIMESTAMP | Date/heure de la tentative |

**Requête pour voir les tentatives :**
```sql
SELECT * FROM admin_attempts ORDER BY created_at DESC;
```

### 4️⃣ Table : `visitors` (Modifiée)
Enregistre les informations des visiteurs.

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | INT | Identifiant unique |
| `page` | VARCHAR(255) | Page visitée |
| `ip_address` | VARCHAR(45) | Adresse IP |
| `user_agent` | TEXT | User-Agent du navigateur |
| `referrer` | VARCHAR(255) | Référent HTTP |
| `session_id` | VARCHAR(100) | ID de session unique |
| `fingerprint` | TEXT | Empreinte digitale |
| `security_data` | JSON | Données de sécurité en JSON |
| `created_at` | TIMESTAMP | Date/heure de la visite |

**Requête pour voir les visiteurs :**
```sql
SELECT id, page, ip_address, session_id, created_at FROM visitors ORDER BY created_at DESC LIMIT 20;
```

---

## 🔍 Requêtes Utiles

### Compter les événements
```sql
SELECT COUNT(*) as total FROM security_logs;
SELECT COUNT(*) as total FROM js_errors;
SELECT COUNT(*) as total FROM admin_attempts;
SELECT COUNT(*) as total FROM visitors;
```

### Voir les menaces récentes (dernière heure)
```sql
SELECT event_type, severity, ip_address, threat_score, created_at 
FROM security_logs 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) 
ORDER BY created_at DESC;
```

### Voir les IPs suspectes
```sql
SELECT ip_address, COUNT(*) as count, MAX(threat_score) as max_threat
FROM security_logs
WHERE severity IN ('high', 'critical')
GROUP BY ip_address
ORDER BY count DESC;
```

### Voir les erreurs JavaScript les plus fréquentes
```sql
SELECT message, filename, COUNT(*) as count
FROM js_errors
GROUP BY message, filename
ORDER BY count DESC
LIMIT 10;
```

### Voir les tentatives admin échouées
```sql
SELECT ip_address, username, COUNT(*) as attempts, MAX(created_at) as last_attempt
FROM admin_attempts
WHERE success = FALSE
GROUP BY ip_address, username
ORDER BY attempts DESC;
```

### Voir les événements bloqués
```sql
SELECT * FROM security_logs 
WHERE blocked = TRUE 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## 🖥️ Outils GUI pour Accéder à MySQL

### Option 1: phpMyAdmin (Web)
```bash
# Si tu as phpMyAdmin installé
http://localhost/phpmyadmin
```

### Option 2: MySQL Workbench
- Télécharge depuis : https://dev.mysql.com/downloads/workbench/
- Connexion rapide :
  - Hostname: `localhost`
  - Port: `3306`
  - Username: `root`
  - Password: `Gbotounousmith1992@@@`

### Option 3: TablePlus
- Télécharge depuis : https://tableplus.com/
- Crée une nouvelle connexion MySQL avec les infos ci-dessus

### Option 4: DBeaver
- Télécharge depuis : https://dbeaver.io/
- Crée une nouvelle connexion MySQL

---

## 📝 Vérifier que Tout Fonctionne

### 1️⃣ Vérifier les tables
```bash
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "
SELECT 'Tables créées:' as status;
SHOW TABLES;
"
```

### 2️⃣ Vérifier la structure
```bash
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "
SELECT 'Structure de security_logs:' as status;
DESCRIBE security_logs;
SELECT '';
SELECT 'Structure de js_errors:' as status;
DESCRIBE js_errors;
SELECT '';
SELECT 'Structure de admin_attempts:' as status;
DESCRIBE admin_attempts;
SELECT '';
SELECT 'Structure de visitors:' as status;
DESCRIBE visitors;
"
```

### 3️⃣ Vérifier que les tables sont vides (nouvelles)
```bash
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "
SELECT 'security_logs' as table_name, COUNT(*) as rows FROM security_logs
UNION ALL
SELECT 'js_errors' as table_name, COUNT(*) as rows FROM js_errors
UNION ALL
SELECT 'admin_attempts' as table_name, COUNT(*) as rows FROM admin_attempts
UNION ALL
SELECT 'visitors' as table_name, COUNT(*) as rows FROM visitors;
"
```

---

## 🧪 Test Complet du Système

### Étape 1: Lancer le serveur
```bash
cd /Users/macbookpro/Documents/FREJSMITHWEB/frejsmithweb
php -S localhost:8000 -t public
```

### Étape 2: Accéder à l'admin
```
http://localhost:8000/admin.php
```

### Étape 3: Connexion
- User: `admin`
- Pass: `ChangeMe123!`

### Étape 4: Voir les données dans MySQL
```bash
mysql -u root -p"Gbotounousmith1992@@@" frejsmithweb -e "
SELECT 'Visiteurs trackés:' as metric;
SELECT COUNT(*) as count FROM visitors;
SELECT '';
SELECT 'Événements de sécurité:' as metric;
SELECT COUNT(*) as count FROM security_logs;
SELECT '';
SELECT 'Erreurs JS:' as metric;
SELECT COUNT(*) as count FROM js_errors;
"
```

---

## ⚠️ Points Important

✅ **Toutes les tables sont créées**  
✅ **La structure est correcte**  
✅ **Les colonnes de sécurité sont ajoutées**  
✅ **Prête pour enregistrer les données**  

🔐 **Sécurité :**
- Les prepared statements sont utilisés (voir `Database.php`)
- Pas d'injection SQL possible
- Les mots de passe sont hashés
- Les données sensibles sont loggées

---

## 🔄 Cycle de Vie des Données

```
1. Visiteur accède au site
   ↓
2. tracker.js collecte les données
   ↓
3. Envoi à /api.php
   ↓
4. SiteController traite et calcule le score
   ↓
5. Insertion dans MySQL (visitors, security_logs, etc.)
   ↓
6. Admin accède à http://localhost:8000/admin.php
   ↓
7. Les données s'affichent dans le tableau de bord
```

---

**Base de données prête ! 🎉**
