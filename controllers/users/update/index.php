<?php
/**
 * User Update Controller
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
    
    $userId = (int)($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $role = sanitize($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    elseif (!isValidEmail($email)) $errors[] = 'Invalid email.';
    if (!array_key_exists($role, USER_ROLES)) $errors[] = 'Invalid role.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=users_edit&id=$userId");
        exit();
    }
    
    $userModel = new User();
    $data = ['name' => $name, 'email' => $email, 'role' => $role];
    
    if (!empty($password)) {
        if (strlen($password) < 6) {
            setFlashMessage('error', 'Password must be at least 6 characters.');
            header("Location: /new-stock-system/index.php?page=users_edit&id=$userId");
            exit();
        }
        $data['password'] = $password;
    }
    
    if ($userModel->update($userId, $data)) {
        logActivity('User updated', "User ID: $userId");
        setFlashMessage('success', 'User updated successfully!');
        header('Location: /new-stock-system/index.php?page=users');
    } else {
        setFlashMessage('error', 'Failed to update user.');
        header("Location: /new-stock-system/index.php?page=users_edit&id=$userId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=users');
exit();
