<?php
/**
 * ============================================
 * FILE: views/tiles/sales/view.php
 * View sale details
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_sale.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Sale Details - ' . APP_NAME;

$saleId = (int)($_GET['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID.');
    header('Location: /new-stock-system/index.php?page=tile_sales');
    exit();
}

$saleModel = new TileSale();
$sale = $saleModel->findById($saleId);

if (!$sale) {
    setFlashMessage('error', 'Sale not found.');
    header('Location: /new-stock-system/index.php?page=tile_sales');
    exit();
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Sale Details</h1>
                <p class="text-muted">Sale #<?= $sale['id'] ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=tile_sales" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Sales
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <!-- Sale Information -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-receipt"></i> Sale Information
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer</h6>
                            <p class="mb-1"><strong><?= htmlspecialchars($sale['customer_name']) ?></strong></p>
                            <?php if ($sale['customer_phone']): ?>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-phone"></i> <?= htmlspecialchars($sale['customer_phone']) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Product</h6>
                            <p class="mb-1"><code><?= htmlspecialchars($sale['product_code']) ?></code></p>
                            <p class="mb-0 small text-muted">
                                <?= htmlspecialchars($sale['design_name']) ?> / 
                                <?= htmlspecialchars($sale['color_name']) ?>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">Quantity</h6>
                            <h4><?= number_format($sale['quantity'], 1) ?> <small>pieces</small></h4>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">Unit Price</h6>
                            <h4>₦<?= number_format($sale['unit_price'], 2) ?></h4>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">Total Amount</h6>
                            <h3 class="text-success">₦<?= number_format($sale['total_amount'], 2) ?></h3>
                        </div>
                    </div>
                    
                    <?php if ($sale['notes']): ?>
                    <hr>
                    <h6 class="text-muted">Notes</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($sale['notes'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Amount Breakdown -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-calculator"></i> Amount Breakdown
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Quantity:</td>
                            <td class="text-end"><?= number_format($sale['quantity'], 1) ?> pieces</td>
                        </tr>
                        <tr>
                            <td>Unit Price:</td>
                            <td class="text-end">₦<?= number_format($sale['unit_price'], 2) ?></td>
                        </tr>
                        <tr>
                            <td>Calculation:</td>
                            <td class="text-end">
                                <?= number_format($sale['quantity'], 1) ?> × 
                                ₦<?= number_format($sale['unit_price'], 2) ?>
                            </td>
                        </tr>
                        <tr class="table-success">
                            <th>Total Amount:</th>
                            <th class="text-end fs-5">₦<?= number_format($sale['total_amount'], 2) ?></th>
                        </tr>
                    </table>
                    
                    <div class="alert alert-info mt-3">
                        <strong>In Words:</strong> 
                        <?= numberToWords($sale['total_amount']) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Sale Status
                </div>
                <div class="card-body text-center">
                    <?php
                    $statusClass = 'secondary';
                    if ($sale['status'] === 'completed') $statusClass = 'success';
                    elseif ($sale['status'] === 'pending') $statusClass = 'warning';
                    ?>
                    <span class="badge bg-<?= $statusClass ?> fs-5 px-4 py-2">
                        <?= htmlspecialchars(TILE_SALE_STATUS[$sale['status']]) ?>
                    </span>
                </div>
            </div>
            
            <!-- Transaction Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Transaction Info
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Sale ID:</th>
                            <td>#<?= $sale['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td><?= formatDate($sale['created_at']) ?></td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td><?= htmlspecialchars($sale['created_by_name']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="bi bi-printer"></i> Print Sale
                    </button>
                    <a href="/new-stock-system/index.php?page=tile_products_view&id=<?= $sale['tile_product_id'] ?>" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-box"></i> View Product
                    </a>
                    <a href="/new-stock-system/index.php?page=customers_view&id=<?= $sale['customer_id'] ?>" 
                       class="btn btn-outline-info">
                        <i class="bi bi-person"></i> View Customer
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>