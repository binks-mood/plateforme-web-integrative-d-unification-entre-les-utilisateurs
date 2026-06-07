<?php
require_once 'includes/functions.php';
$_SESSION['user_id'] = 2;
$_SESSION['user_role'] = 'membre';

// Simulate API call
$uid = 2;
$stmt = db()->prepare("
    SELECT t.id, t.name 
    FROM teams t 
    JOIN team_members tm ON t.id = tm.team_id 
    WHERE tm.user_id = ? 
    ORDER BY t.name ASC
");
$stmt->execute([$uid]);
$teams = $stmt->fetchAll();

print_r($teams);

$channels = [];
foreach ($teams as $t) {
    $ch_id = 'team_' . $t['id'];
    $s = db()->prepare("SELECT COUNT(*) as c FROM messages WHERE channel = ?");
    $s->execute([$ch_id]);
    
    $channels[] = [
        'id' => $ch_id,
        'name' => $t['name'],
        'icon' => '👥',
        'desc' => 'Messagerie de l\'équipe ' . $t['name'],
        'count' => (int)$s->fetch()['c']
    ];
}
print_r($channels);
