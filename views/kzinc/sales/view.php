<?php
/**
 * KZinc Sale Detail View
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/sale.php';
require_once __DIR__ . '/../../../models/production.php';
require_once __DIR__ . '/../../../models/invoice.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'K-Zinc Sale Details - ' . APP_NAME;

$saleId = (int)($_GET['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID.');
    header('Location: /new-stock-system/index.php?page=kzinc_sales');
    exit();
}

$saleModel       = new Sale();
$productionModel = new Production();
$invoiceModel    = new Invoice();

$sale = $saleModel->findById($saleId);

if (!$sale) {
    setFlashMessage('error', 'Sale not found.');
    header('Location: /new-stock-system/index.php?page=kzinc_sales');
    exit();
}

$production    = $productionModel->findBySaleId($saleId);
$invoiceRows   = $invoiceModel->findBySaleId($saleId);
$invoice       = !empty($invoiceRows) ? $invoiceRows[0] : null;

// Decode production paper
$paper = null;
if ($production && !empty($production['production_paper'])) {
    $paper = is_array($production['production_paper'])
        ? $production['production_paper']
        : json_decode($production['production_paper'], true);
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">K-Zinc Sale #<?php echo $saleId; ?></h1>
                <p class="text-muted"><?php echo formatDate($sale['created_at']); ?></p>
            </div>
            <div class="d-flex gap-2">
                <?php if ($invoice): ?>
                <a href="/new-stock-system/index.php?page=invoice_view&id=<?php echo $invoice['id']; ?>"
                   class="btn btn-secondary">
                    <i class="bi bi-receipt"></i> View Invoice
                </a>
                <?php endif; ?>
                <a href="/new-stock-system/index.php?page=kzinc_sales" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">

        <!-- Left: Sale summary -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-info-circle"></i> Sale Info</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th class="ps-3">Customer</th>
                            <td><?php echo htmlspecialchars($sale['customer_name'] ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <th class="ps-3">Coil</th>
                            <td>
                                <a href="/new-stock-system/index.php?page=kzinc_coils_view&id=<?php echo $sale['coil_id']; ?>">
                                    <?php echo htmlspecialchars($sale['coil_code'] ?? '—'); ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-3">Quantity</th>
                            <td>
                                <?php echo number_format($sale['quantity'] ?? 0); ?>
                                <?php echo htmlspecialchars($sale['unit_type'] ?? ''); ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-3">Total</th>
                            <td><strong>₦<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
                        </tr>
                        <tr>
                            <th class="ps-3">Status</th>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($sale['status']); ?>">
                                    <?php echo SALE_STATUSES[$sale['status']] ?? $sale['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php if ($invoice): ?>
                        <tr>
                            <th class="ps-3">Invoice</th>
                            <td>
                                <span class="badge <?php echo $invoice['status'] === INVOICE_STATUS_PAID ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                    <?php echo INVOICE_STATUSES[$invoice['status']] ?? $invoice['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Production paper / colour rows -->
        <div class="col-md-8">
            <?php if ($paper && !empty($paper['properties'])): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-palette"></i> Production Paper
                    <?php if (!empty($paper['production_reference'])): ?>
                    <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($paper['production_reference']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Colour / Label</th>
                                    <th>Bundles</th>
                                    <th>Pieces</th>
                                    <th>Price / Bundle</th>
                                    <th class="pe-3 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paper['properties'] as $prop): ?>
                                <tr>
                                    <td class="ps-3"><?php echo htmlspecialchars($prop['label'] ?? $prop['property_id'] ?? '—'); ?></td>
                                    <td><?php echo number_format($prop['sheet_qty'] ?? $prop['quantity'] ?? 0, 0); ?></td>
                                    <td><?php echo number_format($prop['pieces'] ?? 0); ?></td>
                                    <td>₦<?php echo number_format($prop['unit_price'] ?? 0, 2); ?></td>
                                    <td class="pe-3 text-end">₦<?php echo number_format($prop['row_subtotal'] ?? $prop['subtotal'] ?? 0, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php if (!empty($paper['addons'])): ?>
                            <tbody class="table-warning">
                                <?php foreach ($paper['addons'] as $addon): ?>
                                <tr>
                                    <td class="ps-3 text-muted fst-italic" colspan="3">
                                        <i class="bi bi-plus-square"></i>
                                        <?php echo htmlspecialchars($addon['name'] ?? $addon['code'] ?? '—'); ?>
                                    </td>
                                    <td></td>
                                    <td class="pe-3 text-end <?php echo ($addon['amount'] ?? 0) < 0 ? 'text-danger' : ''; ?>">
                                        ₦<?php echo number_format($addon['amount'] ?? 0, 2); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php endif; ?>
                            <tfoot class="table-light">
                                <?php if (!empty($paper['addons'])): ?>
                                <tr>
                                    <th class="ps-3 text-muted" colspan="4">Production Subtotal</th>
                                    <th class="pe-3 text-end text-muted">₦<?php echo number_format($paper['total_amount'] ?? 0, 2); ?></th>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th class="ps-3" colspan="4">Grand Total</th>
                                    <th class="pe-3 text-end">₦<?php echo number_format($paper['grand_total'] ?? $paper['total_amount'] ?? $sale['total_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php elseif ($production): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Production record found but no paper data available.
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> No production record linked to this sale.
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
