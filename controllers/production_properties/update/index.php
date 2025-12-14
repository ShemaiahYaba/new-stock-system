<?php
/**
 * Production Property Update Controller
 * File: controllers/production_properties/update/index.php
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/production_property.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_PRODUCTION_PROPERTIES, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=production_properties');
        exit();
    }
    
    $propertyId = (int)($_POST['id'] ?? 0);
    $code = sanitize($_POST['code'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $propertyType = sanitize($_POST['property_type'] ?? '');
    $defaultPrice = floatval($_POST['default_price'] ?? 0);
    $sortOrder = intval($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle metadata for bundle-based properties
    $metadata = null;
    if ($propertyType === PROPERTY_TYPE_BUNDLE_BASED) {
        $piecesPerBundle = intval($_POST['pieces_per_bundle'] ?? 0);
        if ($piecesPerBundle > 0) {
            $metadata = json_encode(['pieces_per_bundle' => $piecesPerBundle]);
        }
    }
    
    $errors = [];
    
    if (empty($code)) $errors[] = 'Property code is required.';
    if (empty($name)) $errors[] = 'Property name is required.';
    if (empty($category)) $errors[] = 'Category is required.';
    if (empty($propertyType)) $errors[] = 'Property type is required.';
    if ($defaultPrice < 0) $errors[] = 'Default price cannot be negative.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=production_properties_edit&id=$propertyId");
        exit();
    }
    
    $propertyModel = new ProductionProperty();
    
    // Check if code already exists for another property
    $existing = $propertyModel->findByCode($code);
    if ($existing && $existing['id'] != $propertyId) {
        setFlashMessage('error', 'A property with this code already exists.');
        header("Location: /new-stock-system/index.php?page=production_properties_edit&id=$propertyId");
        exit();
    }
    
    $data = [
        'code' => $code,
        'name' => $name,
        'category' => $category,
        'property_type' => $propertyType,
        'default_price' => $defaultPrice,
        'sort_order' => $sortOrder,
        'metadata' => $metadata,
        'is_active' => $isActive
    ];
    
    if ($propertyModel->update($propertyId, $data)) {
        logActivity('Production Property updated', "Code: $code");
        setFlashMessage('success', 'Production property updated successfully!');
        header('Location: /new-stock-system/index.php?page=production_properties');
    } else {
        setFlashMessage('error', 'Failed to update production property.');
        header("Location: /new-stock-system/index.php?page=production_properties_edit&id=$propertyId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=production_properties');
exit();