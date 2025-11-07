<?php
/**
 * Available Stock Sale Controller
 * File: controllers/sales/create_available_stock.php
 *
 * Handles direct sales of available stock entries
 */

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/coil.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';

// Require permission
requirePermission(MODULE_SALES_MANAGEMENT, ACTION_CREATE);

// Set JSON response header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

// Get current user
$currentUser = getCurrentUser();

try {
    // Parse JSON data from form
    $saleData = json_decode($_POST['sale_data'], true);
    $invoiceData = json_decode($_POST['invoice_data'], true);

    if (!$saleData || !$invoiceData) {
        throw new Exception('Invalid sale data');
    }

    // Validate required fields
    $requiredFields = [
        'customer_id',
        'coil_id',
        'stock_entry_id',
        'meters',
        'price_per_meter',
        'total_amount',
    ];
    foreach ($requiredFields as $field) {
        if (!isset($saleData[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Start database transaction
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // ========================================
    // STEP 1: VALIDATE STOCK ENTRY
    // ========================================
    $stockEntryModel = new StockEntry();
    $stockEntry = $stockEntryModel->findById($saleData['stock_entry_id']);

    if (!$stockEntry) {
        throw new Exception('Stock entry not found');
    }

    // Verify stock entry is available and has sufficient meters
    if ($stockEntry['status'] !== 'available') {
        throw new Exception('Selected stock is not available for direct sale');
    }

    if ($stockEntry['meters_remaining'] < $saleData['meters']) {
        throw new Exception('Insufficient stock available');
    }

    // ========================================
    // STEP 2: CREATE SALE RECORD
    // ========================================
    $saleModel = new Sale();

    $saleRecord = [
        'customer_id' => $saleData['customer_id'],
        'coil_id' => $saleData['coil_id'],
        'stock_entry_id' => $saleData['stock_entry_id'],
        'sale_type' => SALE_TYPE_RETAIL,
        'meters' => $saleData['meters'],
        'price_per_meter' => $saleData['price_per_meter'],
        'total_amount' => $saleData['total_amount'],
        'status' => SALE_STATUS_COMPLETED,
        'created_by' => $currentUser['id'],
    ];

    $saleId = $saleModel->create($saleRecord);

    if (!$saleId) {
        throw new Exception('Failed to create sale record');
    }

    // ========================================
    // STEP 3: UPDATE STOCK ENTRY
    // ========================================
    $newRemaining = $stockEntry['meters_remaining'] - $saleData['meters'];
    $updated = $stockEntryModel->update($stockEntry['id'], [
        'meters_remaining' => $newRemaining,
    ]);

    if (!$updated) {
        throw new Exception('Failed to update stock entry');
    }

    // ========================================
    // STEP 4: CREATE INVOICE
    // ========================================
    $invoiceModel = new Invoice();

    // Build invoice shape
    $invoiceShape = [
        'company' => [
            'name' => INVOICE_COMPANY_NAME,
            'address' => INVOICE_COMPANY_ADDRESS,
            'phone' => INVOICE_COMPANY_PHONE,
            'email' => INVOICE_COMPANY_EMAIL,
        ],
        'customer' => [
            'name' => $saleData['customer_name'] ?? 'Walk-in Customer',
            'email' => $saleData['customer_email'] ?? '',
            'phone' => $saleData['customer_phone'] ?? '',
            'address' => $saleData['customer_address'] ?? '',
        ],
        'meta' => [
            'date' => date('Y-m-d H:i:s'),
            'ref' => '#SO-' . date('Ymd') . '-' . str_pad($saleId, 6, '0', STR_PAD_LEFT),
            'payment_status' => 'Unpaid',
        ],
        'items' => [
            [
                'name' => $saleData['coil_name'] ?? 'Aluminum Coil',
                'qty' => 1,
                'price' => $saleData['total_amount'],
                'total' => $saleData['total_amount'],
                'details' => [
                    'Meters' => number_format($saleData['meters'], 2) . 'm',
                    'Price/m' => '₦' . number_format($saleData['price_per_meter'], 2),
                ],
            ],
        ],
        'order_tax' => 0,
        'discount' => 0,
        'shipping' => 0,
        'grand_total' => $saleData['total_amount'],
        'paid' => 0,
        'due' => $saleData['total_amount'],
        'notes' => [
            'receipt_statement' => 'Received the above goods in good condition.',
            'refund_policy' => 'No refund of money after payment',
        ],
        'signatures' => [
            'customer' => null,
            'for_company' => INVOICE_COMPANY_NAME,
        ],
    ];

    $invoiceRecord = [
        'sale_id' => $saleId,
        'invoice_shape' => $invoiceShape,
        'total' => $saleData['total_amount'],
        'tax' => 0,
        'shipping' => 0,
        'paid_amount' => 0,
        'status' => INVOICE_STATUS_UNPAID,
    ];

    $invoiceId = $invoiceModel->create($invoiceRecord);

    if (!$invoiceId) {
        throw new Exception('Failed to create invoice record');
    }

    // ========================================
    // STEP 5: LOG STOCK LEDGER ENTRY
    // ========================================
    $this->logStockCardEntry(
        $saleData['coil_id'],
        $saleData['stock_entry_id'],
        $saleId,
        $saleData['meters'],
        $newRemaining,
        "Direct sale #$saleId",
        $currentUser['id'],
    );

    // ========================================
    // STEP 6: CHECK & UPDATE COIL STATUS
    // ========================================
    $stockEntryModel->checkAndUpdateCoilStatus($saleData['coil_id']);

    // ========================================
    // COMMIT TRANSACTION
    // ========================================
    $db->commit();

    // Log activity
    logActivity(
        'Direct Sale Completed',
        "Sale ID: $saleId, Invoice ID: $invoiceId, Customer: " .
            ($saleData['customer_name'] ?? 'Unknown'),
    );

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Sale completed successfully',
        'sale_id' => $saleId,
        'invoice_id' => $invoiceId,
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    // Log error
    error_log('Available stock sale error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());

    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}

exit();

/**
 * Log stock card entry
 */
function logStockCardEntry(
    $coilId,
    $stockEntryId,
    $saleId,
    $metersChanged,
    $balanceMeters,
    $note,
    $createdBy,
) {
    try {
        $db = Database::getInstance()->getConnection();

        $sql = "INSERT INTO stock_ledger 
                (coil_id, stock_entry_id, transaction_type, description, 
                 inflow_meters, outflow_meters, balance_meters, 
                 reference_type, reference_id, created_by, created_at) 
                VALUES 
                (:coil_id, :stock_entry_id, :transaction_type, :description,
                 :inflow_meters, :outflow_meters, :balance_meters,
                 :reference_type, :reference_id, :created_by, NOW())";

        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':coil_id' => $coilId,
            ':stock_entry_id' => $stockEntryId,
            ':transaction_type' => 'outflow',
            ':description' => $note,
            ':inflow_meters' => 0,
            ':outflow_meters' => $metersChanged,
            ':balance_meters' => $balanceMeters,
            ':reference_type' => 'sale',
            ':reference_id' => $saleId,
            ':created_by' => $createdBy,
        ]);

        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            error_log('Failed to log stock ledger entry: ' . json_encode($errorInfo));
            throw new Exception('Failed to log stock ledger entry');
        }

        return true;
    } catch (PDOException $e) {
        error_log('Stock ledger entry error: ' . $e->getMessage());
        throw new Exception('Failed to log stock ledger entry: ' . $e->getMessage());
    }
}
