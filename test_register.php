<?php
require_once __DIR__ . '/includes/functions.php';

$data = [
    'firstname' => 'Test',
    'lastname' => 'User',
    'email' => 'test' . time() . '@example.com',
    'password' => 'password123',
    'organisation' => 'Test Org'
];

try {
    $result = register($data);
    print_r($result);
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
