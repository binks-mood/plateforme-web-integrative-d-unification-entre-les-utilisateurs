# NexaFlow PHP – Guide d'Installation

## Prérequis
- Un serveur web avec **PHP 8.0+** (ex: XAMPP, WAMP, MAMP, ou un serveur Linux/Nginx/Apache)
- Une base de données **MySQL / MariaDB**
- L'extension PHP **PDO_MySQL** activée

## Installation pas-à-pas

### 1. Base de données
1. Ouvrez votre gestionnaire MySQL (phpMyAdmin, TablePlus, DBeaver, etc.)
2. Créez une nouvelle base de données nommée `nexaflow`
   ```sql
   CREATE DATABASE nexaflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Importez le fichier fourni : `sql/nexaflow.sql`. 
   Il se chargera de créer toutes les tables et d'insérer des données de test (projets fictifs, utilisateurs, etc).

### 2. Configuration PHP
1. Ouvrez le fichier `includes/config.php`
2. Modifiez si besoin les paramètres de connexion à la base de données (si vous n'utilisez pas l'utilisateur `root` sans mot de passe) :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'nexaflow');
   ```

### 3. Lancer l'application
1. Placez tout le dossier `nexaflow-php` dans la racine de votre serveur web (ex: `htdocs/` pour XAMPP ou `www/` pour WAMP).
2. Accédez au projet via votre navigateur : `http://localhost/nexaflow-php/`
3. Vous serez redirigé vers la page de connexion.

### Comptes de démonstration
Le script SQL génère automatiquement des comptes de test :
- Email : `admin@nexaflow.fr` / Mot de passe : `password`
- Email : `jean@nexaflow.fr` / Mot de passe : `password`
- Email : `marie@nexaflow.fr` / Mot de passe : `password`

Vous pouvez aussi créer un nouveau compte via la page d'inscription !

## Changements depuis la version HTML/JS
- App complètement refaite avec **PHP**.
- Ajout d'une vraie **connexion MySQL**.
- Sécurité : hashage des mots de passes (bcrypt), protection XSS, sessions sécurisées.
- API JSON interne complétement fonctionnelle (`api/`).
- Centralisation des assets CSS dans un fichier `assets/css/style.css` unique pour plus de maintenabilité.
