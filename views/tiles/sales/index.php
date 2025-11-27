<?php
/**
 * ============================================
 * FILE: views/tiles/sales/index.php
 * List all tile sales
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_sale.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Tile Sales - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;

$saleModel = new TileSale();
$sales = $saleModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
$totalSales = $saleModel->count();
$paginationData = getPaginationData($totalSales, $currentPage);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

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
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
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
                            <td class="text-end">₦<?= number_format($sale['unit_price'], 2) ?></td>
                            <td class="text-end"><strong>₦<?= number_format($sale['total_amount'], 2) ?></strong></td>
                            <td>
                                <?php
                                $statusClass = 'secondary';
                                if ($sale['status'] === 'completed') $statusClass = 'success';
                                elseif ($sale['status'] === 'pending') $statusClass = 'warning';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= htmlspecialchars(TILE_SALE_STATUS[$sale['status']]) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/new-stock-system/index.php?page=tile_sales_view&id=<?= $sale['id'] ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">Total:</th>
                            <th class="text-end">
                                <?php
                                $total = array_sum(array_column($sales, 'total_amount'));
                                echo '₦' . number_format($total, 2);
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