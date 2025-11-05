<?php
/**
 * User Delete Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/user.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

// Check permission
requirePermission(MODULE_USER_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        header('Location: /new-stock-system/index.php?page=users');
        exit();
    }
    
    $userId = (int)($_POST['id'] ?? 0);
    
    // Prevent self-deletion
    if ($userId === getCurrentUserId()) {
        setFlashMessage('error', 'You cannot delete your own account.');
        header('Location: /new-stock-system/index.php?page=users');
        exit();
    }
    
    if ($userId <= 0) {
        setFlashMessage('error', 'Invalid user ID.');
        header('Location: /new-stock-system/index.php?page=users');
        exit();
    }
    
    // Delete user
    $userModel = new User();
    $user = $userModel->findById($userId);
    
    if (!$user) {
        setFlashMessage('error', 'User not found.');
        header('Location: /new-stock-system/index.php?page=users');
        exit();
    }
    
    if ($userModel->delete($userId)) {
        logActivity('User deleted', "User ID: $userId, Email: {$user['email']}");
        
        setFlashMessage('success', 'User deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete user. Please try again.');
    }
    
    header('Location: /new-stock-system/index.php?page=users');
    exit();
} else {
    header('Location: /new-stock-system/index.php?page=users');
    exit();
}
