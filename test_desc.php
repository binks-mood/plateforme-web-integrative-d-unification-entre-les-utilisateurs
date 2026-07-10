<?php
require_once 'includes/functions.php';

// Simulate API call
$uid = 2;
$stmt = db()->prepare("
    SELECT t.id, t.name, t.description as desc 
    FROM teams t 
    JOIN team_members tm ON t.id = tm.team_id 
    WHERE tm.user_id = ? 
    ORDER BY t.name ASC
");
$stmt->execute([$uid]);
$teams = $stmt->fetchAll();

print_r($teams);
