<?php
/**
 * REPLACE controllers/sales/create/index.php with this
 * Automatically updates coil status to OUT_OF_STOCK when all stock sold
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
    $coilId = (int)($_POST['coil_id'] ?? 0);
    $stockEntryId = (int)($_POST['stock_entry_id'] ?? 0);
    $saleType = sanitize($_POST['sale_type'] ?? '');
    $meters = floatval($_POST['meters'] ?? 0);
    $pricePerMeter = floatval($_POST['price_per_meter'] ?? 0);
    $totalAmount = floatval($_POST['total_amount'] ?? 0);
    
    $errors = [];
    
    if ($customerId <= 0) $errors[] = 'Please select a customer.';
    if ($coilId <= 0) $errors[] = 'Please select a coil.';
    if ($stockEntryId <= 0) $errors[] = 'Please select a stock entry.';
    if (!in_array($saleType, array_keys(SALE_TYPES))) $errors[] = 'Invalid sale type.';
    if ($meters <= 0) $errors[] = 'Meters must be greater than 0.';
    if ($pricePerMeter <= 0) $errors[] = 'Price per meter must be greater than 0.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=sales_create');
        exit();
    }
    
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
    
    $coil = $coilModel->findById($stockEntry['coil_id']);
    if (!$coil) {
        setFlashMessage('error', 'Coil not found.');
        header('Location: /new-stock-system/index.php?page=sales_create');
        exit();
    }
    
    $stockStatus = $stockEntry['status'] ?? 'available';
    
    // Enforce Wholesale Rules
    if ($saleType === SALE_TYPE_WHOLESALE) {
        if ($stockStatus !== 'available') {
            setFlashMessage('error', 'Wholesale sales can only be made from AVAILABLE stock entries.');
            header('Location: /new-stock-system/index.php?page=sales_create');
            exit();
        }
        
        if ($meters != $stockEntry['meters']) {
            setFlashMessage('error', 'Wholesale sales must use the fixed meter specification (' . number_format($stockEntry['meters'], 2) . 'm).');
            header('Location: /new-stock-system/index.php?page=sales_create');
            exit();
        }
    }
    
    // Enforce Retail Rules
    if ($saleType === SALE_TYPE_RETAIL) {
        if ($stockStatus !== 'factory_use') {
            setFlashMessage('error', 'Retail sales can only be made from FACTORY USE stock entries.');
            header('Location: /new-stock-system/index.php?page=sales_create');
            exit();
        }
        
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
        
        if (!$stockEntryModel->update($stockEntryId, ['meters_remaining' => $newRemaining])) {
            throw new Exception('Failed to update stock entry.');
        }
        
        // Record ledger entry for factory-use stock entries
        if ($stockStatus === 'factory_use') {
            $ledgerModel = new StockLedger();
            $description = "Retail sale to {$customer['name']} ({$meters}m @ ₦{$pricePerMeter}/m)";
            
            if (!$ledgerModel->recordOutflow($coilId, $stockEntryId, $meters, $description, 'sale', $saleId, $currentUser['id'])) {
                throw new Exception('Failed to record ledger entry.');
            }
        }
        
        // ✅ NEW: CHECK AND UPDATE COIL STATUS AUTOMATICALLY
        $stockEntryModel->checkAndUpdateCoilStatus($coilId);
        
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