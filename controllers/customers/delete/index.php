<?php
/**
 * Customer Delete Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/customer.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=customers');
        exit();
    }
    
    $customerId = (int)($_POST['id'] ?? 0);
    
    if ($customerId <= 0) {
        setFlashMessage('error', 'Invalid customer ID.');
        header('Location: /new-stock-system/index.php?page=customers');
        exit();
    }
    
    $customerModel = new Customer();
    $customer = $customerModel->findById($customerId);
    
    if (!$customer) {
        setFlashMessage('error', 'Customer not found.');
        header('Location: /new-stock-system/index.php?page=customers');
        exit();
    }
    
    if ($customerModel->delete($customerId)) {
        logActivity('Customer deleted', "Name: {$customer['name']}");
        setFlashMessage('success', 'Customer deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete customer.');
    }
    
    header('Location: /new-stock-system/index.php?page=customers');
    exit();
}

header('Location: /new-stock-system/index.php?page=customers');
exit();
