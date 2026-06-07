<?php
// =====================================================
// NexaFlow – SQL Schema Patch (Teams & Admin)
// =====================================================
require_once 'includes/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Vérification et mise à jour de la base de données...<br>";

    // Table: teams
    $pdo->exec("CREATE TABLE IF NOT EXISTS teams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    echo "- Table 'teams' prête.<br>";

    // Table: team_members
    $pdo->exec("CREATE TABLE IF NOT EXISTS team_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        team_id INT NOT NULL,
        user_id INT NOT NULL,
        joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_team_member (team_id, user_id)
    ) ENGINE=InnoDB;");
    echo "- Table 'team_members' prête.<br>";

    // Vérifier si l'utilisateur admin existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['emmanuel.soonet@admin.com']);
    if (!$stmt->fetch()) {
        $hash = password_hash('Admin123!', PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->prepare("INSERT INTO users (firstname, lastname, email, password_hash, organisation, role, avatar_initials, is_active) 
                       VALUES ('Emmanuel', 'Soonet', 'emmanuel.soonet@admin.com', ?, 'Direction générale', 'admin', 'ES', 1)")
            ->execute([$hash]);
        echo "- Admin 'Emmanuel Soonet' créé (Admin123!).<br>";
    } else {
        echo "- Admin 'Emmanuel Soonet' existe déjà.<br>";
    }

    echo "<br><b>Mise à jour terminée avec succès !</b>";

} catch (PDOException $e) {
    echo "<br><b style='color:red'>Erreur : " . $e->getMessage() . "</b>";
}
