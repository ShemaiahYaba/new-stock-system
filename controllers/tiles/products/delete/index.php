<?php
/**
 * ============================================
 * FILE: controllers/tiles/products/delete/index.php
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/tile_product.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_TILE_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=tile_products');
        exit();
    }
    
    $productId = (int)($_POST['id'] ?? 0);
    
    if ($productId <= 0) {
        setFlashMessage('error', 'Invalid product ID.');
        header('Location: /new-stock-system/index.php?page=tile_products');
        exit();
    }
    
    $productModel = new TileProduct();
    $product = $productModel->findById($productId);
    
    if (!$product) {
        setFlashMessage('error', 'Product not found.');
        header('Location: /new-stock-system/index.php?page=tile_products');
        exit();
    }
    
    // Check if product has stock
    $currentStock = $productModel->getCurrentStock($productId);
    if ($currentStock > 0) {
        setFlashMessage('error', "Cannot delete product with stock balance of $currentStock pieces.");
        header('Location: /new-stock-system/index.php?page=tile_products');
        exit();
    }
    
    if ($productModel->delete($productId)) {
        logActivity('Tile product deleted', "Code: {$product['code']}");
        setFlashMessage('success', 'Tile product deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete product.');
    }
    
    header('Location: /new-stock-system/index.php?page=tile_products');
    exit();
}

header('Location: /new-stock-system/index.php?page=tile_products');
exit();
