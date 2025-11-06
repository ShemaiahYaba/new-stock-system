<?php
/**
 * Sale Create Controller with Wholesale/Retail Logic
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/sale.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/customer.php';
require_once __DIR__ . '/../../../models/stock_ledger.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_SALES_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=sales_create');
        exit();
    }
    
    $customerId = (int)($_POST['customer_id'] ?? 0);
    $stockEntryId = (int)($_POST['stock_entry_id'] ?? 0);
    $saleType = 'sale'; // Default sale type
    $meters = floatval($_POST['meters'] ?? 0);
    $pricePerMeter = floatval($_POST['price_per_meter'] ?? 0);
    $totalAmount = floatval($_POST['total_amount'] ?? 0);
    
    // Debug log
    error_log("Form data: " . print_r($_POST, true));
    
    $errors = [];
    
    // Basic validation
    if ($customerId <= 0) $errors[] = 'Please select a customer.';
    if ($stockEntryId <= 0) $errors[] = 'Please select a stock entry.';
    if ($meters <= 0) $errors[] = 'Meters must be greater than 0.';
    if ($pricePerMeter <= 0) $errors[] = 'Price per meter must be greater than 0.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=sales_create');
        exit();
    }
    
    // Verify entities exist
    $coilModel = new Coil();
    $stockEntryModel = new StockEntry();
    $customerModel = new Customer();
    
    $stockEntry = $stockEntryModel->findById($stockEntryId);
    $customer = $customerModel->findById($customerId);
    
    if (!$stockEntry) {
        setFlashMessage('error', 'Stock entry not found.');
        header('Location: /new-stock-system/index.php?page=sales_create');
        exit();
    }
    
    if (!$customer) {
        setFlashMessage('error', 'Customer not found.');
        header('Location: /new-stock-system/index.php?page=sales_create');
        exit();
    }
    
    // Get coil from stock entry
    $coilId = $stockEntry['coil_id'];
    $coil = $coilModel->findById($coilId);
    if (!$coil) {
        setFlashMessage('error', 'Coil not found for the selected stock entry.');
        header('Location: /new-stock-system/index.php?page=sales_create');
        exit();
    }
    
    // Get stock entry status
    $stockStatus = $stockEntry['status'] ?? 'available';
    
    // Set sale type based on stock status
    $saleType = ($stockStatus === 'factory_use') ? SALE_TYPE_RETAIL : SALE_TYPE_WHOLESALE;
    
    // Enforce Wholesale Rules (Available Stock)
    if ($saleType === SALE_TYPE_WHOLESALE) {
        // Rule 1: Only AVAILABLE stock entries
        if ($stockStatus !== 'available') {
            setFlashMessage('error', 'Wholesale sales can only be made from AVAILABLE stock entries.');
            header('Location: /new-stock-system/index.php?page=sales_create');
            exit();
        }
        
        // Rule 2: Must use fixed meters from stock entry
        if ($meters != $stockEntry['meters']) {
            setFlashMessage('error', 'Wholesale sales must use the fixed meter specification (' . number_format($stockEntry['meters'], 2) . 'm).');
            header('Location: /new-stock-system/index.php?page=sales_create');
            exit();
        }
    }
    
    // Enforce Retail Rules (Factory Use Stock)
    if ($saleType === SALE_TYPE_RETAIL) {
        // Rule 1: Only FACTORY_USE stock entries
        if ($stockStatus !== 'factory_use') {
            setFlashMessage('error', 'Retail sales can only be made from FACTORY USE stock entries.');
            header('Location: /new-stock-system/index.php?page=sales_create');
            exit();
        }
        
        // Rule 2: Cannot exceed remaining meters
        if ($meters > $stockEntry['meters_remaining']) {
            setFlashMessage('error', 'Sale meters (' . number_format($meters, 2) . 'm) exceed available meters (' . number_format($stockEntry['meters_remaining'], 2) . 'm).');
            header('Location: /new-stock-system/index.php?page=sales_create');
            exit();
        }
    }
    
    // Start transaction
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        // Create sale record
        $saleModel = new Sale();
        $currentUser = getCurrentUser();
        
        $saleData = [
            'customer_id' => $customerId,
            'coil_id' => $coilId,
            'stock_entry_id' => $stockEntryId,
            'sale_type' => $saleType,
            'meters' => $meters,
            'price_per_meter' => $pricePerMeter,
            'total_amount' => $totalAmount,
            'status' => 'completed',
            'created_by' => $currentUser['id']
        ];
        
        $saleId = $saleModel->create($saleData);
        
        if (!$saleId) {
            throw new Exception('Failed to create sale record.');
        }
        
        // Deduct meters from stock entry
        $newRemaining = $stockEntry['meters_remaining'] - $meters;
        
        // Update stock entry with remaining meters
        $updateData = [
            'meters_remaining' => $newRemaining,
            'status' => ($newRemaining <= 0) ? 'sold' : $stockEntry['status']
        ];
        
        if (!$stockEntryModel->update($stockEntryId, $updateData)) {
            throw new Exception('Failed to update stock entry.');
        }
        
        // Record the sale in the ledger for all stock types
        $ledgerModel = new StockLedger();
        $description = "Sale to {$customer['name']} ({$meters}m @ ₦{$pricePerMeter}/m)";
        
        if (!$ledgerModel->recordOutflow($coilId, $stockEntryId, $meters, $description, 'sale', $saleId, $currentUser['id'])) {
            error_log("Failed to record ledger entry for sale $saleId");
            // Don't fail the transaction for ledger issues
        }
        
        
        // Check if stock is exhausted and update coil status
        $allEntries = $stockEntryModel->getByCoil($coilId, 1000, 0);
        $totalRemaining = 0;
        
        foreach ($allEntries as $entry) {
            if ($entry['status'] !== 'sold') {
                $totalRemaining += $entry['meters_remaining'];
            }
        }
        
        if ($totalRemaining <= 0) {
            // Mark coil as SOLD if no remaining meters in any entry
            $coilModel->update($coilId, ['status' => STOCK_STATUS_SOLD]);
            logActivity('Coil marked as sold', "Coil ID: $coilId");
        }
        
        // Commit transaction
        $db->commit();
        
        logActivity('Sale created', "Customer: {$customer['name']}, Coil: {$coil['code']}, Type: $saleType, Meters: $meters");
        setFlashMessage('success', 'Sale created successfully! Stock has been updated.');
        
        // Redirect to invoice page
        header('Location: /new-stock-system/index.php?page=sales_invoice&id=' . $saleId);
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Sale creation error: " . $e->getMessage());
        setFlashMessage('error', 'Failed to create sale: ' . $e->getMessage());
        header('Location: /new-stock-system/index.php?page=sales_create');
    }
    
    exit();
}

header('Location: /new-stock-system/index.php?page=sales_create');
exit();
