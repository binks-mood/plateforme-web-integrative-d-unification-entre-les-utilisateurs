<?php
// =====================================================
// NexaFlow – API : Liste Utilisateurs Administrateur
// =====================================================
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$user = getCurrentUser();
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès interdit.']);
    exit;
}

$stmt = db()->prepare("
    SELECT id, firstname, lastname, email, organisation, role, avatar_initials, created_at, is_active
    FROM users
    ORDER BY created_at DESC
");
$stmt->execute();
$users = $stmt->fetchAll();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($users);

