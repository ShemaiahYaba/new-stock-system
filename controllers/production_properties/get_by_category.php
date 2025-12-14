<?php
/**
 * Get Production Properties by Category (AJAX Endpoint)
 * File: controllers/production_properties/get_by_category.php
 */

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production_property.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';

header('Content-Type: application/json');

// Check if user is a guest (not logged in)
if (isGuest()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check permissions
if (!hasPermission(MODULE_PRODUCTION_MANAGEMENT, ACTION_VIEW)) {
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit();
}

$category = $_GET['category'] ?? '';

if (empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Category is required']);
    exit();
}

if (!in_array($category, ['alusteel', 'aluminum', 'kzinc'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid category']);
    exit();
}

try {
    $propertyModel = new ProductionProperty();
    $properties = $propertyModel->getByCategoryAndActive($category);
    
    // Parse metadata JSON for each property
    foreach ($properties as &$property) {
        if (!empty($property['metadata'])) {
            $property['metadata'] = json_decode($property['metadata'], true);
        } else {
            $property['metadata'] = [];
        }
    }
    
    echo json_encode([
        'success' => true,
        'category' => $category,
        'properties' => $properties
    ]);
    
} catch (Exception $e) {
    error_log('Get properties by category error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch properties'
    ]);
}

exit();