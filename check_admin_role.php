<?php
require_once 'includes/config.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$s = $pdo->prepare("SELECT email, role FROM users WHERE email=?");
$s->execute(['emmanuel.soonet@admin.com']);
print_r($s->fetch());
