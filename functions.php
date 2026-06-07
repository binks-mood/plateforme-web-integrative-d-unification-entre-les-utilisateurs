<?php
// =====================================================
// NexaFlow – Fonctions Utilitaires & Auth
// =====================================================

require_once __DIR__ . '/db.php';

// ─────────────────────────────────────
// AUTHENTIFICATION
// ─────────────────────────────────────

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php?error=session_expired');
        exit;
    }
    
    // Check if user still exists and is active
    $stmt = db()->prepare("SELECT id FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        session_destroy();
        header('Location: login.php?error=account_disabled');
        exit;
    }

    // Vérifier timeout de session
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: login.php?error=session_timeout');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $stmt = db()->prepare("SELECT id, firstname, lastname, email, organisation, role, avatar_initials, created_at FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function login(string $email, string $password): array {
    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([trim($email)]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['last_activity'] = time();

    // Journal d'activité
    logActivity($user['id'], 'S\'est connecté', null, null, null);

    return ['success' => true, 'message' => 'Connexion réussie !'];
}

function register(array $data): array {
    // Validation
    if (empty($data['firstname']) || empty($data['lastname']) || empty($data['email']) || empty($data['password'])) {
        return ['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis.'];
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Adresse email invalide.'];
    }
    if (strlen($data['password']) < 8) {
        return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères.'];
    }

    // Vérifier si l'email existe déjà
    $stmt = db()->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([trim($data['email'])]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Cette adresse email est déjà utilisée.'];
    }

    // Créer l'utilisateur
    $initials = mb_strtoupper(mb_substr($data['firstname'], 0, 1, 'UTF-8') . mb_substr($data['lastname'], 0, 1, 'UTF-8'), 'UTF-8');
    $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);

    $stmt = db()->prepare("SELECT COUNT(*) as cnt FROM users");
    $stmt->execute();
    
    // Détermination du rôle
    if ($stmt->fetch()['cnt'] == 0) {
        $role = 'admin'; // Le premier utilisateur est toujours admin
    } else {
        $role = 'developpeur'; // Par défaut
        if (isset($data['role']) && $data['role'] === 'chef_projet') {
            $role = 'chef_projet';
        }
    }

    $stmt = db()->prepare("
        INSERT INTO users (firstname, lastname, email, password_hash, organisation, avatar_initials, role)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        trim($data['firstname']),
        trim($data['lastname']),
        trim($data['email']),
        $hash,
        trim($data['organisation'] ?? ''),
        $initials,
        $role
    ]);

    $userId = db()->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $data['firstname'] . ' ' . $data['lastname'];
    $_SESSION['user_role'] = $role;
    $_SESSION['last_activity'] = time();

    // Notification de bienvenue
    $stmt = db()->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, 'Bienvenue sur NexaFlow !', 'Votre compte a été créé avec succès. Commencez par créer votre premier projet.', 'success']);

    logActivity($userId, 'A créé son compte', null, null, null);

    return ['success' => true, 'message' => 'Compte créé avec succès !'];
}

function logout(): void {
    if (isLoggedIn()) {
        logActivity($_SESSION['user_id'], 'S\'est déconnecté', null, null, null);
    }
    session_destroy();
    header('Location: login.php');
    exit;
}

// ─────────────────────────────────────
// ACTIVITÉS
// ─────────────────────────────────────

function logActivity(int $userId, string $action, ?string $entityType, ?int $entityId, ?string $entityName): void {
    try {
        $stmt = db()->prepare("INSERT INTO activities (user_id, action, entity_type, entity_id, entity_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $entityType, $entityId, $entityName]);
    } catch (Exception $e) {
        // Silencer les erreurs de journalisation
    }
}

// ─────────────────────────────────────
// UTILITAIRES
// ─────────────────────────────────────

function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function timeAgo(string $datetime): string {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->i < 1) return 'À l\'instant';
    if ($diff->i < 60) return "Il y a {$diff->i} min";
    if ($diff->h < 24) return "Il y a {$diff->h}h";
    if ($diff->d === 1) return 'Hier';
    if ($diff->d < 7) return "Il y a {$diff->d} jours";
    if ($diff->d < 30) return "Il y a " . floor($diff->d / 7) . " sem.";
    return $ago->format('d/m/Y');
}

function formatDate(string $date): string {
    if (empty($date)) return '-';
    return (new DateTime($date))->format('d/m/Y');
}

function getStatusLabel(string $status): string {
    return match($status) {
        'active'      => 'En cours',
        'planned'     => 'Planifié',
        'done'        => 'Terminé',
        'late'        => 'En retard',
        'todo'        => 'À faire',
        'in_progress' => 'En cours',
        'review'      => 'En révision',
        default       => ucfirst($status),
    };
}

function getPriorityLabel(string $priority): string {
    return match($priority) {
        'haute'  => 'Haute',
        'moyenne'=> 'Moyenne',
        'basse'  => 'Basse',
        default  => ucfirst($priority),
    };
}

/**
 * Recalcule et met à jour le pourcentage de progression d'un projet basé sur ses tâches
 */
function updateProjectProgress(int $projectId): void {
    if (!$projectId) return;

    $stmtTotal = db()->prepare("SELECT COUNT(*) as cnt FROM tasks WHERE project_id = ?");
    $stmtTotal->execute([$projectId]);
    $total = (int)$stmtTotal->fetch()['cnt'];

    if ($total === 0) {
        $progress = 0;
    } else {
        $stmtDone = db()->prepare("SELECT COUNT(*) as cnt FROM tasks WHERE project_id = ? AND status = 'done'");
        $stmtDone->execute([$projectId]);
        $done = (int)$stmtDone->fetch()['cnt'];
        $progress = round(($done / $total) * 100);
    }

    $stmtUpd = db()->prepare("UPDATE projects SET progress = ? WHERE id = ?");
    $stmtUpd->execute([$progress, $projectId]);
}
