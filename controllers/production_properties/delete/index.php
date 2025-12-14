<?php
/**
 * Production Property Delete Controller
 * File: controllers/production_properties/delete/index.php
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/production_property.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_PRODUCTION_PROPERTIES, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=production_properties');
        exit();
    }
    
    $propertyId = (int)($_POST['id'] ?? 0);
    
    if ($propertyId <= 0) {
        setFlashMessage('error', 'Invalid property ID.');
        header('Location: /new-stock-system/index.php?page=production_properties');
        exit();
    }
    
    $propertyModel = new ProductionProperty();
    $property = $propertyModel->findById($propertyId);
    
    if (!$property) {
        setFlashMessage('error', 'Production property not found.');
        header('Location: /new-stock-system/index.php?page=production_properties');
        exit();
    }
    
    // Check if property is being used in any productions
    if ($propertyModel->isUsedInProductions($propertyId)) {
        setFlashMessage('error', "Cannot delete property '{$property['name']}' because it is being used in existing production records.");
        header('Location: /new-stock-system/index.php?page=production_properties');
        exit();
    }
    
    if ($propertyModel->delete($propertyId)) {
        logActivity('Production Property deleted', "Code: {$property['code']}");
        setFlashMessage('success', 'Production property deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete production property.');
    }
    
    header('Location: /new-stock-system/index.php?page=production_properties');
    exit();
}

header('Location: /new-stock-system/index.php?page=production_properties');
exit();