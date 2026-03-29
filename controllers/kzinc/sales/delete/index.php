<?php
/**
 * KZinc Sale Delete Controller
 * Cascading delete: receipts → invoices → production → restore pieces → delete sale
 */

session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/sale.php';
require_once __DIR__ . '/../../../../models/stock_entry.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method.');
    header('Location: /new-stock-system/index.php?page=kzinc_sales');
    exit();
}

requirePermission(MODULE_KZINC_MANAGEMENT, ACTION_DELETE);

if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid security token. Please try again.');
    header('Location: /new-stock-system/index.php?page=kzinc_sales');
    exit();
}

$saleId = (int)($_POST['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID.');
    header('Location: /new-stock-system/index.php?page=kzinc_sales');
    exit();
}

try {
    $saleModel        = new Sale();
    $stockEntryModel  = new StockEntry();
    $db               = Database::getInstance()->getConnection();
    $currentUser      = getCurrentUser();

    $sale = $saleModel->findById($saleId);
    if (!$sale) {
        throw new Exception('Sale not found.');
    }

    // Confirm this is a KZinc sale (unit_type != meters)
    if (($sale['unit_type'] ?? 'meters') === 'meters') {
        setFlashMessage('error', 'This sale is not a K-Zinc sale. Use the regular Sales module to delete it.');
        header('Location: /new-stock-system/index.php?page=kzinc_sales');
        exit();
    }

    $db->beginTransaction();

    try {
        $deletionLog = [];

        // --------------------------------------------------------
        // STEP 1: Delete receipts linked to invoices of this sale
        // --------------------------------------------------------
        $db->prepare("DELETE FROM receipts
                      WHERE invoice_id IN (SELECT id FROM invoices WHERE sale_id = ?)")
           ->execute([$saleId]);

        // --------------------------------------------------------
        // STEP 2: Delete invoices
        // --------------------------------------------------------
        $db->prepare("DELETE FROM invoices WHERE sale_id = ?")->execute([$saleId]);

        // --------------------------------------------------------
        // STEP 3: Delete production records (if table exists)
        // --------------------------------------------------------
        try {
            $db->prepare("DELETE FROM production WHERE sale_id = ?")->execute([$saleId]);
        } catch (PDOException $e) {
            error_log("KZinc sale delete — production table skip: " . $e->getMessage());
        }

        // --------------------------------------------------------
        // STEP 4: Restore pieces to KZinc stock entries (LIFO)
        // --------------------------------------------------------
        $saleUnitType = $sale['unit_type'];
        $saleQty      = floatval($sale['quantity'] ?? 0);

        if ($saleUnitType === STOCK_UNIT_PALLETS) {
            // quantity stored as total pieces for pallets in the sale record
            $piecesToRestore = (int)$saleQty;
        } elseif ($saleUnitType === STOCK_UNIT_BUNDLES) {
            $piecesToRestore = (int)($saleQty * KZINC_PIECES_PER_BUNDLE);
        } else {
            // pieces
            $piecesToRestore = (int)$saleQty;
        }

        if ($piecesToRestore > 0) {
            $kzincStmt = $db->prepare(
                "SELECT se.id, se.pieces_total, se.pieces_remaining
                 FROM stock_entries se
                 JOIN coils c ON se.coil_id = c.id
                 WHERE se.coil_id = ?
                   AND c.category = 'kzinc'
                   AND se.deleted_at IS NULL
                 ORDER BY se.created_at DESC"
            );
            $kzincStmt->execute([$sale['coil_id']]);
            $kzincEntries = $kzincStmt->fetchAll();

            $remaining = $piecesToRestore;
            foreach ($kzincEntries as $kzEntry) {
                if ($remaining <= 0) break;
                $canRestore = (int)$kzEntry['pieces_total'] - (int)$kzEntry['pieces_remaining'];
                $toAdd = min($remaining, $canRestore);
                if ($toAdd > 0) {
                    $db->prepare(
                        "UPDATE stock_entries
                         SET pieces_remaining = pieces_remaining + ?,
                             updated_at = NOW()
                         WHERE id = ?"
                    )->execute([$toAdd, $kzEntry['id']]);
                    $remaining -= $toAdd;
                }
            }

            $stockEntryModel->checkAndUpdateCoilStatus($sale['coil_id']);
            $deletionLog[] = "KZinc stock restored: {$piecesToRestore} pieces";
        }

        // --------------------------------------------------------
        // STEP 5: Delete the sale record
        // --------------------------------------------------------
        $db->prepare("DELETE FROM sales WHERE id = ?")->execute([$saleId]);

        $db->commit();

        $logMsg = "KZinc Sale #{$saleId} deleted by {$currentUser['name']}";
        if (!empty($deletionLog)) {
            $logMsg .= '. ' . implode('; ', $deletionLog);
        }
        logActivity('KZinc Sale Deleted', $logMsg);

        $msg = "K-Zinc Sale #{$saleId} deleted successfully";
        if (!empty($deletionLog)) {
            $msg .= ' — ' . implode(', ', $deletionLog);
        }
        setFlashMessage('success', $msg);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log('KZinc sale delete error: ' . $e->getMessage());
    setFlashMessage('error', 'Failed to delete sale: ' . $e->getMessage());
}

header('Location: /new-stock-system/index.php?page=kzinc_sales');
exit();
