<?php
/**
 * User Create Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/user.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

// Check permission
requirePermission(MODULE_USER_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        header('Location: /new-stock-system/index.php?page=users_create');
        exit();
    }
    
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = sanitize($_POST['role'] ?? ROLE_VIEWER);
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please provide a valid email address.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    
    if (!array_key_exists($role, USER_ROLES)) {
        $errors[] = 'Invalid role selected.';
    }
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=users_create');
        exit();
    }
    
    // Check if email already exists
    $userModel = new User();
    $existingUser = $userModel->findByEmail($email);
    
    if ($existingUser) {
        setFlashMessage('error', 'A user with this email already exists.');
        header('Location: /new-stock-system/index.php?page=users_create');
        exit();
    }
    
    // Create user
    $userId = $userModel->create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => $role
    ]);
    
    if ($userId) {
        // Set default permissions for the role
        if (isset(DEFAULT_PERMISSIONS[$role])) {
            $userModel->setPermissions($userId, DEFAULT_PERMISSIONS[$role]);
        }
        
        logActivity('User created', "User: $email, Role: $role");
        
        setFlashMessage('success', 'User created successfully!');
        header('Location: /new-stock-system/index.php?page=users');
        exit();
    } else {
        setFlashMessage('error', 'Failed to create user. Please try again.');
        header('Location: /new-stock-system/index.php?page=users_create');
        exit();
    }
} else {
    // Redirect to create form
    header('Location: /new-stock-system/index.php?page=users_create');
    exit();
}
