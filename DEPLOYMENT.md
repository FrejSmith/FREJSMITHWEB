# Déploiement FrejSmithWeb

## Option recommandée

### 1) Frontend statique sur Vercel
- Importer le dossier `public/` dans Vercel
- Le site public est servi en HTML/CSS/JS
- Les appels API doivent pointer vers votre backend distant

### 2) Backend PHP + MySQL sur Render ou Railway
- Déployer le projet depuis la racine du repo
- Lancer PHP avec : `php -S 0.0.0.0:$PORT -t public`
- Configurer les variables d’environnement MySQL

## Variables d’environnement backend

```env
DB_HOST=your-db-host
DB_PORT=3306
DB_NAME=frejsmithweb
DB_USER=your_db_user
DB_PASS=your_secure_password
ADMIN_USER=admin
ADMIN_PASS=your_admin_password
```

## Frontend API configuration

Avant les scripts JS dans le HTML, ajoutez :

```html
<script>
  window.API_BASE_URL = 'https://votre-backend.example.com';
</script>
```

Les fichiers JS utilisent maintenant cette valeur pour appeler `api.php`.

## Déploiement backend Render

1. Créer un service web sur Render
2. Connecter ce dépôt GitHub
3. Choisir le dossier racine du projet
4. Utiliser le fichier `render.yaml` fourni
5. Définir les variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `ADMIN_PASS`
6. Déployer

## Déploiement frontend Vercel

1. Importer le dépôt GitHub dans Vercel
2. Définir le dossier de sortie sur `public`
3. Ajouter la variable `API_BASE_URL` dans l’environnement de Vercel si besoin
4. Vérifier l’URL publique du backend dans les formulaires

## Vérification

- Site public : accessible sur Vercel
- API : accessible sur le backend distant
- Formulaires : doivent envoyer vers `api.php` sur le backend
- Admin : doit être protégé par session PHP

## Important

Vercel ne peut pas servir le backend PHP de ce projet tel quel. Il faut un backend PHP + base MySQL séparé.
