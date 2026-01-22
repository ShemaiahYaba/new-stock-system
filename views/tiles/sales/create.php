<!-- 
=============================================
COMPLETE FIXED: Tile Sales Form with Add-Ons
File: views/tiles/sales/create.php
REPLACE your entire file with this version
============================================= 
-->

<?php
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

<style>
.addon-card {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.3s;
    cursor: pointer;
}

.addon-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.1);
}

.addon-card.selected {
    border-color: #007bff;
    background-color: #e7f3ff;
}

.addon-card .form-check-input {
    width: 20px;
    height: 20px;
}
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create Tile Sale</h1>
                <p class="text-muted">Process a new tile product sale with optional add-ons</p>
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
                    
                    <form id="tileSaleForm" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="addon_data" id="addon_data_input">
                        
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
                            <div class="alert alert-success" id="subtotalDisplay" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong>Product Subtotal:</strong></span>
                                    <span class="fs-5"><strong>₦<span id="subtotalAmount">0.00</span></strong></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" 
                                      rows="2" placeholder="Optional notes about this sale"></textarea>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Add-Ons Section -->
            <?php if (!empty($availableProducts) && !empty($customers)): ?>
            <div class="card mt-3" id="addons_section" style="display: none;">
                <div class="card-header bg-info text-white">
                    <strong><i class="bi bi-plus-square"></i> Additional Charges & Services</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Add optional services and charges to this sale</p>
                    <div id="addons_container">
                        <!-- Add-ons will be loaded here via JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Grand Total Display -->
            <div class="card mt-3" id="grand_total_card" style="display: none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Product Subtotal:</strong> <span id="final_subtotal">₦0.00</span></p>
                            <p class="mb-1"><strong>Add-On Charges:</strong> <span id="final_addons">₦0.00</span></p>
                            <p class="mb-0"><strong>Adjustments:</strong> <span id="final_adjustments">₦0.00</span></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4 class="mb-0">
                                <strong>Grand Total:</strong> 
                                <span class="text-success">₦<span id="grand_total_amount">0.00</span></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" form="tileSaleForm">
                    <i class="bi bi-check-circle"></i> Create Sale
                </button>
            </div>
            <?php endif; ?>
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
                        <li>Add-ons are calculated and applied</li>
                        <li>Stock is deducted from product</li>
                        <li>Transaction logged in stock card</li>
                        <li>Invoice is generated automatically</li>
                    </ol>
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
                        <small class="text-muted">Subtotal:</small>
                        <div id="calcSubtotal" class="fw-bold text-success" style="font-size: 1.5rem;">₦0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Add-On Modules -->
<script src="/new-stock-system/assets/js/production/property-calculator.js"></script>
<script src="/new-stock-system/assets/js/production/addon-calculator.js"></script>
<script src="/new-stock-system/assets/js/production/addon-renderer.js"></script>

<script>
// ============================================
// TILE SALES WITH ADD-ONS - FIXED VERSION
// ============================================

let availableStock = 0;
let productSubtotal = 0;
let availableAddons = [];
let selectedAddons = new Map();
let addonCalculations = new Map();

document.addEventListener('DOMContentLoaded', function() {
    loadTileAddons();
    
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
    
    ['quantity', 'unit_price'].forEach(id => {
        document.getElementById(id).addEventListener('input', function() {
            updateCalculator();
            if (id === 'quantity') checkQuantity();
        });
    });
    
    document.getElementById('tileSaleForm').addEventListener('submit', handleSubmit);
});

async function loadTileAddons() {
    try {
        const response = await fetch('/new-stock-system/controllers/production_properties/get_by_category.php?category=tile&include_addons=1');
        const data = await response.json();
        
        console.log('📥 API Response:', data);
        
        if (data.success && data.addons) {
            availableAddons = data.addons;
            console.log('✅ Loaded', availableAddons.length, 'tile add-ons');
            renderAddons();
            document.getElementById('addons_section').style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading tile add-ons:', error);
    }
}

function renderAddons() {
    if (availableAddons.length === 0) return;
    
    const container = document.getElementById('addons_container');
    const addonGroup = availableAddons.filter(a => !a.is_refundable);
    const adjustmentGroup = availableAddons.filter(a => a.is_refundable);
    
    let html = '';
    
    if (addonGroup.length > 0) {
        html += AddonRenderer.renderAddonSection(addonGroup, 'addon');
    }
    
    if (adjustmentGroup.length > 0) {
        html += AddonRenderer.renderAddonSection(adjustmentGroup, 'adjustment');
    }
    
    container.innerHTML = html;
    
    setTimeout(() => {
        attachAddonListeners();
    }, 100);
}

function attachAddonListeners() {
    const checkboxes = document.querySelectorAll('.addon-checkbox');
    console.log('🔗 Attaching listeners to', checkboxes.length, 'checkboxes');
    
    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function() {
            const addonData = JSON.parse(this.dataset.addonData);
            const isChecked = this.checked;
            const addonId = this.dataset.addonId;
            
            const inputContainer = document.getElementById(`addon_input_${addonId}`);
            const amountContainer = document.getElementById(`addon_amount_${addonId}`);
            
            if (isChecked) {
                inputContainer?.classList.remove('d-none');
                amountContainer?.classList.remove('d-none');
            } else {
                inputContainer?.classList.add('d-none');
                amountContainer?.classList.add('d-none');
            }
            
            handleAddonToggle(addonData, isChecked);
        });
    });
    
    const inputs = document.querySelectorAll('.addon-custom-amount, .addon-quantity');
    console.log('🔗 Attaching listeners to', inputs.length, 'input fields');
    
    inputs.forEach((input) => {
        input.addEventListener('input', function() {
            handleAddonInputChange(this.dataset.addonId);
        });
    });
}

function handleAddonToggle(addon, isChecked) {
    const addonKey = String(addon.id);
    console.log('🔄 Add-on toggled:', addon.name, 'Checked:', isChecked);
    
    if (isChecked) {
        selectedAddons.set(addonKey, {
            addon_id: addon.id,
            customAmount: null  // FIXED: camelCase
        });
        calculateAddon(addonKey);
    } else {
        selectedAddons.delete(addonKey);
        addonCalculations.delete(addonKey);
    }
    
    calculateAllAddons();
}

function handleAddonInputChange(addonId) {
    const addonKey = String(addonId);
    const inputs = selectedAddons.get(addonKey);
    if (!inputs) return;
    
    const customAmountInput = document.querySelector(`.addon-custom-amount[data-addon-id="${addonId}"]`);
    
    if (customAmountInput) {
        const raw = customAmountInput.value;
        inputs.customAmount = raw === '' ? null : parseFloat(raw);  // FIXED: camelCase
        console.log('📝 Custom amount updated:', addonId, inputs.customAmount);
    }
    
    calculateAddon(addonKey);
    calculateAllAddons();
}

function calculateAddon(addonId) {
    const addonKey = String(addonId);
    const addon = availableAddons.find(a => a.id == addonKey);
    if (!addon) {
        console.error('❌ Add-on not found:', addonId);
        return;
    }
    
    const inputs = selectedAddons.get(addonKey);
    if (!inputs) {
        console.error('❌ Inputs not found for add-on:', addonId);
        return;
    }
    
    console.log('🧮 Calculating add-on:', addon.name, 'Base amount:', productSubtotal, 'Custom:', inputs.customAmount);
    console.log('📋 Add-on config:', {
        calculation_method: addon.calculation_method,
        default_price: addon.default_price,
        is_refundable: addon.is_refundable
    });
    console.log('📥 Inputs being passed:', inputs);
    
    const result = AddonCalculator.calculateAddon(addon, inputs, productSubtotal);
    addonCalculations.set(addonKey, result);
    
    console.log('✅ Result:', result.amount);
    console.log('📤 Full result object:', result);
    
    const amountDisplay = document.getElementById(`addon_amount_value_${addonKey}`);
    if (amountDisplay) {
        amountDisplay.textContent = '₦' + result.amount.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

function calculateAllAddons() {
    let totalCharges = 0;
    let totalAdjustments = 0;
    
    addonCalculations.forEach((result) => {
        if (result.isRefund || result.amount < 0) {
            totalAdjustments += result.amount;
        } else {
            totalCharges += result.amount;
        }
    });
    
    const grandTotal = productSubtotal + totalCharges + totalAdjustments;
    
    console.log('💰 Totals - Subtotal:', productSubtotal, 'Charges:', totalCharges, 'Adjustments:', totalAdjustments, 'Grand:', grandTotal);
    
    document.getElementById('final_subtotal').textContent = '₦' + productSubtotal.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('final_addons').textContent = '₦' + totalCharges.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('final_adjustments').textContent = '₦' + totalAdjustments.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('grand_total_amount').textContent = grandTotal.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('grand_total_card').style.display = selectedAddons.size > 0 ? 'block' : 'none';
}

function updateCalculator() {
    const quantity = parseFloat(document.getElementById('quantity').value || 0);
    const unitPrice = parseFloat(document.getElementById('unit_price').value || 0);
    productSubtotal = quantity * unitPrice;
    
    console.log('💵 Product subtotal updated:', productSubtotal);
    
    const calcQtyEl = document.getElementById('calcQty');
    if (calcQtyEl) {
        calcQtyEl.textContent = quantity > 0 
            ? quantity.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' pcs' 
            : '-';
    }
    
    const calcPriceEl = document.getElementById('calcPrice');
    if (calcPriceEl) {
        calcPriceEl.textContent = unitPrice > 0 
            ? '₦' + unitPrice.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2}) 
            : '-';
    }
    
    const calcSubtotalEl = document.getElementById('calcSubtotal');
    if (calcSubtotalEl) {
        calcSubtotalEl.textContent = '₦' + productSubtotal.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    const subtotalAmountEl = document.getElementById('subtotalAmount');
    const subtotalDisplayEl = document.getElementById('subtotalDisplay');
    
    if (quantity > 0 && unitPrice > 0 && subtotalAmountEl && subtotalDisplayEl) {
        subtotalAmountEl.textContent = productSubtotal.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        subtotalDisplayEl.style.display = 'block';
    } else if (subtotalDisplayEl) {
        subtotalDisplayEl.style.display = 'none';
    }
    
    if (selectedAddons.size > 0) {
        console.log('🔄 Recalculating', selectedAddons.size, 'add-ons with new subtotal');
        selectedAddons.forEach((inputs, addonId) => {
            calculateAddon(addonId);
        });
        calculateAllAddons();
    }
}

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

function handleSubmit(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const addonData = [];
    selectedAddons.forEach((inputs) => {
        addonData.push(inputs);
    });
    
    console.log('📤 Submitting with add-ons:', addonData);
    
    document.getElementById('addon_data_input').value = JSON.stringify(addonData);
    
    const formData = new FormData(this);
    
    fetch('/new-stock-system/controllers/tiles/sales/create/index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        alert('Error creating sale: ' + error.message);
    });
}
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>