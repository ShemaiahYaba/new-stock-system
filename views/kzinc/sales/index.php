<?php
/**
 * KZinc Sales List — mirrors the layout of views/sales/index.php
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/sale.php';
require_once __DIR__ . '/../../../models/production.php';
require_once __DIR__ . '/../../../models/invoice.php';
require_once __DIR__ . '/../../../models/receipt.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'K-Zinc Sales - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = trim($_GET['search'] ?? '');

$saleModel       = new Sale();
$productionModel = new Production();
$invoiceModel    = new Invoice();
$receiptModel    = new Receipt();

// Filter sales to KZinc coils only — positional params required by getFilteredSales
$whereClause = "WHERE s.deleted_at IS NULL AND co.category = ?";
$params      = [STOCK_CATEGORY_KZINC];

if ($searchQuery !== '') {
    $whereClause .= ' AND (c.name LIKE ? OR co.code LIKE ? OR co.name LIKE ?)';
    $q = "%$searchQuery%";
    $params[] = $q;
    $params[] = $q;
    $params[] = $q;
}

$sales      = $saleModel->getFilteredSales($whereClause, $params, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
$totalSales = $saleModel->countFilteredSales($whereClause, $params);

// Enrich with production/invoice/receipts (mirrors legacy sales/index.php pattern)
foreach ($sales as &$sale) {
    // Surface invoice data that getFilteredSales nests under $sale['invoice']
    $sale['invoice_id']     = $sale['invoice']['id']     ?? null;
    $sale['invoice_status'] = $sale['invoice']['status'] ?? null;

    // Production
    $sale['production_id']     = $sale['production_id']     ?? null;
    $sale['production_status'] = $sale['production_status'] ?? null;

    if (empty($sale['id']) || !is_numeric($sale['id'])) continue;

    try {
        if (empty($sale['production_id'])) {
            $prod = $productionModel->findBySaleId($sale['id']);
            if ($prod) {
                $sale['production_id']     = $prod['id'];
                $sale['production_status'] = $prod['status'];
            }
        }

        $sale['has_receipts'] = false;
        if (!empty($sale['invoice_id'])) {
            $sale['has_receipts'] = $receiptModel->count('', $sale['invoice_id']) > 0;
        }
    } catch (Exception $e) {
        error_log('KZinc sales enrichment error for sale #' . $sale['id'] . ': ' . $e->getMessage());
    }
}
unset($sale);

$paginationData = getPaginationData($totalSales, $currentPage);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">K-Zinc Sales</h1>
                <p class="text-muted">
                    Sales on K-Zinc coils
                    <?php if ($searchQuery !== ''): ?>
                        <span class="badge bg-info">Search: "<?php echo htmlspecialchars($searchQuery); ?>"</span>
                    <?php endif; ?>
                    (<?php echo $totalSales; ?> total)
                </p>
            </div>
            <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=kzinc_sales_create" class="btn btn-primary">
                <i class="bi bi-cart-plus"></i> New K-Zinc Sale
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-cart"></i> K-Zinc Sales (<?php echo $totalSales; ?> total)
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="kzinc_sales">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Search customer, coil..."
                               value="<?php echo htmlspecialchars($searchQuery); ?>" autocomplete="off">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if ($searchQuery !== ''): ?>
                        <a href="/new-stock-system/index.php?page=kzinc_sales" class="btn btn-sm btn-secondary ms-2">
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
                <i class="bi bi-info-circle"></i>
                No K-Zinc sales found.
                <a href="/new-stock-system/index.php?page=kzinc_sales_create">Create the first one.</a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Coil</th>
                            <th>Qty / Unit</th>
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
                            <td>#<?php echo $sale['id']; ?></td>
                            <td><?php echo htmlspecialchars($sale['customer_name'] ?? '—'); ?></td>
                            <td>
                                <a href="/new-stock-system/index.php?page=kzinc_coils_view&id=<?php echo $sale['coil_id']; ?>">
                                    <?php echo htmlspecialchars($sale['coil_code'] ?? '—'); ?>
                                </a>
                                <small class="d-block text-muted"><?php echo htmlspecialchars($sale['coil_name'] ?? ''); ?></small>
                            </td>
                            <td>
                                <?php if (!empty($sale['quantity']) && !empty($sale['unit_type'])): ?>
                                    <?php echo number_format($sale['quantity'], 0); ?>
                                    <span class="text-capitalize"><?php echo htmlspecialchars($sale['unit_type']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo formatCurrency($sale['total_amount']); ?></strong></td>
                            <td>
                                <span class="badge <?php echo $sale['status'] === SALE_STATUS_COMPLETED ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo SALE_STATUSES[$sale['status']] ?? ucfirst($sale['status'] ?? ''); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Production icon -->
                                    <a href="<?php echo $sale['production_id']
                                        ? "/new-stock-system/index.php?page=production&action=view&id={$sale['production_id']}"
                                        : '#'; ?>"
                                       class="text-decoration-none"
                                       title="Production: <?php echo $sale['production_status'] ?: 'Not started'; ?>">
                                        <i class="bi bi-gear-fill <?php echo $sale['production_status'] ? 'text-success' : 'text-muted'; ?>"></i>
                                    </a>
                                    <!-- Invoice icon -->
                                    <a href="<?php echo $sale['invoice_id']
                                        ? "/new-stock-system/index.php?page=invoice_view&id={$sale['invoice_id']}"
                                        : '#'; ?>"
                                       class="text-decoration-none"
                                       title="Invoice: <?php echo $sale['invoice_status'] ? (INVOICE_STATUSES[$sale['invoice_status']] ?? $sale['invoice_status']) : 'Not created'; ?>">
                                        <i class="bi bi-receipt <?php echo $sale['invoice_id'] ? 'text-primary' : 'text-muted'; ?>"></i>
                                    </a>
                                    <!-- Receipts icon -->
                                    <a href="<?php echo $sale['invoice_id']
                                        ? "/new-stock-system/index.php?page=receipts&invoice_id={$sale['invoice_id']}"
                                        : '#'; ?>"
                                       class="text-decoration-none"
                                       title="<?php echo $sale['has_receipts'] ? 'View Receipts' : 'No receipts'; ?>">
                                        <i class="bi bi-cash-coin <?php echo $sale['has_receipts'] ? 'text-success' : 'text-muted'; ?>"></i>
                                    </a>
                                </div>
                            </td>
                            <td><small><?php echo formatDate($sale['created_at']); ?></small></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_VIEW)): ?>
                                    <a href="/new-stock-system/index.php?page=kzinc_sales_view&id=<?php echo $sale['id']; ?>"
                                       class="btn btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (!empty($sale['invoice_id'])): ?>
                                    <a href="/new-stock-system/index.php?page=invoice_view&id=<?php echo $sale['invoice_id']; ?>"
                                       class="btn btn-secondary" title="Invoice">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if ($totalSales > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php
            $queryParams = ['page' => 'kzinc_sales'];
            if ($searchQuery !== '') $queryParams['search'] = $searchQuery;
            include __DIR__ . '/../../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
