<?php
/**
 * ============================================
 * FILE: views/tiles/stock/card.php
 * Digital stock card (ledger view)
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_product.php';
require_once __DIR__ . '/../../../models/tile_stock_ledger.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Stock Card - ' . APP_NAME;

$productModel = new TileProduct();
$ledgerModel = new TileStockLedger();

$selectedProductId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
$products = $productModel->getAll([], 1000, 0);

$selectedProduct = null;
$ledgerEntries = [];
$summary = ['total_in' => 0, 'total_out' => 0, 'current_balance' => 0];

if ($selectedProductId) {
    $selectedProduct = $productModel->findById($selectedProductId);
    if ($selectedProduct) {
        $ledgerEntries = $ledgerModel->getByProduct($selectedProductId, 100, 0);
        
        // Calculate summary from ledger entries to ensure consistent ordering
        $summary = ['total_in' => 0, 'total_out' => 0, 'current_balance' => 0];
        
        if (!empty($ledgerEntries)) {
            // Get the first entry (most recent) for current balance
            $summary['current_balance'] = (float)$ledgerEntries[0]['balance'];
            
            // Calculate totals from all entries
            foreach ($ledgerEntries as $entry) {
                $summary['total_in'] += (float)$entry['quantity_in'];
                $summary['total_out'] += (float)$entry['quantity_out'];
            }
        }
    }
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Card</h1>
            </div>
            <a href="/new-stock-system/index.php?page=tile_products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>
    
    <!-- Product Selector -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="bi bi-funnel"></i> Select Product
        </div>
        <div class="card-body">
            <form method="GET" action="/new-stock-system/index.php">
                <input type="hidden" name="page" value="tile_stock_card">
                <div class="row">
                    <div class="col-md-10">
                        <select class="form-select" name="product_id" required>
                            <option value="">-- Select a product --</option>
                            <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>" <?= $selectedProductId == $product['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($product['code']) ?> - 
                                <?= htmlspecialchars($product['design_name']) ?> / 
                                <?= htmlspecialchars($product['color_name']) ?> 
                                (<?= number_format($product['current_stock'] ?? 0, 0) ?> pcs)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> View
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($selectedProduct): ?>
    <!-- Stock Card Header -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-journal-text"></i> STOCK CARD - <?= htmlspecialchars($selectedProduct['code']) ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Product:</strong> <?= htmlspecialchars($selectedProduct['code']) ?></p>
                    <p class="mb-1"><strong>Design:</strong> <?= htmlspecialchars($selectedProduct['design_name']) ?></p>
                    <p class="mb-1"><strong>Color:</strong> <?= htmlspecialchars($selectedProduct['color_name']) ?></p>
                    <p class="mb-0"><strong>Gauge:</strong> <?= htmlspecialchars(TILE_GAUGES[$selectedProduct['gauge']]) ?></p>
                </div>
                <div class="col-md-6">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="bg-success text-white p-2 rounded">
                                <h4 class="mb-0"><?= number_format($summary['total_in'], 1) ?></h4>
                                <small>Total IN</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-danger text-white p-2 rounded">
                                <h4 class="mb-0"><?= number_format($summary['total_out'], 1) ?></h4>
                                <small>Total OUT</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-primary text-white p-2 rounded">
                                <h4 class="mb-0"><?= number_format($summary['current_balance'], 1) ?></h4>
                                <small>Balance</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaction Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list"></i> Transaction History
        </div>
        <div class="card-body p-0">
            <?php if (empty($ledgerEntries)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No transactions recorded for this product yet.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Code/Ref</th>
                            <th class="text-end">IN</th>
                            <th class="text-end">OUT</th>
                            <th class="text-end">Balance</th>
                            <th>Description</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reverse the array to show newest first
                        $reversedEntries = array_reverse($ledgerEntries);
                        foreach ($reversedEntries as $entry): 
                        ?>
                        <tr>
                            <td><?= formatDate($entry['created_at'], 'm/d/y H:i') ?></td>
                            <td>
                                <?php
                                $typeClass = 'secondary';
                                $typeIcon = 'circle';
                                $typeText = 'Other';
                                
                                switch ($entry['reference_type']) {
                                    case 'stock_in':
                                        $typeClass = 'success';
                                        $typeIcon = 'arrow-down-circle';
                                        $typeText = 'Stock IN';
                                        break;
                                    case 'sale':
                                        $typeClass = 'danger';
                                        $typeIcon = 'cart';
                                        $typeText = 'Sale';
                                        break;
                                    case 'adjustment':
                                        $typeClass = 'warning';
                                        $typeIcon = 'gear';
                                        $typeText = 'Adjust';
                                        break;
                                }
                                ?>
                                <span class="badge bg-<?= $typeClass ?>">
                                    <i class="bi bi-<?= $typeIcon ?>"></i> <?= $typeText ?>
                                </span>
                            </td>
                            <td><small><?= htmlspecialchars($entry['transaction_code'] ?: '-') ?></small></td>
                            <td class="text-end">
                                <?php if ($entry['quantity_in'] > 0): ?>
                                <strong class="text-success">+<?= number_format($entry['quantity_in'], 1) ?></strong>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($entry['quantity_out'] > 0): ?>
                                <strong class="text-danger">-<?= number_format($entry['quantity_out'], 1) ?></strong>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <strong><?= number_format($entry['balance'], 1) ?></strong>
                            </td>
                            <td><small><?= htmlspecialchars(truncateText($entry['description'] ?? '', 50)) ?></small></td>
                            <td><small><?= htmlspecialchars($entry['created_by_name'] ?? '-') ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-journal-text text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">Select a product above to view its stock card</h5>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>

