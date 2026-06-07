<?php
require_once __DIR__ . '/includes/config.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    $email = 'emmanuel.soonet@admin.com';
    $password = 'Admin123!';
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);

    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "L'administrateur existe déjà.\n";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO users (firstname, lastname, email, password_hash, organisation, avatar_initials, role)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'Emmanuel',
            'Soonet',
            $email,
            $hash,
            'Direction générale',
            'ES',
            'admin'
        ]);
        echo "Compte administrateur créé avec succès.\n";
    }

} catch (PDOException $e) {
    echo "Erreur BDD : " . $e->getMessage() . "\n";
}
