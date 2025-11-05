<?php
/**
 * Authentication Middleware
 * 
 * Protects routes and enforces authentication and authorization
 * Validates user sessions and permissions
 */

require_once __DIR__ . '/../config/constants.php';

/**
 * Check if user is authenticated
 * Redirects to login page if not authenticated
 */
function checkAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
        header('Location: /new-stock-system/login.php');
        exit();
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header('Location: /new-stock-system/login.php?timeout=1');
        exit();
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Check if user has a specific role
 * 
 * @param string|array $roles Single role or array of roles
 * @return bool
 */
function hasRole($roles) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    if (is_array($roles)) {
        return in_array($_SESSION['user_role'], $roles);
    }
    
    return $_SESSION['user_role'] === $roles;
}

/**
 * Check if user has permission to access a module with specific action
 * 
 * @param string $module Module name
 * @param string $action Action name (view, create, edit, delete)
 * @return bool
 */
function hasPermission($module, $action = ACTION_VIEW) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Super admin has all permissions
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === ROLE_SUPER_ADMIN) {
        return true;
    }
    
    if (!isset($_SESSION['permissions'])) {
        return false;
    }
    
    $permissions = $_SESSION['permissions'];
    
    if (!isset($permissions[$module])) {
        return false;
    }
    
    return in_array($action, $permissions[$module]);
}

/**
 * Require specific permission or redirect to access denied page
 * 
 * @param string $module Module name
 * @param string $action Action name
 */
function requirePermission($module, $action = ACTION_VIEW) {
    checkAuth();
    
    if (!hasPermission($module, $action)) {
        header('Location: /new-stock-system/index.php?page=access_denied');
        exit();
    }
}

/**
 * Require specific role or redirect to access denied page
 * 
 * @param string|array $roles Single role or array of roles
 */
function requireRole($roles) {
    checkAuth();
    
    if (!hasRole($roles)) {
        header('Location: /new-stock-system/index.php?page=access_denied');
        exit();
    }
}

/**
 * Get current user ID
 * 
 * @return int|null
 */
function getCurrentUserId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 * 
 * @return array|null
 */
function getCurrentUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'],
        'name' => $_SESSION['user_name'] ?? '',
        'role' => $_SESSION['user_role'] ?? '',
        'permissions' => $_SESSION['permissions'] ?? []
    ];
}

/**
 * Check if user is guest (not authenticated)
 * 
 * @return bool
 */
function isGuest() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return !isset($_SESSION['user_id']);
}

/**
 * Redirect authenticated users away from guest pages
 */
function redirectIfAuthenticated() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id'])) {
        header('Location: /new-stock-system/index.php?page=dashboard');
        exit();
    }
}
