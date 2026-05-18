# FrejSmithWeb - Système de Sécurité Avancé

## 🚨 Fonctionnalités de Sécurité Implémentées

### 1. **Tracking Avancé des Visiteurs**
- **Empreinte digitale** du navigateur (fingerprinting)
- **Détection d'automatisation** (bots, scrapers)
- **Surveillance des comportements suspects** (clics rapides, navigation inhabituelle)
- **Collecte d'informations détaillées** (résolution écran, plugins, timezone, etc.)

### 2. **Système de Détection de Menaces**
- **Score de menace** calculé automatiquement (0-100)
- **Classification par sévérité** (low, medium, high, critical)
- **Détection d'attaques** :
  - Tentatives d'injection SQL
  - Scripts malveillants (XSS)
  - Utilisation d'outils de développement
  - Automatisation suspecte
  - Traversée de répertoires

### 3. **Logs de Sécurité Complets**
- **Événements de sécurité** avec détails complets
- **Erreurs JavaScript** capturées et analysées
- **Tentatives de connexion admin** tracées
- **Statistiques en temps réel**

### 4. **Interface d'Administration Sécurisée**
- **Authentification requise** pour accéder à l'admin
- **Tableau de bord** avec statistiques de sécurité
- **Visualisation des menaces** en temps réel
- **Gestion des logs** et analyse des incidents

### 5. **Protection au Niveau Serveur (.htaccess)**
- **Blocage des User-Agents suspects** (bots, scanners)
- **Protection XSS** et injection
- **Limitation des requêtes** (anti-DDoS)
- **Headers de sécurité** avancés
- **CSP (Content Security Policy)**

## 📊 Tables de Base de Données

### visitors
- Informations détaillées sur chaque visiteur
- Empreinte digitale et données de sécurité

### security_logs
- Tous les événements de sécurité détectés
- Score de menace et niveau de sévérité

### js_errors
- Erreurs JavaScript capturées côté client

### admin_attempts
- Tentatives de connexion à l'interface admin

## 🔧 Installation et Configuration

### 1. Mise à Jour de la Base de Données
```bash
mysql -u root -p < frejsmithweb/database/update_security.sql
```

### 2. Configuration des Identifiants Admin
Dans `frejsmithweb/.env` :
```env
ADMIN_USER=votre_username
ADMIN_PASS=votre_mot_de_passe_sécurise
```

### 3. Lancement du Serveur
```bash
cd frejsmithweb
php -S localhost:8000 -t public
```

### 4. Accès à l'Interface Admin
- URL : `http://localhost:8000/admin.php`
- Utilisez les identifiants configurés

## 📈 Fonctionnement du Système

### Collecte Automatique
Le système `tracker.js` s'exécute automatiquement sur chaque page et :
- Collecte des données de sécurité toutes les 30 secondes
- Détecte les comportements suspects en temps réel
- Envoie les données à l'API pour analyse

### Analyse des Menaces
- **Score de menace** calculé basé sur :
  - Comportement de navigation
  - User-Agent
  - Référent
  - Adresse IP
  - Actions suspectes détectées

### Alertes et Réponses
- **Blocage automatique** pour les scores > 80
- **Logs détaillés** pour analyse post-incident
- **Interface admin** pour monitoring en temps réel

## 🛡️ Mesures de Sécurité Implémentées

### Frontend
- **CSP stricte** pour prévenir les injections
- **Surveillance DOM** pour détecter les modifications suspectes
- **Anti-debugging** et détection d'outils de développement
- **Validation côté client** avant envoi

### Backend
- **Prepared statements** pour toutes les requêtes SQL
- **Sanitisation** de toutes les entrées utilisateur
- **Logging sécurisé** des événements sensibles
- **Sessions PHP** pour l'authentification admin

### Serveur
- **Headers de sécurité** avancés
- **Blocage des scanners** et bots connus
- **Protection contre les attaques courantes**
- **Limites de requêtes** pour prévenir les abus

## 📊 Métriques Disponibles

### Statistiques Générales
- Nombre total de visiteurs
- Nombre d'événements de sécurité
- Menaces à haut risque
- Tentatives bloquées

### Analyse en Temps Réel
- Événements de la dernière heure
- IPs les plus suspectes
- Tendances de sécurité

### Logs Détaillés
- Historique complet des incidents
- Tentatives de connexion admin
- Erreurs JavaScript capturées

## 🔍 Utilisation pour la Détection de Piratage

### Indicateurs de Compromission
- **Score de menace élevé** (> 60)
- **Activités d'automatisation** détectées
- **Tentatives d'injection** répétées
- **Accès depuis IPs suspectes**

### Réponse aux Incidents
1. **Identifier** l'IP source
2. **Analyser** le comportement
3. **Bloquer** si nécessaire
4. **Documenter** l'incident

### Monitoring Continu
- Vérifiez régulièrement les statistiques
- Surveillez les nouvelles menaces
- Ajustez les règles de sécurité si besoin

## ⚠️ Recommandations de Sécurité

### Production
- Changez régulièrement les mots de passe admin
- Utilisez HTTPS en production
- Surveillez les logs régulièrement
- Mettez à jour les règles de sécurité

### Développement
- Testez les fonctionnalités de sécurité
- Vérifiez les logs pendant les tests
- Ajustez les seuils de détection si nécessaire

---

**Système développé pour FrejSmithWeb - Protection avancée contre les menaces web** 🛡️