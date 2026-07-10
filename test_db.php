<?php
require_once __DIR__ . '/includes/config.php';
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $stmt = $pdo->query("SELECT id, email, role, is_active FROM users WHERE email='emmanuel.soonet@admin.com'");
    print_r($stmt->fetch());
} catch (Exception $e) {
    echo $e->getMessage();
}
