<?php
/**
 * Handle creation of sales from available stock
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../models/production.php';
require_once __DIR__ . '/../../utils/helpers.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method');
    redirect('/new-stock-system/index.php?page=sales');
}

// Check permissions
if (!hasPermission(MODULE_SALES, ACTION_CREATE)) {
    setFlashMessage('error', 'You do not have permission to create sales');
    redirect('/new-stock-system/index.php?page=sales');
}

// Validate required fields
$required = ['customer_id', 'sale_date'];
$missing = [];
$data = [];

foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $missing[] = $field;
    } else {
        $data[$field] = trim($_POST[$field]);
    }
}

// Check for missing required fields
if (!empty($missing)) {
    setFlashMessage('error', 'Please fill in all required fields');
    $_SESSION['form_data'] = $_POST;
    redirect('/new-stock-system/index.php?page=sales_create_available');
}

// Validate stock items
if (empty($_POST['unit_price']) || !is_array($_POST['unit_price'])) {
    setFlashMessage('error', 'Please add at least one item to the sale');
    $_SESSION['form_data'] = $_POST;
    redirect('/new-stock-system/index.php?page=sales_create_available');
}

// Initialize models
$saleModel = new Sale();
$stockEntryModel = new StockEntry();
$customerModel = new Customer();
$invoiceModel = new Invoice();
$db = Database::getInstance()->getConnection();

// Start transaction
$transactionStarted = false;

try {
    $db->beginTransaction();
    $transactionStarted = true;

    // Get customer details first
    $customer = $customerModel->findById($data['customer_id']);

    if (!$customer) {
        throw new Exception('Customer not found');
    }

    $totalAmount = 0;
    $saleItems = [];
    $stockEntries = [];
    $firstSaleId = null;

    // Process each stock item first to calculate total amount
    foreach ($_POST['unit_price'] as $stockEntryId => $unitPrice) {
        $stockEntry = $stockEntryModel->findById($stockEntryId);

        if (!$stockEntry || $stockEntry['status'] !== STOCK_STATUS_AVAILABLE) {
            throw new Exception('Invalid or unavailable stock entry: ' . $stockEntryId);
        }

        // Get quantity from POST or use remaining meters
        $quantity = isset($_POST['quantity'][$stockEntryId])
            ? floatval($_POST['quantity'][$stockEntryId])
            : $stockEntry['meters_remaining'];

        // Validate unit price
        $unitPrice = floatval($unitPrice);
        if ($unitPrice <= 0) {
            throw new Exception('Invalid unit price for stock entry: ' . $stockEntryId);
        }

        // Calculate line total
        $lineTotal = $quantity * $unitPrice;
        $totalAmount += $lineTotal;

        // Add to sale items
        $saleItems[] = [
            'coil_id' => $stockEntry['coil_id'],
            'stock_entry_id' => $stockEntryId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $lineTotal,
            'stock_entry' => $stockEntry,
        ];
    }

    // Create sale record for each item
    foreach ($saleItems as $item) {
        $saleData = [
            'customer_id' => $data['customer_id'],
            'coil_id' => $item['coil_id'],
            'stock_entry_id' => $item['stock_entry_id'],
            'sale_type' => 'available_stock',
            'meters' => $item['quantity'],
            'price_per_meter' => $item['unit_price'],
            'total_amount' => $item['total'],
            'status' => SALE_STATUS_COMPLETED,
            'created_by' => $_SESSION['user_id'],
            'notes' => $_POST['notes'] ?? null,
        ];

        $saleId = $saleModel->create($saleData);

        if (!$saleId) {
            throw new Exception('Failed to create sale record');
        }

        // Store first sale ID for reference
        if ($firstSaleId === null) {
            $firstSaleId = $saleId;
        }

        // Mark stock entry as used
        $stockEntries[] = [
            'id' => $item['stock_entry_id'],
            'status' => STOCK_STATUS_SOLD,
            'meters_used' => $item['quantity'],
        ];
    }

    // Update stock entries
    foreach ($stockEntries as $entry) {
        $updated = $stockEntryModel->updateStatusAndUsage(
            $entry['id'],
            $entry['status'],
            $entry['meters_used'],
        );

        if (!$updated) {
            throw new Exception('Failed to update stock entry: ' . $entry['id']);
        }
    }

    // Prepare invoice items
    $invoiceItems = [];
    foreach ($saleItems as $item) {
        $invoiceItems[] = [
            'description' =>
                $item['stock_entry']['coil_code'] . ' - ' . $item['stock_entry']['coil_name'],
            'quantity' => $item['quantity'],
            'qty_text' => number_format($item['quantity'], 2) . ' meters', // ← ADDED THIS
            'unit_price' => $item['unit_price'],
            'subtotal' => $item['total'], // FIXED: Was 'total_price'
        ];
    }

    // Get tax rate from POST or default to 0
    $taxRate = isset($_POST['tax_rate']) ? floatval($_POST['tax_rate']) : 0;
    $tax = ($totalAmount * $taxRate) / 100;
    $grandTotal = $totalAmount + $tax;

    // Create invoice shape
    $invoiceShape = [
        'company' => [
            'name' => INVOICE_COMPANY_NAME,
            'address' => INVOICE_COMPANY_ADDRESS,
            'phone' => INVOICE_COMPANY_PHONE,
            'email' => INVOICE_COMPANY_EMAIL,
        ],
        'customer' => [
            'name' => $customer['name'],
            'company' => $customer['company'] ?? '',
            'email' => $customer['email'] ?? '',
            'phone' => $customer['phone'] ?? '',
            'address' => $customer['address'] ?? '',
        ],
        'meta' => [
            'date' => date('Y-m-d H:i:s'),
            'ref' => '#SO-' . date('Ymd') . '-' . str_pad($firstSaleId, 6, '0', STR_PAD_LEFT),
            'sale_id' => $firstSaleId,
            'payment_status' => 'Unpaid',
        ],
        'items' => $invoiceItems,
        'subtotal' => $totalAmount,
        'order_tax' => $tax,
        'discount' => 0,
        'shipping' => 0,
        'grand_total' => $grandTotal,
        'paid' => 0,
        'due' => $grandTotal,
        'notes' => [
            'receipt_statement' => 'Received the above goods in good condition.',
            'refund_policy' => 'No refund of money after payment',
            'custom_notes' => $_POST['notes'] ?? '',
        ],
        'signatures' => [
            'customer' => null,
            'for_company' => INVOICE_COMPANY_NAME,
        ],
    ];

    // Create invoice record
    $invoiceData = [
        'sale_id' => $firstSaleId,
        'invoice_shape' => json_encode($invoiceShape),
        'total' => $grandTotal,
        'tax' => $tax,
        'shipping' => 0,
        'paid_amount' => 0,
        'status' => INVOICE_STATUS_UNPAID,
    ];

    $invoiceId = $invoiceModel->create($invoiceData);

    if (!$invoiceId) {
        throw new Exception('Failed to create invoice record');
    }

    // Commit transaction
    $db->commit();
    $transactionStarted = false;

    // Log activity
    logActivity(
        'Sale from Available Stock Created - Sale #' . $firstSaleId . ' for ' . $customer['name'],
    );

    // Redirect to sales view
    setFlashMessage('success', 'Sale and invoice created successfully');
    redirect("/new-stock-system/index.php?page=sales_view&id=$firstSaleId");
} catch (Exception $e) {
    // Rollback transaction on error - only if transaction was started
    if ($transactionStarted && $db->inTransaction()) {
        $db->rollBack();
    }

    // Log error
    error_log('Error in create_available_workflow: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());

    // Set error message and redirect back
    setFlashMessage('error', 'Failed to create sale: ' . $e->getMessage());
    $_SESSION['form_data'] = $_POST;
    redirect('/new-stock-system/index.php?page=sales_create_available');
}
