<?php
/**
 * Warehouse Delete Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/warehouse.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=warehouses');
        exit();
    }

    $warehouseId = (int) ($_POST['id'] ?? 0);

    if ($warehouseId <= 0) {
        setFlashMessage('error', 'Invalid warehouse ID.');
        header('Location: /new-stock-system/index.php?page=warehouses');
        exit();
    }

    $warehouseModel = new Warehouse();
    $warehouse = $warehouseModel->findById($warehouseId);

    if (!$warehouse) {
        setFlashMessage('error', 'Warehouse not found.');
        header('Location: /new-stock-system/index.php?page=warehouses');
        exit();
    }

    if ($warehouseModel->delete($warehouseId)) {
        logActivity('Warehouse deleted', "Name: {$warehouse['name']}");
        setFlashMessage('success', 'Warehouse deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete warehouse.');
    }

    header('Location: /new-stock-system/index.php?page=warehouses');
    exit();
}

header('Location: /new-stock-system/index.php?page=warehouses');
exit();
