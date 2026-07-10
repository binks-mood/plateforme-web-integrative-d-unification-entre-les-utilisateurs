<?php
require_once '../../includes/functions.php';
$_SESSION['user_id'] = 2; // Simulate a user

// Simulate API call to create event
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['action'] = 'create_event';
$_POST = [
    'title' => 'Test event',
    'event_date' => '2026-05-15',
    'event_time' => '10:00',
    'color' => '#ff0000'
];

ob_start();
include 'index.php';
$output = ob_get_clean();

echo "API Output: " . $output . "\n\n";

$stmt = db()->query("SELECT * FROM events");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
