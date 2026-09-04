<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$type = isset($_GET['type']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['type']) : '';
$valid_types = ['blogs', 'doctors', 'staff', 'team'];

if (in_array($type, $valid_types)) {
    $filePath = __DIR__ . '/../data/' . $type . '.json';
    if (file_exists($filePath)) {
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
        echo json_encode([
            'success' => true,
            'type' => $type,
            'data' => $data ? $data : []
        ]);
        exit;
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Invalid endpoint or file not found'
]);
exit;
