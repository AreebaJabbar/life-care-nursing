<?php
/**
 * LifeCare Nursing & Medical Services - Cards API
 * Returns JSON data for Doctors, Staff, and Team cards
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'all';

if ($type === 'all') {
    echo json_encode([
        'doctors' => getData('doctors'),
        'staff'   => getData('staff'),
        'team'    => getData('team')
    ], JSON_UNESCAPED_UNICODE);
} elseif (in_array($type, ['doctors', 'staff', 'team'])) {
    echo json_encode([
        'success' => true,
        'data' => getData($type)
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid type specified. Allowed: doctors, staff, team, all'
    ]);
}
