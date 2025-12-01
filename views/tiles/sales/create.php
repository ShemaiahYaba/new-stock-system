<?php
/**
 * ============================================
 * FIXED: views/tiles/sales/create.php
 * Quick Calculator now updates in real-time!
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_product.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Create Tile Sale - ' . APP_NAME;

$productModel = new TileProduct();
$availableProducts = $productModel->getAvailable();

$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM customers WHERE deleted_at IS NULL ORDER BY name ASC LIMIT 1000");
$customers = $stmt->fetchAll();

$selectedProductId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create Tile Sale</h1>
                <p class="text-muted">Process a new tile product sale</p>
            </div>
            <a href="/new-stock-system/index.php?page=tile_sales" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Sales
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-cart-plus"></i> Sale Information
                </div>
                <div class="card-body">
                    <?php if (empty($availableProducts)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>No products available for sale.</strong>
                        <p class="mb-0">Please <a href="/new-stock-system/index.php?page=tile_stock_add">add stock to products</a> before creating sales.</p>
                    </div>
                    <?php elseif (empty($customers)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>No customers available.</strong>
                        <p class="mb-2">You need to create a customer before making sales.</p>
                        <a href="/new-stock-system/index.php?page=customers_create" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create Customer Now
                        </a>
                    </div>
                    <?php else: ?>
                    
                    <form action="/new-stock-system/controllers/tiles/sales/create/index.php" 
                          method="POST" class="needs-validation" novalidate id="saleForm">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>">
                                    <?= htmlspecialchars($customer['name']) ?>
                                    <?php if ($customer['company']): ?>
                                        - <?= htmlspecialchars($customer['company']) ?>
                                    <?php endif; ?>
                                    (<?= htmlspecialchars($customer['phone']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a customer.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">-- Select Product --</option>
                                <?php foreach ($availableProducts as $product): ?>
                                <option value="<?= $product['id'] ?>" 
                                        data-stock="<?= $product['current_stock'] ?>"
                                        data-code="<?= htmlspecialchars($product['code']) ?>"
                                        <?= $selectedProductId == $product['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($product['code']) ?> - 
                                    <?= htmlspecialchars($product['design_name']) ?> / 
                                    <?= htmlspecialchars($product['color_name']) ?> 
                                    (Stock: <?= number_format($product['current_stock'], 2) ?> pcs)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a product.</div>
                            
                            <div id="stockInfo" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    Available Stock: <strong><span id="availableStock">0</span> pieces</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity (pieces) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   step="0.01" min="0.01" placeholder="e.g., 500 or 750.5" required>
                            <div class="invalid-feedback">Please enter quantity.</div>
                            <small class="text-muted">Accepts decimal values (e.g., 500.5)</small>
                            <div id="quantityWarning" class="text-danger small mt-1" style="display: none;">
                                ⚠️ Quantity exceeds available stock!
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="unit_price" class="form-label">Unit Price (₦/piece) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="unit_price" name="unit_price" 
                                   step="0.01" min="0.01" placeholder="e.g., 850.00" required>
                            <div class="invalid-feedback">Please enter unit price.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="alert alert-success" id="totalDisplay" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong>Total Amount:</strong></span>
                                    <span class="fs-4"><strong>₦<span id="totalAmount">0.00</span></strong></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" 
                                      rows="2" placeholder="Optional notes about this sale"></textarea>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Note:</strong> Stock will be automatically deducted when this sale is completed.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=tile_sales" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle"></i> Create Sale
                            </button>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Sale Process
                </div>
                <div class="card-body">
                    <h6>What Happens?</h6>
                    <ol class="small">
                        <li>Sale record is created</li>
                        <li>Stock is deducted from product</li>
                        <li>Transaction logged in stock card</li>
                        <li>Product status updated if out of stock</li>
                    </ol>
                    
                    <h6 class="mt-3">Important</h6>
                    <ul class="small">
                        <li>Verify stock availability</li>
                        <li>Check unit price carefully</li>
                        <li>Cannot exceed available stock</li>
                        <li>Transaction is immediate</li>
                        <li>Accepts decimal quantities</li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-calculator"></i> Quick Calculator
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Quantity:</small>
                        <div id="calcQty" class="fw-bold fs-5 text-primary">-</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Unit Price:</small>
                        <div id="calcPrice" class="fw-bold fs-5 text-primary">-</div>
                    </div>
                    <hr>
                    <div>
                        <small class="text-muted">Total Amount:</small>
                        <div id="calcTotal" class="fw-bold text-success" style="font-size: 1.5rem;">₦0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let availableStock = 0;

// Update stock info when product changes
document.getElementById('product_id').addEventListener('change', function() {
    const selected = this.selectedOptions[0];
    availableStock = parseFloat(selected?.getAttribute('data-stock') || 0);
    
    if (this.value) {
        document.getElementById('availableStock').textContent = availableStock.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('stockInfo').style.display = 'block';
    } else {
        document.getElementById('stockInfo').style.display = 'none';
        availableStock = 0;
    }
    
    updateCalculator();
    checkQuantity();
});

// Check if quantity exceeds stock
function checkQuantity() {
    const quantity = parseFloat(document.getElementById('quantity').value || 0);
    const warning = document.getElementById('quantityWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    if (quantity > availableStock && availableStock > 0) {
        warning.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        warning.style.display = 'none';
        submitBtn.disabled = false;
    }
}

// Update calculator in real-time
function updateCalculator() {
    const quantity = parseFloat(document.getElementById('quantity').value || 0);
    const unitPrice = parseFloat(document.getElementById('unit_price').value || 0);
    const total = quantity * unitPrice;
    
    // Update quantity display
    if (quantity > 0) {
        document.getElementById('calcQty').textContent = quantity.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' pcs';
    } else {
        document.getElementById('calcQty').textContent = '-';
    }
    
    // Update unit price display
    if (unitPrice > 0) {
        document.getElementById('calcPrice').textContent = '₦' + unitPrice.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    } else {
        document.getElementById('calcPrice').textContent = '-';
    }
    
    // Update total display
    if (quantity > 0 && unitPrice > 0) {
        document.getElementById('calcTotal').textContent = '₦' + total.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        // Also update main total display
        document.getElementById('totalAmount').textContent = total.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('totalDisplay').style.display = 'block';
    } else {
        document.getElementById('calcTotal').textContent = '₦0.00';
        document.getElementById('totalDisplay').style.display = 'none';
    }
}

// Add event listeners for real-time updates
document.getElementById('quantity').addEventListener('input', function() {
    checkQuantity();
    updateCalculator();
});

document.getElementById('quantity').addEventListener('keyup', function() {
    checkQuantity();
    updateCalculator();
});

document.getElementById('unit_price').addEventListener('input', updateCalculator);
document.getElementById('unit_price').addEventListener('keyup', updateCalculator);

// Trigger on page load if product is preselected
if (document.getElementById('product_id').value) {
    document.getElementById('product_id').dispatchEvent(new Event('change'));
}
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>