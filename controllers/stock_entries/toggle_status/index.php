<?php
/**
 * Stock Entry Toggle Status Controller
 * Moves stock between 'available' and 'factory_use'
 * Creates ledger inflow when moving TO factory_use
 * Creates ledger outflow when moving FROM factory_use
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_ledger.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }

    $entryId = (int) ($_POST['id'] ?? 0);
    $currentStatus = sanitize($_POST['current_status'] ?? 'available');

    if ($entryId <= 0) {
        setFlashMessage('error', 'Invalid stock entry ID.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }

    $stockEntryModel = new StockEntry();
    $entry = $stockEntryModel->findById($entryId);

    if (!$entry) {
        setFlashMessage('error', 'Stock entry not found.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }

    // Check if entry has remaining meters
    if ($entry['meters_remaining'] <= 0) {
        setFlashMessage('error', 'Cannot change status of exhausted stock entry.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }

    // Toggle status
    $newStatus = $currentStatus === 'available' ? 'factory_use' : 'available';

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        $ledgerModel = new StockLedger();
        $currentUser = getCurrentUser();

        // CASE 1: Moving TO factory_use (available → factory_use)
        if ($currentStatus === 'available' && $newStatus === 'factory_use') {
            // Update stock entry status
            if (!$stockEntryModel->update($entryId, ['status' => $newStatus])) {
                throw new Exception('Failed to update stock entry status.');
            }

            // Record INFLOW to ledger
            $description = "Stock moved to factory use - Entry #{$entryId} ({$entry['meters_remaining']}m available)";

            if (
                !$ledgerModel->recordInflow(
                    $entry['coil_id'],
                    $entryId,
                    $entry['meters_remaining'],
                    $description,
                    $currentUser['id'],
                )
            ) {
                throw new Exception('Failed to create ledger inflow entry.');
            }

            $message = "Stock entry moved to Factory Use. Ledger updated with {$entry['meters_remaining']}m inflow.";
        }
        // CASE 2: Moving FROM factory_use (factory_use → available)
        elseif ($currentStatus === 'factory_use' && $newStatus === 'available') {
            // Get current ledger balance for this stock entry
            $currentBalance = $ledgerModel->getCurrentBalance($entryId);

            // If there's a balance in the ledger, record outflow to zero it out
            if ($currentBalance > 0) {
                $description = "Stock moved back to available - Entry #{$entryId} (removing {$currentBalance}m from factory tracking)";

                if (
                    !$ledgerModel->recordOutflow(
                        $entry['coil_id'],
                        $entryId,
                        $currentBalance, // Remove the entire current balance
                        $description,
                        'status_change', // Reference type
                        $entryId, // Reference ID
                        $currentUser['id'],
                    )
                ) {
                    throw new Exception('Failed to create ledger outflow entry.');
                }
            }

            // Update stock entry status
            if (!$stockEntryModel->update($entryId, ['status' => $newStatus])) {
                throw new Exception('Failed to update stock entry status.');
            }

            $message = "Stock entry moved back to Available. Ledger balance cleared ({$currentBalance}m removed from factory tracking).";
        } else {
            throw new Exception('Invalid status transition.');
        }

        $db->commit();

        logActivity(
            'Stock entry status changed',
            "Entry #{$entryId}: {$currentStatus} → {$newStatus}",
        );
        setFlashMessage('success', $message);
    } catch (Exception $e) {
        $db->rollBack();
        error_log('Stock entry status toggle error: ' . $e->getMessage());
        setFlashMessage('error', 'Failed to change status: ' . $e->getMessage());
    }

    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

header('Location: /new-stock-system/index.php?page=stock_entries');
exit();
