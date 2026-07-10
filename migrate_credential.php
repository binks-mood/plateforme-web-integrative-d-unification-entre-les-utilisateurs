<?php
require_once 'includes/functions.php';
try {
    // Check if column already exists
    $stmt = db()->query("SHOW COLUMNS FROM integrations LIKE 'credential'");
    if ($stmt->rowCount() === 0) {
        db()->exec("ALTER TABLE integrations ADD COLUMN credential VARCHAR(500) NULL");
        echo "Migration OK: colonne credential ajoutée.";
    } else {
        echo "La colonne credential existe déjà.";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
