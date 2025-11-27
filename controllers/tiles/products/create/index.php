<?php
/**
 * ============================================
 * FILE: controllers/tiles/products/create/index.php
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/tile_product.php';
require_once __DIR__ . '/../../../../models/design.php';
require_once __DIR__ . '/../../../../models/color.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_TILE_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=tile_products_create');
        exit();
    }
    
    $designId = (int)($_POST['design_id'] ?? 0);
    $colorId = (int)($_POST['color_id'] ?? 0);
    $gauge = sanitize($_POST['gauge'] ?? '');
    
    $errors = [];
    
    if ($designId <= 0) $errors[] = 'Please select a design.';
    if ($colorId <= 0) $errors[] = 'Please select a color.';
    if (!in_array($gauge, ['thick', 'normal', 'light'])) $errors[] = 'Invalid gauge selected.';
    
    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=tile_products_create');
        exit();
    }
    
    $productModel = new TileProduct();
    $designModel = new Design();
    $colorModel = new Color();
    
    // Verify design and color exist
    $design = $designModel->findById($designId);
    $color = $colorModel->findById($colorId);
    
    if (!$design || !$color) {
        setFlashMessage('error', 'Invalid design or color selected.');
        header('Location: /new-stock-system/index.php?page=tile_products_create');
        exit();
    }
    
    // Check if product combination already exists
    if ($productModel->exists($designId, $colorId, $gauge)) {
        setFlashMessage('error', 'This product combination (Design + Color + Gauge) already exists.');
        header('Location: /new-stock-system/index.php?page=tile_products_create');
        exit();
    }
    
    // Generate product code
    $code = $productModel->generateCode($design['code'], $color['code'], $gauge);
    
    $currentUser = getCurrentUser();
    
    $data = [
        'code' => $code,
        'design_id' => $designId,
        'color_id' => $colorId,
        'gauge' => $gauge,
        'status' => 'out_of_stock',
        'created_by' => $currentUser['id']
    ];
    
    if ($productModel->create($data)) {
        logActivity('Tile product created', "Code: $code");
        setFlashMessage('success', 'Tile product created successfully!');
        header('Location: /new-stock-system/index.php?page=tile_products');
    } else {
        setFlashMessage('error', 'Failed to create tile product.');
        header('Location: /new-stock-system/index.php?page=tile_products_create');
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=tile_products_create');
exit();
