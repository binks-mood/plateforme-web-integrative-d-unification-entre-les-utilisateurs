<?php
// =====================================================
// NexaFlow – Configuration Principale
// =====================================================

// Paramètres de base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Modifier selon votre config
define('DB_PASS', 'Superemm@nuel1');           // Modifier selon votre config
define('DB_NAME', 'nexaflow');
define('DB_CHARSET', 'utf8mb4');

// Paramètres applicatifs
define('APP_NAME', 'NexaFlow');
define('APP_URL', 'http://localhost/projet%20de%20memoire/nexaflow-php');
define('APP_VERSION', '1.0.0');
define('SESSION_TIMEOUT', 3600 * 24 * 7); // 7 jours

// Sécurité
define('HASH_COST', 12); // bcrypt cost factor

// Démarrage de session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path'     => '/',
        'secure'   => false, // true en production HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Fuseau horaire
date_default_timezone_set('Europe/Paris');

// Rapport d'erreurs (désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
