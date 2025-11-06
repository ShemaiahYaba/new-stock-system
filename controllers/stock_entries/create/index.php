<?php
/**
 * REPLACE controllers/stock_entries/create/index.php with this
 * Automatically sets coil to AVAILABLE when new stock added to OUT_OF_STOCK coil
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_ledger.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
        exit();
    }
    
    $coilId = (int)($_POST['coil_id'] ?? 0);
    $meters = floatval($_POST['meters'] ?? 0);
    
    $errors = [];
    
    if ($coilId <= 0) $errors[] = 'Please select a coil.';
    if ($meters <= 0) $errors[] = 'Meters must be greater than 0.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
        exit();
    }
    
    // Verify coil exists
    $coilModel = new Coil();
    $coil = $coilModel->findById($coilId);
    
    if (!$coil) {
        setFlashMessage('error', 'Coil not found.');
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
        exit();
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        $stockEntryModel = new StockEntry();
        $currentUser = getCurrentUser();
        
        $data = [
            'coil_id' => $coilId,
            'meters' => $meters,
            'meters_remaining' => $meters,
            'created_by' => $currentUser['id']
        ];
        
        $entryId = $stockEntryModel->create($data);
        
        if (!$entryId) {
            throw new Exception('Failed to create stock entry.');
        }
        
        // Record ledger entry for factory-use coils
        if ($coil['status'] === STOCK_STATUS_FACTORY_USE) {
            $ledgerModel = new StockLedger();
            $description = "Stock entry added - {$meters}m for {$coil['code']}";
            $ledgerModel->recordInflow($coilId, $entryId, $meters, $description, $currentUser['id']);
        }
        
        // ✅ NEW: CHECK AND UPDATE COIL STATUS AUTOMATICALLY
        // If coil was OUT_OF_STOCK, this will set it back to AVAILABLE
        $statusChanged = $stockEntryModel->checkAndUpdateCoilStatus($coilId);
        
        $db->commit();
        
        logActivity('Stock entry created', "Coil: {$coil['code']}, Meters: $meters");
        
        if ($statusChanged && $coil['status'] === STOCK_STATUS_OUT_OF_STOCK) {
            setFlashMessage('success', 'Stock entry created successfully! Coil status changed from OUT OF STOCK to AVAILABLE.');
        } else {
            setFlashMessage('success', 'Stock entry created successfully!');
        }
        
        header('Location: /new-stock-system/index.php?page=stock_entries');
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Stock entry creation error: " . $e->getMessage());
        setFlashMessage('error', 'Failed to create stock entry: ' . $e->getMessage());
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
    }
    
    exit();
}

header('Location: /new-stock-system/index.php?page=stock_entries_create');
exit();