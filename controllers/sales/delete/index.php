<?php
/**
 * Delete Sale Controller - LEDGER-BASED VERSION
 * File: controllers/sales/delete/index.php
 * Handles cascading deletion of sales records with proper ledger restoration
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/sale.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/stock_ledger.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method');
    redirect('/new-stock-system/index.php?page=sales');
}

// Check permissions
if (!hasPermission(MODULE_SALES, ACTION_DELETE)) {
    setFlashMessage('error', 'You do not have permission to delete sales');
    redirect('/new-stock-system/index.php?page=sales');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid security token. Please try again.');
    redirect('/new-stock-system/index.php?page=sales');
}

// Get sale ID
$saleId = (int) ($_POST['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID');
    redirect('/new-stock-system/index.php?page=sales');
}

try {
    $saleModel = new Sale();
    $stockEntryModel = new StockEntry();
    $ledgerModel = new StockLedger();
    $db = Database::getInstance()->getConnection();
    
    // Check if sale exists
    $sale = $saleModel->findById($saleId);
    
    if (!$sale) {
        throw new Exception('Sale not found');
    }
    
    // Get current user for logging
    $currentUser = getCurrentUser();
    
    // Start transaction - we'll delete everything in one atomic operation
    $db->beginTransaction();
    
    try {
        // Track what we're deleting for logging
        $deletionLog = [
            'sale_id' => $saleId,
            'customer' => $sale['customer_name'] ?? 'Unknown',
            'amount' => $sale['total_amount'],
            'deleted_items' => []
        ];
        
        // ========================================
        // STEP 1: Delete receipts (payments) linked to invoices
        // ========================================
        $receiptsSql = "SELECT r.id, r.amount_paid, r.payment_method 
                        FROM receipts r
                        INNER JOIN invoices i ON r.invoice_id = i.id
                        WHERE i.sale_id = ?";
        $receiptsStmt = $db->prepare($receiptsSql);
        $receiptsStmt->execute([$saleId]);
        $receipts = $receiptsStmt->fetchAll();
        
        if (!empty($receipts)) {
            $receiptIds = array_column($receipts, 'id');
            $totalPayments = array_sum(array_column($receipts, 'amount_paid'));
            
            // Delete receipts
            $deleteReceiptsSql = "DELETE FROM receipts 
                                 WHERE invoice_id IN (SELECT id FROM invoices WHERE sale_id = ?)";
            $deleteReceiptsStmt = $db->prepare($deleteReceiptsSql);
            $deleteReceiptsStmt->execute([$saleId]);
            
            $deletionLog['deleted_items'][] = count($receipts) . " payment(s) totaling ₦" . number_format($totalPayments, 2);
        }
        
        // ========================================
        // STEP 2: Delete invoices
        // ========================================
        $invoicesSql = "SELECT id, invoice_number, total, paid_amount, status 
                        FROM invoices 
                        WHERE sale_id = ?";
        $invoicesStmt = $db->prepare($invoicesSql);
        $invoicesStmt->execute([$saleId]);
        $invoices = $invoicesStmt->fetchAll();
        
        if (!empty($invoices)) {
            $invoiceNumbers = array_column($invoices, 'invoice_number');
            
            // Delete invoices
            $deleteInvoicesSql = "DELETE FROM invoices WHERE sale_id = ?";
            $deleteInvoicesStmt = $db->prepare($deleteInvoicesSql);
            $deleteInvoicesStmt->execute([$saleId]);
            
            $deletionLog['deleted_items'][] = count($invoices) . " invoice(s): " . implode(', ', $invoiceNumbers);
        }
        
        // ========================================
        // STEP 3: Delete production records (if table exists)
        // ========================================
        try {
            // Check if production table exists
            $tableCheckSql = "SHOW TABLES LIKE 'production'";
            $tableCheckStmt = $db->query($tableCheckSql);
            $tableExists = $tableCheckStmt->rowCount() > 0;
            
            if ($tableExists) {
                $productionSql = "SELECT id FROM production WHERE sale_id = ?";
                $productionStmt = $db->prepare($productionSql);
                $productionStmt->execute([$saleId]);
                $production = $productionStmt->fetchAll();
                
                if (!empty($production)) {
                    // Delete production records
                    $deleteproductionSql = "DELETE FROM production WHERE sale_id = ?";
                    $deleteproductionStmt = $db->prepare($deleteproductionSql);
                    $deleteproductionStmt->execute([$saleId]);
                    
                    $deletionLog['deleted_items'][] = count($production) . " production record(s)";
                }
            }
        } catch (PDOException $e) {
            // Table doesn't exist or error accessing it - skip this step
            error_log("Production table check/delete skipped: " . $e->getMessage());
        }
        
        // ========================================
        // STEP 4: DON'T delete stock ledger entries - keep them for audit trail
        // We'll add a new INFLOW entry to cancel them out instead
        // ========================================
        // (Removed the deletion of ledger entries)
        
        // ========================================
        // STEP 5: Handle stock restoration
        // ========================================
        $isKzincSale = ($sale['unit_type'] ?? 'meters') !== 'meters';

        if ($isKzincSale) {
            // -------------------------------------------------------
            // KZinc: restore pieces_remaining across entries LIFO
            // -------------------------------------------------------
            try {
                // Compute total pieces sold
                $saleUnitType = $sale['unit_type'];
                $saleQty      = floatval($sale['quantity'] ?? 0);

                if ($saleUnitType === STOCK_UNIT_BUNDLES) {
                    $piecesToRestore = (int)($saleQty * KZINC_PIECES_PER_BUNDLE);
                } else {
                    $piecesToRestore = (int)$saleQty; // pieces
                }

                if ($piecesToRestore > 0) {
                    // Get KZinc entries for the coil in LIFO order (reverse of deduction order)
                    $kzincEntriesSql = "SELECT id, pieces_total, pieces_remaining
                                        FROM stock_entries
                                        WHERE coil_id = ?
                                          AND unit_type != 'meters'
                                          AND deleted_at IS NULL
                                        ORDER BY created_at DESC";
                    $kzincStmt = $db->prepare($kzincEntriesSql);
                    $kzincStmt->execute([$sale['coil_id']]);
                    $kzincEntries = $kzincStmt->fetchAll();

                    $remaining = $piecesToRestore;
                    foreach ($kzincEntries as $kzEntry) {
                        if ($remaining <= 0) break;
                        $canRestore = (int)$kzEntry['pieces_total'] - (int)$kzEntry['pieces_remaining'];
                        $toAdd = min($remaining, $canRestore);
                        if ($toAdd > 0) {
                            $restoreSql = "UPDATE stock_entries
                                           SET pieces_remaining = pieces_remaining + ?,
                                               updated_at = NOW()
                                           WHERE id = ?";
                            $db->prepare($restoreSql)->execute([$toAdd, $kzEntry['id']]);
                            $remaining -= $toAdd;
                        }
                    }

                    $stockEntryModel->checkAndUpdateCoilStatus($sale['coil_id']);
                    $deletionLog['deleted_items'][] = "KZinc stock restored: {$piecesToRestore} pieces";
                }
            } catch (Exception $e) {
                error_log("KZinc stock restoration failed: " . $e->getMessage());
                $deletionLog['deleted_items'][] = "KZinc stock restoration failed: " . $e->getMessage();
            }
        } elseif ($sale['stock_entry_id']) {
            // -------------------------------------------------------
            // Meter-based: restore meters_remaining + ledger inflow
            // -------------------------------------------------------
            try {
                $stockEntry = $stockEntryModel->findById($sale['stock_entry_id']);

                if (!$stockEntry) {
                    throw new Exception('Stock entry not found for restoration');
                }

                $isFactoryUse = ($stockEntry['status'] === 'factory_use');

                $restoreStockSql = "UPDATE stock_entries
                                   SET meters_remaining = meters_remaining + ?,
                                       weight_kg_remaining = COALESCE(weight_kg_remaining, 0) + ?,
                                       status = CASE
                                           WHEN status = 'sold' AND meters_remaining + ? >= meters THEN 'available'
                                           ELSE status
                                       END
                                   WHERE id = ?";
                $db->prepare($restoreStockSql)->execute([
                    $sale['meters'],
                    $sale['weight_kg'] ?? 0,
                    $sale['meters'],
                    $sale['stock_entry_id']
                ]);

                if ($isFactoryUse) {
                    $description = "Stock restored from deleted sale #{$saleId} - {$sale['meters']}m returned to factory tracking";
                    $ledgerInflowResult = $ledgerModel->recordInflow(
                        $stockEntry['coil_id'],
                        $sale['stock_entry_id'],
                        $sale['meters'],
                        $description,
                        $currentUser['id']
                    );
                    if (!$ledgerInflowResult) {
                        throw new Exception('Failed to create ledger inflow for stock restoration');
                    }
                    $deletionLog['deleted_items'][] = "Stock restored: " . number_format($sale['meters'], 2) . "m"
                        . ($sale['weight_kg'] ? " (" . number_format($sale['weight_kg'], 2) . "kg)" : "")
                        . " + ledger inflow created";
                } else {
                    $deletionLog['deleted_items'][] = "Stock restored: " . number_format($sale['meters'], 2) . "m"
                        . ($sale['weight_kg'] ? " (" . number_format($sale['weight_kg'], 2) . "kg)" : "");
                }

                $stockEntryModel->checkAndUpdateCoilStatus($stockEntry['coil_id']);

            } catch (Exception $e) {
                error_log("Stock restoration failed: " . $e->getMessage());
                $deletionLog['deleted_items'][] = "Stock restoration failed: " . $e->getMessage();
            }
        }
        
        // ========================================
        // STEP 6: Delete the sale record itself
        // ========================================
        $deleteSaleSql = "DELETE FROM sales WHERE id = ?";
        $deleteSaleStmt = $db->prepare($deleteSaleSql);
        $deleteSaleResult = $deleteSaleStmt->execute([$saleId]);
        
        if (!$deleteSaleResult) {
            throw new Exception('Failed to delete sale record');
        }
        
        // Commit all deletions
        $db->commit();
        
        // ========================================
        // STEP 7: Log the deletion activity
        // ========================================
        $logMessage = "Sale #$saleId PERMANENTLY DELETED by {$currentUser['name']}. ";
        $logMessage .= "Customer: {$deletionLog['customer']}, Amount: ₦" . number_format($deletionLog['amount'], 2);
        if (!empty($deletionLog['deleted_items'])) {
            $logMessage .= ". Actions taken: " . implode('; ', $deletionLog['deleted_items']);
        }
        
        logActivity('Sale Cascade Deleted', $logMessage);
        
        // Create detailed message for user
        $successMessage = "Sale #$saleId deleted successfully";
        if (!empty($deletionLog['deleted_items'])) {
            $successMessage .= " along with: " . implode(', ', $deletionLog['deleted_items']);
        }
        
        setFlashMessage('success', $successMessage);
        redirect('/new-stock-system/index.php?page=sales');
        
    } catch (Exception $e) {
        // Rollback on any error
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('=== CASCADE DELETE ERROR ===');
    error_log('Error deleting sale: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    setFlashMessage('error', 'Failed to delete sale: ' . $e->getMessage());
    redirect('/new-stock-system/index.php?page=sales_view&id=' . $saleId);
}