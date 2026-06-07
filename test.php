<?php
require_once 'includes/functions.php';
$stmt = db()->query("SELECT * FROM team_members");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "TEAM MEMBERS:\n";
print_r($res);

$stmt2 = db()->query("SELECT * FROM teams");
$res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo "TEAMS:\n";
print_r($res2);
