<?php
// =====================================================
// NexaFlow – Connexion PDO à la Base de Données
// =====================================================

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // En production: logger l'erreur sans la divulguer
                die(json_encode([
                    'error' => true,
                    'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()
                ]));
            }
        }
        return self::$instance;
    }
}

// Fonction helper rapide
function db(): PDO {
    return Database::getConnection();
}
