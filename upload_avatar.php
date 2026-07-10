<?php
// =====================================================
// NexaFlow – API : Upload Avatar Utilisateur
// =====================================================
require_once '../../includes/functions.php';
requireAuth();
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['avatar'])) {
    jsonResponse(['success' => false, 'message' => 'Fichier manquant.'], 400);
}

$file     = $_FILES['avatar'];
$allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxSize  = 2 * 1024 * 1024; // 2 Mo

if ($file['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['success' => false, 'message' => 'Erreur lors de l\'upload.'], 400);
}

if (!in_array($file['type'], $allowed)) {
    jsonResponse(['success' => false, 'message' => 'Format non supporté. Utilisez JPG, PNG, GIF ou WEBP.'], 400);
}

if ($file['size'] > $maxSize) {
    jsonResponse(['success' => false, 'message' => 'Fichier trop volumineux (max 2 Mo).'], 400);
}

$ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $uid . '_' . time() . '.' . strtolower($ext);
$uploadDir = __DIR__ . '/../../uploads/avatars/';
$dest      = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    jsonResponse(['success' => false, 'message' => 'Impossible de sauvegarder le fichier.'], 500);
}

// Ajouter la colonne avatar_url si elle n'existe pas encore
try {
    db()->exec("ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255) NULL AFTER avatar_initials");
} catch (PDOException $e) {
    // Colonne déjà existante, on ignore
}

// Supprimer l'ancien avatar s'il existe
$stmt = db()->prepare("SELECT avatar_url FROM users WHERE id = ?");
$stmt->execute([$uid]);
$oldUrl = $stmt->fetchColumn();
if ($oldUrl) {
    $oldPath = $uploadDir . basename($oldUrl);
    if (file_exists($oldPath)) @unlink($oldPath);
}

$avatarUrl = 'uploads/avatars/' . $filename;
db()->prepare("UPDATE users SET avatar_url = ? WHERE id = ?")->execute([$avatarUrl, $uid]);
$_SESSION['user_avatar'] = $avatarUrl;

logActivity($uid, 'a changé son avatar', 'user', $uid, $_SESSION['user_name'] ?? '');
jsonResponse(['success' => true, 'message' => 'Avatar mis à jour !', 'avatar_url' => $avatarUrl]);
