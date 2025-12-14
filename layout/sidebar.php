<?php
/**
 * Sidebar Layout Component
 *
 * Navigation sidebar with permission-based menu items
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

$currentPage = $_GET['page'] ?? 'dashboard';
?>

<style>
    .sidebar {
        position: fixed;
        top: 56px;
        left: 0;
        bottom: 0;
        width: var(--sidebar-width);
        background: var(--primary-color);
        overflow-y: auto;
        transition: transform 0.3s;
        z-index: 1000;
    }
    
    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-nav .nav-item {
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar-nav .nav-link {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .sidebar-nav .nav-link:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }
    
    .sidebar-nav .nav-link.active {
        background: var(--secondary-color);
        color: white;
    }
    
    .sidebar-nav .nav-link i {
        margin-right: 10px;
        font-size: 18px;
    }
    
    .sidebar-header {
        padding: 20px;
        background: rgba(0,0,0,0.2);
        color: white;
        font-weight: 600;
        text-align: center;
    }
    
    .logout-container {
        position: sticky;
        bottom: 0;
        width: 100%;
        background: var(--primary-color);
        border-top: 1px solid rgba(255,255,255,0.1);
        margin-top: auto;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>

<aside class="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-grid-3x3-gap"></i> Navigation
    </div>
    
    <ul class="sidebar-nav">
        <!-- Dashboard - Available to all authenticated users -->
        <?php if (hasPermission(MODULE_DASHBOARD)): ?>
        <li class="nav-item">
            <a href="/new-stock-system/index.php?page=dashboard" class="nav-link <?php echo $currentPage ===
            'dashboard'
                ? 'active'
                : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <?php endif; ?>
        
        <!-- User Management -->
        <?php if (hasPermission(MODULE_USER_MANAGEMENT)): ?>
        <li class="nav-item">
            <a href="/new-stock-system/index.php?page=users" class="nav-link <?php echo $currentPage ===
            'users'
                ? 'active'
                : ''; ?>">
                <i class="bi bi-people"></i>
                <span>User Management</span>
            </a>
        </li>
        <?php endif; ?>
        
        <!-- Customer Management -->
        <?php if (hasPermission(MODULE_CUSTOMER_MANAGEMENT)): ?>
        <li class="nav-item">
            <a href="/new-stock-system/index.php?page=customers" class="nav-link <?php echo $currentPage ===
            'customers'
                ? 'active'
                : ''; ?>">
                <i class="bi bi-person-badge"></i>
                <span>Customers</span>
            </a>
        </li>
        <?php endif; ?>

         <!-- Warehouse Management -->
        <?php if (hasPermission(MODULE_WAREHOUSE_MANAGEMENT)): ?>
        <li class="nav-item">
            <a href="/new-stock-system/index.php?page=warehouses" class="nav-link <?php echo $currentPage ===
            'warehouses'
                ? 'active'
                : ''; ?>">
                <i class="bi bi-person-badge"></i>
                <span>Warehouses</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- Color Management -->
        <?php if (hasPermission(MODULE_COLOR_MANAGEMENT)): ?>
        <li class="nav-item">
            <a href="/new-stock-system/index.php?page=colors" class="nav-link <?php echo $currentPage === 'colors' ? 'active' : ''; ?>">
                <i class="bi bi-palette"></i>
                <span>Color Management</span>
            </a>
        </li>
        <?php endif; ?>
        
        <!-- Stock Management -->
        <?php if (hasPermission(MODULE_STOCK_MANAGEMENT)): ?>
        <li class="nav-item">
            <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#stockMenu">
                <i class="bi bi-box-seam"></i>
                <span>Stock Management</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled ps-4" id="stockMenu">
                <li>
                    <a href="/new-stock-system/index.php?page=coils" class="nav-link <?php echo $currentPage ===
                    'coils'
                        ? 'active'
                        : ''; ?>">
                        <i class="bi bi-circle"></i> All Coils
                    </a>
                </li>
                <li>
                    <a href="/new-stock-system/index.php?page=coils&category=<?php echo STOCK_CATEGORY_ALUSTEEL; ?>" class="nav-link">
                        <i class="bi bi-circle"></i> Alusteel
                    </a>
                </li>
                <li>
                    <a href="/new-stock-system/index.php?page=coils&category=<?php echo STOCK_CATEGORY_ALUMINUM; ?>" class="nav-link">
                        <i class="bi bi-circle"></i> Aluminum
                    </a>
                </li>
                <li>
                    <a href="/new-stock-system/index.php?page=coils&category=<?php echo STOCK_CATEGORY_KZINC; ?>" class="nav-link">
                        <i class="bi bi-circle"></i> K-Zinc
                    </a>
                </li>
                <li>
                    <a href="/new-stock-system/index.php?page=stock_entries" class="nav-link <?php echo $currentPage ===
                    'stock_entries'
                        ? 'active'
                        : ''; ?>">
                        <i class="bi bi-circle"></i> Stock Entries
                    </a>
                </li>
                
            </ul>
        </li>
        <?php endif; ?>
        
        <!-- Sales Management -->
<?php if (hasPermission(MODULE_SALES_MANAGEMENT)): ?>
<li class="nav-item">
    <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#salesMenu">
        <i class="bi bi-cart"></i>
        <span>Sales</span>
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul class="collapse list-unstyled ps-4" id="salesMenu">
        <li>
            <a href="/new-stock-system/index.php?page=sales" class="nav-link">
                <i class="bi bi-circle"></i> All Sales
            </a>
        </li>
        <li>
            <a href="/new-stock-system/index.php?page=production" class="nav-link">
                <i class="bi bi-circle"></i> Production
            </a>
        </li>
        <li>
            <a href="/new-stock-system/index.php?page=supply" class="nav-link">
                <i class="bi bi-circle"></i> Supply / Delivery
            </a>
        </li>
        <li>
            <a href="/new-stock-system/index.php?page=invoices" class="nav-link">
                <i class="bi bi-circle"></i> Invoices
            </a>
        </li>
        <li>
            <a href="/new-stock-system/index.php?page=receipts" class="nav-link">
                <i class="bi bi-circle"></i> Receipts
            </a>
        </li>
    </ul>
</li>
<?php endif; ?>
<!-- Tile Management -->
<?php if (hasPermission(MODULE_TILE_MANAGEMENT) || hasPermission(MODULE_TILE_SALES)): ?>
<li class="nav-item">
    <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#tilesMenu">
        <i class="bi bi-grid-3x3"></i>
        <span>Roofing Tiles</span>
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul class="collapse list-unstyled ps-4" id="tilesMenu">
        <?php if (hasPermission(MODULE_DESIGN_MANAGEMENT)): ?>
        <li>
            <a href="/new-stock-system/index.php?page=designs" class="nav-link">
                <i class="bi bi-circle"></i> Designs
            </a>
        </li>
        <?php endif; ?>
        
        <?php if (hasPermission(MODULE_TILE_MANAGEMENT)): ?>
        <li>
            <a href="/new-stock-system/index.php?page=tile_products" class="nav-link">
                <i class="bi bi-circle"></i> Products
            </a>
        </li>
        <li>
            <a href="/new-stock-system/index.php?page=tile_stock" class="nav-link">
                <i class="bi bi-circle"></i> Stock Overview
            </a>
        </li>
        <li>
            <a href="/new-stock-system/index.php?page=tile_stock_card" class="nav-link">
                <i class="bi bi-circle"></i> Stock Card
            </a>
        </li>
        <?php endif; ?>
        
        <?php if (hasPermission(MODULE_TILE_SALES)): ?>
        <li>
            <a href="/new-stock-system/index.php?page=tile_sales" class="nav-link">
                <i class="bi bi-circle"></i> Sales
            </a>
        </li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>
        
        <!-- Reports -->
        <?php if (hasPermission(MODULE_REPORTS)): ?>
        <li class="nav-item">
            <a href="/new-stock-system/index.php?page=reports" class="nav-link <?php echo $currentPage ===
            'reports'
                ? 'active'
                : ''; ?>">
                <i class="bi bi-graph-up"></i>
                <span>Reports</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
    
    <!-- Logout Button -->
    <div class="logout-container">
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="/new-stock-system/logout.php" class="nav-link" style="color: #ff6b6b;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Bootstrap JS for collapse functionality -->
<script>
    // Auto-expand active menu
    document.addEventListener('DOMContentLoaded', function() {
        const activeLink = document.querySelector('.sidebar-nav .nav-link.active');
        if (activeLink) {
            const parentCollapse = activeLink.closest('.collapse');
            if (parentCollapse) {
                parentCollapse.classList.add('show');
            }
        }
    });
</script>
