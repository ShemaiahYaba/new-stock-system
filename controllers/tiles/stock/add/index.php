<?php
/**
 * ============================================
 * FILE: controllers/tiles/stock/add/index.php
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/tile_product.php';
require_once __DIR__ . '/../../../../models/tile_stock_ledger.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_TILE_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=tile_stock_add');
        exit();
    }
    
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = floatval($_POST['quantity'] ?? 0);
    $transactionCode = sanitize($_POST['transaction_code'] ?? '');
    $description = sanitize($_POST['description'] ?? 'Stock addition');
    
    $errors = [];
    
    if ($productId <= 0) $errors[] = 'Please select a product.';
    if ($quantity <= 0) $errors[] = 'Quantity must be greater than 0.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=tile_stock_add');
        exit();
    }
    
    $productModel = new TileProduct();
    $ledgerModel = new TileStockLedger();
    
    // Verify product exists
    $product = $productModel->findById($productId);
    if (!$product) {
        setFlashMessage('error', 'Product not found.');
        header('Location: /new-stock-system/index.php?page=tile_stock_add');
        exit();
    }
    
    $currentUser = getCurrentUser();
    
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        // Record stock IN
        $ledgerId = $ledgerModel->recordStockIn(
            $productId,
            $quantity,
            $transactionCode,
            $description,
            $currentUser['id']
        );
        
        if (!$ledgerId) {
            throw new Exception('Failed to record stock ledger entry');
        }
        
        // Update product status to available
        $productModel->updateStatus($productId, 'available');
        
        $db->commit();
        
        logActivity('Tile stock added', "Product: {$product['code']}, Quantity: $quantity");
        setFlashMessage('success', "Successfully added $quantity pieces to {$product['code']}!");
        header('Location: /new-stock-system/index.php?page=tile_products');
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log('Tile stock addition error: ' . $e->getMessage());
        setFlashMessage('error', 'Failed to add stock.');
        header('Location: /new-stock-system/index.php?page=tile_stock_add');
    }
    
    exit();
}

header('Location: /new-stock-system/index.php?page=tile_stock_add');
exit();
