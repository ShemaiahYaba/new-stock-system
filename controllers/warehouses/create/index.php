<?php
/**
 * Warehouse Create Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/warehouse.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=warehouses_create');
        exit();
    }

    $name = sanitize($_POST['name'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $contact = sanitize($_POST['contact'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Warehouse name is required.';
    }

    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=warehouses_create');
        exit();
    }

    $warehouseModel = new Warehouse();

    $data = [
        'name' => $name,
        'location' => $location ?: null,
        'contact' => $contact ?: null,
        'is_active' => $is_active,
    ];

    if ($warehouseModel->create($data)) {
        logActivity('Warehouse created', "Name: $name");
        setFlashMessage('success', 'Warehouse created successfully!');
        header('Location: /new-stock-system/index.php?page=warehouses');
    } else {
        setFlashMessage('error', 'Failed to create warehouse.');
        header('Location: /new-stock-system/index.php?page=warehouses_create');
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=warehouses_create');
exit();
