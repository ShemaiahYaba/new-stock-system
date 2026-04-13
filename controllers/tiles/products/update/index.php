<?php
/**
 * ============================================
 * FILE: controllers/tiles/products/update/index.php
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

requirePermission(MODULE_TILE_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=tile_products');
        exit();
    }

    $productId = (int)($_POST['id'] ?? 0);
    $designId  = (int)($_POST['design_id'] ?? 0);
    $colorId   = (int)($_POST['color_id'] ?? 0);
    $gauge     = sanitize($_POST['gauge'] ?? '');
    $status    = sanitize($_POST['status'] ?? '');

    $errors = [];

    if ($productId <= 0)                                    $errors[] = 'Invalid product.';
    if ($designId <= 0)                                     $errors[] = 'Please select a design.';
    if ($colorId  <= 0)                                     $errors[] = 'Please select a color.';
    if (!array_key_exists($gauge, TILE_GAUGES))             $errors[] = 'Invalid gauge selected.';
    if (!array_key_exists($status, TILE_STOCK_STATUS))      $errors[] = 'Invalid status selected.';

    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header("Location: /new-stock-system/index.php?page=tile_products_edit&id=$productId");
        exit();
    }

    $productModel = new TileProduct();
    $designModel  = new Design();
    $colorModel   = new Color();

    $product = $productModel->findById($productId);
    if (!$product) {
        setFlashMessage('error', 'Product not found.');
        header('Location: /new-stock-system/index.php?page=tile_products');
        exit();
    }

    $design = $designModel->findById($designId);
    $color  = $colorModel->findById($colorId);

    if (!$design || !$color) {
        setFlashMessage('error', 'Invalid design or color selected.');
        header("Location: /new-stock-system/index.php?page=tile_products_edit&id=$productId");
        exit();
    }

    // Regenerate code from new combination
    $newCode = $productModel->generateCode($design['code'], $color['code'], $gauge);

    // Check if new code conflicts with a DIFFERENT product
    $existing = $productModel->findByCode($newCode);
    if ($existing && $existing['id'] != $productId) {
        setFlashMessage('error', "A product with code <strong>$newCode</strong> already exists.");
        header("Location: /new-stock-system/index.php?page=tile_products_edit&id=$productId");
        exit();
    }

    $data = [
        'code'      => $newCode,
        'design_id' => $designId,
        'color_id'  => $colorId,
        'gauge'     => $gauge,
        'status'    => $status,
    ];

    if ($productModel->update($productId, $data)) {
        logActivity('Tile product updated', "ID: $productId, Code: $newCode");
        setFlashMessage('success', 'Product updated successfully!');
        header("Location: /new-stock-system/index.php?page=tile_products_view&id=$productId");
    } else {
        setFlashMessage('error', 'Failed to update product.');
        header("Location: /new-stock-system/index.php?page=tile_products_edit&id=$productId");
    }
    exit();
}

header('Location: /new-stock-system/index.php?page=tile_products');
exit();
