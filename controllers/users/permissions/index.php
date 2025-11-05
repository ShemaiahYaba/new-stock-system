<?php
/**
 * User Permissions Update Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/user.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_USER_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=users');
        exit();
    }
    
    $userId = (int)($_POST['user_id'] ?? 0);
    $permissions = $_POST['permissions'] ?? [];
    
    if ($userId <= 0) {
        setFlashMessage('error', 'Invalid user ID.');
        header('Location: /new-stock-system/index.php?page=users');
        exit();
    }
    
    $userModel = new User();
    
    if ($userModel->setPermissions($userId, $permissions)) {
        logActivity('User permissions updated', "User ID: $userId");
        setFlashMessage('success', 'Permissions updated successfully!');
    } else {
        setFlashMessage('error', 'Failed to update permissions.');
    }
    
    header("Location: /new-stock-system/index.php?page=users_permissions&id=$userId");
    exit();
}

header('Location: /new-stock-system/index.php?page=users');
exit();
