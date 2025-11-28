<?php
/**
 * ============================================
 * FILE: controllers/tiles/sales/create/index.php
 * UPDATED: Now generates invoices automatically
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/tile_sale.php';
require_once __DIR__ . '/../../../../models/tile_product.php';
require_once __DIR__ . '/../../../../models/tile_stock_ledger.php';
require_once __DIR__ . '/../../../../models/customer.php';
require_once __DIR__ . '/../../../../models/invoice.php';
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
    $invoiceModel = new Invoice();
    
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
    
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        // 1. Create tile sale
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
        
        if (!$saleId) {
            throw new Exception('Failed to create sale record');
        }
        
        // 2. Build invoice shape with tile-specific details
        $invoiceShape = [
            'company' => [
                'name' => INVOICE_COMPANY_NAME,
                'address' => INVOICE_COMPANY_ADDRESS,
                'phone' => INVOICE_COMPANY_PHONE,
                'email' => INVOICE_COMPANY_EMAIL
            ],
            'customer' => [
                'name' => $customer['name'],
                'phone' => $customer['phone'],
                'address' => $customer['address'] ?? '',
                'email' => $customer['email'] ?? ''
            ],
            'items' => [[
                'product_code' => $product['code'],
                'description' => "Roofing Tile - {$product['design_name']}",
                'details' => "Color: {$product['color_name']}\nGauge: " . ucfirst($product['gauge']) . "\nDesign: {$product['design_code']}",
                'quantity' => $quantity,
                'unit_price' => $unitPrice
            ]],
            'tax' => 0,
            'shipping' => 0,
            'discount' => 0,
            'notes' => [
                'receipt_statement' => $notes,
                'refund_policy' => 'All sales are final. No refunds or exchanges.',
                'additional_notes' => 'Thank you for your business!'
            ],
            'meta' => [
                'ref' => "TILE-SALE-{$saleId}",
                'type' => 'tile_sale',
                'sale_id' => $saleId,
                'product_details' => [
                    'design' => $product['design_name'],
                    'color' => $product['color_name'],
                    'gauge' => $product['gauge']
                ]
            ]
        ];
        
        // 3. Create invoice with polymorphic reference
        $invoiceId = $invoiceModel->create([
            'sale_type' => 'tile_sale',
            'sale_reference_id' => $saleId,
            'invoice_shape' => $invoiceShape,
            'total' => $totalAmount,
            'tax' => 0,
            'shipping' => 0,
            'paid_amount' => 0,
            'status' => INVOICE_STATUS_UNPAID
        ]);
        
        if (!$invoiceId) {
            throw new Exception('Failed to create invoice');
        }
        
        $db->commit();
        
        logActivity('Tile sale created with invoice', "Sale ID: $saleId, Invoice ID: $invoiceId, Product: {$product['code']}, Customer: {$customer['name']}, Quantity: $quantity");
        setFlashMessage('success', 'Sale created successfully! Invoice generated.');
        
        // Redirect to invoice view directly (as per requirement)
        header("Location: /new-stock-system/index.php?page=invoice_view&id=$invoiceId");
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log('Tile sale creation error: ' . $e->getMessage());
        setFlashMessage('error', 'Failed to create sale: ' . $e->getMessage());
        header('Location: /new-stock-system/index.php?page=tile_sales_create');
    }
    
    exit();
}

header('Location: /new-stock-system/index.php?page=tile_sales_create');
exit();