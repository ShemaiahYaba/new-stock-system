<?php
/**
 * Reports View - Updated with Tiles Module Stats
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/coil.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../models/stock_ledger.php';
require_once __DIR__ . '/../../models/tile_product.php';
require_once __DIR__ . '/../../models/tile_sale.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Reports - ' . APP_NAME;

// Get statistics for coil/sheet module
$saleModel = new Sale();
$coilModel = new Coil();
$customerModel = new Customer();
$stockEntryModel = new StockEntry();
$stockLedgerModel = new StockLedger();

$totalSales = $saleModel->count();
$totalRevenue = $saleModel->getTotalSalesAmount();
$totalCoils = $coilModel->count();
$totalCustomers = $customerModel->count();

// Monthly revenue
$currentMonth = date('Y-m-01 00:00:00');
$endOfMonth = date('Y-m-t 23:59:59');
$monthlyRevenue = $saleModel->getTotalSalesAmount($currentMonth, $endOfMonth);

// Get stock entry statistics by status
$availableStock = $stockEntryModel->getByStatus(STOCK_STATUS_AVAILABLE);
$factoryUseStock = $stockEntryModel->getByStatus(STOCK_STATUS_FACTORY_USE);
$soldStock = $stockEntryModel->countByStatus(STOCK_STATUS_SOLD);

// Calculate total meters by status
$totalAvailableMeters = array_sum(array_column($availableStock, 'meters_remaining'));
$totalFactoryUseMeters = 0;
foreach ($factoryUseStock as $entry) {
    $balance = $stockLedgerModel->getCurrentBalance($entry['id']);
    $totalFactoryUseMeters += $balance;
}

// ===== NEW: Get Tile Module Statistics =====
$tileProductModel = new TileProduct();
$tileSaleModel = new TileSale();

$totalTileProducts = $tileProductModel->count();
$totalTileSales = $tileSaleModel->count();

// Calculate total tiles in stock
$allTileProducts = $tileProductModel->getAll([], 10000, 0);
$totalTilesInStock = 0;
foreach ($allTileProducts as $product) {
    $totalTilesInStock += floatval($product['current_stock'] ?? 0);
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="text-muted">View system statistics and reports</p>
    </div>
    
    <!-- Coil/Sheet Module Stats -->
    <div class="mb-4">
        <h5 class="mb-3"><i class="bi bi-layers"></i> Coil & Sheet Module</h5>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title text-white">Total Sales</h6>
                        <h2 class="mb-0"><?php echo $totalSales; ?></h2>
                        <small>All time</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title text-white">Total Revenue</h6>
                        <h2 class="mb-0"><?php echo formatCurrency($totalRevenue); ?></h2>
                        <small>All time</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6 class="card-title text-white">Monthly Revenue</h6>
                        <h2 class="mb-0"><?php echo formatCurrency($monthlyRevenue); ?></h2>
                        <small><?php echo date('F Y'); ?></small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title text-white">Total Customers</h6>
                        <h2 class="mb-0"><?php echo $totalCustomers; ?></h2>
                        <small>Active customers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- NEW: Roofing Tiles Module Stats -->
    <div class="mb-4">
        <h5 class="mb-3"><i class="bi bi-grid-3x3"></i> Roofing Tiles Module</h5>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-purple" style="background-color: #6f42c1;">
                    <div class="card-body">
                        <h6 class="card-title text-white">Tile Products</h6>
                        <h2 class="mb-0"><?php echo $totalTileProducts; ?></h2>
                        <small>Product variants</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-white bg-teal" style="background-color: #20c997;">
                    <div class="card-body">
                        <h6 class="card-title text-white">Tiles in Stock</h6>
                        <h2 class="mb-0"><?php echo number_format($totalTilesInStock, 0); ?></h2>
                        <small>Total pieces</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <h6 class="card-title text-white">Tile Sales</h6>
                        <h2 class="mb-0"><?php echo $totalTileSales; ?></h2>
                        <small>All time</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-2 border-primary">
                    <div class="card-body text-center">
                        <a href="/new-stock-system/index.php?page=tile_products" 
                           class="text-decoration-none">
                            <i class="bi bi-grid-3x3 text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-0">View Tile Products</h6>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-box-seam"></i> Coil Categories
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (STOCK_CATEGORIES as $catKey => $catName): ?>
                            <tr>
                                <td><?php echo $catName; ?></td>
                                <td><strong><?php echo $coilModel->countByCategory(
                                    $catKey,
                                ); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-active">
                                <td><strong>Total Coils</strong></td>
                                <td><strong><?php echo $totalCoils; ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-graph-up"></i> Stock Entry Status
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Entries</th>
                                <th>Total Meters</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="badge bg-success">
                                        Available
                                    </span>
                                </td>
                                <td><strong><?php echo count($availableStock); ?></strong></td>
                                <td><strong><?php echo number_format(
                                    $totalAvailableMeters,
                                    2,
                                ); ?>m</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge bg-warning">
                                        Factory Use
                                    </span>
                                </td>
                                <td><strong><?php echo count($factoryUseStock); ?></strong></td>
                                <td><strong><?php echo number_format(
                                    $totalFactoryUseMeters,
                                    2,
                                ); ?>m</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge bg-primary">
                                        Sold
                                    </span>
                                </td>
                                <td><strong><?php echo $soldStock; ?></strong></td>
                                <td><span class="text-muted">Completed</span></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Total Stock Entries</strong></td>
                                <td colspan="2"><strong><?php echo $stockEntryModel->count(); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-speedometer2"></i> Quick Stats
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4 class="text-primary"><?php echo $totalCoils; ?></h4>
                            <p class="text-muted mb-0">Total Coils Registered</p>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-success"><?php echo number_format(
                                $totalAvailableMeters,
                                2,
                            ); ?>m</h4>
                            <p class="text-muted mb-0">Available for Sale</p>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-warning"><?php echo number_format(
                                $totalFactoryUseMeters,
                                2,
                            ); ?>m</h4>
                            <p class="text-muted mb-0">In Factory Use</p>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-info"><?php echo $totalSales; ?></h4>
                            <p class="text-muted mb-0">Completed Sales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-download"></i> Export Options
                </div>
                <div class="card-body">
                    <p class="text-muted">Export reports and data (Feature coming soon)</p>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-file-earmark-pdf"></i> Export to PDF
                        </button>
                        <button class="btn btn-outline-success" disabled>
                            <i class="bi bi-file-earmark-excel"></i> Export to Excel
                        </button>
                        <button class="btn btn-outline-info" disabled>
                            <i class="bi bi-file-earmark-text"></i> Export to CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>