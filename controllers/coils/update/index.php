<?php
/**
 * Coil Update Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/color.php'; // ADD THIS
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=coils');
        exit();
    }
    
    $coilId = (int)($_POST['id'] ?? 0);
    $code = sanitize($_POST['code'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $colorId = (int)($_POST['color_id'] ?? 0); // CHANGED
    $netWeight = floatval($_POST['net_weight'] ?? 0);
    $category = sanitize($_POST['category'] ?? '');
    $status = sanitize($_POST['status'] ?? '');
    
    $errors = [];
    
    if (empty($code)) $errors[] = 'Coil code is required.';
    if (empty($name)) $errors[] = 'Coil name is required.';
    if ($colorId <= 0) $errors[] = 'Please select a valid color.'; // CHANGED
    if ($netWeight <= 0) $errors[] = 'Net weight must be greater than 0.';
    if (!array_key_exists($category, STOCK_CATEGORIES)) $errors[] = 'Invalid category.';
    if (!array_key_exists($status, STOCK_STATUSES)) $errors[] = 'Invalid status.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=coils_edit&id=$coilId");
        exit();
    }
    
    // Verify color exists and is active
    $colorModel = new Color();
    $color = $colorModel->findById($colorId);
    if (!$color || !$color['is_active']) {
        setFlashMessage('error', 'Selected color is not valid or inactive.');
        header("Location: /new-stock-system/index.php?page=coils_edit&id=$coilId");
        exit();
    }
    
    $coilModel = new Coil();
    
    // Check if code already exists for another coil
    $existing = $coilModel->findByCode($code);
    if ($existing && $existing['id'] != $coilId) {
        setFlashMessage('error', 'A coil with this code already exists.');
        header("Location: /new-stock-system/index.php?page=coils_edit&id=$coilId");
        exit();
    }
    
    $data = [
        'code' => $code,
        'name' => $name,
        'color_id' => $colorId, // CHANGED
        'net_weight' => $netWeight,
        'category' => $category,
        'status' => $status
    ];
    
    if ($coilModel->update($coilId, $data)) {
        logActivity('Coil updated', "Code: $code");
        setFlashMessage('success', 'Coil updated successfully!');
        header('Location: /new-stock-system/index.php?page=coils');
    } else {
        setFlashMessage('error', 'Failed to update coil.');
        header("Location: /new-stock-system/index.php?page=coils_edit&id=$coilId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=coils');
exit();