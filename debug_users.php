<?php
require_once 'includes/config.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$s = $pdo->query('SELECT id, firstname, lastname, role, email FROM users');
print_r($s->fetchAll(PDO::FETCH_ASSOC));
