<?php
// =====================================================
// NexaFlow – API : Activités Administrateur
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
    SELECT a.*, u.firstname, u.lastname, u.avatar_initials
    FROM activities a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 100
");
$stmt->execute();
$activities = $stmt->fetchAll();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($activities);

