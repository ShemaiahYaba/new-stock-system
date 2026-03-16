<?php
/**
 * Create Sale from Available Stock Workflow
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../models/customer.php';

// Check permissions
if (!hasPermission(MODULE_SALES_MANAGEMENT, ACTION_CREATE)) {
    setFlashMessage('error', 'You do not have permission to create sales');
    redirect('/new-stock-system/index.php?page=sales');
}

$pageTitle = 'Create Sale from Available Stock - ' . APP_NAME;

// Get available stock entries
$stockEntryModel = new StockEntry();
$availableStock = $stockEntryModel->getAvailableStock();

// Get customers for dropdown
$customerModel = new Customer();
$customers = $customerModel->getAll(1000, 0);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
    .table th {
        white-space: nowrap;
    }
    .btn:disabled {
        cursor: not-allowed;
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <h1>Create Sale from Available Stock</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/new-stock-system">Home</a></li>
                <li class="breadcrumb-item"><a href="/new-stock-system/index.php?page=sales">Sales</a></li>
                <li class="breadcrumb-item active">New Sale from Available Stock</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="saleForm" method="POST" action="/new-stock-system/controllers/sales/create_available_workflow.php">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_id">Customer <span class="text-danger">*</span></label>
                            <select class="form-control" id="customer_id" name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>">
                                        <?= htmlspecialchars($customer['name']) ?>
                                        <?php if (!empty($customer['company'])): ?>
                                            (<?= htmlspecialchars($customer['company']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sale_date">Sale Date</label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" 
                                   value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Available Stock Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="stockTable">
<thead>
    <tr>
        <th width="5%">#</th>
        <th width="5%">Select</th>
        <th>Coil Code</th>
        <th>Description</th>
        <th>Status</th>
        <th>Meters Remaining</th>
        <th>Weight (kg)</th>
        <th>Unit Price (₦/m)</th>
        <th>Action</th>
    </tr>
</thead>
<tbody id="availableStockItems">
    <?php if (empty($availableStock)): ?>
    <tr>
        <td colspan="9" class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
            No available stock entries found. Add stock entries with meters to coils first.
        </td>
    </tr>
    <?php else: ?>
    <?php foreach ($availableStock as $index => $stock): ?>
        <tr data-stock-id="<?= $stock['id'] ?>"
            data-coil-code="<?= htmlspecialchars($stock['coil_code']) ?>"
            data-coil-name="<?= htmlspecialchars($stock['coil_name']) ?>"
            data-weight="<?= $stock['weight_kg_remaining'] ?? 0 ?>"
            data-meters="<?= $stock['meters_remaining'] ?>">

            <td><?= $index + 1 ?></td>
            <td>
                <input type="checkbox" class="form-check-input select-stock"
                       data-stock-id="<?= $stock['id'] ?>">
            </td>
            <td class="coil-code"><?= htmlspecialchars($stock['coil_code']) ?></td>
            <td class="coil-name"><?= htmlspecialchars($stock['coil_name']) ?></td>
            <td>
                <span class="badge <?php echo $stock['status'] === STOCK_STATUS_AVAILABLE ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <?php echo STOCK_STATUSES[$stock['status']] ?? $stock['status']; ?>
                </span>
            </td>

            <!-- PRIMARY: Meters -->
            <td class="available-meters">
                <strong><?= number_format($stock['meters_remaining'], 2) ?> m</strong>
            </td>

            <!-- SECONDARY: KG (may be empty for older entries) -->
            <td class="available-weight text-muted">
                <?php if ($stock['weight_kg_remaining'] > 0): ?>
                    <?= number_format($stock['weight_kg_remaining'], 2) ?> kg
                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </td>

            <td>
                <input type="number"
                       class="form-control form-control-sm unit-price"
                       data-stock-id="<?= $stock['id'] ?>"
                       min="0"
                       step="0.01"
                       placeholder="₦/m">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary add-to-sale"
                        data-stock-id="<?= $stock['id'] ?>">
                    <i class="bi bi-plus"></i> Add
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php endif; ?>
</tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sale Items Section -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sale Items</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addSelectedItems">
                            <i class="bi bi-plus-circle"></i> Add Selected Items
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="saleItemsTable">
        <thead>
    <tr>
        <th width="5%">#</th>
        <th>Coil Code</th>
        <th>Description</th>
        <th>Meters</th>
        <th>Weight (kg)</th>
        <th>Unit Price (₦/m)</th>
        <th>Total (₦)</th>
        <th>Action</th>
    </tr>
</thead>
                                <tbody id="saleItems">
                                    <tr id="emptyRow">
                                        <td colspan="7" class="text-center text-muted">No items added yet. Select items from above to add to sale.</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Subtotal:</td>
                                        <td class="fw-bold">₦ <span id="subtotal">0.00</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">
                                            Tax (%):
                                            <input type="number"
                                                   name="tax_rate"
                                                   id="taxRate"
                                                   class="form-control form-control-sm d-inline-block"
                                                   style="width: 80px;"
                                                   value="7.5"
                                                   min="0"
                                                   max="100"
                                                   step="0.1">
                                        </td>
                                        <td class="fw-bold">₦ <span id="tax">0.00</span></td>
                                        <td></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="6" class="text-end fw-bold fs-5">Total Amount:</td>
                                        <td class="fw-bold fs-5">₦ <span id="totalAmount">0.00</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="notes">Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="bi bi-save"></i> Create Sale & Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden form for storing sale items -->
<div id="saleItemsData" style="display: none;"></div>

<script>
const saleItems = [];

document.addEventListener('DOMContentLoaded', function() {
    
    const saleItemsTable = document.getElementById('saleItems');
    const submitBtn = document.getElementById('submitBtn');
    const saleForm = document.getElementById('saleForm');
    const taxRateInput = document.getElementById('taxRate');
    const addSelectedBtn = document.getElementById('addSelectedItems');
    
    // Add single item to sale - Event delegation on document
    document.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.add-to-sale');
        if (addBtn) {
            e.preventDefault();
            addItemToSale(addBtn.getAttribute('data-stock-id'));
        }
    });
    
   // Add multiple selected items to sale
addSelectedBtn.addEventListener('click', function(e) {
    e.preventDefault();

    const checkboxes = document.querySelectorAll('.select-stock:checked');

    if (checkboxes.length === 0) {
        alert('Please select at least one item to add.');
        return;
    }

    let addedCount = 0;
    let skippedCount = 0;
    let missingPriceCount = 0;

    checkboxes.forEach(function(checkbox) {
        const stockId = checkbox.getAttribute('data-stock-id');
        const row = document.querySelector('tr[data-stock-id="' + stockId + '"]');

        if (!row) return;

        if (saleItems.some(item => item.stockId === stockId)) {
            skippedCount++;
            checkbox.checked = false;
            return;
        }

        const availableMeters = parseFloat(row.getAttribute('data-meters')) || 0;
        if (availableMeters <= 0) return;

        const unitPriceInput = row.querySelector('.unit-price');
        const unitPrice = parseFloat(unitPriceInput.value) || 0;

        if (unitPrice <= 0) {
            missingPriceCount++;
            return;
        }

        const coilCode   = row.querySelector('.coil-code').textContent.trim();
        const description = row.querySelector('.coil-name').textContent.trim();
        const weightKg   = parseFloat(row.getAttribute('data-weight')) || 0;

        saleItems.push({
            stockId:     stockId,
            coilCode:    coilCode,
            description: description,
            quantity:    availableMeters, // meters — primary
            meters:      availableMeters,
            weightKg:    weightKg,
            unitPrice:   unitPrice,
            total:       unitPrice * availableMeters
        });

        addedCount++;
        checkbox.checked = false;
    });

    if (addedCount > 0) updateSaleTable();

    if (missingPriceCount > 0) {
        alert(missingPriceCount + ' item(s) skipped: Please enter unit prices first.');
    }
    if (skippedCount > 0) {
        alert(skippedCount + ' item(s) already in the sale list.');
    }
});
function addItemToSale(stockId) {
    const row = document.querySelector('tr[data-stock-id="' + stockId + '"]');
    if (!row) return;

    if (saleItems.some(item => item.stockId === stockId)) {
        alert('This item is already in the sale.');
        return;
    }

    const availableMeters = parseFloat(row.getAttribute('data-meters')) || 0;
    const weightKg        = parseFloat(row.getAttribute('data-weight')) || 0;
    const unitPriceInput  = row.querySelector('.unit-price');
    const unitPrice       = parseFloat(unitPriceInput.value) || 0;

    if (availableMeters <= 0) {
        alert('No meters remaining for this stock entry.');
        return;
    }

    if (unitPrice <= 0) {
        alert('Please enter a valid unit price (₦/m) before adding to sale.');
        unitPriceInput.focus();
        return;
    }

    saleItems.push({
        stockId:     stockId,
        coilCode:    row.querySelector('.coil-code').textContent.trim(),
        description: row.querySelector('.coil-name').textContent.trim(),
        quantity:    availableMeters,
        meters:      availableMeters,
        weightKg:    weightKg,
        unitPrice:   unitPrice,
        total:       unitPrice * availableMeters
    });

    updateSaleTable();
}
    function updateSaleTable() {
    
    // Clear table
    saleItemsTable.innerHTML = '';
    
    if (saleItems.length === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.id = 'emptyRow';
        emptyRow.innerHTML = '<td colspan="8" class="text-center text-muted">No items added yet. Select items from above to add to sale.</td>';
        saleItemsTable.appendChild(emptyRow);
        submitBtn.disabled = true;
    } else {
        saleItems.forEach(function(item, index) {
            const row = document.createElement('tr');
            row.setAttribute('data-stock-id', item.stockId);
            const weightDisplay = item.weightKg > 0 ? item.weightKg.toFixed(2) + ' kg' : '—';
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.coilCode}</td>
                <td>${item.description}</td>
                <td><strong>${item.meters.toFixed(2)} m</strong></td>
                <td class="text-muted">${weightDisplay}</td>
                <td>₦${item.unitPrice.toFixed(2)}/m</td>
                <td><strong>₦${item.total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item" data-stock-id="${item.stockId}">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </td>
            `;
            saleItemsTable.appendChild(row);
        });
        submitBtn.disabled = false;
    }
    
    updateTotals();
    updateFormData();
}
    // Update totals
    function updateTotals() {
        let subtotal = 0;
        
        saleItems.forEach(function(item) {
            subtotal += item.total;
        });
        
        const taxRate = parseFloat(taxRateInput.value) || 0;
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;
        
        document.getElementById('subtotal').textContent = subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('tax').textContent = tax.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('totalAmount').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    // Tax rate change listener
    taxRateInput.addEventListener('input', function() {
        updateTotals();
    });
    
    // Remove item from sale - Event delegation
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-item');
        if (removeBtn) {
            const stockId = removeBtn.getAttribute('data-stock-id');
            const index = saleItems.findIndex(item => item.stockId === stockId);
            
            if (index > -1) {
                if (confirm('Remove this item from the sale?')) {
                    saleItems.splice(index, 1);
                    updateSaleTable();
                }
            }
        }
    });
    
    // Update form data before submission
    function updateFormData() {
        const saleFormElement = document.getElementById('saleForm');
        
        // Remove any existing hidden item inputs first
        const existingInputs = saleFormElement.querySelectorAll('input[name^="unit_price["], input[name^="quantity["]');
        existingInputs.forEach(input => input.remove());
        
        // Add fresh hidden inputs for each item
        // PHP expects: unit_price[stockId] and quantity[stockId]
        saleItems.forEach(function(item) {
            const priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = 'unit_price[' + item.stockId + ']';
            priceInput.value = item.unitPrice;
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity[' + item.stockId + ']';
            quantityInput.value = item.quantity;
            
            // Append directly to the form
            saleFormElement.appendChild(priceInput);
            saleFormElement.appendChild(quantityInput);
        });
        
    }
    
    // Handle form submission
    saleForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const customerId = document.getElementById('customer_id').value;
        if (!customerId) {
            alert('Please select a customer.');
            return false;
        }

        if (saleItems.length === 0) {
            alert('Please add at least one item to the sale.');
            return false;
        }

        updateFormData();

        // Verify hidden inputs were created
        let itemsCount = 0;
        for (let pair of new FormData(saleForm).entries()) {
            if (pair[0].startsWith('unit_price[')) itemsCount++;
        }

        if (itemsCount === 0) {
            alert('Error preparing form data. Please try again.');
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        setTimeout(() => saleForm.submit(), 300);
    });
    
});

</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
