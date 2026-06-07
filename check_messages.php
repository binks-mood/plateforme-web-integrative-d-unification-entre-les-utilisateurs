<?php
require_once 'includes/config.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$s = $pdo->query('DESCRIBE messages');
print_r($s->fetchAll(PDO::FETCH_ASSOC));
