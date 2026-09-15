<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$type = isset($_GET['type']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['type']) : '';
$valid_types = ['blogs', 'doctors', 'staff', 'team'];

if (in_array($type, $valid_types)) {
    $data = getData($type);
    $data = $data ? $data : [];

    // For doctors & staff, only expose publicly APPROVED profiles.
    // Pending self-registrations stay hidden until an admin approves them.
    if (in_array($type, ['doctors', 'staff'])) {
        $data = array_values(array_filter($data, function ($item) {
            $status = $item['status'] ?? 'approved';
            return $status === 'approved';
        }));
    }

    echo json_encode([
        'success' => true,
        'type' => $type,
        'data' => $data
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Invalid endpoint or data not found'
]);
exit;
