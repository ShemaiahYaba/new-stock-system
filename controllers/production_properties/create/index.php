<?php
/**
 * EXTENDED Production Property Create Controller - WITH ADD-ON SUPPORT
 * File: controllers/production_properties/create/index.php
 * REPLACE the existing create controller with this version
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/production_property.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_PRODUCTION_PROPERTIES, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=production_properties_create');
        exit();
    }
    
    $code = sanitize($_POST['code'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $isAddon = isset($_POST['is_addon']) ? intval($_POST['is_addon']) : 0;
    $defaultPrice = floatval($_POST['default_price'] ?? 0);
    $sortOrder = intval($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Property type handling
    if ($isAddon) {
        // For add-ons, always use unit_based
        $propertyType = PROPERTY_TYPE_UNIT_BASED;
        $calculationMethod = sanitize($_POST['calculation_method'] ?? CALC_METHOD_FIXED);
        $appliesTo = sanitize($_POST['applies_to'] ?? APPLIES_TO_TOTAL);
        $isRefundable = isset($_POST['is_refundable']) ? 1 : 0;
        $displaySection = sanitize($_POST['display_section'] ?? DISPLAY_SECTION_ADDON);
    } else {
        // For production properties
        $propertyType = sanitize($_POST['property_type'] ?? '');
        $calculationMethod = CALC_METHOD_PER_UNIT;
        $appliesTo = APPLIES_TO_TOTAL;
        $isRefundable = 0;
        $displaySection = DISPLAY_SECTION_PRODUCTION;
    }
    
    // Handle metadata for bundle-based properties
    $metadata = null;
    if ($propertyType === PROPERTY_TYPE_BUNDLE_BASED) {
        $piecesPerBundle = intval($_POST['pieces_per_bundle'] ?? 0);
        if ($piecesPerBundle > 0) {
            $metadata = json_encode(['pieces_per_bundle' => $piecesPerBundle]);
        }
    }
    
    $errors = [];
    
    // Validation
    if (empty($code)) $errors[] = 'Property code is required.';
    if (empty($name)) $errors[] = 'Property name is required.';
    if (empty($category)) $errors[] = 'Category is required.';
    
    if (!$isAddon) {
        if (empty($propertyType)) $errors[] = 'Property type is required.';
        if (!in_array($propertyType, [PROPERTY_TYPE_UNIT_BASED, PROPERTY_TYPE_METER_BASED, PROPERTY_TYPE_BUNDLE_BASED])) {
            $errors[] = 'Invalid property type.';
        }
    } else {
        if (!in_array($calculationMethod, [CALC_METHOD_FIXED, CALC_METHOD_PERCENTAGE])) {
            $errors[] = 'Invalid calculation method.';
        }
    }
    
    if ($defaultPrice < 0) $errors[] = 'Default price cannot be negative.';
    
    if (!in_array($category, ['alusteel', 'aluminum', 'kzinc'])) {
        $errors[] = 'Invalid category.';
    }
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=production_properties_create');
        exit();
    }
    
    $propertyModel = new ProductionProperty();
    
    // Check if code already exists
    $existing = $propertyModel->findByCode($code);
    if ($existing) {
        setFlashMessage('error', 'A property with this code already exists.');
        header('Location: /new-stock-system/index.php?page=production_properties_create');
        exit();
    }
    
    $currentUser = getCurrentUser();
    
    $data = [
        'code' => $code,
        'name' => $name,
        'category' => $category,
        'property_type' => $propertyType,
        'is_addon' => $isAddon,
        'calculation_method' => $calculationMethod,
        'applies_to' => $appliesTo,
        'is_refundable' => $isRefundable,
        'display_section' => $displaySection,
        'default_price' => $defaultPrice,
        'sort_order' => $sortOrder,
        'metadata' => $metadata,
        'is_active' => $isActive,
        'created_by' => $currentUser['id']
    ];
    
    if ($propertyModel->create($data)) {
        $propertyTypeLabel = $isAddon ? 'Add-on' : 'Production property';
        logActivity("$propertyTypeLabel created", "Code: $code, Category: $category");
        setFlashMessage('success', "$propertyTypeLabel created successfully!");
        header('Location: /new-stock-system/index.php?page=production_properties');
    } else {
        setFlashMessage('error', 'Failed to create property.');
        header('Location: /new-stock-system/index.php?page=production_properties_create');
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=production_properties_create');
exit();