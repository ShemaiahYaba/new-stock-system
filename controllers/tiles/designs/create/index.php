<?php
/**
 * ============================================
 * FILE: controllers/tiles/designs/create/index.php
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/design.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_DESIGN_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=designs_create');
        exit();
    }
    
    $code = strtoupper(sanitize($_POST['code'] ?? ''));
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    $errors = [];
    
    if (empty($code)) $errors[] = 'Design code is required.';
    if (empty($name)) $errors[] = 'Design name is required.';
    if (strlen($code) < 3 || strlen($code) > 50) $errors[] = 'Code must be 3-50 characters.';
    if (!preg_match('/^[A-Z0-9_]+$/', $code)) $errors[] = 'Code must be alphanumeric with underscores only.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=designs_create');
        exit();
    }
    
    $designModel = new Design();
    
    // Check if code already exists
    $existing = $designModel->findByCode($code);
    if ($existing) {
        setFlashMessage('error', 'A design with this code already exists.');
        header('Location: /new-stock-system/index.php?page=designs_create');
        exit();
    }
    
    $currentUser = getCurrentUser();
    
    $data = [
        'code' => $code,
        'name' => $name,
        'description' => $description,
        'is_active' => $isActive,
        'created_by' => $currentUser['id']
    ];
    
    if ($designModel->create($data)) {
        logActivity('Design created', "Code: $code");
        setFlashMessage('success', 'Design created successfully!');
        header('Location: /new-stock-system/index.php?page=designs');
    } else {
        setFlashMessage('error', 'Failed to create design.');
        header('Location: /new-stock-system/index.php?page=designs_create');
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=designs_create');
exit();