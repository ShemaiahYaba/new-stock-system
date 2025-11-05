<?php
/**
 * Reports View
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/coil.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Reports - ' . APP_NAME;

// Get statistics
$saleModel = new Sale();
$coilModel = new Coil();
$customerModel = new Customer();

$totalSales = $saleModel->count();
$totalRevenue = $saleModel->getTotalSalesAmount();
$totalCoils = $coilModel->count();
$totalCustomers = $customerModel->count();

// Monthly revenue
$currentMonth = date('Y-m-01 00:00:00');
$endOfMonth = date('Y-m-t 23:59:59');
$monthlyRevenue = $saleModel->getTotalSalesAmount($currentMonth, $endOfMonth);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="text-muted">View system statistics and reports</p>
    </div>
    
    <div class="row g-4 mb-4">
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
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-box-seam"></i> Stock Overview
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
                                <td><strong><?php echo $coilModel->count($catKey); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-active">
                                <td><strong>Total</strong></td>
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
                    <i class="bi bi-graph-up"></i> Stock Status
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (STOCK_STATUSES as $statusKey => $statusName): ?>
                            <?php $statusCoils = $coilModel->getByStatus($statusKey); ?>
                            <tr>
                                <td>
                                    <span class="badge <?php echo getStatusBadgeClass($statusKey); ?>">
                                        <?php echo $statusName; ?>
                                    </span>
                                </td>
                                <td><strong><?php echo count($statusCoils); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
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
