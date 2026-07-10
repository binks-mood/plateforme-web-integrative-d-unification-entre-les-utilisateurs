<?php
require_once 'includes/config.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$s = $pdo->query('SELECT * FROM projects');
$projects = $s->fetchAll(PDO::FETCH_ASSOC);
foreach($projects as $p) {
    echo "ID: {$p['id']} | Name: {$p['name']} | Owner: {$p['owner_id']}\n";
}
