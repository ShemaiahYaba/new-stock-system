<?php
/**
 * Stock Entry Toggle Status Controller
 * Moves stock between 'available' and 'factory_use'
 * Auto-creates ledger inflow when moving to factory_use
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_ledger.php';
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
    $currentStatus = sanitize($_POST['current_status'] ?? 'available');
    
    if ($entryId <= 0) {
        setFlashMessage('error', 'Invalid stock entry ID.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }
    
    $stockEntryModel = new StockEntry();
    $entry = $stockEntryModel->findById($entryId);
    
    if (!$entry) {
        setFlashMessage('error', 'Stock entry not found.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }
    
    // Check if entry has remaining meters
    if ($entry['meters_remaining'] <= 0) {
        setFlashMessage('error', 'Cannot change status of exhausted stock entry.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }
    
    // Toggle status
    $newStatus = ($currentStatus === 'available') ? 'factory_use' : 'available';
    
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        // Update stock entry status
        if (!$stockEntryModel->update($entryId, ['status' => $newStatus])) {
            throw new Exception('Failed to update stock entry status.');
        }
        
        // If moving TO factory_use, create ledger inflow entry
        if ($newStatus === 'factory_use') {
            $ledgerModel = new StockLedger();
            $coilModel = new Coil();
            $coil = $coilModel->findById($entry['coil_id']);
            $currentUser = getCurrentUser();
            
            $description = "Stock moved to factory use - Entry #{$entryId} ({$entry['meters_remaining']}m available)";
            
            if (!$ledgerModel->recordInflow(
                $entry['coil_id'], 
                $entryId, 
                $entry['meters_remaining'], 
                $description, 
                $currentUser['id']
            )) {
                throw new Exception('Failed to create ledger entry.');
            }
            
            $message = "Stock entry moved to Factory Use. Ledger entry created with {$entry['meters_remaining']}m inflow.";
        } else {
            // If moving FROM factory_use to available, we should reverse the ledger
            // For now, just show a message
            $message = "Stock entry moved back to Available.";
        }
        
        $db->commit();
        
        logActivity('Stock entry status changed', "Entry #{$entryId}: {$currentStatus} → {$newStatus}");
        setFlashMessage('success', $message);
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Stock entry status toggle error: " . $e->getMessage());
        setFlashMessage('error', 'Failed to change status: ' . $e->getMessage());
    }
    
    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

header('Location: /new-stock-system/index.php?page=stock_entries');
exit();
