<?php
require_once 'includes/functions.php';
$projects = db()->query('SELECT id FROM projects')->fetchAll();
foreach($projects as $p) {
    updateProjectProgress((int)$p['id']);
    echo "Projet ID {$p['id']} mis à jour.\n";
}
echo "Terminé.";
