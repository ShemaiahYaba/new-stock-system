<?php
/**
 * KZinc Stock Entry Delete Controller
 */

session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/stock_entry.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_KZINC_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=kzinc_stock');
        exit();
    }

    $entryId = (int)($_POST['id'] ?? 0);

    if ($entryId <= 0) {
        setFlashMessage('error', 'Invalid stock entry ID.');
        header('Location: /new-stock-system/index.php?page=kzinc_stock');
        exit();
    }

    $stockEntryModel = new StockEntry();
    $entry = $stockEntryModel->findById($entryId);

    if (!$entry) {
        setFlashMessage('error', 'Stock entry not found.');
        header('Location: /new-stock-system/index.php?page=kzinc_stock');
        exit();
    }

    $coilId = $entry['coil_id'];

    if ($stockEntryModel->delete($entryId)) {
        $stockEntryModel->checkAndUpdateCoilStatus($coilId);
        logActivity('KZinc stock entry deleted', "Entry ID: $entryId");
        setFlashMessage('success', 'Stock entry deleted successfully.');
    } else {
        setFlashMessage('error', 'Failed to delete stock entry.');
    }

    header('Location: /new-stock-system/index.php?page=kzinc_stock');
    exit();
}

header('Location: /new-stock-system/index.php?page=kzinc_stock');
exit();
