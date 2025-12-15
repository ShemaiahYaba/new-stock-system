<?php
/**
 * UPDATED Get Production Properties by Category (AJAX Endpoint)
 * File: controllers/production_properties/get_by_category.php
 * REPLACE the existing get_by_category.php with this version
 */

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production_property.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';

header('Content-Type: application/json');

// Check if user is logged in
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
$includeAddons = isset($_GET['include_addons']) && $_GET['include_addons'] == '1';

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
    
    if ($includeAddons) {
        // Get ALL properties (production + add-ons) for this category
        $properties = $propertyModel->getWorkflowProperties($category);
    } else {
        // Get only production properties (backward compatible)
        $properties = $propertyModel->getProductionPropertiesByCategory($category);
    }
    
    // Parse metadata JSON for each property
    foreach ($properties as &$property) {
        if (!empty($property['metadata'])) {
            $property['metadata'] = json_decode($property['metadata'], true);
        } else {
            $property['metadata'] = [];
        }
        
        // Add helpful flags
        $property['is_addon'] = (int)$property['is_addon'];
        $property['is_refundable'] = (int)$property['is_refundable'];
        $property['is_active'] = (int)$property['is_active'];
    }
    
    // Group properties if requested
    if ($includeAddons) {
        // Separate into production and add-ons
        $productionProps = array_filter($properties, function($p) {
            return $p['is_addon'] == 0;
        });
        
        $addonProps = array_filter($properties, function($p) {
            return $p['is_addon'] == 1;
        });
        
        echo json_encode([
            'success' => true,
            'category' => $category,
            'properties' => array_values($productionProps),
            'addons' => array_values($addonProps),
            'all' => $properties
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'category' => $category,
            'properties' => $properties
        ]);
    }
    
} catch (Exception $e) {
    error_log('Get properties by category error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch properties'
    ]);
}

exit();