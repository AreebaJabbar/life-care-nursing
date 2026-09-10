<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$type = isset($_GET['type']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['type']) : '';
$valid_types = ['blogs', 'doctors', 'staff', 'team'];

if (in_array($type, $valid_types)) {
    $data = getData($type);
    echo json_encode([
        'success' => true,
        'type' => $type,
        'data' => $data ? $data : []
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Invalid endpoint or data not found'
]);
exit;
