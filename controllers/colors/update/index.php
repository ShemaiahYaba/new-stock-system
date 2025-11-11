<?php
/**
 * Color Update Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_COLOR_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=colors');
        exit();
    }
    
    $colorId = (int)($_POST['id'] ?? 0);
    $code = sanitize($_POST['code'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $hexCode = sanitize($_POST['hex_code'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    $errors = [];
    
    if (empty($code)) $errors[] = 'Color code is required.';
    if (empty($name)) $errors[] = 'Color name is required.';
    if (!empty($hexCode) && !preg_match('/^#[0-9A-F]{6}$/i', $hexCode)) {
        $errors[] = 'Invalid hex color code format.';
    }
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=colors_edit&id=$colorId");
        exit();
    }
    
    $colorModel = new Color();
    
    // Check if code already exists for another color
    $existing = $colorModel->findByCode($code);
    if ($existing && $existing['id'] != $colorId) {
        setFlashMessage('error', 'A color with this code already exists.');
        header("Location: /new-stock-system/index.php?page=colors_edit&id=$colorId");
        exit();
    }
    
    $data = [
        'code' => $code,
        'name' => $name,
        'hex_code' => $hexCode ?: null,
        'is_active' => $isActive
    ];
    
    if ($colorModel->update($colorId, $data)) {
        logActivity('Color updated', "Code: $code");
        setFlashMessage('success', 'Color updated successfully!');
        header('Location: /new-stock-system/index.php?page=colors');
    } else {
        setFlashMessage('error', 'Failed to update color.');
        header("Location: /new-stock-system/index.php?page=colors_edit&id=$colorId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=colors');
exit();