<?php
/**
 * ============================================
 * FILE: controllers/tiles/designs/update/index.php
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/design.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_DESIGN_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=designs');
        exit();
    }
    
    $designId = (int)($_POST['id'] ?? 0);
    $code = strtoupper(sanitize($_POST['code'] ?? ''));
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    $errors = [];
    
    if (empty($code)) $errors[] = 'Design code is required.';
    if (empty($name)) $errors[] = 'Design name is required.';
    if (strlen($code) < 3 || strlen($code) > 50) $errors[] = 'Code must be 3-50 characters.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=designs_edit&id=$designId");
        exit();
    }
    
    $designModel = new Design();
    
    // Check if code already exists for another design
    $existing = $designModel->findByCode($code);
    if ($existing && $existing['id'] != $designId) {
        setFlashMessage('error', 'A design with this code already exists.');
        header("Location: /new-stock-system/index.php?page=designs_edit&id=$designId");
        exit();
    }
    
    $data = [
        'code' => $code,
        'name' => $name,
        'description' => $description,
        'is_active' => $isActive
    ];
    
    if ($designModel->update($designId, $data)) {
        logActivity('Design updated', "Code: $code");
        setFlashMessage('success', 'Design updated successfully!');
        header('Location: /new-stock-system/index.php?page=designs');
    } else {
        setFlashMessage('error', 'Failed to update design.');
        header("Location: /new-stock-system/index.php?page=designs_edit&id=$designId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=designs');
exit();
