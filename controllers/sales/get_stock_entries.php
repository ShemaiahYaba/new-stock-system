<?php
/**
 * Get Stock Entries for a Coil (AJAX Endpoint)
 */

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';

header('Content-Type: application/json');

// Check authentication
if (!isAuthenticated()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$coilId = (int)($_GET['coil_id'] ?? 0);

if ($coilId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid coil ID']);
    exit();
}

$stockEntryModel = new StockEntry();
$entries = $stockEntryModel->getByCoil($coilId, 1000, 0);

// Filter only entries with remaining meters
$availableEntries = array_filter($entries, function($entry) {
    return $entry['meters_remaining'] > 0;
});

echo json_encode([
    'success' => true,
    'entries' => array_values($availableEntries)
]);
