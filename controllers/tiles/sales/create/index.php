<?php
/**
 * ============================================
 * FILE: controllers/tiles/sales/create/index.php
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/tile_sale.php';
require_once __DIR__ . '/../../../../models/tile_product.php';
require_once __DIR__ . '/../../../../models/tile_stock_ledger.php';
require_once __DIR__ . '/../../../../models/customer.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_TILE_SALES, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=tile_sales_create');
        exit();
    }
    
    $customerId = (int)($_POST['customer_id'] ?? 0);
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = floatval($_POST['quantity'] ?? 0);
    $unitPrice = floatval($_POST['unit_price'] ?? 0);
    $notes = sanitize($_POST['notes'] ?? '');
    
    $errors = [];
    
    if ($customerId <= 0) $errors[] = 'Please select a customer.';
    if ($productId <= 0) $errors[] = 'Please select a product.';
    if ($quantity <= 0) $errors[] = 'Quantity must be greater than 0.';
    if ($unitPrice <= 0) $errors[] = 'Unit price must be greater than 0.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=tile_sales_create');
        exit();
    }
    
    $saleModel = new TileSale();
    $productModel = new TileProduct();
    $ledgerModel = new TileStockLedger();
    $customerModel = new Customer();
    
    // Verify customer and product exist
    $customer = $customerModel->findById($customerId);
    $product = $productModel->findById($productId);
    
    if (!$customer || !$product) {
        setFlashMessage('error', 'Invalid customer or product.');
        header('Location: /new-stock-system/index.php?page=tile_sales_create');
        exit();
    }
    
    // Check stock availability
    if (!$ledgerModel->canDeductStock($productId, $quantity)) {
        $available = $ledgerModel->getCurrentBalance($productId);
        setFlashMessage('error', "Insufficient stock. Available: $available pieces, Requested: $quantity pieces.");
        header('Location: /new-stock-system/index.php?page=tile_sales_create');
        exit();
    }
    
    $currentUser = getCurrentUser();
    $totalAmount = $quantity * $unitPrice;
    
    $data = [
        'customer_id' => $customerId,
        'tile_product_id' => $productId,
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'total_amount' => $totalAmount,
        'status' => 'completed',
        'notes' => $notes,
        'created_by' => $currentUser['id']
    ];
    
    $saleId = $saleModel->create($data);
    
    if ($saleId) {
        logActivity('Tile sale created', "Product: {$product['code']}, Customer: {$customer['name']}, Quantity: $quantity");
        setFlashMessage('success', 'Sale created successfully!');
        header("Location: /new-stock-system/index.php?page=tile_sales_view&id=$saleId");
    } else {
        setFlashMessage('error', 'Failed to create sale. Please check stock availability.');
        header('Location: /new-stock-system/index.php?page=tile_sales_create');
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=tile_sales_create');
exit();