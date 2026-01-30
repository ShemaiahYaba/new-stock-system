<?php
/**
 * ============================================
 * ENHANCED: Tile Sale Creation with Add-Ons Support
 * File: controllers/tiles/sales/create/index.php
 * REPLACE existing file with this version
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
require_once __DIR__ . '/../../../../models/production_property.php'; // NEW
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
    $saleDateInput = sanitize($_POST['sale_date'] ?? '');
    $saleDate = !empty($saleDateInput) ? $saleDateInput : date('Y-m-d');
    
    // NEW: Parse add-on data
    $addonData = isset($_POST['addon_data']) ? json_decode($_POST['addon_data'], true) : [];
    $configData = isset($_POST['config_data']) ? json_decode($_POST['config_data'], true) : [];
    
    // Debug logging
    error_log('📥 Received addon_data: ' . $_POST['addon_data']);
    error_log('📋 Parsed addon data: ' . print_r($addonData, true));
    
    $errors = [];
    
    if ($customerId <= 0) $errors[] = 'Please select a customer.';
    if ($productId <= 0) $errors[] = 'Please select a product.';
    
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
    $propertyModel = new ProductionProperty(); // NEW
    
    // Verify customer and product exist
    $customer = $customerModel->findById($customerId);
    $product = $productModel->findById($productId);
    
    if (!$customer || !$product) {
        setFlashMessage('error', 'Invalid customer or product.');
        header('Location: /new-stock-system/index.php?page=tile_sales_create');
        exit();
    }
    
    $simpleSubtotal = $quantity * $unitPrice;
    $configSubtotal = 0;
    $configItems = [];

    if (!empty($configData)) {
        foreach ($configData as $configItem) {
            $configId = $configItem['config_id'] ?? null;
            $configQty = floatval($configItem['quantity'] ?? 0);
            $configUnitPrice = floatval($configItem['unit_price'] ?? 0);

            if (!$configId || $configQty <= 0 || $configUnitPrice <= 0) {
                continue;
            }

            $config = $propertyModel->findById($configId);
            if (!$config || $config['category'] !== 'tile' || (int)$config['is_addon'] !== 0) {
                continue;
            }

            $amount = $configQty * $configUnitPrice;
            $configSubtotal += $amount;

            $configItems[] = [
                'config_id' => $configId,
                'name' => $config['name'],
                'quantity' => $configQty,
                'unit_price' => $configUnitPrice,
                'amount' => $amount
            ];
        }
    }

    if ($quantity <= 0) $errors[] = 'Quantity must be greater than 0.';
    if ($unitPrice <= 0) $errors[] = 'Unit price must be greater than 0.';

    $productSubtotal = $simpleSubtotal + $configSubtotal;

    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
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
    
    // ========================================
    // NEW: Process Add-Ons
    // ========================================
    $addonItems = [];
    $totalAddonCharges = 0;
    $totalAdjustments = 0;
    
    if (!empty($addonData)) {
        foreach ($addonData as $addonItem) {
            $addonId = $addonItem['addon_id'] ?? null;
            $customAmount = $addonItem['customAmount'] ?? null;
            
            if (!$addonId) continue;
            
            $addon = $propertyModel->findById($addonId);
            if (!$addon || $addon['category'] !== 'tile') continue;
            
            // Calculate add-on amount
            $amount = $propertyModel->calculateAddonAmount(
                $addon, 
                $productSubtotal, 
                $customAmount
            );
            
            // Apply refund logic (make negative)
            if ($addon['is_refundable'] == 1 && $amount > 0) {
                $amount = -$amount;
            }
            
            // Track totals
            if ($addon['is_refundable'] || $amount < 0) {
                $totalAdjustments += $amount;
            } else {
                $totalAddonCharges += $amount;
            }
            
            // Store add-on for invoice
            $addonItems[] = [
                'addon_id' => $addonId,
                'code' => $addon['code'],
                'name' => $addon['name'],
                'amount' => $amount,
                'calculation_method' => $addon['calculation_method'],
                'display_section' => $addon['display_section']
            ];
        }
    }
    
    // Calculate grand total
    $grandTotal = $productSubtotal + $totalAddonCharges + $totalAdjustments;
    
    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        // 1. Create tile sale
    $data = [
        'customer_id' => $customerId,
        'tile_product_id' => $productId,
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'total_amount' => $grandTotal, // Updated to include add-ons
        'status' => 'completed',
        'notes' => $notes,
        'created_by' => $currentUser['id'],
        'created_at' => $saleDate . ' 00:00:00'
    ];
        
        $saleId = $saleModel->create($data);
        
        if (!$saleId) {
            throw new Exception('Failed to create sale record');
        }
        
        // 2. Build invoice items array
        $invoiceItems = [[
            'product_code' => $product['code'],
            'description' => "Roofing Tile - {$product['design_name']}",
            'details' => "Color: {$product['color_name']}\nGauge: " . ucfirst($product['gauge']) . "\nDesign: {$product['design_code']}",
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $simpleSubtotal
        ]];

        if (!empty($configItems)) {
            foreach ($configItems as $config) {
                $invoiceItems[] = [
                    'product_code' => $product['code'],
                    'description' => "{$config['name']} - {$product['design_name']}",
                    'details' => "Color: {$product['color_name']}\nGauge: " . ucfirst($product['gauge']) . "\nDesign: {$product['design_code']}",
                    'quantity' => $config['quantity'],
                    'unit_price' => $config['unit_price'],
                    'subtotal' => $config['amount']
                ];
            }
        }
        
        // 3. Add add-on items to invoice
        foreach ($addonItems as $addon) {
            $invoiceItems[] = [
                'description' => $addon['name'],
                'details' => $addon['calculation_method'] === 'percentage' 
                    ? "Calculated as percentage" 
                    : "Add-on charge",
                'quantity' => 1,
                'unit_price' => $addon['amount'],
                'subtotal' => $addon['amount'],
                'is_addon' => true
            ];
        }
        
        // 4. Build invoice shape with add-ons
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
            'items' => $invoiceItems,
            'breakdown' => [
                'product_subtotal' => $productSubtotal,
                'addon_charges' => $totalAddonCharges,
                'adjustments' => $totalAdjustments,
            ],
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
                ],
                'addons' => $addonItems,
                'configurations' => $configItems
            ]
        ];
        
        // 5. Create invoice with polymorphic reference
        $invoiceId = $invoiceModel->create([
            'sale_type' => 'tile_sale',
            'sale_reference_id' => $saleId,
            'invoice_shape' => $invoiceShape,
            'total' => $grandTotal,
            'tax' => 0,
            'shipping' => 0,
            'paid_amount' => 0,
            'status' => INVOICE_STATUS_UNPAID
        ]);
        
        if (!$invoiceId) {
            throw new Exception('Failed to create invoice');
        }
        
        $db->commit();
        
        logActivity(
            'Tile sale created with add-ons', 
            "Sale ID: $saleId, Invoice ID: $invoiceId, Product: {$product['code']}, " .
            "Customer: {$customer['name']}, Quantity: $quantity, Add-ons: " . count($addonItems)
        );
        
        setFlashMessage('success', 'Sale created successfully with add-ons! Invoice generated.');
        
        // Redirect to invoice view
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
