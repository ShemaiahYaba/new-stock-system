<?php
/**
 * Customer Update Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/customer.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=customers');
        exit();
    }
    
    $customerId = (int)($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $company = sanitize($_POST['company'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    $errors = [];
    
    if (empty($name)) $errors[] = 'Customer name is required.';
    if (empty($phone)) $errors[] = 'Phone number is required.';
    if (!empty($email) && !isValidEmail($email)) $errors[] = 'Invalid email address.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=customers_edit&id=$customerId");
        exit();
    }
    
    $customerModel = new Customer();
    
    $data = [
        'name' => $name,
        'email' => $email ?: null,
        'phone' => $phone,
        'company' => $company ?: null,
        'address' => $address ?: null
    ];
    
    if ($customerModel->update($customerId, $data)) {
        logActivity('Customer updated', "Name: $name");
        setFlashMessage('success', 'Customer updated successfully!');
        header('Location: /new-stock-system/index.php?page=customers');
    } else {
        setFlashMessage('error', 'Failed to update customer.');
        header("Location: /new-stock-system/index.php?page=customers_edit&id=$customerId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=customers');
exit();
