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
    $weightKg = isset($_POST['weight_kg']) ? floatval($_POST['weight_kg']) : null;
    
    $errors = [];
    
    if ($meters <= 0) $errors[] = 'Meters must be greater than 0.';
    if ($weightKg !== null && $weightKg < 0) $errors[] = 'Weight must be 0 or greater.';
    
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
    
    // Handle weight update if weight_kg is provided
    if ($weightKg !== null) {
    if (isset($entry['weight_kg']) && $entry['weight_kg'] > 0) {
        // If weight exists, maintain the same ratio for remaining weight
        $weightRatio = $entry['weight_kg_remaining'] / $entry['weight_kg'];
        $newRemainingWeight = $weightKg * $weightRatio;
    } else {
        // If this is the first time setting weight, set remaining weight equal to the new weight
        $newRemainingWeight = $weightKg;
    }
    
    $data['weight_kg'] = $weightKg;
    $data['weight_kg_remaining'] = $newRemainingWeight;
}
    
    if ($stockEntryModel->update($entryId, $data)) {
        // NEW: CHECK AND UPDATE COIL STATUS AUTOMATICALLY
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