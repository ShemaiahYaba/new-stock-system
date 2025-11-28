<?php
/**
 * ============================================
 * FILE: views/tiles/sales/index.php
 * UPDATED: Shows payment status for each sale
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_sale.php';
require_once __DIR__ . '/../../../models/invoice.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Tile Sales - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;

$saleModel = new TileSale();
$invoiceModel = new Invoice();

$sales = $saleModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
$totalSales = $saleModel->count();
$paginationData = getPaginationData($totalSales, $currentPage);

// Attach invoice data to each sale
foreach ($sales as &$sale) {
    $invoice = $invoiceModel->findByTileSale($sale['id']);
    $sale['invoice'] = $invoice;
    
    if ($invoice) {
        $sale['payment_status'] = $invoice['status'];
        $sale['paid_amount'] = $invoice['paid_amount'];
        $sale['invoice_total'] = $invoice['total'];
        $sale['balance'] = $invoice['total'] - $invoice['paid_amount'];
    }
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<style>
.payment-status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}
.status-paid { background-color: #d1e7dd; color: #0f5132; }
.status-partial { background-color: #fff3cd; color: #856404; }
.status-unpaid { background-color: #f8d7da; color: #842029; }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Tile Sales</h1>
                <p class="text-muted">Manage tile product sales (<?= $totalSales ?> total)</p>
            </div>
            <?php if (hasPermission(MODULE_TILE_SALES, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=tile_sales_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Sale
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <i class="bi bi-cart"></i> Sales List
        </div>
        <div class="card-body p-0">
            <?php if (empty($sales)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No sales recorded yet.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sale ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Amount</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><strong>#<?= $sale['id'] ?></strong></td>
                            <td><?= formatDate($sale['created_at'], 'm/d/y') ?></td>
                            <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                            <td><code><?= htmlspecialchars($sale['product_code']) ?></code></td>
                            <td class="text-end"><?= number_format($sale['quantity'], 1) ?> pcs</td>
                            <td class="text-end"><strong>₦<?= number_format($sale['total_amount'], 2) ?></strong></td>
                            <td>
                                <?php if (!empty($sale['invoice'])): ?>
                                    <?php
                                    $statusClass = 'unpaid';
                                    $statusText = 'Unpaid';
                                    $statusIcon = 'x-circle';
                                    
                                    if ($sale['payment_status'] === 'paid') {
                                        $statusClass = 'paid';
                                        $statusText = 'Paid';
                                        $statusIcon = 'check-circle';
                                    } elseif ($sale['payment_status'] === 'partial') {
                                        $statusClass = 'partial';
                                        $statusText = 'Partial';
                                        $statusIcon = 'dash-circle';
                                    }
                                    ?>
                                    <span class="payment-status-badge status-<?= $statusClass ?>">
                                        <i class="bi bi-<?= $statusIcon ?>"></i> <?= $statusText ?>
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        Paid: ₦<?= number_format($sale['paid_amount'], 0) ?>
                                        <?php if ($sale['balance'] > 0): ?>
                                        <br><span class="text-danger">Due: ₦<?= number_format($sale['balance'], 0) ?></span>
                                        <?php endif; ?>
                                    </small>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Legacy</span>
                                    <br><small class="text-muted">No invoice</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/new-stock-system/index.php?page=tile_sales_view&id=<?= $sale['id'] ?>" 
                                       class="btn btn-info" title="View Sale">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (!empty($sale['invoice'])): ?>
                                    <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $sale['invoice']['id'] ?>" 
                                       class="btn btn-primary" title="View Invoice">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Total Sales:</th>
                            <th class="text-end">
                                <?php
                                $totalRevenue = array_sum(array_column($sales, 'total_amount'));
                                echo '₦' . number_format($totalRevenue, 2);
                                ?>
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($sales)): ?>
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>