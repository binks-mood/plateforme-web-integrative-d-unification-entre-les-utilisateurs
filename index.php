<?php
require_once '../../includes/functions.php';
requireAuth();
$uid = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

// ── GET: Liste des équipes avec leurs membres ──
if ($method === 'GET' && $action === 'list') {
    $stmt = db()->query("SELECT * FROM teams ORDER BY created_at DESC");
    $teams = $stmt->fetchAll();
    
    foreach ($teams as &$t) {
        $stmtMembers = db()->prepare("
            SELECT u.id, u.firstname, u.lastname, u.avatar_initials, u.role
            FROM team_members tm
            JOIN users u ON tm.user_id = u.id
            WHERE tm.team_id = ?
        ");
        $stmtMembers->execute([$t['id']]);
        $t['members'] = $stmtMembers->fetchAll();
    }
    jsonResponse(['success' => true, 'data' => $teams]);
}

// ── POST: Créer une équipe (ADMIN ONLY) ──
if ($method === 'POST' && $action === 'create') {
    if ($_SESSION['user_role'] !== 'admin') {
        jsonResponse(['success' => false, 'message' => 'Non autorisé.'], 403);
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    if (empty($data['name'])) jsonResponse(['success' => false, 'message' => 'Nom requis.'], 400);

    $stmt = db()->prepare("INSERT INTO teams (name) VALUES (?)");
    $stmt->execute([sanitize($data['name'])]);
    logActivity($uid, 'a créé l\'équipe', 'team', db()->lastInsertId(), $data['name']);
    
    jsonResponse(['success' => true, 'message' => 'Équipe créée avec succès !']);
}

// ── POST: Ajouter un utilisateur à une équipe (ADMIN ONLY) ──
if ($method === 'POST' && $action === 'add_member') {
    if ($_SESSION['user_role'] !== 'admin') {
        jsonResponse(['success' => false, 'message' => 'Non autorisé.'], 403);
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    if (empty($data['team_id']) || empty($data['user_id'])) {
        jsonResponse(['success' => false, 'message' => 'Données incomplètes.'], 400);
    }

    try {
        $stmt = db()->prepare("INSERT INTO team_members (team_id, user_id) VALUES (?, ?)");
        $stmt->execute([(int)$data['team_id'], (int)$data['user_id']]);
        logActivity($uid, 'a ajouté un membre à une équipe', 'team', $data['team_id'], '');
        jsonResponse(['success' => true, 'message' => 'Utilisateur ajouté à l\'équipe !']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Code erreur pour duplicata
            jsonResponse(['success' => false, 'message' => 'L\'utilisateur est déjà dans cette équipe.'], 400);
        }
        jsonResponse(['success' => false, 'message' => 'Erreur serveur.'], 500);
    }
}

// ── POST: Retirer un utilisateur d'une équipe (ADMIN ONLY) ──
if ($method === 'POST' && $action === 'remove_member') {
    if ($_SESSION['user_role'] !== 'admin') {
        jsonResponse(['success' => false, 'message' => 'Non autorisé.'], 403);
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    if (empty($data['team_id']) || empty($data['user_id'])) {
        jsonResponse(['success' => false, 'message' => 'Données incomplètes.'], 400);
    }

    try {
        $stmt = db()->prepare("DELETE FROM team_members WHERE team_id = ? AND user_id = ?");
        $stmt->execute([(int)$data['team_id'], (int)$data['user_id']]);
        logActivity($uid, 'a retiré un membre d\'une équipe', 'team', $data['team_id'], '');
        jsonResponse(['success' => true, 'message' => 'Utilisateur retiré de l\'équipe.']);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Erreur serveur.'], 500);
    }
}

jsonResponse(['success' => false, 'message' => 'Requête invalide.'], 400);
