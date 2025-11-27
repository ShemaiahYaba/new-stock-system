<?php
/**
 * ============================================
 * FIX #1: CREATE views/tiles/stock/index.php
 * Stock overview page (was missing!)
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_product.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Stock Overview - ' . APP_NAME;

$productModel = new TileProduct();
$products = $productModel->getAll([], 1000, 0);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Overview</h1>
                <p class="text-muted">Current stock levels for all tile products</p>
            </div>
            <div>
                <?php if (hasPermission(MODULE_TILE_MANAGEMENT, ACTION_CREATE)): ?>
                <a href="/new-stock-system/index.php?page=tile_stock_add" class="btn btn-success me-2">
                    <i class="bi bi-plus-circle"></i> Add Stock
                </a>
                <?php endif; ?>
                <a href="/new-stock-system/index.php?page=tile_stock_card" class="btn btn-outline-primary">
                    <i class="bi bi-journal-text"></i> Stock Card
                </a>
            </div>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <?php
        $totalStock = 0;
        $availableProducts = 0;
        $outOfStock = 0;
        
        foreach ($products as $product) {
            $stock = floatval($product['current_stock'] ?? 0);
            $totalStock += $stock;
            if ($stock > 0) $availableProducts++;
            else $outOfStock++;
        }
        ?>
        
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Products</h6>
                    <h2><?= count($products) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Stock</h6>
                    <h2><?= number_format($totalStock, 0) ?> pcs</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Available</h6>
                    <h2><?= $availableProducts ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Out of Stock</h6>
                    <h2><?= $outOfStock ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-table"></i> Stock Levels
        </div>
        <div class="card-body p-0">
            <?php if (empty($products)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No products found. Create products first.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product Code</th>
                            <th>Design</th>
                            <th>Color</th>
                            <th>Gauge</th>
                            <th class="text-end">Current Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <?php
                        $stock = floatval($product['current_stock'] ?? 0);
                        $stockClass = 'danger';
                        if ($stock > 100) $stockClass = 'success';
                        elseif ($stock > 0) $stockClass = 'warning';
                        ?>
                        <tr>
                            <td><code><?= htmlspecialchars($product['code']) ?></code></td>
                            <td><?= htmlspecialchars($product['design_name']) ?></td>
                            <td>
                                <?php if (!empty($product['color_hex'])): ?>
                                <span style="display: inline-block; width: 15px; height: 15px; 
                                      background: <?= htmlspecialchars($product['color_hex']) ?>; 
                                      border: 1px solid #ddd; border-radius: 3px; margin-right: 5px;"></span>
                                <?php endif; ?>
                                <?= htmlspecialchars($product['color_name']) ?>
                            </td>
                            <td><span class="badge bg-secondary"><?= strtoupper($product['gauge']) ?></span></td>
                            <td class="text-end">
                                <strong class="text-<?= $stockClass ?>"><?= number_format($stock, 2) ?></strong> pcs
                            </td>
                            <td>
                                <?php if ($product['status'] === 'available'): ?>
                                <span class="badge bg-success">Available</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/new-stock-system/index.php?page=tile_products_view&id=<?= $product['id'] ?>" 
                                       class="btn btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (hasPermission(MODULE_TILE_MANAGEMENT, ACTION_CREATE)): ?>
                                    <a href="/new-stock-system/index.php?page=tile_stock_add&product_id=<?= $product['id'] ?>" 
                                       class="btn btn-success" title="Add Stock">
                                        <i class="bi bi-plus"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="/new-stock-system/index.php?page=tile_stock_card&product_id=<?= $product['id'] ?>" 
                                       class="btn btn-secondary" title="Stock Card">
                                        <i class="bi bi-journal-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
