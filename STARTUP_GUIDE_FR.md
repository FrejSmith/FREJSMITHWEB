# 🚀 Guide de Démarrage - Système de Sécurité FrejSmithWeb

## ✅ Pré-requis Vérifés
- ✅ PHP 8.4.14 (CLI) installé
- ✅ MySQL 9.6.0 disponible
- ✅ Tous les fichiers PHP validés (pas d'erreurs de syntaxe)
- ✅ Composer dépendances installées

## 📋 Étapes de Configuration

### 1️⃣ Appliquer les Mises à Jour de Sécurité à la Base de Données

```bash
cd /Users/macbookpro/Documents/FREJSMITHWEB/frejsmithweb
mysql -u root -p < database/update_security.sql
```

**À la demande du mot de passe**, entre : `Gbotounousmith1992@@@`

**Attendu :**
```
mysql> USE frejsmithweb;
mysql> SOURCE database/update_security.sql;
Mise à jour de sécurité terminée avec succès !
```

### 2️⃣ Vérifier la Configuration .env

```bash
cat .env
```

**Doit contenir :**
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=frejsmithweb
DB_USER=root
DB_PASS="Gbotounousmith1992@@@"
ADMIN_USER=admin
ADMIN_PASS=ChangeMe123!
```

### 3️⃣ Lancer le Serveur de Développement

```bash
cd /Users/macbookpro/Documents/FREJSMITHWEB/frejsmithweb
php -S localhost:8000 -t public
```

**Tu verras :**
```
Development Server (http://localhost:8000) started
```

## 🌐 Accès aux Pages

### Pages Publiques (Automatiquement suivies)
- `http://localhost:8000/index.html` - Accueil
- `http://localhost:8000/about.html` - À propos
- `http://localhost:8000/services.html` - Services
- `http://localhost:8000/portfolio.html` - Portfolio
- `http://localhost:8000/contact.html` - Formulaire de contact
- `http://localhost:8000/api.php?action=...` - API REST

### Interface Admin (Suivi de Sécurité)
- `http://localhost:8000/admin.php` - Tableau de bord

**Identifiants :**
- Utilisateur : `admin`
- Mot de passe : `ChangeMe123!`

## 📊 Qu'est-ce qui est Suivi ?

### Sur Chaque Page Visitée
1. **Empreinte du navigateur** (fingerprint)
   - Résolution écran
   - Navigateur et version
   - Plugins installés
   - Timezone
   - Langue
   - Canvas signature

2. **Données de sécurité**
   - Adresse IP
   - User-Agent
   - Referrer
   - URL visité
   - Timestamp

3. **Comportements suspects**
   - Clics rapides excessifs
   - Outils de développement ouverts
   - Tentatives eval()
   - Scripts injectés
   - Navigation inhabituellement rapide
   - Détection d'automatisation (Selenium, Puppeteer, Phantom)

### Erreurs JavaScript
- Message d'erreur
- Fichier source
- Ligne et colonne
- URL complète
- Session ID

### Tentatives Admin
- Connexions réussies
- Connexions échouées
- IP source
- User-Agent

## 🔐 Fonctionnalités du Tableau de Bord Admin

### 📈 Statistiques Globales
- Nombre total de visiteurs
- Total d'événements de sécurité
- Menaces de haut risque détectées
- Tentatives bloquées

### 🚨 Événements Récents
- Affichage des menaces détectées l'heure passée
- Code couleur par sévérité
  - 🔴 **CRITICAL** - Menace immédiate (score > 80)
  - 🟠 **HIGH** - Risque élevé (score > 60)
  - 🟡 **MEDIUM** - Risque modéré (score > 40)
  - ⚪ **LOW** - Faible risque

### 📍 IPs Suspectes
- Liste des IPs avec plusieurs événements
- Score de menace maximum par IP
- Nombre d'événements

### 📝 Données Formulaires
- Messages de contact
- Avis clients
- Réservations
- Historique complet

### 🔒 Tentatives de Connexion Admin
- Toutes les tentatives (réussies et échouées)
- IP source
- User-Agent utilisé
- Timestamp

### ⚠️ Erreurs JavaScript
- Message complet d'erreur
- Fichier affecté
- Position (ligne:colonne)
- URL du contexte

### 🛡️ Logs de Sécurité Détaillés
- Type d'événement
- Sévérité
- IP source
- Score de menace calculé
- Statut de blocage

## 🧪 Tests de Sécurité

### 1️⃣ Tester le Tracking de Base
1. Ouvre `http://localhost:8000/index.html`
2. Le script `tracker.js` s'exécute automatiquement
3. Les données sont envoyées à l'API
4. Va dans `http://localhost:8000/admin.php`
5. Vérifie que tu apparais dans la liste des visiteurs

### 2️⃣ Tester l'Ouverture des DevTools
1. Ouvre `http://localhost:8000/index.html`
2. Appuie sur `F12` pour ouvrir les outils de développement
3. Regarde la console : tu devrais voir `[SecurityTracker] Initialisé avec session...`
4. Le comportement est enregistré comme "dev_tools_open"
5. Vérifie l'admin pour voir l'événement

### 3️⃣ Tester la Détection d'Erreurs
1. Ouvre la console JavaScript (F12)
2. Lance : `throw new Error('Test error')`
3. Retourne à l'admin
4. Cherche dans "Erreurs JavaScript"

### 4️⃣ Tester le Formulaire de Contact
1. Va sur `http://localhost:8000/contact.html`
2. Remplis et soumets le formulaire
3. Vérifie dans l'admin sous "Messages de contact"

## 📱 Intégration dans tes Pages HTML

Pour que le tracking fonctionne, ajoute avant `</body>` :

```html
<!-- Tracking de Sécurité FrejSmithWeb -->
<script src="/tracker.js"></script>
```

Ou dans ta page de contact, après le formulaire :

```html
<form id="contact-form" method="post">
    <!-- Tes champs... -->
</form>

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('/api.php?action=submit-contact', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Tracker l'événement
            if (window.trackEvent) {
                window.trackEvent('contact_submitted', {form: 'contact'});
            }
        } else {
            alert('Erreur: ' + data.message);
        }
    });
});
</script>
```

## 🔑 Commandes Utiles

### Redémarrer le serveur
```bash
# Arrête avec Ctrl+C, puis relance
php -S localhost:8000 -t public
```

### Vérifier la base de données
```bash
mysql -u root -p frejsmithweb -e "SELECT COUNT(*) as visitors FROM visitors;"
```

### Voir les derniers logs de sécurité
```bash
mysql -u root -p frejsmithweb -e "SELECT * FROM security_logs ORDER BY created_at DESC LIMIT 10;"
```

### Réinitialiser la base de données
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/update_security.sql
```

## 🆘 Dépannage

### Erreur: "Access denied for user 'root'@'localhost'"
- Vérifie que le mot de passe dans `.env` est correct
- Le mot de passe est : `Gbotounousmith1992@@@`

### Erreur: "Cannot find /api.php"
- Assure-toi que tu es dans le bon répertoire
- Le serveur doit être lancé depuis `frejsmithweb/` avec l'option `-t public`

### Pas de données dans l'admin
- Ouvre la console du navigateur (F12)
- Cherche des erreurs
- Vérifie que `tracker.js` est chargé

### Admin: "Identifiants incorrects"
- Défaut : `admin` / `ChangeMe123!`
- Modifie dans `.env` si tu as changé

## 📚 Fichiers Importants

- `public/api.php` - API REST pour tous les endpoints
- `public/tracker.js` - Script de suivi client
- `public/admin.php` - Interface d'administration
- `src/Controller/SiteController.php` - Logique métier
- `src/Database.php` - Couche de base de données
- `database/update_security.sql` - Schéma de sécurité

---

**🎉 Ton système de sécurité avancé est prêt à l'emploi !**

Toute l'activité est tracée, les menaces sont détectées en temps réel, et tu as une visibilité complète sur tous les visiteurs et les tentatives de piratage.
