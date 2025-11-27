<?php
/**
 * ============================================
 * FILE: views/tiles/products/view.php
 * Product details with stock card
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_product.php';
require_once __DIR__ . '/../../../models/tile_stock_ledger.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Product Details - ' . APP_NAME;

$productId = (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    setFlashMessage('error', 'Invalid product ID.');
    header('Location: /new-stock-system/index.php?page=tile_products');
    exit();
}

$productModel = new TileProduct();
$ledgerModel = new TileStockLedger();

$product = $productModel->findById($productId);
if (!$product) {
    setFlashMessage('error', 'Product not found.');
    header('Location: /new-stock-system/index.php?page=tile_products');
    exit();
}

$ledgerEntries = $ledgerModel->getByProduct($productId, 20, 0);
$currentStock = $productModel->getCurrentStock($productId);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Product Details</h1>
                <p class="text-muted"><?= htmlspecialchars($product['code']) ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=tile_products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Color preview -->
                    <div class="mb-3 mx-auto" style="width: 120px; height: 120px; 
                         background: <?= htmlspecialchars($product['color_hex'] ?? '#cccccc') ?>; 
                         border: 3px solid #dee2e6; border-radius: 8px;"></div>
                    
                    <h4><?= htmlspecialchars($product['design_name']) ?></h4>
                    <p class="text-muted mb-2"><?= htmlspecialchars($product['color_name']) ?></p>
                    <span class="badge bg-secondary mb-3"><?= strtoupper($product['gauge']) ?> Gauge</span>
                    
                    <div class="mb-3">
                        <?php if ($product['status'] === 'available'): ?>
                        <span class="badge bg-success fs-6">Available</span>
                        <?php else: ?>
                        <span class="badge bg-secondary fs-6">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="text-primary"><?= number_format($currentStock, 1) ?></h3>
                    <p class="text-muted">Pieces in Stock</p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Product Information
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Code:</th>
                            <td><code><?= htmlspecialchars($product['code']) ?></code></td>
                        </tr>
                        <tr>
                            <th>Design:</th>
                            <td><?= htmlspecialchars($product['design_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Color:</th>
                            <td><?= htmlspecialchars($product['color_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Gauge:</th>
                            <td><?= htmlspecialchars(TILE_GAUGES[$product['gauge']]) ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?= formatDate($product['created_at']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if (hasPermission(MODULE_TILE_MANAGEMENT, ACTION_CREATE)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Quick Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="/new-stock-system/index.php?page=tile_stock_add&product_id=<?= $product['id'] ?>" 
                       class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Stock
                    </a>
                    <?php if ($currentStock > 0 && hasPermission(MODULE_TILE_SALES, ACTION_CREATE)): ?>
                    <a href="/new-stock-system/index.php?page=tile_sales_create&product_id=<?= $product['id'] ?>" 
                       class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Create Sale
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-journal-text"></i> Stock Card - Transaction History
                </div>
                <div class="card-body">
                    <?php if (empty($ledgerEntries)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No stock transactions yet. Add stock to get started.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Code</th>
                                    <th class="text-end">IN</th>
                                    <th class="text-end">OUT</th>
                                    <th class="text-end">Balance</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Reverse the array to show newest first
                                $reversedEntries = array_reverse($ledgerEntries);
                                foreach ($reversedEntries as $entry): 
                                ?>
                                <tr>
                                    <td><?= formatDate($entry['created_at'], 'm/d/y') ?></td>
                                    <td>
                                        <?php if ($entry['reference_type'] === 'stock_in'): ?>
                                        <span class="badge bg-success">IN</span>
                                        <?php else: ?>
                                        <span class="badge bg-danger">OUT</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small><?= htmlspecialchars($entry['transaction_code'] ?: '-') ?></small></td>
                                    <td class="text-end">
                                        <?php if ($entry['quantity_in'] > 0): ?>
                                        <strong class="text-success">+<?= number_format($entry['quantity_in'], 1) ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($entry['quantity_out'] > 0): ?>
                                        <strong class="text-danger">-<?= number_format($entry['quantity_out'], 1) ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <strong><?= number_format($entry['balance'], 1) ?></strong>
                                    </td>
                                    <td><small class="text-muted"><?= htmlspecialchars(truncateText($entry['description'] ?? '', 40)) ?></small></td>
                                </tr>
                                <?php endforeach; unset($reversedEntries); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="/new-stock-system/index.php?page=tile_stock_card&product_id=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-outline-primary">
                            View Full Stock Card <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>

