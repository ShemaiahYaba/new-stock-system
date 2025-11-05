<?php
/**
 * Dashboard View
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/customer.php';
require_once __DIR__ . '/../models/coil.php';
require_once __DIR__ . '/../models/sale.php';

$pageTitle = 'Dashboard - ' . APP_NAME;

$userModel = new User();
$customerModel = new Customer();
$coilModel = new Coil();
$saleModel = new Sale();

$totalUsers = $userModel->count();
$totalCustomers = $customerModel->count();
$totalCoils = $coilModel->count();
$totalSales = $saleModel->count();

$currentUser = getCurrentUser();

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?>!</p>
    </div>
    
    <div class="row g-4 mb-4">
        <?php if (hasPermission(MODULE_USER_MANAGEMENT)): ?>
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white">Total Users</h6>
                            <h2 class="mb-0"><?php echo $totalUsers; ?></h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (hasPermission(MODULE_CUSTOMER_MANAGEMENT)): ?>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white">Total Customers</h6>
                            <h2 class="mb-0"><?php echo $totalCustomers; ?></h2>
                        </div>
                        <i class="bi bi-person-badge" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (hasPermission(MODULE_STOCK_MANAGEMENT)): ?>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white">Total Coils</h6>
                            <h2 class="mb-0"><?php echo $totalCoils; ?></h2>
                        </div>
                        <i class="bi bi-box-seam" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (hasPermission(MODULE_SALES_MANAGEMENT)): ?>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white">Total Sales</h6>
                            <h2 class="mb-0"><?php echo $totalSales; ?></h2>
                        </div>
                        <i class="bi bi-cart" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Quick Links
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE)): ?>
                        <div class="col-md-3 mb-3">
                            <a href="/new-stock-system/index.php?page=coils_create" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add New Coil
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (hasPermission(MODULE_SALES_MANAGEMENT, ACTION_CREATE)): ?>
                        <div class="col-md-3 mb-3">
                            <a href="/new-stock-system/index.php?page=sales_create" class="btn btn-success w-100">
                                <i class="bi bi-cart-plus"></i> New Sale
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (hasPermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_CREATE)): ?>
                        <div class="col-md-3 mb-3">
                            <a href="/new-stock-system/index.php?page=customers_create" class="btn btn-info w-100">
                                <i class="bi bi-person-plus"></i> Add Customer
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (hasPermission(MODULE_REPORTS)): ?>
                        <div class="col-md-3 mb-3">
                            <a href="/new-stock-system/index.php?page=reports" class="btn btn-warning w-100">
                                <i class="bi bi-graph-up"></i> View Reports
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
