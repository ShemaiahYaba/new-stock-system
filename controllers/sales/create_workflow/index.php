<?php
/**
 * Production Workflow Controller
 * File: controllers/sales/create_workflow/index.php
 *
 * Handles the 3-tab workflow submission:
 * 1. Creates Sale record
 * 2. Creates Production record (immutable)
 * 3. Creates Invoice record (immutable)
 * 4. Deducts stock meters
 * 5. Creates Stock Card entries
 * 6. Returns success response
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/sale.php';
require_once __DIR__ . '/../../../models/production.php';
require_once __DIR__ . '/../../../models/invoice.php';
require_once __DIR__ . '/../../../models/warehouse.php';
require_once __DIR__ . '/../../../models/customer.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

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
    // Parse JSON data from hidden inputs
    $productionData = json_decode($_POST['production_data'], true);
    $invoiceData = json_decode($_POST['invoice_data'], true);

    if (!$productionData || !$invoiceData) {
        throw new Exception('Invalid workflow data');
    }

    // Validate required fields
    if (
        !isset($productionData['customer_id']) ||
        !isset($productionData['warehouse_id']) ||
        !isset($productionData['coil_id']) ||
        !isset($productionData['production_paper'])
    ) {
        throw new Exception('Missing required production data');
    }

    // Start database transaction
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // ========================================
    // STEP 1: CREATE SALE RECORD
    // ========================================
    $saleModel = new Sale();
    $productionPaper = $productionData['production_paper'];

    $saleData = [
        'customer_id' => $productionData['customer_id'],
        'coil_id' => $productionData['coil_id'],
        'stock_entry_id' => null, // Will be set after stock entry creation
        'sale_type' => SALE_TYPE_RETAIL, // Production workflow is always retail
        'meters' => $productionPaper['summary']['totalMeters'],
        'price_per_meter' => calculateAveragePrice($productionPaper['properties']),
        'total_amount' => $productionPaper['summary']['totalAmount'],
        'status' => SALE_STATUS_COMPLETED,
        'created_by' => $currentUser['id'],
    ];

    $saleId = $saleModel->create($saleData);

    if (!$saleId) {
        throw new Exception('Failed to create sale record');
    }

    // ========================================
    // STEP 2: CREATE PRODUCTION RECORD (IMMUTABLE)
    // ========================================
    $productionModel = new Production();

    // Build production paper structure
    $productionPaperStructure = [
        'production_reference' =>
            'PR-' . date('Ymd') . '-' . str_pad($saleId, 4, '0', STR_PAD_LEFT),
        'sale_id' => $saleId,
        'warehouse_id' => $productionData['warehouse_id'],
        'customer' => $productionPaper['customer'],
        'warehouse' => $productionPaper['warehouse'],
        'coil_id' => $productionData['coil_id'],
        'coil' => $productionPaper['coil'],
        'properties' => array_map(function ($prop) {
            return [
                'property_id' => $prop['propertyType'],
                'label' => ucfirst($prop['propertyType']),
                'sheet_qty' => $prop['sheetQty'],
                'sheet_meter' => $prop['sheetMeter'],
                'meters' => $prop['meters'],
                'unit_price' => $prop['unitPrice'],
                'row_subtotal' => $prop['subtotal'],
            ];
        }, $productionPaper['properties']),
        'total_meters' => $productionPaper['summary']['totalMeters'],
        'total_amount' => $productionPaper['summary']['totalAmount'],
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $productionRecordData = [
        'sale_id' => $saleId,
        'warehouse_id' => $productionData['warehouse_id'],
        'production_paper' => $productionPaperStructure,
        'status' => PRODUCTION_STATUS_COMPLETED,
        'created_by' => $currentUser['id'],
    ];

    $productionId = $productionModel->create($productionRecordData);

    if (!$productionId) {
        throw new Exception('Failed to create production record');
    }

    // ========================================
    // STEP 3: CREATE INVOICE RECORD (IMMUTABLE)
    // ========================================
    $invoiceModel = new Invoice();

    // Build invoice shape structure
    $invoiceShape = [
        'company' => [
            'name' => COMPANY_NAME,
            'address' => COMPANY_ADDRESS,
            'phone' => COMPANY_PHONE,
            'email' => COMPANY_EMAIL,
        ],
        'customer' => $invoiceData['customer'],
        'meta' => [
            'date' => date('Y-m-d H:i:s'),
            'ref' => '#SO-' . date('Ymd') . '-' . str_pad($saleId, 6, '0', STR_PAD_LEFT),
            'payment_status' => 'Unpaid',
        ],
        'items' => $invoiceData['items'],
        'order_tax' => $invoiceData['tax'],
        'discount' => $invoiceData['discount'],
        'shipping' => $invoiceData['shipping'],
        'grand_total' => $invoiceData['grandTotal'],
        'paid' => 0.0,
        'due' => $invoiceData['grandTotal'],
        'notes' => [
            'receipt_statement' => 'Received the above goods in good condition.',
            'refund_policy' => 'No refund of money after payment',
            'custom_notes' => $invoiceData['notes'],
        ],
        'signatures' => [
            'customer' => null,
            'for_company' => COMPANY_NAME,
        ],
    ];

    $invoiceRecordData = [
        'sale_id' => $saleId,
        'production_id' => $productionId,
        'invoice_shape' => $invoiceShape,
        'total' => $invoiceData['grandTotal'],
        'tax' => $invoiceData['tax'],
        'shipping' => $invoiceData['shipping'],
        'paid_amount' => 0,
        'status' => INVOICE_STATUS_UNPAID,
    ];

    $invoiceId = $invoiceModel->create($invoiceRecordData);

    if (!$invoiceId) {
        throw new Exception('Failed to create invoice record');
    }

    // ========================================
    // STEP 4: DEDUCT STOCK METERS & CREATE STOCK CARD ENTRIES
    // ========================================
    $stockEntryModel = new StockEntry();
    $totalMeters = $productionPaper['summary']['totalMeters'];

    // Get coil's factory-use stock entries
    $stockEntries = $stockEntryModel->getByCoilAndStatus(
        $productionData['coil_id'],
        STOCK_STATUS_FACTORY_USE,
    );

    if (empty($stockEntries)) {
        throw new Exception('No factory-use stock entries available for this coil');
    }

    // Deduct meters from available stock entries (FIFO)
    $remainingToDeduct = $totalMeters;
    $usedStockEntries = [];

    foreach ($stockEntries as $entry) {
        if ($remainingToDeduct <= 0) {
            break;
        }

        $availableMeters = $entry['meters_remaining'];
        $deductAmount = min($remainingToDeduct, $availableMeters);

        // Update stock entry
        $newRemaining = $availableMeters - $deductAmount;
        $stockEntryModel->update($entry['id'], [
            'meters_remaining' => $newRemaining,
        ]);

        // Log stock card entry
        logStockCardEntry(
            $productionData['coil_id'],
            $productionId,
            $saleId,
            $deductAmount,
            $newRemaining,
            "Production drawdown for sale #$saleId",
            $currentUser['id'],
        );

        $usedStockEntries[] = $entry['id'];
        $remainingToDeduct -= $deductAmount;
    }

    if ($remainingToDeduct > 0) {
        throw new Exception(
            "Insufficient stock: Need $totalMeters meters, only " .
                ($totalMeters - $remainingToDeduct) .
                ' meters available',
        );
    }

    // Update sale with first stock entry ID
    $saleModel->update($saleId, [
        'stock_entry_id' => $usedStockEntries[0],
    ]);

    // ========================================
    // STEP 5: CHECK & UPDATE COIL STATUS
    // ========================================
    $stockEntryModel->checkAndUpdateCoilStatus($productionData['coil_id']);

    // ========================================
    // COMMIT TRANSACTION
    // ========================================
    $db->commit();

    // Log activity
    logActivity(
        'Production Workflow Completed',
        "Sale ID: $saleId, Production ID: $productionId, Invoice ID: $invoiceId, Customer: {$productionPaper['customer']['name']}",
    );

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'sale_id' => $saleId,
        'production_id' => $productionId,
        'invoice_id' => $invoiceId,
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    // Log error
    error_log('Production workflow error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());

    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}

exit();

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Calculate average price per meter from properties
 */
function calculateAveragePrice($properties)
{
    $totalMeters = 0;
    $totalAmount = 0;

    foreach ($properties as $prop) {
        $totalMeters += $prop['meters'];
        $totalAmount += $prop['subtotal'];
    }

    return $totalMeters > 0 ? $totalAmount / $totalMeters : 0;
}

/**
 * Log stock card entry
 */
function logStockCardEntry(
    $coilId,
    $productionId,
    $saleId,
    $metersChanged,
    $balanceMeters,
    $note,
    $createdBy,
) {
    try {
        $db = Database::getInstance()->getConnection();

        $sql = "INSERT INTO stock_card 
                (coil_id, production_id, sale_id, change_type, meters_changed, balance_meters, note, created_at) 
                VALUES 
                (:coil_id, :production_id, :sale_id, :change_type, :meters_changed, :balance_meters, :note, NOW())";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':coil_id' => $coilId,
            ':production_id' => $productionId,
            ':sale_id' => $saleId,
            ':change_type' => 'drawdown',
            ':meters_changed' => $metersChanged,
            ':balance_meters' => $balanceMeters,
            ':note' => $note,
        ]);

        return true;
    } catch (PDOException $e) {
        error_log('Stock card entry error: ' . $e->getMessage());
        throw new Exception('Failed to log stock card entry');
    }
}
