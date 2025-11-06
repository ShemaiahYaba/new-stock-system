<?php
/**
 * Sales List View - FIXED VERSION
 * Replace views/sales/index.php with this
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Sales - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

$saleModel = new Sale();

if (!empty($searchQuery)) {
    $sales = $saleModel->search($searchQuery, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalSales = count($sales);
} else {
    $sales = $saleModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalSales = $saleModel->count();
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
            <a href="/new-stock-system/index.php?page=sales_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Sale
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-cart"></i> Sales List (<?php echo $totalSales; ?> total)
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="sales">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search sales..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=sales" class="btn btn-sm btn-secondary ms-2">
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
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td>#<?php echo $sale['id']; ?></td>
                            <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($sale['coil_code'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo ucfirst($sale['sale_type'] ?? 'unknown'); ?>
                                </span>
                            </td>
                            <td><?php echo number_format($sale['meters'], 2); ?>m</td>
                            <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                            <td>
                                <span class="badge <?php echo $sale['status'] === 'completed' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo ucfirst($sale['status'] ?? 'pending'); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($sale['created_at']); ?></td>
                            <td>
                                <a href="/new-stock-system/index.php?page=sales_invoice&id=<?php echo $sale['id']; ?>" 
                                   class="btn btn-sm btn-success" title="View Invoice">
                                    <i class="bi bi-file-text"></i>
                                </a>
                                <?php if (hasPermission(MODULE_SALES_MANAGEMENT, ACTION_VIEW)): ?>
                                <a href="/new-stock-system/index.php?page=sales_view&id=<?php echo $sale['id']; ?>" 
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (hasPermission(MODULE_SALES_MANAGEMENT, ACTION_EDIT)): ?>
                                <a href="/new-stock-system/index.php?page=sales_edit&id=<?php echo $sale['id']; ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
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
            <?php $queryParams = $_GET; include __DIR__ . '/../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>