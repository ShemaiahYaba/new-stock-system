<?php
/**
 * Get Available KZinc Pieces for a Coil (AJAX Endpoint)
 */

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';

header('Content-Type: application/json');

checkAuth();

$coilId = (int) ($_GET['coil_id'] ?? 0);

if ($coilId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid coil ID']);
    exit();
}

try {
    $stockEntryModel = new StockEntry();
    $entries = $stockEntryModel->getAvailableKzincEntries($coilId);

    $totalPieces = array_sum(array_column($entries, 'pieces_remaining'));

    echo json_encode([
        'success'      => true,
        'total_pieces' => (int) $totalPieces,
        'entries'      => $entries,
    ]);
} catch (Exception $e) {
    error_log('Error in get_kzinc_pieces: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving KZinc pieces',
    ]);
}
