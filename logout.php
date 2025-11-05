<?php
/**
 * Logout Script
 * 
 * Terminates user session and redirects to login page
 */

session_start();

require_once __DIR__ . '/utils/helpers.php';

// Log activity before destroying session
if (isset($_SESSION['user_email'])) {
    logActivity('User logged out', "User: {$_SESSION['user_email']}");
}

// Clear all session variables
$_SESSION = [];

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: /new-stock-system/login.php');
exit();
