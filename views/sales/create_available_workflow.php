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
if (!hasPermission(MODULE_SALES, ACTION_CREATE)) {
    setFlashMessage('error', 'You do not have permission to create sales');
    redirect('/new-stock-system/index.php?page=sales');
}

$pageTitle = 'Create Sale from Available Stock - ' . APP_NAME;

// Get available stock entries
$stockEntryModel = new StockEntry();
$availableStock = $stockEntryModel->getAvailableStock();

// Get customers for dropdown
$customerModel = new Customer();
$customers = $customerModel->getAll();

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
                            <label for="sale_date">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" 
                                   value="<?= date('Y-m-d') ?>" required>
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
        <th>Available (KG)</th> <!-- Primary metric -->
        <th>Meters</th> <!-- Secondary info -->
        <th>Unit Price (₦/KG)</th> <!-- Price per KG -->
        <th>Action</th>
    </tr>
</thead>
<tbody id="availableStockItems">
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
            
            <!-- PRIMARY: Show KG -->
            <td class="available-weight">
                <?php if ($stock['weight_kg_remaining'] && $stock['weight_kg_remaining'] > 0): ?>
                    <strong><?= number_format($stock['weight_kg_remaining'], 2) ?> kg</strong>
                <?php else: ?>
                    <span class="text-danger">No weight data</span>
                <?php endif; ?>
            </td>
            
            <!-- SECONDARY: Show meters for reference -->
            <td class="available-meters text-muted">
                <?= number_format($stock['meters_remaining'], 2) ?> m
            </td>
            
            <td>
                <input type="number" 
                       class="form-control form-control-sm unit-price" 
                       data-stock-id="<?= $stock['id'] ?>"
                       min="0" 
                       step="0.01" 
                       placeholder="₦/KG">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary add-to-sale" 
                        data-stock-id="<?= $stock['id'] ?>"
                        <?= (!$stock['weight_kg_remaining'] || $stock['weight_kg_remaining'] <= 0) ? 'disabled title="No weight data available"' : '' ?>>
                    <i class="bi bi-plus"></i> Add
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
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
        <th>Quantity (KG)</th> <!-- PRIMARY -->
        <th>Meters</th> <!-- SECONDARY -->
        <th>Unit Price (₦/KG)</th>
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
                                        <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                        <td class="fw-bold">₦ <span id="subtotal">0.00</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">
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
                                        <td colspan="5" class="text-end fw-bold fs-5">Total Amount:</td>
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
console.log('Script starting...');

// Store sale items
const saleItems = [];

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Ready');
    
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
            console.log('Add button clicked');
            const stockId = addBtn.getAttribute('data-stock-id');
            console.log('Stock ID:', stockId);
            addItemToSale(stockId);
        }
    });
    
   // Add multiple selected items to sale
addSelectedBtn.addEventListener('click', function(e) {
    e.preventDefault();
    console.log('Add selected items clicked');
    
    const checkboxes = document.querySelectorAll('.select-stock:checked');
    console.log('Checked items:', checkboxes.length);
    
    if (checkboxes.length === 0) {
        alert('Please select at least one item to add.');
        return;
    }
    
    let addedCount = 0;
    let skippedCount = 0;
    let missingPriceCount = 0;
    let noWeightCount = 0; // NEW
    
    checkboxes.forEach(function(checkbox) {
        const stockId = checkbox.getAttribute('data-stock-id');
        const row = document.querySelector('tr[data-stock-id="' + stockId + '"]');
        
        if (!row) return;
        
        // Skip if already in sale
        if (saleItems.some(item => item.stockId === stockId)) {
            skippedCount++;
            checkbox.checked = false;
            return;
        }
        
        // Get weight (primary metric)
        const availableWeight = parseFloat(row.getAttribute('data-weight')) || 0;
        
        // Skip if no weight data
        if (availableWeight <= 0) {
            noWeightCount++;
            return;
        }
        
        const unitPriceInput = row.querySelector('.unit-price');
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        
        if (unitPrice <= 0) {
            missingPriceCount++;
            return;
        }
        
        const coilCode = row.querySelector('.coil-code').textContent.trim();
        const description = row.querySelector('.coil-name').textContent.trim();
        const availableMeters = parseFloat(row.getAttribute('data-meters')) || 0;
        
        const quantity = availableWeight; // KG
        const total = unitPrice * quantity; // ₦/KG × KG
        
        saleItems.push({
            stockId: stockId,
            coilCode: coilCode,
            description: description,
            quantity: quantity, // KG
            meters: availableMeters, // Display only
            unitPrice: unitPrice,
            total: total
        });
        
        addedCount++;
        checkbox.checked = false;
    });
    
    if (addedCount > 0) {
        console.log('Items added:', addedCount);
        updateSaleTable();
    }
    
    // Show feedback messages
    if (noWeightCount > 0) {
        alert(noWeightCount + ' item(s) skipped: No weight data available.');
    }
    if (missingPriceCount > 0) {
        alert(missingPriceCount + ' item(s) skipped: Please enter unit prices for all selected items.');
    }
    if (skippedCount > 0) {
        alert(skippedCount + ' item(s) already in the sale list.');
    }
});
    // Add item to sale function
   
   // Add item to sale function
function addItemToSale(stockId) {
    console.log('Adding item with stock ID:', stockId);
    
    const row = document.querySelector('tr[data-stock-id="' + stockId + '"]');
    
    if (!row) {
        console.log('Row not found for stock ID:', stockId);
        return;
    }
    
    // Check if item already exists in sale
    if (saleItems.some(item => item.stockId === stockId)) {
        alert('This item is already in the sale.');
        return;
    }
    
    const coilCode = row.querySelector('.coil-code').textContent.trim();
    const description = row.querySelector('.coil-name').textContent.trim();
    
    // PRIMARY: Get KG for calculation
    const availableWeight = parseFloat(row.getAttribute('data-weight')) || 0;
    
    // SECONDARY: Get meters for display only
    const availableMeters = parseFloat(row.getAttribute('data-meters')) || 0;
    
    const unitPriceInput = row.querySelector('.unit-price');
    const unitPrice = parseFloat(unitPriceInput.value) || 0;
    
    console.log('Item data:', {coilCode, description, availableWeight, availableMeters, unitPrice});
    
    // Validation
    if (availableWeight <= 0) {
        alert('No weight data available for this item. Cannot add to sale.');
        return;
    }
    
    if (unitPrice <= 0) {
        alert('Please enter a valid unit price (₦/KG) before adding to sale.');
        unitPriceInput.focus();
        return;
    }
    
    // Calculate based on KG
    const quantity = availableWeight; // In KG
    const total = unitPrice * quantity; // ₦/KG × KG = ₦
    
    const item = {
        stockId: stockId,
        coilCode: coilCode,
        description: description,
        quantity: quantity, // KG - this is what's used for sale
        meters: availableMeters, // Meters - display only
        unitPrice: unitPrice, // Per KG
        total: total
    };
    
    saleItems.push(item);
    console.log('Item added. Total items:', saleItems.length);
    console.log('Sale item:', item);
    updateSaleTable();
}
    // Update sale items table
function updateSaleTable() {
    console.log('Updating sale table. Items count:', saleItems.length);
    
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
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.coilCode}</td>
                <td>${item.description}</td>
                <td><strong>${item.quantity.toFixed(2)} kg</strong></td>
                <td class="text-muted">${item.meters.toFixed(2)} m</td>
                <td>₦${item.unitPrice.toFixed(2)}/kg</td>
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
    
    console.log('Table updated successfully');
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
                    console.log('Item removed. Remaining items:', saleItems.length);
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
        
        const hiddenInputs = saleFormElement.querySelectorAll('input[name^="unit_price["], input[name^="quantity["]');
        console.log('Form data updated. Hidden inputs:', hiddenInputs.length);
        console.log('Hidden inputs:', Array.from(hiddenInputs).map(i => i.name + ' = ' + i.value));
    }
    
    // Handle form submission
    saleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('=== FORM SUBMISSION ===');
        console.log('Sale items in array:', saleItems.length);
        console.log('Items:', JSON.stringify(saleItems, null, 2));
        
        // Validate customer selection
        const customerId = document.getElementById('customer_id').value;
        if (!customerId) {
            alert('Please select a customer.');
            return false;
        }
        
        // Validate sale items
        if (saleItems.length === 0) {
            alert('Please add at least one item to the sale.');
            return false;
        }
        
        // Update the hidden form fields with current sale items
        updateFormData();
        
        // Log what we're submitting
        const formData = new FormData(saleForm);
        console.log('=== FORM DATA BEING SUBMITTED ===');
        let itemsDataSummary = [];
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
            if (pair[0].startsWith('items[')) {
                itemsDataSummary.push(pair[0] + ' = ' + pair[1]);
            }
        }
        
        // Check if items data exists
        let itemsFound = false;
        let itemsCount = 0;
        for (let pair of formData.entries()) {
            if (pair[0].startsWith('unit_price[')) {
                itemsFound = true;
                itemsCount++;
            }
        }
        
        if (!itemsFound) {
            alert('ERROR: Form data is not being created properly. Check console.');
            console.error('No items[] fields found in form data!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Create Sale & Generate Invoice';
            return false;
        }
        
        console.log('✓ Items found in form data:', itemsCount, 'items');
        
        // Show what's being submitted
        const confirmation = `Submitting sale with ${itemsCount} item(s):\n\n${itemsDataSummary.join('\n')}\n\nCheck console for full details.`;
        console.log(confirmation);
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        // Allow time to see console logs before submission
        setTimeout(function() {
            console.log('Now submitting form...');
            saleForm.submit();
        }, 500);
    });
    
    console.log('All event listeners attached');
    
    // Debug button to show form data without submitting
    document.getElementById('debugBtn').addEventListener('click', function() {
        if (saleItems.length === 0) {
            alert('No items in sale. Add some items first.');
            return;
        }
        
        updateFormData();
        const formData = new FormData(saleForm);
        
        let debugInfo = '=== FORM DATA DEBUG ===\n\n';
        debugInfo += 'Customer ID: ' + document.getElementById('customer_id').value + '\n';
        debugInfo += 'Sale Date: ' + document.getElementById('sale_date').value + '\n';
        debugInfo += 'Notes: ' + document.getElementById('notes').value + '\n\n';
        debugInfo += 'ITEMS:\n';
        
        for (let pair of formData.entries()) {
            if (pair[0].startsWith('unit_price[') || pair[0].startsWith('quantity[')) {
                debugInfo += pair[0] + ' = ' + pair[1] + '\n';
            }
        }
        
        debugInfo += '\n=== EXPECTED PHP STRUCTURE ===\n\n';
        debugInfo += '$_POST[\'customer_id\'] = ' + document.getElementById('customer_id').value + '\n';
        debugInfo += '$_POST[\'sale_date\'] = ' + document.getElementById('sale_date').value + '\n';
        debugInfo += '$_POST[\'unit_price\'] = Array(\n';
        
        saleItems.forEach(function(item) {
            debugInfo += '  [' + item.stockId + '] => ' + item.unitPrice + '\n';
        });
        debugInfo += ')\n';
        debugInfo += '$_POST[\'quantity\'] = Array(\n';
        
        saleItems.forEach(function(item) {
            debugInfo += '  [' + item.stockId + '] => ' + item.quantity + '\n';
        });
        debugInfo += ')\n';
        
        console.log(debugInfo);
        alert(debugInfo);
    });
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
