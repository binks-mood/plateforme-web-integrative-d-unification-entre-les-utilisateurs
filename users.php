<?php
// =====================================================
// NexaFlow – API : Liste Utilisateurs Administrateur
// =====================================================
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$user = getCurrentUser();
if ($user['role'] !== 'admin') {
    jsonResponse(['success' => false, 'message' => 'Accès interdit.'], 403);
}

$stmt = db()->prepare("
    SELECT id, firstname, lastname, email, organisation, role, avatar_initials, created_at, is_active
    FROM users
    ORDER BY created_at DESC
");
$stmt->execute();
$users = $stmt->fetchAll();

jsonResponse(['success' => true, 'data' => $users]);

