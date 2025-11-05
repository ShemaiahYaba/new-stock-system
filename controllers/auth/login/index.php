<?php
/**
 * Login Controller
 * 
 * Handles user authentication
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/user.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

// Redirect if already authenticated
redirectIfAuthenticated();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        header('Location: /new-stock-system/login.php');
        exit();
    }
    
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        setFlashMessage('error', 'Please provide both email and password.');
        header('Location: /new-stock-system/login.php');
        exit();
    }
    
    if (!isValidEmail($email)) {
        setFlashMessage('error', 'Please provide a valid email address.');
        header('Location: /new-stock-system/login.php');
        exit();
    }
    
    // Verify credentials
    $userModel = new User();
    $user = $userModel->verifyCredentials($email, $password);
    
    if ($user) {
        // Get user permissions
        $permissions = $userModel->getPermissions($user['id']);
        
        // If no custom permissions, use default role permissions
        if (empty($permissions)) {
            $permissions = DEFAULT_PERMISSIONS[$user['role']] ?? [];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['permissions'] = $permissions;
        $_SESSION['last_activity'] = time();
        
        // Log activity
        logActivity('User logged in', "User: {$user['email']}");
        
        setFlashMessage('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');
        header('Location: /new-stock-system/index.php?page=dashboard');
        exit();
    } else {
        setFlashMessage('error', 'Invalid email or password.');
        header('Location: /new-stock-system/login.php');
        exit();
    }
} else {
    // Redirect to login page if accessed directly
    header('Location: /new-stock-system/login.php');
    exit();
}
