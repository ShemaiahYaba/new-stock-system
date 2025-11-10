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
checkAuth();

$coilId = (int) ($_GET['coil_id'] ?? 0);

if ($coilId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid coil ID']);
    exit();
}

try {
    $stockEntryModel = new StockEntry();

    // Get available entries (already filtered by the model)
    $entries = $stockEntryModel->getByCoilId($coilId, 1000, 0);

    // Return the entries array directly since that's what the frontend expects
    echo json_encode([
        'success' => true,
        'entries' => $entries
    ]);
} catch (Exception $e) {
    error_log('Error in get_stock_entries: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving stock entries',
        'error' => $e->getMessage(),
    ]);
}
