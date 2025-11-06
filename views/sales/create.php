<?php
/**
 * Create Sale Form - Redesigned
 * Select from Stock Entries (not coils)
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Create Sale - ' . APP_NAME;

// Get customers and stock entries
$customerModel = new Customer();
$stockEntryModel = new StockEntry();

$customers = $customerModel->getAll(1000, 0);
$availableStock = $stockEntryModel->getByStatus('available'); // Available stock
$factoryStock = $stockEntryModel->getByStatus('factory_use'); // Factory use stock

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New Sale</h1>
                <p class="text-muted">Process a sale from stock entries</p>
            </div>
            <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">
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
                    <form action="/new-stock-system/controllers/sales/create/index.php" method="POST" class="needs-validation" novalidate id="saleForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <!-- Customer Selection -->
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">Select customer</option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                        data-phone="<?php echo htmlspecialchars($customer['phone']); ?>"
                                        data-company="<?php echo htmlspecialchars($customer['company'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($customer['name']); ?> - <?php echo htmlspecialchars($customer['phone']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a customer.</div>
                            <small class="form-text text-muted">
                                <a href="/new-stock-system/index.php?page=customers_create" target="_blank">Create new customer</a>
                            </small>
                        </div>
                        
                        <div id="customer_details" class="alert alert-info d-none mb-3">
                            <strong>Customer:</strong> <span id="customer_info"></span>
                        </div>
                        
                        <!-- Hidden sale type field (kept for backward compatibility) -->
                        <input type="hidden" name="sale_type" id="sale_type" value="sale">
                        
                        <!-- Stock Entry Selection -->
                        <div class="mb-3">
                            <label for="stock_entry_id" class="form-label">Select Stock Entry <span class="text-danger">*</span></label>
                            <select class="form-select" id="stock_entry_id" name="stock_entry_id" required>
                                <option value="">Select stock entry</option>
                                <?php 
                                // Combine both available and factory use stock
                                $allStock = array_merge($availableStock, $factoryStock);
                                foreach ($allStock as $entry): 
                                    $status = in_array($entry, $availableStock) ? 'available' : 'factory_use';
                                ?>
                                <option value="<?php echo $entry['id']; ?>" 
                                        data-status="<?php echo $status; ?>"
                                        data-coil-id="<?php echo $entry['coil_id']; ?>"
                                        data-coil-code="<?php echo htmlspecialchars($entry['coil_code']); ?>"
                                        data-coil-name="<?php echo htmlspecialchars($entry['coil_name']); ?>"
                                        data-coil-color="<?php echo COIL_COLORS[$entry['coil_color']] ?? $entry['coil_color']; ?>"
                                        data-coil-weight="<?php echo $entry['coil_weight']; ?>"
                                        data-coil-category="<?php echo STOCK_CATEGORIES[$entry['coil_category']]; ?>"
                                        data-meters="<?php echo $entry['meters']; ?>"
                                        data-remaining="<?php echo $entry['meters_remaining']; ?>">
                                    <?php echo htmlspecialchars($entry['coil_code']); ?> - 
                                    <?php echo htmlspecialchars($entry['coil_name']); ?> 
                                    (<?php echo number_format($entry['meters_remaining'], 2); ?>m remaining)
                                    <span class="badge bg-<?php echo $status === 'available' ? 'success' : 'warning'; ?> ms-2">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a stock entry.</div>
                        </div>
                        
                        <div id="stock_details" class="alert alert-success d-none mb-3">
                            <strong>Stock Details:</strong><br>
                            <span id="stock_info"></span>
                        </div>
                        
                        <!-- Meters and Price -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meters" class="form-label">Meters <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="meters" name="meters" 
                                       step="0.01" min="0.01" placeholder="Meters to sell" required>
                                <div class="invalid-feedback">Please provide meters.</div>
                                <small class="form-text text-muted" id="meters_hint">Enter meters to sell</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="price_per_meter" class="form-label">Price per Meter (₦) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price_per_meter" name="price_per_meter" 
                                       step="0.01" min="0.01" placeholder="e.g., 50.00" required>
                                <div class="invalid-feedback">Please provide price.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Total Amount</label>
                            <input type="text" class="form-control form-control-lg" id="total_amount_display" readonly value="₦0.00">
                            <input type="hidden" name="total_amount" id="total_amount" value="0">
                            <input type="hidden" name="coil_id" id="coil_id" value="">
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Available</strong> stock entries have fixed meter quantities that cannot be changed.</li>
                                <li><strong>Factory Use</strong> stock entries allow custom meter quantities up to the available amount.</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Create Sale & Generate Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> How It Works
                </div>
                <div class="card-body">
                    <h6>Workflow</h6>
                    <ol class="small">
                        <li>Select customer</li>
                        <li>Select stock entry from the dropdown</li>
                        <li>Meters will auto-fill based on stock type</li>
                        <li>Adjust meters if needed (for Factory Use stock)</li>
                        <li>Enter price per meter</li>
                        <li>Submit to create sale & generate invoice</li>
                    </ol>
                    
                    <hr>
                    
                    <h6>Stock Status</h6>
                    <p class="small mb-1"><span class="badge bg-success">Available</span> - Fixed meter quantities</p>
                    <p class="small"><span class="badge bg-warning">Factory Use</span> - Customizable meter quantities</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show customer details
document.getElementById('customer_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (this.value) {
        const name = selectedOption.dataset.name;
        const phone = selectedOption.dataset.phone;
        const company = selectedOption.dataset.company;
        
        let info = `${name} - ${phone}`;
        if (company) info += ` (${company})`;
        
        document.getElementById('customer_info').textContent = info;
        document.getElementById('customer_details').classList.remove('d-none');
    } else {
        document.getElementById('customer_details').classList.add('d-none');
    }
});

// Handle stock entry selection
document.getElementById('stock_entry_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const metersInput = document.getElementById('meters');
    const metersHint = document.getElementById('meters_hint');
    
    if (!this.value) {
        document.getElementById('stock_details').classList.add('d-none');
        metersInput.readOnly = false;
        document.getElementById('coil_id').value = '';
        return;
    }
    
    const status = (selectedOption.dataset.status || '').toLowerCase();
    const coilId = selectedOption.dataset.coilId;
    const coilCode = selectedOption.dataset.coilCode;
    const coilName = selectedOption.dataset.coilName;
    const coilColor = selectedOption.dataset.coilColor;
    const coilWeight = selectedOption.dataset.coilWeight;
    const coilCategory = selectedOption.dataset.coilCategory;
    const meters = parseFloat(selectedOption.dataset.meters);
    const remaining = parseFloat(selectedOption.dataset.remaining);
    
    // Update hidden coil_id field
    document.getElementById('coil_id').value = coilId;
    
    const stockInfo = `
        <strong>Coil:</strong> ${coilCode} - ${coilName}<br>
        <strong>Color:</strong> ${coilColor} | <strong>Weight:</strong> ${coilWeight}kg | <strong>Category:</strong> ${coilCategory}<br>
        <strong>Total Meters:</strong> ${meters.toFixed(2)}m | <strong>Available:</strong> ${remaining.toFixed(2)}m
    `;
    
    document.getElementById('stock_info').innerHTML = stockInfo;
    document.getElementById('stock_details').classList.remove('d-none');
    
    // Lock/unlock meters based on stock status
    if (status === 'available') {
        // Available stock: lock to remaining meters
        metersInput.value = remaining.toFixed(2);
        metersInput.readOnly = true;
        metersInput.max = remaining;
        metersHint.textContent = 'Fixed quantity (Available Stock)';
        metersHint.className = 'form-text text-muted';
    } else if (status === 'factory_use') {
        // Factory use: Allow custom up to remaining
        metersInput.value = remaining.toFixed(2);
        metersInput.max = remaining;
        metersInput.readOnly = false;
        metersHint.textContent = `Enter quantity (max: ${remaining.toFixed(2)}m)`;
        metersHint.className = 'form-text text-muted';
    }

    // Update price per meter if not set
    const pricePerMeter = document.getElementById('price_per_meter');
    if (!pricePerMeter.value) {
        pricePerMeter.value = '0.00';
    }

    // Recalculate totals whenever selection changes
    calculateTotal();
});

// Calculate total amount
function calculateTotal() {
    const meters = parseFloat(document.getElementById('meters').value) || 0;
    const pricePerMeter = parseFloat(document.getElementById('price_per_meter').value) || 0;
    const total = meters * pricePerMeter;
    
    // Update hidden total field with raw number
    document.getElementById('total_amount').value = total.toFixed(2);
    
    // Format the display value with currency symbol and thousand separators
    const formattedTotal = '₦' + total.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    document.getElementById('total_amount_display').value = formattedTotal;
    
    return total;
}

document.getElementById('meters').addEventListener('input', calculateTotal);
document.getElementById('price_per_meter').addEventListener('input', calculateTotal);

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Trigger change event on stock entry if one is selected
    const stockEntry = document.getElementById('stock_entry_id');
    if (stockEntry.value) {
        stockEntry.dispatchEvent(new Event('change'));
    }
    
    // Initialize price per meter if empty
    const pricePerMeter = document.getElementById('price_per_meter');
    if (!pricePerMeter.value) {
        pricePerMeter.value = '0.00';
    }
});

// Form validation
document.getElementById('saleForm').addEventListener('submit', function(e) {
    const stockEntry = document.getElementById('stock_entry_id');
    const metersInput = document.getElementById('meters');
    const priceInput = document.getElementById('price_per_meter');
    
    if (!stockEntry.value) {
        e.preventDefault();
        alert('Please select a stock entry.');
        return false;
    }
    
    const selectedOption = stockEntry.options[stockEntry.selectedIndex];
    const status = selectedOption.dataset.status;
    const meters = parseFloat(metersInput.value) || 0;
    const remaining = parseFloat(selectedOption.dataset.remaining) || 0;
    const pricePerMeter = parseFloat(priceInput.value) || 0;
    
    if (meters <= 0) {
        e.preventDefault();
        alert('Please enter a valid meter value.');
        metersInput.focus();
        return false;
    }
    
    if (meters > remaining) {
        e.preventDefault();
        alert(`Cannot sell ${meters}m, only ${remaining.toFixed(2)}m available.`);
        metersInput.focus();
        return false;
    }
    
    if (pricePerMeter <= 0) {
        e.preventDefault();
        alert('Please enter a valid price per meter.');
        priceInput.focus();
        return false;
    }
    
    // No need to validate sale type against stock status anymore
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
