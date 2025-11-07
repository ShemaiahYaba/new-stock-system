<?php
/**
 * Sales List View with Workflow Actions
 * File: views/sales/index.php
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/production.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../models/receipt.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Sales - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

$saleModel = new Sale();
$productionModel = new Production();
$invoiceModel = new Invoice();
$receiptModel = new Receipt();

if (!empty($searchQuery)) {
    $sales = $saleModel->search(
        $searchQuery,
        RECORDS_PER_PAGE,
        ($currentPage - 1) * RECORDS_PER_PAGE,
    );
    $totalSales = count($sales);
} else {
    $sales = $saleModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalSales = $saleModel->count();
}

// Enhance sales data with workflow status
foreach ($sales as &$sale) {
    // Get production status
    $production = $productionModel->findBySaleId($sale['id']);
    $sale['production_status'] = $production ? $production['status'] : null;
    $sale['production_id'] = $production ? $production['id'] : null;

    // Get invoice status
    $invoice = $invoiceModel->findBySaleId($sale['id']);
    $sale['invoice_status'] = $invoice ? $invoice['status'] : null;
    $sale['invoice_id'] = $invoice ? $invoice['id'] : null;

    // Get receipt info
    $sale['has_receipts'] = $invoice ? $receiptModel->count('', $invoice['id']) > 0 : false;
}

$paginationData = getPaginationData($totalSales, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Sales Management</h1>
                <p class="text-muted">Track and manage sales transactions</p>
            </div>
            <?php if (hasPermission(MODULE_SALES_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="?page=sales_create_new" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Sale
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-cart"></i> Sales List (<?= $totalSales ?> total)
                </div>
                <div class="col-md-6">
                    <form method="GET" action="index.php" class="d-flex">
                        <input type="hidden" name="page" value="sales">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search sales..." value="<?= htmlspecialchars(
                                   $searchQuery,
                               ) ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="?page=sales" class="btn btn-sm btn-secondary ms-2">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($sales)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No sales found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Coil</th>
                            <th>Type</th>
                            <th>Meters</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Workflow</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td>#<?= $sale['id'] ?></td>
                            <td><?= htmlspecialchars($sale['customer_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($sale['coil_code'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?= ucfirst($sale['sale_type'] ?? 'unknown') ?>
                                </span>
                            </td>
                            <td><?= number_format($sale['meters'], 2) ?>m</td>
                            <td><?= formatCurrency($sale['total_amount']) ?></td>
                            <td>
                                <span class="badge <?= $sale['status'] === 'completed'
                                    ? 'bg-success'
                                    : 'bg-warning' ?>">
                                    <?= ucfirst($sale['status'] ?? 'pending') ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Production Status -->
                                    <a href="<?= $sale['production_id']
                                        ? "?page=production&action=view&id={$sale['production_id']}"
                                        : '#' ?>" 
                                       class="text-decoration-none me-2" 
                                       title="Production: <?= $sale['production_status'] ?:
                                           'Not started' ?>">
                                        <i class="bi bi-gear-fill <?= $sale['production_status']
                                            ? 'text-success'
                                            : 'text-muted' ?>"></i>
                                    </a>
                                    
                                    <!-- Invoice Status -->
                                    <a href="<?= $sale['invoice_id']
                                        ? "?page=invoices&action=view&id={$sale['invoice_id']}"
                                        : '#' ?>" 
                                       class="text-decoration-none me-2" 
                                       title="Invoice: <?= $sale['invoice_status'] ?:
                                           'Not created' ?>">
                                        <i class="bi bi-receipt <?= $sale['invoice_status']
                                            ? 'text-primary'
                                            : 'text-muted' ?>"></i>
                                    </a>
                                    
                                    <!-- Receipts Status -->
                                    <a href="<?= $sale['invoice_id']
                                        ? "?page=receipts&invoice_id={$sale['invoice_id']}"
                                        : '#' ?>" 
                                       class="text-decoration-none" 
                                       title="<?= $sale['has_receipts']
                                           ? 'View Receipts'
                                           : 'No receipts' ?>">
                                        <i class="bi bi-cash-coin <?= $sale['has_receipts']
                                            ? 'text-success'
                                            : 'text-muted' ?>"></i>
                                    </a>
                                </div>
                            </td>
                            <td><?= formatDate($sale['created_at']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- View Details -->
                                    <?php if (
                                        hasPermission(MODULE_SALES_MANAGEMENT, ACTION_VIEW)
                                    ): ?>
                                    <a href="?page=sales_view&id=<?= $sale['id'] ?>" 
                                       class="btn btn-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <!-- Edit -->
                                    <?php if (
                                        hasPermission(MODULE_SALES_MANAGEMENT, ACTION_EDIT)
                                    ): ?>
                                    <a href="?page=sales_edit&id=<?= $sale['id'] ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <!-- Quick Actions Dropdown -->
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-lightning"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <!-- Production Action -->
                                            <li>
                                                <a class="dropdown-item <?= !$sale[
                                                    'production_status'
                                                ]
                                                    ? 'disabled'
                                                    : '' ?>" 
                                                   href="<?= $sale['production_id']
                                                       ? "?page=production&action=view&id={$sale['production_id']}"
                                                       : '#' ?>">
                                                    <i class="bi bi-gear me-2"></i> View Production
                                                </a>
                                            </li>
                                            
                                            <!-- Invoice Action -->
                                            <li>
                                                <a class="dropdown-item <?= !$sale['invoice_id']
                                                    ? 'disabled'
                                                    : '' ?>" 
                                                   href="<?= $sale['invoice_id']
                                                       ? "?page=invoices&action=view&id={$sale['invoice_id']}"
                                                       : '#' ?>">
                                                    <i class="bi bi-receipt me-2"></i> View Invoice
                                                </a>
                                            </li>
                                            
                                            <!-- Receipts Action -->
                                            <li>
                                                <a class="dropdown-item <?= !$sale['has_receipts']
                                                    ? 'disabled'
                                                    : '' ?>" 
                                                   href="<?= $sale['invoice_id']
                                                       ? "?page=receipts&invoice_id={$sale['invoice_id']}"
                                                       : '#' ?>">
                                                    <i class="bi bi-cash-coin me-2"></i> View Receipts
                                                </a>
                                            </li>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <!-- Create Invoice (if doesn't exist) -->
                                            <?php if (
                                                !$sale['invoice_id'] &&
                                                hasPermission(MODULE_INVOICES, ACTION_CREATE)
                                            ): ?>
                                            <li>
                                                <a class="dropdown-item" href="?page=invoices&action=create&sale_id=<?= $sale[
                                                    'id'
                                                ] ?>">
                                                    <i class="bi bi-file-earmark-plus me-2"></i> Create Invoice
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($sales)): ?>
        <div class="card-footer">
            <?php
            $queryParams = $_GET;
            include __DIR__ . '/../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

<style>
/* Add some spacing for workflow icons */
.workflow-icon {
    font-size: 1.2rem;
    margin: 0 2px;
}
/* Style for disabled dropdown items */
.dropdown-item.disabled {
    opacity: 0.6;
    pointer-events: none;
}
</style>

<script>
// Add any JavaScript for tooltips if needed
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>