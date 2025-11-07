<?php
/**
 * Step 2: Configure Properties for Selected Stock
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

// Check authentication
checkAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /new-stock-system/index.php?page=sales_production');
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid request. Please try again.');
    header('Location: /new-stock-system/index.php?page=sales_production');
    exit();
}

$stockEntryId = (int) ($_POST['stock_entry_id'] ?? 0);

if ($stockEntryId <= 0) {
    setFlashMessage('error', 'Invalid stock entry selected.');
    header('Location: /new-stock-system/index.php?page=sales_production');
    exit();
}

// Get stock entry and coil details
$stockEntryModel = new StockEntry();
$coilModel = new Coil();

$stockEntry = $stockEntryModel->findById($stockEntryId);

if (!$stockEntry) {
    setFlashMessage('error', 'Stock entry not found.');
    header('Location: /new-stock-system/index.php?page=sales_production');
    exit();
}

$coil = $coilModel->findById($stockEntry['coil_id']);

if (!$coil) {
    setFlashMessage('error', 'Coil not found.');
    header('Location: /new-stock-system/index.php?page=sales_production');
    exit();
}

// Get available properties for this coil category
$properties = [];
$propertyFiles = glob(
    __DIR__ . "/../../../config/production_workflow/stock/{$coil['category']}/properties/*.php",
);

foreach ($propertyFiles as $file) {
    $property = require $file;
    if (is_array($property) && isset($property['id'])) {
        $properties[] = $property;
    }
}

// Include header and sidebar
$pageTitle = 'New Sale - Configure Properties';
require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">New Sale - Configure Properties</h1>
                <p class="text-muted">Configure properties for <?php echo htmlspecialchars(
                    $coil['code'] . ' - ' . $coil['name'],
                ); ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=sales_production" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Stock Selection
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Configure Properties
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Stock Details:</strong> 
                        <?php echo htmlspecialchars($coil['code'] . ' - ' . $coil['name']); ?> | 
                        Available: <?php echo number_format($stockEntry['meters_remaining'], 2); ?>m
                    </div>
                    
                    <form id="propertiesForm" method="POST" action="?page=sales_production&step=customer">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="stock_entry_id" value="<?php echo $stockEntryId; ?>">
                        
                        <div id="propertiesContainer">
                            <?php foreach ($properties as $index => $property): ?>
                                <div class="property-group mb-4 p-3 border rounded" data-property-id="<?php echo $property[
                                    'id'
                                ]; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5><?php echo htmlspecialchars($property['label']); ?></h5>
                                        <?php if ($index > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-property">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Sheets</label>
                                                <input type="number" 
                                                       name="properties[<?php echo $property[
                                                           'id'
                                                       ]; ?>][sheet_qty]" 
                                                       class="form-control sheet-qty" 
                                                       min="1" 
                                                       value="1" 
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Meters per Sheet</label>
                                                <input type="number" 
                                                       name="properties[<?php echo $property[
                                                           'id'
                                                       ]; ?>][sheet_meter]" 
                                                       class="form-control sheet-meter" 
                                                       step="0.01" 
                                                       min="0.1" 
                                                       value="1.00" 
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Total Meters</label>
                                                <input type="text" 
                                                       class="form-control total-meters" 
                                                       readonly 
                                                       value="1.00">
                                            </div>
                                        </div>
                                        <?php if ($property['price_required'] ?? false): ?>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Price per Meter (₦)</label>
                                                    <input type="number" 
                                                           name="properties[<?php echo $property[
                                                               'id'
                                                           ]; ?>][price_per_meter]" 
                                                           class="form-control price-per-meter" 
                                                           step="0.01" 
                                                           min="0.01" 
                                                           value="0.00" 
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Subtotal (₦)</label>
                                                    <input type="text" 
                                                           class="form-control property-subtotal" 
                                                           readonly 
                                                           value="0.00">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (count($properties) > 1): ?>
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" id="addProperty" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus-circle"></i> Add Another Property
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Total Meters: <span id="totalMeters">0.00</span>m</h5>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <h4>Grand Total: ₦<span id="grandTotal">0.00</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/new-stock-system/index.php?page=sales_production" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" id="saveDraft">
                                    <i class="bi bi-save"></i> Save Draft
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Next: Customer Details <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate total meters and subtotal for a property group
    function calculatePropertyTotal(group) {
        const qty = parseFloat(group.querySelector('.sheet-qty').value) || 0;
        const meter = parseFloat(group.querySelector('.sheet-meter').value) || 0;
        const priceInput = group.querySelector('.price-per-meter');
        const price = priceInput ? parseFloat(priceInput.value) || 0 : 0;
        
        const totalMeters = qty * meter;
        const subtotal = totalMeters * price;
        
        // Update display
        group.querySelector('.total-meters').value = totalMeters.toFixed(2);
        if (priceInput) {
            group.querySelector('.property-subtotal').value = subtotal.toFixed(2);
        }
        
        // Update grand total
        updateGrandTotal();
        
        return { totalMeters, subtotal };
    }
    
    // Update grand total
    function updateGrandTotal() {
        let totalMeters = 0;
        let grandTotal = 0;
        
        document.querySelectorAll('.property-group').forEach(group => {
            const result = calculatePropertyTotal(group);
            totalMeters += result.totalMeters;
            grandTotal += result.subtotal;
        });
        
        document.getElementById('totalMeters').textContent = totalMeters.toFixed(2);
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
    }
    
    // Add event listeners to all input fields
    function initPropertyInputs() {
        document.querySelectorAll('.property-group').forEach(group => {
            // Remove existing listeners to prevent duplicates
            const qtyInput = group.querySelector('.sheet-qty');
            const meterInput = group.querySelector('.sheet-meter');
            const priceInput = group.querySelector('.price-per-meter');
            
            // Clone inputs to remove event listeners
            const newQty = qtyInput.cloneNode(true);
            const newMeter = meterInput.cloneNode(true);
            qtyInput.parentNode.replaceChild(newQty, qtyInput);
            meterInput.parentNode.replaceChild(newMeter, meterInput);
            
            // Add new event listeners
            newQty.addEventListener('input', updateGrandTotal);
            newMeter.addEventListener('input', updateGrandTotal);
            
            if (priceInput) {
                const newPrice = priceInput.cloneNode(true);
                priceInput.parentNode.replaceChild(newPrice, priceInput);
                newPrice.addEventListener('input', updateGrandTotal);
            }
            
            // Add remove button functionality
            const removeBtn = group.querySelector('.remove-property');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (document.querySelectorAll('.property-group').length > 1) {
                        group.remove();
                        updateGrandTotal();
                    } else {
                        alert('At least one property is required.');
                    }
                });
            }
        });
    }
    
    // Add new property (duplicate the first property group)
    document.getElementById('addProperty')?.addEventListener('click', function() {
        const firstGroup = document.querySelector('.property-group');
        if (firstGroup) {
            const newGroup = firstGroup.cloneNode(true);
            
            // Clear values in the new group
            newGroup.querySelectorAll('input[type="number"]').forEach(input => {
                if (input.classList.contains('sheet-qty')) {
                    input.value = '1';
                } else if (input.classList.contains('sheet-meter')) {
                    input.value = '1.00';
                } else if (input.classList.contains('price-per-meter')) {
                    input.value = '0.00';
                }
            });
            
            // Reset calculated fields
            newGroup.querySelector('.total-meters').value = '0.00';
            const subtotalInput = newGroup.querySelector('.property-subtotal');
            if (subtotalInput) subtotalInput.value = '0.00';
            
            // Make sure remove button is visible
            const removeBtn = newGroup.querySelector('.remove-property');
            if (removeBtn) removeBtn.style.display = 'block';
            
            // Insert before the add property button
            const addBtn = document.querySelector('#addProperty').parentElement.parentElement;
            addBtn.parentNode.insertBefore(newGroup, addBtn);
            
            // Reinitialize inputs
            initPropertyInputs();
            updateGrandTotal();
        }
    });
    
    // Initialize
    initPropertyInputs();
    updateGrandTotal();
    
    // Form validation
    document.getElementById('propertiesForm').addEventListener('submit', function(e) {
        const totalMeters = parseFloat(document.getElementById('totalMeters').textContent) || 0;
        const availableMeters = <?php echo (float) $stockEntry['meters_remaining']; ?>;
        
        if (totalMeters <= 0) {
            e.preventDefault();
            alert('Please add at least one property with valid values.');
            return false;
        }
        
        if (totalMeters > availableMeters) {
            e.preventDefault();
            alert(`Total meters (${totalMeters.toFixed(2)}m) exceed available meters (${availableMeters.toFixed(2)}m).`);
            return false;
        }
        
        // Additional validation can be added here
    });
    
    // Save draft functionality
    document.getElementById('saveDraft')?.addEventListener('click', function() {
        // Implement draft saving logic here
        alert('Draft saving functionality will be implemented here.');
    });
});
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php';
?>
