<?php
/**
 * Registration Controller
 * 
 * Handles new user registration
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
        header('Location: /new-stock-system/register.php');
        exit();
    }
    
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
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
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/register.php');
        exit();
    }
    
    // Check if email already exists
    $userModel = new User();
    $existingUser = $userModel->findByEmail($email);
    
    if ($existingUser) {
        setFlashMessage('error', 'An account with this email already exists.');
        header('Location: /new-stock-system/register.php');
        exit();
    }
    
    // Create new user with default viewer role
    $userId = $userModel->create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => ROLE_VIEWER
    ]);
    
    if ($userId) {
        // Set default permissions for viewer role
        $userModel->setPermissions($userId, DEFAULT_PERMISSIONS[ROLE_VIEWER]);
        
        logActivity('New user registered', "Email: $email");
        
        setFlashMessage('success', 'Registration successful! Please log in.');
        header('Location: /new-stock-system/login.php');
        exit();
    } else {
        setFlashMessage('error', 'Registration failed. Please try again.');
        header('Location: /new-stock-system/register.php');
        exit();
    }
} else {
    // Redirect to registration page if accessed directly
    header('Location: /new-stock-system/register.php');
    exit();
}
