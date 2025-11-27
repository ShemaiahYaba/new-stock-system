<?php
/**
 * ============================================
 * COMPLETE FIXED FILE: views/tiles/stock/add.php
 * Copy this entire file to replace the old one
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_product.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Add Stock - ' . APP_NAME;

$productModel = new TileProduct();
$selectedProductId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

$products = $productModel->getAll([], 1000, 0);
$selectedProduct = null;

if ($selectedProductId) {
    $selectedProduct = $productModel->findById($selectedProductId);
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Add Stock</h1>
                <p class="text-muted">Increase product inventory</p>
            </div>
            <a href="/new-stock-system/index.php?page=tile_stock" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-plus-circle"></i> Stock Addition
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/tiles/stock/add/index.php" 
                          method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_id" name="product_id" required 
                                    <?= $selectedProduct ? 'disabled' : '' ?>>
                                <option value="">-- Select Product --</option>
                                <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" 
                                        <?= $selectedProductId == $product['id'] ? 'selected' : '' ?>
                                        data-current-stock="<?= number_format($product['current_stock'] ?? 0, 2) ?>">
                                    <?= htmlspecialchars($product['code']) ?> - 
                                    <?= htmlspecialchars($product['design_name']) ?> 
                                    (Stock: <?= number_format($product['current_stock'] ?? 0, 2) ?> pcs)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($selectedProduct): ?>
                            <input type="hidden" name="product_id" value="<?= $selectedProduct['id'] ?>">
                            <?php endif; ?>
                            <div class="invalid-feedback">Please select a product.</div>
                            
                            <div id="currentStockInfo" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    Current Stock: <strong><span id="currentStockValue">0</span> pieces</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity (pieces) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   step="0.01" min="0.01" placeholder="e.g., 1000 or 1500.5" required>
                            <div class="invalid-feedback">Please enter quantity (minimum 0.01).</div>
                            <small class="text-muted">Accepts decimal values (e.g., 1500.5)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="transaction_code" class="form-label">Transaction Code</label>
                            <input type="text" class="form-control" id="transaction_code" name="transaction_code" 
                                   placeholder="Optional reference (e.g., PO-2024-001)">
                            <small class="form-text text-muted">Optional purchase order or reference code</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="2" placeholder="Optional notes about this stock addition"></textarea>
                        </div>
                        
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> 
                            <strong>Note:</strong> Adding stock will automatically update the product status to "Available".
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=tile_stock" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Add Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Stock Addition Info
                </div>
                <div class="card-body">
                    <h6>What Happens?</h6>
                    <ul class="small">
                        <li>Quantity added to product balance</li>
                        <li>Transaction recorded in stock card</li>
                        <li>Product status updated to "Available"</li>
                        <li>Activity logged for audit</li>
                    </ul>
                    
                    <h6 class="mt-3">Decimal Values</h6>
                    <p class="small">You can enter fractional quantities:</p>
                    <ul class="small">
                        <li>1000 (whole pieces)</li>
                        <li>1500.5 (half piece)</li>
                        <li>2750.25 (quarter piece)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show current stock when product selected
document.getElementById('product_id').addEventListener('change', function() {
    const selected = this.selectedOptions[0];
    const stock = selected?.getAttribute('data-current-stock') || '0';
    
    if (this.value) {
        document.getElementById('currentStockValue').textContent = stock;
        document.getElementById('currentStockInfo').style.display = 'block';
    } else {
        document.getElementById('currentStockInfo').style.display = 'none';
    }
});

// Trigger on page load if product is preselected
if (document.getElementById('product_id').value) {
    document.getElementById('product_id').dispatchEvent(new Event('change'));
}
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>