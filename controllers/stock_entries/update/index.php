<?php
/**
 * REPLACE controllers/stock_entries/update/index.php
 * Check status after updating stock entry meters
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }
    
    $entryId = (int)($_POST['id'] ?? 0);
    $meters = floatval($_POST['meters'] ?? 0);
    
    $errors = [];
    
    if ($meters <= 0) $errors[] = 'Meters must be greater than 0.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
        exit();
    }
    
    $stockEntryModel = new StockEntry();
    $entry = $stockEntryModel->findById($entryId);
    
    if (!$entry) {
        setFlashMessage('error', 'Stock entry not found.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }
    
    // Calculate meters used
    $metersUsed = $entry['meters'] - $entry['meters_remaining'];
    
    // Ensure new total is not less than meters already used
    if ($meters < $metersUsed) {
        setFlashMessage('error', 'Total meters cannot be less than meters already used (' . number_format($metersUsed, 2) . 'm).');
        header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
        exit();
    }
    
    // Calculate new remaining meters
    $newRemaining = $meters - $metersUsed;
    
    $data = [
        'meters' => $meters,
        'meters_remaining' => $newRemaining
    ];
    
    if ($stockEntryModel->update($entryId, $data)) {
        // ✅ NEW: CHECK AND UPDATE COIL STATUS AUTOMATICALLY
        $stockEntryModel->checkAndUpdateCoilStatus($entry['coil_id']);
        
        logActivity('Stock entry updated', "Entry ID: $entryId, New meters: $meters");
        setFlashMessage('success', 'Stock entry updated successfully!');
        header('Location: /new-stock-system/index.php?page=stock_entries');
    } else {
        setFlashMessage('error', 'Failed to update stock entry.');
        header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=stock_entries');
exit();