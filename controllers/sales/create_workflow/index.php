<?php
/**
 * Production Workflow Controller - UPDATED FOR ALUMINIUM
 * File: controllers/sales/create_workflow/index.php
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/sale.php';
require_once __DIR__ . '/../../../models/production.php';
require_once __DIR__ . '/../../../models/invoice.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_SALES_MANAGEMENT, ACTION_CREATE);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

$currentUser = getCurrentUser();

try {
    $productionData = json_decode($_POST['production_data'], true);
    $invoiceData = json_decode($_POST['invoice_data'], true);

    if (!$productionData || !$invoiceData) {
        throw new Exception('Invalid workflow data');
    }

    if (
        !isset($productionData['customer_id']) ||
        !isset($productionData['warehouse_id']) ||
        !isset($productionData['coil_id']) ||
        !isset($productionData['production_paper'])
    ) {
        throw new Exception('Missing required production data');
    }

    // ✅ UPDATED: Check if this is a KZINC coil (aluminium and alusteel use stock-based workflow)
    $coilModel = new Coil();
    $coil = $coilModel->findById($productionData['coil_id']);
    $coilCategory = strtolower($coil['category']);
    $isKzinc = $coilCategory === 'kzinc';

    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    $productionPaper = $productionData['production_paper'];
    $primaryStockEntryId = null; // ✅ Initialize to track the stock entry

    // ========================================
    // STEP 0: PRE-VALIDATE & GET STOCK ENTRY FOR STOCK-BASED CATEGORIES (ALUSTEEL & ALUMINIUM)
    // ========================================
    if (!$isKzinc) {
        $stockEntryModel = new StockEntry();
        $totalMeters = $productionPaper['summary']['totalMeters'];

        // Get available stock entries
        $stockEntries = $stockEntryModel->getByCoilAndStatus(
            $productionData['coil_id'],
            STOCK_STATUS_FACTORY_USE,
        );

        if (empty($stockEntries)) {
            throw new Exception('No factory-use stock entries available for this coil');
        }

        // Pre-calculate and validate stock availability
        $remainingToDeduct = $totalMeters;
        $plannedDeductions = [];

        foreach ($stockEntries as $entry) {
            if ($remainingToDeduct <= 0) {
                break;
            }

            $availableMeters = $entry['meters_remaining'];
            $deductAmount = min($remainingToDeduct, $availableMeters);

            $plannedDeductions[] = [
                'entry_id' => $entry['id'],
                'deduct_amount' => $deductAmount,
                'new_remaining' => $availableMeters - $deductAmount
            ];

            $remainingToDeduct -= $deductAmount;
        }

        // Validate sufficient stock BEFORE creating sale
        if ($remainingToDeduct > 0) {
            throw new Exception(
                "Insufficient stock: Need $totalMeters meters, only " .
                    ($totalMeters - $remainingToDeduct) .
                    ' meters available',
            );
        }

        // ✅ Set the primary stock entry (the first one we'll use)
        $primaryStockEntryId = $plannedDeductions[0]['entry_id'];
        
        error_log("Stock-based sale ($coilCategory): Will use stock_entry_id = $primaryStockEntryId");
    } else {
        error_log("KZINC sale: No stock entry required");
    }

    // ========================================
    // STEP 1: CREATE SALE RECORD
    // ========================================
    $saleModel = new Sale();

    $saleData = [
        'customer_id' => $productionData['customer_id'],
        'coil_id' => $productionData['coil_id'],
        'stock_entry_id' => $primaryStockEntryId, // ✅ NOW CORRECTLY SET!
        'sale_type' => SALE_TYPE_RETAIL,
        'meters' => $productionPaper['summary']['totalMeters'],
        'price_per_meter' => $isKzinc ? 0 : calculateAveragePrice($productionPaper['properties']),
        'total_amount' => $productionPaper['summary']['totalAmount'],
        'status' => SALE_STATUS_COMPLETED,
        'created_by' => $currentUser['id'],
    ];

    $saleId = $saleModel->create($saleData);

    if (!$saleId) {
        throw new Exception('Failed to create sale record');
    }

    error_log("Sale #$saleId created with stock_entry_id = " . ($primaryStockEntryId ?? 'NULL'));

    // ========================================
    // STEP 2: CREATE PRODUCTION RECORD (IMMUTABLE)
    // ========================================
    $productionModel = new Production();

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
                'sheet_qty' => $prop['sheetQty'] ?? $prop['quantity'],
                'sheet_meter' => $prop['sheetMeter'] ?? 0,
                'meters' => $prop['meters'] ?? 0,
                'quantity' => $prop['quantity'] ?? 0,
                'pieces' => $prop['pieces'] ?? 0,
                'unit_price' => $prop['unitPrice'],
                'row_subtotal' => $prop['subtotal'],
            ];
        }, $productionPaper['properties']),
        // NEW: Add add-ons to production paper
        'addons' => isset($productionPaper['addons']) ? array_map(function($addon) {
            return [
                'addon_id' => $addon['addon_id'] ?? null,
                'code' => $addon['code'] ?? '',
                'name' => $addon['name'] ?? '',
                'amount' => $addon['amount'] ?? 0,
                'calculation_method' => $addon['calculation_method'] ?? 'fixed',
                'display_section' => $addon['display_section'] ?? 'addon'
            ];
        }, $productionPaper['addons']) : [],
        'total_meters' => $productionPaper['summary']['totalMeters'],
        'total_amount' => $productionPaper['summary']['totalAmount'],
        // NEW: Add-on summary
        'addon_summary' => [
            'total_charges' => $productionPaper['addonSummary']['totalCharges'] ?? 0,
            'total_adjustments' => $productionPaper['addonSummary']['totalAdjustments'] ?? 0
        ],
        // NEW: Grand total
        'grand_total' => $productionPaper['grandTotal'] ?? $productionPaper['summary']['totalAmount'],
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

    // ============================================================
    // STEP 3: CREATE INVOICE RECORD - ENHANCED WITH ADD-ONS
    // ============================================================
    
    $invoiceModel = new Invoice();

    // Merge production items with add-ons for invoice
    $allInvoiceItems = array_merge(
        $invoiceData['items'], // Production items
        $invoiceData['addon_items'] ?? [] // Add-on items
    );
    
    // Calculate totals
    $productionSubtotal = array_reduce($invoiceData['items'], function($sum, $item) {
        return $sum + ($item['subtotal'] ?? 0);
    }, 0);
    
    $addonTotal = array_reduce($invoiceData['addon_items'] ?? [], function($sum, $item) {
        return $sum + ($item['subtotal'] ?? 0);
    }, 0);
    
    $invoiceSubtotal = $productionSubtotal + $addonTotal;

    $invoiceShape = [
        'company' => [
            'name' => INVOICE_COMPANY_NAME,
            'address' => INVOICE_COMPANY_ADDRESS,
            'phone' => INVOICE_COMPANY_PHONE,
            'email' => INVOICE_COMPANY_EMAIL,
        ],
        'customer' => $invoiceData['customer'],
        'meta' => [
            'date' => date('Y-m-d H:i:s'),
            'ref' => '#SO-' . date('Ymd') . '-' . str_pad($saleId, 6, '0', STR_PAD_LEFT),
            'payment_status' => 'Unpaid',
        ],
        'items' => $allInvoiceItems, // Combined items
        // NEW: Breakdown
        'breakdown' => [
            'production_subtotal' => $productionSubtotal,
            'addon_charges' => $invoiceData['addon_summary']['total_charges'] ?? 0,
            'adjustments' => $invoiceData['addon_summary']['total_adjustments'] ?? 0,
        ],
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
            'for_company' => INVOICE_COMPANY_NAME,
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
    // STEP 4: DEDUCT STOCK METERS (STOCK-BASED CATEGORIES ONLY: ALUSTEEL & ALUMINIUM)
    // ========================================
    if (!$isKzinc) {
        // ✅ Use the pre-calculated deductions
        $stockEntryModel = new StockEntry();

        foreach ($plannedDeductions as $deduction) {
            $entryId = $deduction['entry_id'];
            $deductAmount = $deduction['deduct_amount'];
            $newRemaining = $deduction['new_remaining'];

            // Update stock entry
            $updateResult = $stockEntryModel->update($entryId, [
                'meters_remaining' => $newRemaining,
            ]);

            if (!$updateResult) {
                throw new Exception("Failed to update stock entry #$entryId");
            }

            // Log to stock ledger
            logStockCardEntry(
                $productionData['coil_id'],
                $productionId,
                $saleId,
                $deductAmount,
                $newRemaining,
                "Production drawdown for sale #$saleId",
                $currentUser['id'],
                $entryId,
            );

            error_log("Deducted $deductAmount meters from stock_entry #$entryId, new remaining: $newRemaining");
        }

        // Update coil status if needed
        $stockEntryModel->checkAndUpdateCoilStatus($productionData['coil_id']);
    } else {
        error_log("KZINC sale #$saleId: Skipping meter deduction and ledger entries");
    }

    $db->commit();

    logActivity(
        'Production Workflow Completed',
        "Sale ID: $saleId, Production ID: $productionId, Invoice ID: $invoiceId, Customer: {$productionPaper['customer']['name']}, Type: $coilCategory",
    );

    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'sale_id' => $saleId,
        'production_id' => $productionId,
        'invoice_id' => $invoiceId,
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    error_log('Production workflow error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}

exit();

// ========================================
// HELPER FUNCTIONS
// ========================================

function calculateAveragePrice($properties)
{
    $totalMeters = 0;
    $totalAmount = 0;

    foreach ($properties as $prop) {
        $totalMeters += $prop['meters'] ?? 0;
        $totalAmount += $prop['subtotal'];
    }

    return $totalMeters > 0 ? $totalAmount / $totalMeters : 0;
}

function logStockCardEntry(
    $coilId,
    $productionId,
    $saleId,
    $metersChanged,
    $balanceMeters,
    $note,
    $createdBy,
    $stockEntryId = null,
) {
    try {
        $db = Database::getInstance()->getConnection();

        $transactionType = 'outflow';
        $outflowMeters = $metersChanged;
        $inflowMeters = 0.0;

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
            ':transaction_type' => $transactionType,
            ':description' => $note ?: 'Stock drawdown for production',
            ':inflow_meters' => $inflowMeters,
            ':outflow_meters' => $outflowMeters,
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