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
        <div class="col-12">
            <div class="card mb-3">
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
                        <input type="hidden" name="config_data" id="config_data_input">
                        <input type="hidden" name="quantity" id="quantity">
                        <input type="hidden" name="unit_price" id="unit_price">
                        
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
                            <label class="form-label">Sale Mode</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sale_mode" id="sale_mode_config" value="config" checked>
                                    <label class="form-check-label" for="sale_mode_config">Configuration</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sale_mode" id="sale_mode_simple" value="simple">
                                    <label class="form-check-label" for="sale_mode_simple">Simple Quantity/Price</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="simple_section" style="display: none;">
                            <label class="form-label">Simple Sale</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Quantity (pieces)</label>
                                        <input type="number" class="form-control" id="simple_quantity" min="0.01" step="0.01" placeholder="e.g., 500">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Unit Price (&#8358;/piece)</label>
                                        <input type="number" class="form-control" id="simple_unit_price" min="0.01" step="0.01" placeholder="e.g., 850.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="config_section" style="display: none;">
                            <label class="form-label">Configuration (Mainsheet / Flatsheet)</label>
                            <div class="border rounded p-3 bg-light">
                                <div id="configs_container"></div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add_config_btn">
                                        <i class="bi bi-plus"></i> Add Configuration
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Total Pieces</label>
                                        <input type="text" class="form-control" id="total_quantity_display" readonly>
                                    </div>
                                </div>
                                <div id="quantityWarning" class="text-danger small mt-2" style="display: none;">
                                    Warning: Quantity exceeds available stock!
                                </div>
                                <div id="configLoadWarning" class="text-danger small mt-2" style="display: none;">
                                    Configuration options failed to load. Please refresh the page.
                                </div>
                            </div>
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
    </div>
</div>

<!-- Load Add-On Modules -->
<script src="/new-stock-system/assets/js/production/property-calculator.js"></script>
<script src="/new-stock-system/assets/js/production/addon-calculator.js"></script>
<script src="/new-stock-system/assets/js/production/addon-renderer.js"></script>

<script>
// ============================================
// TILE SALES WITH CONFIGURATIONS + ADD-ONS
// ============================================

const currency = '\u20A6';

let availableStock = 0;
let productSubtotal = 0;
let availableAddons = [];
let availableConfigs = [];
let selectedAddons = new Map();
let addonCalculations = new Map();
let currentTotalQuantity = 0;
let currentConfigData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadTileProperties();
    
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
        
        checkQuantity();
    });
    
    document.getElementById('tileSaleForm').addEventListener('submit', handleSubmit);

    document.getElementById('sale_mode_config')?.addEventListener('change', updateSaleMode);
    document.getElementById('sale_mode_simple')?.addEventListener('change', updateSaleMode);
    document.getElementById('simple_quantity')?.addEventListener('input', updateSimpleTotals);
    document.getElementById('simple_unit_price')?.addEventListener('input', updateSimpleTotals);
});

function updateSaleMode() {
    const isSimple = document.getElementById('sale_mode_simple')?.checked;
    const configSection = document.getElementById('config_section');
    const simpleSection = document.getElementById('simple_section');

    if (isSimple) {
        if (configSection) configSection.style.display = 'none';
        if (simpleSection) simpleSection.style.display = 'block';
        currentConfigData = [];
        productSubtotal = 0;
        updateSimpleTotals();
    } else {
        if (configSection) configSection.style.display = 'block';
        if (simpleSection) simpleSection.style.display = 'none';
        updateConfigTotals();
    }
}

function updateSimpleTotals() {
    const quantity = parseFloat(document.getElementById('simple_quantity')?.value || 0);
    const unitPrice = parseFloat(document.getElementById('simple_unit_price')?.value || 0);
    const subtotal = quantity * unitPrice;

    currentTotalQuantity = quantity;
    productSubtotal = subtotal;
    currentConfigData = [];

    document.getElementById('quantity').value = quantity > 0 ? quantity : '';
    document.getElementById('unit_price').value = unitPrice > 0 ? unitPrice.toFixed(2) : '';

    const qtyDisplay = document.getElementById('total_quantity_display');
    if (qtyDisplay) {
        qtyDisplay.value = quantity > 0
            ? quantity.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : '';
    }

    updateCalculatorDisplay(quantity, subtotal);
    updateSubtotalDisplay(subtotal);

    if (selectedAddons.size > 0) {
        selectedAddons.forEach((inputs, addonId) => {
            calculateAddon(addonId);
        });
    }
    calculateAllAddons();
    checkQuantity();
}


async function loadTileProperties() {
    try {
        const response = await fetch('/new-stock-system/controllers/production_properties/get_by_category.php?category=tile&include_addons=1&include_configs=1');
        const data = await response.json();
        
        if (data.success) {
            availableAddons = data.addons || [];
            availableConfigs = data.configs || [];
            
            if (availableConfigs.length > 0) {
                renderConfigs();
            } else {
                const warn = document.getElementById('configLoadWarning');
                if (warn) warn.style.display = 'block';
            }
            updateSaleMode();
            
            if (availableAddons.length > 0) {
                renderAddons();
                document.getElementById('addons_section').style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error loading tile properties:', error);
    }
}

function renderConfigs() {
    const container = document.getElementById('configs_container');
    container.innerHTML = '';
    addConfigRow();

    const addBtn = document.getElementById('add_config_btn');
    if (addBtn) {
        addBtn.addEventListener('click', addConfigRow);
    }

    updateConfigTotals();
}

function addConfigRow() {
    const container = document.getElementById('configs_container');
    const rowId = `config_row_${Date.now()}_${Math.floor(Math.random() * 10000)}`;
    const options = availableConfigs.map((config) => {
        return `<option value="${config.id}" data-default-price="${config.default_price || 0}">${config.name}</option>`;
    }).join('');

    const html = `
        <div class="row align-items-end mb-3 config-row" data-row-id="${rowId}">
            <div class="col-md-4">
                <label class="form-label mb-1">Configuration</label>
                <select class="form-select config-select" data-row-id="${rowId}">
                    <option value="">-- Select --</option>
                    ${options}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Quantity</label>
                <input type="number" class="form-control config-quantity" data-row-id="${rowId}" min="0" step="0.01" placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Unit Price (&#8358;)</label>
                <input type="number" class="form-control config-unit-price" data-row-id="${rowId}" min="0" step="0.01" placeholder="0.00">
                <div class="text-end small mt-1">Line Total: &#8358;<span id="config_total_${rowId}">0.00</span></div>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-config" data-row-id="${rowId}">
                    <i class="bi bi-x"></i> Remove
                </button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    const row = container.querySelector(`[data-row-id="${rowId}"]`);
    if (!row) return;

    row.querySelector('.config-select')?.addEventListener('change', () => handleConfigSelect(rowId));
    row.querySelector('.config-quantity')?.addEventListener('input', updateConfigTotals);
    row.querySelector('.config-unit-price')?.addEventListener('input', updateConfigTotals);
    row.querySelector('.remove-config')?.addEventListener('click', () => {
        row.remove();
        updateConfigTotals();
    });
}

function handleConfigSelect(rowId) {
    const row = document.querySelector(`[data-row-id="${rowId}"]`);
    if (!row) return;

    const select = row.querySelector('.config-select');
    const priceInput = row.querySelector('.config-unit-price');
    const selectedOption = select?.options[select.selectedIndex];
    const defaultPrice = parseFloat(selectedOption?.getAttribute('data-default-price') || 0);

    if (priceInput && !priceInput.value && defaultPrice > 0) {
        priceInput.value = defaultPrice.toFixed(2);
    }

    updateConfigTotals();
}

function updateConfigTotals() {
    const rows = document.querySelectorAll('.config-row');
    let totalQuantity = 0;
    let subtotal = 0;
    const configData = [];

    rows.forEach((row) => {
        const rowId = row.dataset.rowId;
        const select = row.querySelector('.config-select');
        const configId = select?.value || '';
        const property = availableConfigs.find((item) => String(item.id) === String(configId));
        const qtyInput = row.querySelector('.config-quantity');
        const priceInput = row.querySelector('.config-unit-price');
        const quantity = parseFloat(qtyInput?.value || 0);
        const unitPrice = parseFloat(priceInput?.value || 0);
        let amount = 0;
        let pieces = 0;

        if (configId && property) {
            const result = PropertyCalculator.calculate(property, {
                quantity: quantity,
                unitPrice: unitPrice
            });
            amount = result.subtotal || 0;
            pieces = result.pieces > 0 ? result.pieces : (result.quantity || 0);
        }

        const totalEl = document.getElementById(`config_total_${rowId}`);
        if (totalEl) {
            totalEl.textContent = amount.toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        if (configId && quantity > 0 && unitPrice > 0) {
            totalQuantity += pieces || quantity;
            subtotal += amount;
            configData.push({
                config_id: parseInt(configId, 10),
                quantity: pieces || quantity,
                unit_price: unitPrice,
                amount: amount
            });
        }
    });

    currentTotalQuantity = totalQuantity;
    productSubtotal = subtotal;
    currentConfigData = configData;

    document.getElementById('quantity').value = totalQuantity > 0 ? totalQuantity : '';
    document.getElementById('unit_price').value = '';

    const qtyDisplay = document.getElementById('total_quantity_display');
    if (qtyDisplay) {
        qtyDisplay.value = totalQuantity > 0
            ? totalQuantity.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : '';
    }

    updateCalculatorDisplay(totalQuantity, subtotal);
    updateSubtotalDisplay(subtotal);

    if (selectedAddons.size > 0) {
        selectedAddons.forEach((inputs, addonId) => {
            calculateAddon(addonId);
        });
    }
    calculateAllAddons();
    checkQuantity();
}

function updateCalculatorDisplay(quantity, subtotal) {
    const calcQtyEl = document.getElementById('calcQty');
    if (calcQtyEl) {
        calcQtyEl.textContent = quantity > 0
            ? quantity.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' pcs'
            : '-';
    }

    const calcPriceEl = document.getElementById('calcPrice');
    if (calcPriceEl) {
        calcPriceEl.textContent = '-';
    }

    const calcSubtotalEl = document.getElementById('calcSubtotal');
    if (calcSubtotalEl) {
        calcSubtotalEl.textContent = currency + subtotal.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

function updateSubtotalDisplay(subtotal) {
    const subtotalAmountEl = document.getElementById('subtotalAmount');
    const subtotalDisplayEl = document.getElementById('subtotalDisplay');
    
    if (subtotal > 0 && subtotalAmountEl && subtotalDisplayEl) {
        subtotalAmountEl.textContent = subtotal.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        subtotalDisplayEl.style.display = 'block';
    } else if (subtotalDisplayEl) {
        subtotalDisplayEl.style.display = 'none';
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
    
    inputs.forEach((input) => {
        input.addEventListener('input', function() {
            handleAddonInputChange(this.dataset.addonId);
        });
    });
}

function handleAddonToggle(addon, isChecked) {
    const addonKey = String(addon.id);
    
    if (isChecked) {
        selectedAddons.set(addonKey, {
            addon_id: addon.id,
            customAmount: null
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
        inputs.customAmount = raw === '' ? null : parseFloat(raw);
    }
    
    calculateAddon(addonKey);
    calculateAllAddons();
}

function calculateAddon(addonId) {
    const addonKey = String(addonId);
    const addon = availableAddons.find(a => a.id == addonKey);
    if (!addon) {
        return;
    }
    
    const inputs = selectedAddons.get(addonKey);
    if (!inputs) {
        return;
    }
    
    const result = AddonCalculator.calculateAddon(addon, inputs, productSubtotal);
    addonCalculations.set(addonKey, result);
    
    const amountDisplay = document.getElementById(`addon_amount_value_${addonKey}`);
    if (amountDisplay) {
        amountDisplay.textContent = currency + result.amount.toLocaleString('en-NG', {
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
    
    document.getElementById('final_subtotal').textContent = currency + productSubtotal.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('final_addons').textContent = currency + totalCharges.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('final_adjustments').textContent = currency + totalAdjustments.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('grand_total_amount').textContent = grandTotal.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('grand_total_card').style.display = (productSubtotal > 0 || selectedAddons.size > 0) ? 'block' : 'none';
}

function checkQuantity() {
    const warning = document.getElementById('quantityWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    if (currentTotalQuantity > availableStock && availableStock > 0) {
        warning.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        warning.style.display = 'none';
        submitBtn.disabled = false;
    }
}

function validateConfigInputs() {
    const rows = document.querySelectorAll('.config-row');
    let hasSelection = false;
    for (const row of rows) {
        const select = row.querySelector('.config-select');
        const configId = select?.value || '';
        const qtyInput = row.querySelector('.config-quantity');
        const priceInput = row.querySelector('.config-unit-price');
        const quantity = parseFloat(qtyInput?.value || 0);
        const unitPrice = parseFloat(priceInput?.value || 0);

        if (configId) {
            hasSelection = true;
            if (quantity <= 0 || unitPrice <= 0) {
                return false;
            }
        }
    }
    return hasSelection;
}

function handleSubmit(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const isSimple = document.getElementById('sale_mode_simple')?.checked;
    if (!isSimple) {
        if (!validateConfigInputs()) {
            alert('Please enter both quantity and unit price for each configuration you use.');
            return;
        }

        if (!currentConfigData || currentConfigData.length === 0) {
            alert('Please enter at least one configuration.');
            return;
        }
    } else {
        const simpleQty = parseFloat(document.getElementById('simple_quantity')?.value || 0);
        const simplePrice = parseFloat(document.getElementById('simple_unit_price')?.value || 0);
        if (simpleQty <= 0 || simplePrice <= 0) {
            alert('Please enter quantity and unit price for the simple sale.');
            return;
        }
    }
    
    if (currentTotalQuantity > availableStock && availableStock > 0) {
        alert('Quantity exceeds available stock.');
        return;
    }
    
    document.getElementById('config_data_input').value = JSON.stringify(currentConfigData);
    
    const addonData = [];
    selectedAddons.forEach((inputs) => {
        addonData.push(inputs);
    });
    
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
