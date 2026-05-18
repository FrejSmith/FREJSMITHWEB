# FrejSmithWeb - HTML/CSS/JS + PHP/Symfony

Site web professionnel pour FrejSmithWeb avec frontend HTML/CSS/JS et backend PHP pour la gestion de la base de données.

## 📁 Structure du projet

```
/
├── public/                 # Fichiers publics (HTML, CSS, JS)
│   ├── index.html         # Page d'accueil
│   ├── about.html         # À propos
│   ├── services.html      # Services
│   ├── portfolio.html     # Portfolio
│   ├── contact.html       # Contact
│   ├── styles.css         # Styles
│   ├── carousel.js        # Carousel
│   ├── contact.js         # Gestion formulaire
│   └── api.php            # API PHP
├── src/                   # Code PHP
│   ├── Database.php       # Classe de connexion BD
│   └── Controller/
│       └── ContactController.php  # Contrôleur contact
├── database/
│   └── schema.sql         # Structure de la BD
├── composer.json          # Dépendances PHP
└── README.md
```

## 🚀 Installation

### 1. Cloner le projet
```bash
cd /Users/macbookpro/Documents/FREJSMITHWEB/frejsmithweb
```

### 2. Installer les dépendances PHP
```bash
composer install
```

### 3. Configurer la base de données
- Copier `.env.example` en `.env`
- Modifier les paramètres DB dans `.env`
- Créer la BD avec `database/schema.sql` dans phpMyAdmin

### 4. Lancer le serveur
```bash
php -S localhost:8000 -t public
```

Puis ouvrir : `http://localhost:8000/index.html`

## 🐳 Exécuter avec Docker
Cette configuration ajoute MySQL et phpMyAdmin en local, ainsi qu'un serveur PHP Apache.

1. Installez Docker Desktop sur macOS.
2. Dans le dossier du projet :
```bash
docker compose up -d --build
```
3. Ouvrez le site :
```text
http://localhost:8000
```
4. Ouvrez phpMyAdmin :
```text
http://localhost:8081
```

### phpMyAdmin
- Serveur : `db`
- Utilisateur : `root`
- Mot de passe : `Gbotounousmith1992@@@`
- Base : `frejsmithweb`

> Note : le service MySQL du conteneur est mappé sur le port hôte `3307` pour éviter un conflit si MySQL est déjà installé sur votre machine.

## 🔗 Publier sur GitHub
1. Initialiser le dépôt local :
```bash
git init
git add .
git commit -m "Initial commit"
```
2. Créer le dépôt GitHub et relier la télécommande :
```bash
git branch -M main
git remote add origin git@github.com:<votre-utilisateur>/<votre-repo>.git
git push -u origin main
```

Si vous avez le CLI GitHub (`gh`) :
```bash
gh auth login
gh repo create <votre-utilisateur>/<votre-repo> --public --source=. --remote=origin --push
```

## 📝 Fichiers principaux

- **public/index.html** - Page d'accueil avec carousel
- **public/contact.html** - Formulaire de contact
- **public/styles.css** - Tous les styles
- **public/carousel.js** - Logique carousel
- **public/contact.js** - AJAX pour le formulaire
- **public/api.php** - Endpoints API PHP
- **src/Database.php** - Classe d'accès BD
- **src/Controller/ContactController.php** - Logique contact

## 🛠️ Technologies

- **Frontend** : HTML, CSS, JavaScript vanilla
- **Backend** : PHP 8.0+
- **BD** : MySQL
- **Framework** : Symfony 6 (optionnel)

## 📧 Formulaire de contact

Le formulaire envoie les données via AJAX à `api.php` qui les sauvegarde en BD.

Les messages sont stockés dans la table `contacts`.

## 🔧 Configuration

Modifiez `.env` pour la connexion BD :
```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=frejsmithweb
DB_USER=root
DB_PASS=
```

## ✨ Fonctionnalités

✅ Carousel automatique  
✅ Navigation complète  
✅ Formulaire contact avec BD  
✅ API PHP pour les interactions  
✅ Design responsive  
✅ Logo personnalisable  

## 📝 Notes

- Remplacez les images placeholder par vos vraies images
- Importez `database/schema.sql` dans votre BD
- Configurez correctement `.env`
- Testez le formulaire de contact

