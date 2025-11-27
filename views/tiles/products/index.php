<?php
/**
 * ============================================
 * FILE: views/tiles/products/index.php
 * List all tile products with filters
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_product.php';
require_once __DIR__ . '/../../../models/design.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Tile Products - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$filters = [
    'design_id' => isset($_GET['design_id']) ? (int)$_GET['design_id'] : null,
    'color_id' => isset($_GET['color_id']) ? (int)$_GET['color_id'] : null,
    'gauge' => $_GET['gauge'] ?? null,
    'status' => $_GET['status'] ?? null,
];

$productModel = new TileProduct();
$designModel = new Design();
$colorModel = new Color();

$products = $productModel->getAll($filters, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
$totalProducts = $productModel->count($filters);
$paginationData = getPaginationData($totalProducts, $currentPage);

// Get filter options
$designs = $designModel->getActive();
$colors = $colorModel->getActive();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Tile Products</h1>
                <p class="text-muted">Manage tile product variants (<?= $totalProducts ?> total)</p>
            </div>
            <?php if (hasPermission(MODULE_TILE_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=tile_products_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Product
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="/new-stock-system/index.php" class="row g-3">
                <input type="hidden" name="page" value="tile_products">
                
                <div class="col-md-3">
                    <label class="form-label small">Design</label>
                    <select class="form-select form-select-sm" name="design_id">
                        <option value="">All Designs</option>
                        <?php foreach ($designs as $design): ?>
                        <option value="<?= $design['id'] ?>" <?= $filters['design_id'] == $design['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($design['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small">Color</label>
                    <select class="form-select form-select-sm" name="color_id">
                        <option value="">All Colors</option>
                        <?php foreach ($colors as $color): ?>
                        <option value="<?= $color['id'] ?>" <?= $filters['color_id'] == $color['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($color['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">Gauge</label>
                    <select class="form-select form-select-sm" name="gauge">
                        <option value="">All Gauges</option>
                        <?php foreach (TILE_GAUGES as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['gauge'] === $key ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All Status</option>
                        <?php foreach (TILE_STOCK_STATUS as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="/new-stock-system/index.php?page=tile_products" class="btn btn-sm btn-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="row g-3">
        <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No products found. Create your first product to get started.
            </div>
        </div>
        <?php else: ?>
        <?php foreach ($products as $product): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <!-- Color indicator -->
                    <div class="mb-2" style="height: 40px; background: <?= htmlspecialchars($product['color_hex'] ?? '#cccccc') ?>; 
                         border-radius: 4px; border: 1px solid #dee2e6;"></div>
                    
                    <h6 class="card-title">
                        <code class="small"><?= htmlspecialchars($product['code']) ?></code>
                    </h6>
                    
                    <p class="card-text small mb-2">
                        <strong><?= htmlspecialchars($product['design_name']) ?></strong><br>
                        <span class="text-muted"><?= htmlspecialchars($product['color_name']) ?></span> • 
                        <span class="badge bg-secondary"><?= strtoupper($product['gauge']) ?></span>
                    </p>
                    
                    <!-- Stock info -->
                    <div class="mb-2">
                        <?php
                        $stock = floatval($product['current_stock']);
                        $stockClass = 'secondary';
                        if ($stock > 100) $stockClass = 'success';
                        elseif ($stock > 0) $stockClass = 'warning';
                        else $stockClass = 'danger';
                        ?>
                        <span class="badge bg-<?= $stockClass ?> w-100">
                            <?= number_format($stock, 1) ?> pieces
                        </span>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <?php if ($product['status'] === 'available'): ?>
                        <span class="badge bg-success w-100">Available</span>
                        <?php else: ?>
                        <span class="badge bg-secondary w-100">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="/new-stock-system/index.php?page=tile_products_view&id=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        
                        <?php if (hasPermission(MODULE_TILE_MANAGEMENT, ACTION_CREATE)): ?>
                        <a href="/new-stock-system/index.php?page=tile_stock_add&product_id=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-plus"></i> Add Stock
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($products) && $totalProducts > RECORDS_PER_PAGE): ?>
    <div class="card mt-3">
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../../layout/pagination.php'; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>

